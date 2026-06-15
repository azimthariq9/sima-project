<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BipaController extends Controller
{
    /*
    |==========================================================================
    | HELPER PRIVATE
    |==========================================================================
    */

    private function unreadNotif(): int
    {
        return DB::table('notifikasi')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
    }

    private function kirimNotifMahasiswa(int $userId, string $judul, string $pesan): void
    {
        DB::table('notifikasi')->insert([
            'user_id'    => $userId,
            'judul'      => $judul,
            'pesan'      => $pesan,
            'is_read'    => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }


    /*
    |==========================================================================
    | DASHBOARD
    | Route: GET bipa/dashboard -> bipa.dashboard
    |==========================================================================
    */

    public function dashboard()
    {
        $totalMahasiswa = DB::table('mahasiswa')
            ->where('status', 'aktif')
            ->count();

        $pendingRequest = DB::table('request_dokumen')
            ->where('req_bagian', 'bipa')
            ->where('status', 'pending')
            ->count();

        $totalJadwal = DB::table('jadwal')
            ->where('jenis', 'BIPA')
            ->count();

        // Sertifikat BIPA yang sudah dikeluarkan bulan ini
        $sertifikatBulanIni = DB::table('dokumen')
            ->where('uploaded_by_role', 'bipa')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $announcements = DB::table('announcements')
            ->where('role_pengirim', 'bipa')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        $requestTerbaru = DB::table('request_dokumen')
            ->join('mahasiswa', 'request_dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('request_dokumen.req_bagian', 'bipa')
            ->select(
                'request_dokumen.*',
                'mahasiswa.nama as nama_mahasiswa',
                'mahasiswa.npm'
            )
            ->orderBy('request_dokumen.created_at', 'desc')
            ->limit(5)
            ->get();

        // Jadwal BIPA hari ini
        $hariIni = now()->locale('id')->translatedFormat('l');
        $jadwalHariIni = DB::table('jadwal')
            ->leftJoin('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
            ->leftJoin('users as dosen_user', 'jadwal.dosen_id', '=', 'dosen_user.id')
            ->where('jadwal.jenis', 'BIPA')
            ->where('jadwal.hari', $hariIni)
            ->select(
                'jadwal.*',
                'matakuliah.namaMk as nama_matkul',
                'dosen_user.name   as nama_dosen'
            )
            ->orderBy('jadwal.jam_mulai')
            ->get();

        $unreadNotifCount = $this->unreadNotif();

        return view('bipa.dashboard', compact(
            'totalMahasiswa',
            'pendingRequest',
            'totalJadwal',
            'sertifikatBulanIni',
            'announcements',
            'requestTerbaru',
            'jadwalHariIni',
            'unreadNotifCount'
        ));
    }


    /*
    |==========================================================================
    | PROFIL
    | Route: GET bipa/profil -> bipa.profil
    |==========================================================================
    */

    public function profil()
    {
        $user = Auth::user();
        $unreadNotifCount = $this->unreadNotif();

        return view('bipa.profil', compact('user', 'unreadNotifCount'));
    }


    /*
    |==========================================================================
    | NOTIFIKASI
    | Route: GET bipa/notifikasi -> bipa.notifikasi
    |==========================================================================
    */

    public function notifikasi()
    {
        DB::table('notifikasi')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'updated_at' => now()]);

        $notifikasi = DB::table('notifikasi')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('bipa.notifikasi', compact('notifikasi'));
    }


    /*
    |==========================================================================
    | ANNOUNCEMENT
    | Route: GET bipa/announcement -> bipa.announcement
    |==========================================================================
    */

    public function announcement()
    {
        $announcements = DB::table('announcements')
            ->where('role_pengirim', 'bipa')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $unreadNotifCount = $this->unreadNotif();

        return view('bipa.announcement', compact('announcements', 'unreadNotifCount'));
    }


    /*
    |==========================================================================
    | DOKUMEN
    |==========================================================================
    */

    /**
     * Daftar semua dokumen yang pernah diupload oleh BIPA.
     * Route: GET bipa/dokumen -> bipa.dokumen.index
     */
    public function dokumen(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = DB::table('dokumen')
            ->join('mahasiswa', 'dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('dokumen.uploaded_by_role', 'bipa')
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

        return view('bipa.dokumen', compact('dokumens', 'search', 'status', 'unreadNotifCount'));
    }

    /**
     * Detail satu dokumen — JSON untuk modal.
     * Route: GET bipa/dokumen/{id} -> bipa.dokumen.show
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
     * Route: POST bipa/dokumen/{id}/upload -> bipa.dokumen.upload
     */
    public function uploadDokumen(Request $request, int $id)
    {
        $request->validate([
            'file'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'jenis' => 'required|string|max:100',
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

        $path = $request->file('file')->store(
            'dokumen/bipa/' . $reqDokumen->mahasiswa_id,
            'public'
        );

        DB::table('dokumen')->insert([
            'mahasiswa_id'      => $reqDokumen->mahasiswa_id,
            'request_dokumen_id'=> $id,
            'jenis'             => $request->jenis,
            'file_path'         => $path,
            'uploaded_by_role'  => 'bipa',
            'status'            => 'selesai',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        DB::table('request_dokumen')
            ->where('id', $id)
            ->update([
                'status'         => 'selesai',
                'tanggal_upload' => now(),
                'updated_at'     => now(),
            ]);

        $this->kirimNotifMahasiswa(
            $reqDokumen->user_id,
            'Dokumen BIPA Siap',
            "Dokumen '{$request->jenis}' dari BIPA sudah siap. Silakan unduh di menu Dokumen."
        );

        return back()->with('success', 'Dokumen berhasil diupload dan mahasiswa telah diberitahu.');
    }


    /*
    |==========================================================================
    | REQUEST DOKUMEN
    |==========================================================================
    */

    /**
     * Daftar semua request dokumen yang masuk ke BIPA.
     * Route: GET bipa/requestDok -> bipa.request.index
     */
    public function indexReqDocument(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');

        $query = DB::table('request_dokumen')
            ->join('mahasiswa', 'request_dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('request_dokumen.req_bagian', 'bipa')
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

        $totalPending = DB::table('request_dokumen')->where('req_bagian', 'bipa')->where('status', 'pending')->count();
        $totalProses  = DB::table('request_dokumen')->where('req_bagian', 'bipa')->where('status', 'diproses')->count();
        $totalSelesai = DB::table('request_dokumen')->where('req_bagian', 'bipa')->where('status', 'selesai')->count();

        $unreadNotifCount = $this->unreadNotif();

        return view('bipa.request-dokumen', compact(
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
     * Route: GET bipa/requestDok/{id} -> bipa.request.show
     */
    public function showReqDocument(int $id)
    {
        $req = DB::table('request_dokumen')
            ->join('mahasiswa', 'request_dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('request_dokumen.id', $id)
            ->where('request_dokumen.req_bagian', 'bipa')
            ->select('request_dokumen.*', 'mahasiswa.nama as nama_mahasiswa', 'mahasiswa.npm')
            ->first();

        if (!$req) {
            return back()->with('error', 'Request tidak ditemukan.');
        }

        return response()->json($req);
    }

    /**
     * Update status request.
     * Route: PATCH bipa/requestDok/{id}/status -> bipa.request.status
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
            ->where('request_dokumen.req_bagian', 'bipa')
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
            'diproses' => "Permintaan dokumen '{$req->jenis_dokumen}' Anda sedang diproses oleh BIPA.",
            'selesai'  => "Dokumen '{$req->jenis_dokumen}' dari BIPA sudah selesai. Silakan unduh.",
            'ditolak'  => "Permintaan dokumen '{$req->jenis_dokumen}' tidak dapat diproses." . ($request->catatan ? " Alasan: {$request->catatan}" : ''),
            default    => "Status permintaan dokumen Anda telah diperbarui.",
        };

        $this->kirimNotifMahasiswa($req->user_id, 'Update Dokumen BIPA', $pesanNotif);

        return back()->with('success', 'Status request berhasil diperbarui.');
    }

    /**
     * Upload dokumen langsung dari halaman request.
     * Route: POST bipa/requestDok/{id}/upload -> bipa.request.upload
     */
    public function uploadReqDokumen(Request $request, int $id)
    {
        return $this->uploadDokumen($request, $id);
    }
}
