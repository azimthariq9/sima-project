<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GunadarmaController extends Controller
{
    /*
    |==========================================================================
    | HELPER PRIVATE
    |==========================================================================
    | Per spek BPMN: Gunadarma hanya mengelola Input Document
    | (tidak punya Schedule atau Announcement seperti BIPA/Jurusan).
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
    | Route: GET gunadarma/dashboard -> gunadarma.dashboard
    |==========================================================================
    */

    public function dashboard()
    {
        // Total mahasiswa asing aktif
        $totalMahasiswa = DB::table('mahasiswa')
            ->where('status', 'aktif')
            ->count();

        // Request dokumen yang masuk ke gunadarma
        $pendingRequest = DB::table('request_dokumen')
            ->where('req_bagian', 'gunadarma')
            ->where('status', 'pending')
            ->count();

        $prosesRequest = DB::table('request_dokumen')
            ->where('req_bagian', 'gunadarma')
            ->where('status', 'diproses')
            ->count();

        $selesaiRequest = DB::table('request_dokumen')
            ->where('req_bagian', 'gunadarma')
            ->where('status', 'selesai')
            ->count();

        // Total dokumen yang pernah diupload oleh gunadarma
        $totalDokumen = DB::table('dokumen')
            ->where('uploaded_by_role', 'gunadarma')
            ->count();

        // Request terbaru (7)
        $requestTerbaru = DB::table('request_dokumen')
            ->join('mahasiswa', 'request_dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('request_dokumen.req_bagian', 'gunadarma')
            ->select(
                'request_dokumen.*',
                'mahasiswa.nama as nama_mahasiswa',
                'mahasiswa.npm',
                'mahasiswa.warga_negara'
            )
            ->orderBy('request_dokumen.created_at', 'desc')
            ->limit(7)
            ->get();

        // Dokumen diunggah bulan ini
        $dokumenBulanIni = DB::table('dokumen')
            ->where('uploaded_by_role', 'gunadarma')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $unreadNotifCount = $this->unreadNotif();

        return view('gunadarma.dashboard', compact(
            'totalMahasiswa',
            'pendingRequest',
            'prosesRequest',
            'selesaiRequest',
            'totalDokumen',
            'requestTerbaru',
            'dokumenBulanIni',
            'unreadNotifCount'
        ));
    }


    /*
    |==========================================================================
    | PROFIL
    | Route: GET gunadarma/profil -> gunadarma.profil
    |==========================================================================
    */

    public function profil()
    {
        $user = Auth::user();
        $unreadNotifCount = $this->unreadNotif();

        return view('gunadarma.profil', compact('user', 'unreadNotifCount'));
    }


    /*
    |==========================================================================
    | NOTIFIKASI
    | Route: GET gunadarma/notifikasi -> gunadarma.notifikasi
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

        return view('gunadarma.notifikasi', compact('notifikasi'));
    }


    /*
    |==========================================================================
    | DOKUMEN
    |==========================================================================
    */

    /**
     * Daftar semua dokumen yang pernah diupload oleh Gunadarma.
     * Route: GET gunadarma/dokumen -> gunadarma.dokumen.index
     */
    public function dokumen(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $jenis  = $request->get('jenis');

        $query = DB::table('dokumen')
            ->join('mahasiswa', 'dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('dokumen.uploaded_by_role', 'gunadarma')
            ->select(
                'dokumen.*',
                'mahasiswa.nama          as nama_mahasiswa',
                'mahasiswa.npm',
                'mahasiswa.warga_negara'
            );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('mahasiswa.nama',  'like', "%{$search}%")
                  ->orWhere('mahasiswa.npm', 'like', "%{$search}%")
                  ->orWhere('dokumen.jenis', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('dokumen.status', $status);
        }

        if ($jenis) {
            $query->where('dokumen.jenis', $jenis);
        }

        $dokumens = $query->orderBy('dokumen.created_at', 'desc')->paginate(15)->withQueryString();

        // Jenis dokumen yang pernah diupload (untuk filter dropdown)
        $jenisList = DB::table('dokumen')
            ->where('uploaded_by_role', 'gunadarma')
            ->distinct()
            ->pluck('jenis');

        $unreadNotifCount = $this->unreadNotif();

        return view('gunadarma.dokumen', compact(
            'dokumens',
            'search',
            'status',
            'jenis',
            'jenisList',
            'unreadNotifCount'
        ));
    }

    /**
     * Detail satu dokumen — JSON untuk modal.
     * Route: GET gunadarma/dokumen/{id} -> gunadarma.dokumen.show
     */
    public function showDokumen(int $id)
    {
        $dokumen = DB::table('dokumen')
            ->join('mahasiswa', 'dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('dokumen.id', $id)
            ->where('dokumen.uploaded_by_role', 'gunadarma')
            ->select(
                'dokumen.*',
                'mahasiswa.nama          as nama_mahasiswa',
                'mahasiswa.npm',
                'mahasiswa.warga_negara'
            )
            ->first();

        if (!$dokumen) {
            return response()->json(['error' => 'Dokumen tidak ditemukan.'], 404);
        }

        return response()->json($dokumen);
    }

    /**
     * Upload / update file dokumen ke mahasiswa.
     * Route: POST gunadarma/dokumen/{id}/upload -> gunadarma.dokumen.upload
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
            ->where('request_dokumen.req_bagian', 'gunadarma')
            ->select(
                'request_dokumen.*',
                'mahasiswa.nama as nama_mahasiswa',
                'users.id       as user_id'
            )
            ->first();

        if (!$reqDokumen) {
            return back()->with('error', 'Request dokumen tidak ditemukan.');
        }

        $path = $request->file('file')->store(
            'dokumen/gunadarma/' . $reqDokumen->mahasiswa_id,
            'public'
        );

        DB::table('dokumen')->insert([
            'mahasiswa_id'      => $reqDokumen->mahasiswa_id,
            'request_dokumen_id'=> $id,
            'jenis'             => $request->jenis,
            'file_path'         => $path,
            'uploaded_by_role'  => 'gunadarma',
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
            'Dokumen Kampus Siap',
            "Dokumen '{$request->jenis}' dari Gunadarma sudah siap. Silakan unduh di menu Dokumen."
        );

        return back()->with('success', 'Dokumen berhasil diupload dan mahasiswa telah diberitahu.');
    }


    /*
    |==========================================================================
    | REQUEST DOKUMEN
    |==========================================================================
    */

    /**
     * Daftar semua request dokumen yang masuk ke Gunadarma.
     * Route: GET gunadarma/requestDok -> gunadarma.request.index
     */
    public function indexReqDocument(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');

        $query = DB::table('request_dokumen')
            ->join('mahasiswa', 'request_dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('request_dokumen.req_bagian', 'gunadarma')
            ->select(
                'request_dokumen.*',
                'mahasiswa.nama          as nama_mahasiswa',
                'mahasiswa.npm',
                'mahasiswa.warga_negara'
            );

        if ($status) {
            $query->where('request_dokumen.status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('mahasiswa.nama', 'like', "%{$search}%")
                  ->orWhere('mahasiswa.npm', 'like', "%{$search}%")
                  ->orWhere('request_dokumen.jenis_dokumen', 'like', "%{$search}%");
            });
        }

        $requests = $query->orderBy('request_dokumen.created_at', 'desc')->paginate(15)->withQueryString();

        $totalPending = DB::table('request_dokumen')->where('req_bagian', 'gunadarma')->where('status', 'pending')->count();
        $totalProses  = DB::table('request_dokumen')->where('req_bagian', 'gunadarma')->where('status', 'diproses')->count();
        $totalSelesai = DB::table('request_dokumen')->where('req_bagian', 'gunadarma')->where('status', 'selesai')->count();

        $unreadNotifCount = $this->unreadNotif();

        return view('gunadarma.request-dokumen', compact(
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
     * Detail satu request — JSON untuk modal.
     * Route: GET gunadarma/requestDok/{id} -> gunadarma.request.show
     */
    public function showReqDocument(int $id)
    {
        $req = DB::table('request_dokumen')
            ->join('mahasiswa', 'request_dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('request_dokumen.id', $id)
            ->where('request_dokumen.req_bagian', 'gunadarma')
            ->select(
                'request_dokumen.*',
                'mahasiswa.nama          as nama_mahasiswa',
                'mahasiswa.npm',
                'mahasiswa.warga_negara'
            )
            ->first();

        if (!$req) {
            return response()->json(['error' => 'Request tidak ditemukan.'], 404);
        }

        return response()->json($req);
    }

    /**
     * Update status request (pending -> diproses -> selesai / ditolak).
     * Route: PATCH gunadarma/requestDok/{id}/status -> gunadarma.request.status
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
            ->where('request_dokumen.req_bagian', 'gunadarma')
            ->select(
                'request_dokumen.*',
                'users.id              as user_id',
                'mahasiswa.nama        as nama_mahasiswa'
            )
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
            'diproses' => "Permintaan dokumen '{$req->jenis_dokumen}' Anda sedang diproses oleh Kampus.",
            'selesai'  => "Dokumen '{$req->jenis_dokumen}' dari Kampus sudah selesai. Silakan unduh.",
            'ditolak'  => "Permintaan dokumen '{$req->jenis_dokumen}' tidak dapat diproses." . ($request->catatan ? " Alasan: {$request->catatan}" : ''),
            default    => "Status permintaan dokumen Anda telah diperbarui.",
        };

        $this->kirimNotifMahasiswa($req->user_id, 'Update Dokumen Kampus', $pesanNotif);

        return back()->with('success', 'Status request berhasil diperbarui.');
    }

    /**
     * Upload dokumen langsung dari halaman request (shortcut ke uploadDokumen).
     * Route: POST gunadarma/requestDok/{id}/upload -> gunadarma.request.upload
     */
    public function uploadReqDokumen(Request $request, int $id)
    {
        return $this->uploadDokumen($request, $id);
    }
}
