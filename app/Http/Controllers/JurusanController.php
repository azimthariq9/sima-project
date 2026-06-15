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
            'judul'      => $judul,
            'pesan'      => $pesan,
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
        // Total mahasiswa asing aktif di jurusan ini
        $totalMahasiswa = DB::table('mahasiswa')
            ->where('status', 'aktif')
            ->count();

        // Request dokumen pending ke jurusan
        $pendingRequest = DB::table('request_dokumen')
            ->where('req_bagian', 'jurusan')
            ->where('status', 'pending')
            ->count();

        // Jadwal minggu ini
        $totalJadwal = DB::table('jadwal')
            ->where('jenis', 'Jurusan')
            ->count();

        // Pengumuman terbaru (3)
        $announcements = DB::table('announcement')
            ->where('sumber', 'jurusan')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Request terbaru (5)
        $requestTerbaru = DB::table('request_dokumen')
            ->join('mahasiswa', 'request_dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('request_dokumen.req_bagian', 'jurusan')
            ->select(
                'request_dokumen.*',
                'mahasiswa.nama as nama_mahasiswa',
                'mahasiswa.npm'
            )
            ->orderBy('request_dokumen.created_at', 'desc')
            ->limit(5)
            ->get();

        // Jadwal hari ini
        $hariIni = now()->locale('id')->translatedFormat('l');
        $jadwalHariIni = DB::table('jadwal')
            ->leftJoin('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
            ->leftJoin('users as dosen_user', 'jadwal.dosen_id', '=', 'dosen_user.id')
            ->where('jadwal.jenis', 'Jurusan')
            ->where('jadwal.hari', $hariIni)
            ->select(
                'jadwal.*',
                'matakuliah.namaMk as nama_matkul',
                'dosen_user.name   as nama_dosen'
            )
            ->orderBy('jadwal.jam_mulai')
            ->get();

        $unreadNotifCount = $this->unreadNotif();

        return view('jurusan.dashboard', compact(
            'totalMahasiswa',
            'pendingRequest',
            'totalJadwal',
            'announcements',
            'requestTerbaru',
            'jadwalHariIni',
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
        $user = Auth::user();
        $unreadNotifCount = $this->unreadNotif();

        return view('jurusan.profil', compact('user', 'unreadNotifCount'));
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
        $announcements = DB::table('announcement')
            ->where('sumber', 'jurusan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $unreadNotifCount = $this->unreadNotif();

        return view('jurusan.announcement', compact('announcements', 'unreadNotifCount'));
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
        $search = $request->input('search');
        $status = $request->input('status');

        $query = DB::table('dokumen')
            ->join('mahasiswa', 'dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('dokumen.uploaded_by_role', 'jurusan')
            ->select(
                'dokumen.*',
                'mahasiswa.nama as nama_mahasiswa',
                'mahasiswa.npm'
            );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('mahasiswa.nama', 'like', "%{$search}%")
                  ->orWhere('mahasiswa.npm',  'like', "%{$search}%")
                  ->orWhere('dokumen.jenis',  'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('dokumen.status', $status);
        }

        $dokumens = $query->orderBy('dokumen.created_at', 'desc')->paginate(15)->withQueryString();
        $unreadNotifCount = $this->unreadNotif();

        return view('jurusan.dokumen', compact('dokumens', 'search', 'status', 'unreadNotifCount'));
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
            'file'   => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'jenis'  => 'required|string|max:100',
        ]);

        $reqDokumen = DB::table('request_dokumen')
            ->join('mahasiswa', 'request_dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->where('request_dokumen.id', $id)
            ->select('request_dokumen.*', 'mahasiswa.nama as nama_mahasiswa', 'users.id as user_id')
            ->first();

        if (!$reqDokumen) {
            return back()->with('error', 'Request dokumen tidak ditemukan.');
        }

        // Simpan file
        $path = $request->file('file')->store(
            'dokumen/jurusan/' . $reqDokumen->mahasiswa_id,
            'public'
        );

        // Catat di tabel dokumen
        DB::table('dokumen')->insert([
            'mahasiswa_id'     => $reqDokumen->mahasiswa_id,
            'request_dokumen_id'=> $id,
            'jenis'            => $request->jenis,
            'file_path'        => $path,
            'uploaded_by_role' => 'jurusan',
            'status'           => 'selesai',
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Update status request
        DB::table('request_dokumen')
            ->where('id', $id)
            ->update([
                'status'          => 'selesai',
                'tanggal_upload'  => now(),
                'updated_at'      => now(),
            ]);

        // Kirim notifikasi ke mahasiswa
        $this->kirimNotifMahasiswa(
            $reqDokumen->user_id,
            'Dokumen Siap',
            "Dokumen '{$request->jenis}' dari Jurusan sudah siap. Silakan unduh di menu Dokumen."
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
        $status = $request->input('status');
        $search = $request->input('search');

        $query = DB::table('request_dokumen')
            ->join('mahasiswa', 'request_dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('request_dokumen.req_bagian', 'jurusan')
            ->select(
                'request_dokumen.*',
                'mahasiswa.nama as nama_mahasiswa',
                'mahasiswa.npm'
            );

        if ($status) {
            $query->where('request_dokumen.status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('mahasiswa.nama', 'like', "%{$search}%")
                  ->orWhere('mahasiswa.npm',  'like', "%{$search}%")
                  ->orWhere('request_dokumen.jenis_dokumen', 'like', "%{$search}%");
            });
        }

        $requests = $query->orderBy('request_dokumen.created_at', 'desc')->paginate(15)->withQueryString();

        $totalPending  = DB::table('request_dokumen')->where('req_bagian', 'jurusan')->where('status', 'pending')->count();
        $totalProses   = DB::table('request_dokumen')->where('req_bagian', 'jurusan')->where('status', 'diproses')->count();
        $totalSelesai  = DB::table('request_dokumen')->where('req_bagian', 'jurusan')->where('status', 'selesai')->count();

        $unreadNotifCount = $this->unreadNotif();

        return view('jurusan.request-dokumen', compact(
            'requests',
            'status',
            'search',
            'totalPending',
            'totalProses',
            'totalSelesai',
            'unreadNotifCount'
        ));
    }

    /**
     * Detail satu request dokumen.
     * Route: GET jurusan/requestDok/{id} -> jurusan.request.show
     */
    public function showReqDocument(int $id)
    {
        $req = DB::table('request_dokumen')
            ->join('mahasiswa', 'request_dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('request_dokumen.id', $id)
            ->where('request_dokumen.req_bagian', 'jurusan')
            ->select('request_dokumen.*', 'mahasiswa.nama as nama_mahasiswa', 'mahasiswa.npm')
            ->first();

        if (!$req) {
            return back()->with('error', 'Request tidak ditemukan.');
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
            'status'  => 'required|in:diproses,selesai,ditolak',
            'catatan' => 'nullable|string|max:500',
        ]);

        $req = DB::table('request_dokumen')
            ->join('mahasiswa', 'request_dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->where('request_dokumen.id', $id)
            ->where('request_dokumen.req_bagian', 'jurusan')
            ->select('request_dokumen.*', 'users.id as user_id', 'mahasiswa.nama as nama_mahasiswa')
            ->first();

        if (!$req) {
            return back()->with('error', 'Request tidak ditemukan.');
        }

        DB::table('request_dokumen')
            ->where('id', $id)
            ->update([
                'status'     => $request->status,
                'catatan'    => $request->catatan,
                'updated_at' => now(),
            ]);

        $pesanNotif = match($request->status) {
            'diproses' => "Permintaan dokumen '{$req->jenis_dokumen}' Anda sedang diproses oleh Jurusan.",
            'selesai'  => "Dokumen '{$req->jenis_dokumen}' dari Jurusan sudah selesai. Silakan unduh.",
            'ditolak'  => "Permintaan dokumen '{$req->jenis_dokumen}' tidak dapat diproses." . ($request->catatan ? " Alasan: {$request->catatan}" : ''),
            default    => "Status permintaan dokumen Anda telah diperbarui.",
        };

        $this->kirimNotifMahasiswa($req->user_id, 'Update Dokumen', $pesanNotif);

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
