<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MahasiswaRequestController extends Controller
{
    public function index()
    {
        $mahasiswa = DB::table('mahasiswa')->where('user_id', Auth::id())->first();
        $mahasiswaId = $mahasiswa->id ?? 0;

        $requests = DB::table('reqDokumen')
            ->where('mahasiswa_id', $mahasiswaId)
            ->leftJoinSub(
                DB::table('fileDetail')
                    ->selectRaw('"reqDokumen_id", COUNT(*) as cnt')
                    ->groupByRaw('"reqDokumen_id"'),
                'fd',
                DB::raw('fd."reqDokumen_id"'),
                '=',
                'reqDokumen.id'
            )
            ->selectRaw('"reqDokumen".*, COALESCE(fd.cnt, 0) > 0 as has_file')
            ->orderByDesc('reqDokumen.created_at')
            ->get();

        return view('mahasiswa.request.index', compact('requests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipeDkmn' => 'required|string',
            'message'  => 'required|string|max:500',
        ], [
            'tipeDkmn.required' => 'Jenis dokumen wajib dipilih.',
            'message.required'  => 'Keperluan wajib diisi.',
        ]);

        $mahasiswa = DB::table('mahasiswa')->where('user_id', Auth::id())->first();
        if (!$mahasiswa) {
            return back()->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        DB::table('reqDokumen')->insert([
            'user_id'      => Auth::id(),
            'mahasiswa_id' => $mahasiswa->id,
            'tipeDkmn'     => $request->tipeDkmn,
            'namaDkmn'     => str_replace('_', ' ', $request->tipeDkmn),
            'message'      => $request->message,
            'status'       => 'pending',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return redirect()->route('mahasiswa.request.create')
            ->with('success', 'Request berhasil dikirim. KLN akan segera memproses permintaan Anda.');
    }

    public function downloadFile(int $id)
    {
        $req = DB::table('reqDokumen')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        abort_if(!$req, 403);

        $file = DB::table('fileDetail')
            ->where(DB::raw('"reqDokumen_id"'), $id)
            ->orderByDesc('created_at')
            ->first();

        abort_if(!$file, 404);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('local');
        abort_if(!$storage->exists($file->path), 404);

        return $storage->download($file->path, basename($file->path));
    }
}
