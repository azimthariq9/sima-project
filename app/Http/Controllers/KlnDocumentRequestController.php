<?php

namespace App\Http\Controllers;

use App\Services\DocumentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class KlnDocumentRequestController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['status' => ['nullable', Rule::in(['pending', 'approved', 'rejected'])], 'search' => ['nullable', 'string', 'max:100']]);
        $base = DB::table('reqDokumen');
        $total = (clone $base)->count();
        $pending = (clone $base)->where('status', 'pending')->count();
        $approved = (clone $base)->where('status', 'approved')->count();
        $rejected = (clone $base)->where('status', 'rejected')->count();
        $requests = $base->join('mahasiswa', 'reqDokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->when($request->filled('status'), fn ($q) => $q->where('reqDokumen.status', $request->input('status')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.mb_strtolower($request->input('search')).'%';
                $q->where(fn ($q) => $q->whereRaw('LOWER(mahasiswa.nama) LIKE ?', [$term])->orWhereRaw('LOWER(mahasiswa.npm) LIKE ?', [$term]));
            })
            ->select('reqDokumen.*', 'mahasiswa.nama as nama_mahasiswa', 'mahasiswa.npm')
            ->orderByDesc('reqDokumen.id')->get();
        return view('kln.dokumen', compact('requests', 'total', 'pending', 'approved', 'rejected'));
    }

    public function show(int $id)
    {
        $req = DB::table('reqDokumen')->join('mahasiswa', 'reqDokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('reqDokumen.id', $id)->select('reqDokumen.*', 'mahasiswa.nama', 'mahasiswa.npm')->first();
        abort_unless($req, 404);
        $file = DB::table('fileDetail')->where('reqDokumen_id', $id)->orderByDesc('id')->first();
        return response()->json([
            'id' => $req->id, 'mahasiswa' => $req->nama, 'npm' => $req->npm,
            'tipe' => $req->tipeDkmn, 'status' => $req->status, 'message' => $req->message,
            'keterangan' => $req->keterangan,
            'file' => $file ? $this->fileInfo($file->path, $file->mimeType, $file->fileSize, $id) : null,
        ]);
    }

    public function reject(Request $request, int $id, DocumentNotification $notifications)
    {
        $data = $request->validate(['keterangan' => ['required', 'string', 'max:1000']]);
        DB::transaction(function () use ($id, $data, $notifications) {
            $req = DB::table('reqDokumen')->where('id', $id)->lockForUpdate()->first();
            abort_unless($req, 404);
            abort_if($req->status === 'approved', 409, 'Request sudah selesai. Muat ulang halaman untuk melihat status terbaru.');
            DB::table('reqDokumen')->where('id', $id)->update([
                'status' => 'rejected', 'keterangan' => $data['keterangan'], 'updated_at' => now(),
            ]);
            $notifications->student($req->mahasiswa_id, 'Request Dokumen Ditolak', 'REQ-'.$id.' ditolak KLN. Alasan: '.$data['keterangan']);
        });
        return response()->json(['success' => true]);
    }

    public function upload(Request $request, int $id, DocumentNotification $notifications)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,xls,xlsx,csv', 'max:10240']]);
        abort_unless(DB::table('reqDokumen')->where('id', $id)->exists(), 404);
        $file = $request->file('file');
        $path = $file->store('req_dokumen/'.$id, 'local');
        abort_unless($path, 500, 'Penyimpanan file gagal.');
        try {
            DB::transaction(function () use ($id, $file, $path, $notifications) {
                $req = DB::table('reqDokumen')->where('id', $id)->lockForUpdate()->first();
                abort_unless($req, 404);
                // Keep previous versions intact until the new file and status are committed.
                DB::table('fileDetail')->insert([
                    'reqDokumen_id' => $id, 'path' => $path, 'mimeType' => $file->getMimeType(), 'fileSize' => $file->getSize(),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                DB::table('reqDokumen')->where('id', $id)->update(['status' => 'approved', 'keterangan' => null, 'updated_at' => now()]);
                $notifications->student($req->mahasiswa_id, 'Dokumen dari KLN Siap', 'REQ-'.$id.' disetujui. Unduh hasilnya melalui menu Request ke KLN.');
            });
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);
            throw $e;
        }
        return response()->json(['success' => true, 'file' => $this->fileInfo($path, $file->getMimeType(), $file->getSize(), $id)]);
    }

    public function download(int $id)
    {
        abort_unless(DB::table('reqDokumen')->where('id', $id)->exists(), 404);
        $file = DB::table('fileDetail')->where('reqDokumen_id', $id)->orderByDesc('id')->first();
        abort_unless($file, 404);
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($file->path)) {
                return Storage::disk($disk)->download($file->path, basename($file->path));
            }
        }
        abort(404, 'File tidak ditemukan.');
    }

    public function destroy(int $id)
    {
        DB::transaction(function () use ($id) {
            abort_unless(DB::table('reqDokumen')->where('id', $id)->lockForUpdate()->first(), 404);
            DB::table('fileDetail')->where('reqDokumen_id', $id)->delete();
            DB::table('reqDokumen')->where('id', $id)->delete();
        });
        return response()->json(['success' => true]);
    }

    private function fileInfo(string $path, string $mime, int $size, int $id): array
    {
        return ['path' => basename($path), 'mimeType' => $mime, 'fileSize' => $size, 'url' => route('kln.dokumen.file', $id)];
    }
}
