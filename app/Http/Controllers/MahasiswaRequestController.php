<?php

namespace App\Http\Controllers;

use App\Enums\TipeDok;
use App\Services\DocumentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MahasiswaRequestController extends Controller
{
    public function index(Request $request)
    {
        $studentId = DB::table('mahasiswa')->where('user_id', $request->user()->id)->value('id');
        abort_unless($studentId, 403, 'Profil mahasiswa tidak ditemukan.');
        $requests = DB::table('reqDokumen')->where('mahasiswa_id', $studentId)
            ->select('reqDokumen.*')->selectSub(
                DB::table('fileDetail')->selectRaw('COUNT(*)')->whereColumn('reqDokumen_id', 'reqDokumen.id'), 'has_file'
            )->orderByDesc('id')->get();
        return view('mahasiswa.request.index', compact('requests'));
    }

    public function store(Request $request, DocumentNotification $notifications)
    {
        $data = $request->validate([
            'tipeDkmn' => ['required', Rule::enum(TipeDok::class)],
            'message' => ['required', 'string', 'max:255'],
        ], [
            'tipeDkmn.required' => 'Jenis dokumen wajib dipilih.',
            'message.required' => 'Keperluan wajib diisi.',
            'message.max' => 'Keperluan maksimal 255 karakter.',
        ]);
        $student = DB::table('mahasiswa')->where('user_id', $request->user()->id)->first();
        abort_unless($student, 403, 'Profil mahasiswa tidak ditemukan.');
        DB::transaction(function () use ($student, $request, $data, $notifications) {
            $id = DB::table('reqDokumen')->insertGetId([
                'user_id' => $request->user()->id, 'mahasiswa_id' => $student->id,
                'tipeDkmn' => $data['tipeDkmn'], 'namaDkmn' => str_replace('_', ' ', $data['tipeDkmn']),
                'message' => $data['message'], 'status' => 'pending',
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $notifications->kln('Request Dokumen Baru', 'REQ-'.$id.' dari '.$student->nama.' ('.$student->npm.'). Periksa menu Request Documents.');
        });
        return redirect()->route('mahasiswa.request.index')->with('success', 'Request berhasil dikirim ke KLN. Pantau status dan unduh hasilnya di halaman ini.');
    }

    public function quick(Request $request, DocumentNotification $notifications)
    {
        // Keep the legacy endpoint, but use the same table and validation as the main form.
        $request->validate(['req_bagian' => ['nullable', Rule::in(['kln', 'KLN'])]]);
        $request->merge([
            'tipeDkmn' => $request->input('tipeDkmn', $request->input('jenis_dokumen')),
            'message' => $request->input('message', $request->input('deskripsi')),
        ]);
        return $this->store($request, $notifications);
    }

    public function downloadFile(Request $request, int $id)
    {
        $studentId = DB::table('mahasiswa')->where('user_id', $request->user()->id)->value('id');
        $req = DB::table('reqDokumen')->where('id', $id)->where('mahasiswa_id', $studentId)
            ->where('user_id', $request->user()->id)->where('status', 'approved')->first();
        abort_unless($req, 404);
        $file = DB::table('fileDetail')->where('reqDokumen_id', $id)->orderByDesc('id')->first();
        abort_unless($file, 404, 'File tidak tersedia.');
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($file->path)) {
                return Storage::disk($disk)->download($file->path, basename($file->path));
            }
        }
        abort(404, 'File tidak ditemukan.');
    }
}
