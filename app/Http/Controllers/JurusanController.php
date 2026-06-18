<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class JurusanController extends Controller
{
    /*
    |==========================================================================
    | HELPER PRIVATE
    |==========================================================================
    */

    private function unreadNotif(): int
    {
        return DB::table('notification_users')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
    }

    private function kirimNotifMahasiswa(int $userId, string $judul, string $pesan): void
    {
        $notifId = DB::table('notification')->insertGetId([
            'subject'    => $judul,
            'message'    => $pesan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('notification_users')->insert([
            'user_id'         => $userId,
            'notification_id' => $notifId,
            'is_read'         => false,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }


    /*
    |==========================================================================
    | DASHBOARD
    | Route: GET jurusan/dashboard -> jurusan.dashboard
    |==========================================================================
    */

    public function dashboard()
    {
        $totalMahasiswa  = DB::table('mahasiswa')->count();
        $jadwalAktif     = DB::table('jadwal')->count();
        $totalMatakuliah = DB::table('matakuliah')->count();
        $totalDosen      = DB::table('dosen')->count();

        $pendingRequest = DB::table('reqDokumen')
            ->where('status', 'pending')
            ->count();

        $announcements = DB::table('announcement')
            ->where('sumber', 'jurusan')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        $requestTerbaru = DB::table('reqDokumen')
            ->join('mahasiswa', 'reqDokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->select('reqDokumen.*', 'mahasiswa.nama as nama_mahasiswa', 'mahasiswa.npm')
            ->orderBy('reqDokumen.created_at', 'desc')
            ->limit(5)
            ->get();

        $hariIni = now()->locale('id')->translatedFormat('l');
        $jadwalHariIni = DB::table('jadwal')
            ->leftJoin('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
            ->leftJoin('dosen', 'jadwal.dosen_id', '=', 'dosen.id')
            ->where('jadwal.hari', $hariIni)
            ->select('jadwal.*', 'matakuliah.namaMk as nama_matkul', 'dosen.nama as nama_dosen')
            ->orderBy('jadwal.jam')
            ->get();

        $unreadNotifCount = $this->unreadNotif();

        return view('jurusan.dashboard', compact(
            'totalMahasiswa', 'jadwalAktif', 'totalMatakuliah', 'totalDosen',
            'pendingRequest', 'announcements', 'requestTerbaru', 'jadwalHariIni',
            'unreadNotifCount'
        ));
    }


    /*
    |==========================================================================
    | PROFIL
    | Route: GET jurusan/profil -> jurusan.profil
    |==========================================================================
    */

    public function profil()
    {
        return redirect()->route('jurusan.dashboard')
            ->with('info', 'Halaman profil sedang dalam pengembangan.');
    }


    /*
    |==========================================================================
    | NOTIFIKASI
    | Route: GET jurusan/notifikasi -> jurusan.notifikasi
    |==========================================================================
    */

    public function notifikasi()
    {
        DB::table('notification_users')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'updated_at' => now()]);

        $notifikasi = DB::table('notification_users')
            ->join('notification', 'notification_users.notification_id', '=', 'notification.id')
            ->where('notification_users.user_id', Auth::id())
            ->select('notification.*', 'notification_users.is_read', 'notification_users.created_at as received_at')
            ->orderBy('notification_users.created_at', 'desc')
            ->paginate(15);

        return view('jurusan.notifikasi', compact('notifikasi'));
    }


    /*
    |==========================================================================
    | ANNOUNCEMENT
    | Route: GET jurusan/announcement -> jurusan.announcement
    |==========================================================================
    */

    public function announcement()
    {
        return redirect()->route('jurusan.dashboard')
            ->with('info', 'Halaman announcement sedang dalam pengembangan.');
    }


    /*
    |==========================================================================
    | DOKUMEN
    |==========================================================================
    */

    /**
     * Daftar semua dokumen yang pernah diupload oleh jurusan.
     * Route: GET jurusan/dokumen -> jurusan.dokumen.index
     */
    public function dokumen(Request $request)
    {
        return redirect()->route('jurusan.dashboard')
            ->with('info', 'Halaman dokumen sedang dalam pengembangan.');
    }

    /**
     * Detail satu dokumen.
     * Route: GET jurusan/dokumen/{id} -> jurusan.dokumen.show
     */
    public function showDokumen(int $id)
    {
        $dokumen = DB::table('dokumen')
            ->join('mahasiswa', 'dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('dokumen.id', $id)
            ->select('dokumen.*', 'mahasiswa.nama as nama_mahasiswa', 'mahasiswa.npm')
            ->first();

        if (!$dokumen) {
            return back()->with('error', 'Dokumen tidak ditemukan.');
        }

        return response()->json($dokumen);
    }

    /**
     * Upload / update file dokumen ke mahasiswa.
     * Route: POST jurusan/dokumen/{id}/upload -> jurusan.dokumen.upload
     */
    public function uploadDokumen(Request $request, int $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $req = DB::table('reqDokumen')
            ->join('mahasiswa', 'reqDokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->where('reqDokumen.id', $id)
            ->select('reqDokumen.*', 'mahasiswa.nama as nama_mahasiswa', 'users.id as user_id')
            ->first();

        if (!$req) {
            return back()->with('error', 'Request dokumen tidak ditemukan.');
        }

        $file = $request->file('file');
        $path = $file->store('dokumen/jurusan/' . $req->mahasiswa_id, 'public');

        DB::table('fileDetail')->insert([
            'reqDokumen_id' => $id,
            'path'          => $path,
            'mimeType'      => $file->getClientMimeType(),
            'fileSize'      => $file->getSize(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('reqDokumen')
            ->where('id', $id)
            ->update(['status' => 'selesai', 'updated_at' => now()]);

        $this->kirimNotifMahasiswa(
            $req->user_id,
            'Dokumen Jurusan Siap',
            "Dokumen '{$req->namaDkmn}' dari Jurusan sudah siap. Silakan unduh di menu Dokumen."
        );

        return back()->with('success', 'Dokumen berhasil diupload dan mahasiswa telah diberitahu.');
    }


    /*
    |==========================================================================
    | REQUEST DOKUMEN
    |==========================================================================
    */

    /**
     * Daftar semua request dokumen yang masuk ke jurusan.
     * Route: GET jurusan/requestDok -> jurusan.request.index
     */
    public function indexReqDocument(Request $request)
    {
        return redirect()->route('jurusan.dashboard')
            ->with('info', 'Halaman request dokumen sedang dalam pengembangan.');
    }

    /**
     * Detail satu request dokumen.
     * Route: GET jurusan/requestDok/{id} -> jurusan.request.show
     */
    public function showReqDocument(int $id)
    {
        $req = DB::table('reqDokumen')
            ->join('mahasiswa', 'reqDokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('reqDokumen.id', $id)
            ->select('reqDokumen.*', 'mahasiswa.nama as nama_mahasiswa', 'mahasiswa.npm')
            ->first();

        if (!$req) {
            return response()->json(['error' => 'Request tidak ditemukan.'], 404);
        }

        return response()->json($req);
    }

    /**
     * Update status request (pending -> diproses -> selesai / ditolak).
     * Route: PATCH jurusan/requestDok/{id}/status -> jurusan.request.status
     */
    public function updateReqDokumen(Request $request, int $id)
    {
        $request->validate([
            'status'  => 'required|in:pending,diproses,selesai,ditolak',
            'message' => 'nullable|string|max:500',
        ]);

        $req = DB::table('reqDokumen')
            ->join('mahasiswa', 'reqDokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->where('reqDokumen.id', $id)
            ->select('reqDokumen.*', 'users.id as user_id', 'mahasiswa.nama as nama_mahasiswa')
            ->first();

        if (!$req) {
            return back()->with('error', 'Request tidak ditemukan.');
        }

        $updateData = ['status' => $request->status, 'updated_at' => now()];
        if ($request->filled('message')) {
            $updateData['message'] = $request->message;
        }

        DB::table('reqDokumen')->where('id', $id)->update($updateData);

        $pesanNotif = match($request->status) {
            'diproses' => "Permintaan dokumen '{$req->namaDkmn}' Anda sedang diproses oleh Jurusan.",
            'selesai'  => "Dokumen '{$req->namaDkmn}' dari Jurusan sudah selesai. Silakan unduh.",
            'ditolak'  => "Permintaan dokumen '{$req->namaDkmn}' tidak dapat diproses." . ($request->message ? " Alasan: {$request->message}" : ''),
            default    => "Status permintaan dokumen Anda telah diperbarui.",
        };

        $this->kirimNotifMahasiswa($req->user_id, 'Update Dokumen Jurusan', $pesanNotif);

        return back()->with('success', 'Status request berhasil diperbarui.');
    }

    /**
     * Upload dokumen langsung dari halaman request.
     * Route: POST jurusan/requestDok/{id}/upload -> jurusan.request.upload
     */
    public function uploadReqDokumen(Request $request, int $id)
    {
        return $this->uploadDokumen($request, $id);
    }
}
