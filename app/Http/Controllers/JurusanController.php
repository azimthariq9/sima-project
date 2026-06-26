<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JurusanController extends Controller
{
    /*
    |==========================================================================
    | HELPER PRIVATE
    |==========================================================================
    */

    private function jurusanId(): int
    {
        return Auth::user()->jurusan_id;
    }

    private function unreadNotif(): int
    {
        return DB::table('notification_users')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
    }

    /** Kirim notif ke mahasiswa via notification_mahasiswa (bukan notification_users) */
    private function kirimNotifMahasiswa(int $userId, string $judul, string $pesan): void
    {
        $notifId = DB::table('notification')->insertGetId([
            'subject'    => $judul,
            'message'    => $pesan,
            'type'       => 'document',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $mahasiswaId = DB::table('mahasiswa')->where('user_id', $userId)->value('id');

        if ($mahasiswaId) {
            DB::table('notification_mahasiswa')->insert([
                'notification_id' => $notifId,
                'mahasiswa_id'    => $mahasiswaId,
                'is_read'         => false,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }


    /*
    |==========================================================================
    | DASHBOARD
    |==========================================================================
    */

    public function dashboard()
    {
        $jid = $this->jurusanId();

        $totalMahasiswa = DB::table('mahasiswa')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->where('users.jurusan_id', $jid)
            ->count();

        $totalMatakuliah = DB::table('matakuliah')
            ->where('jurusan_id', $jid)
            ->count();

        $totalDosen = DB::table('dosen')
            ->join('users', 'dosen.user_id', '=', 'users.id')
            ->where('users.jurusan_id', $jid)
            ->count();

        $jadwalAktif = DB::table('jadwal')
            ->join('dosen', 'jadwal.dosen_id', '=', 'dosen.id')
            ->join('users', 'dosen.user_id', '=', 'users.id')
            ->where('users.jurusan_id', $jid)
            ->count();

        $pendingRequest = DB::table('reqDokumen')
            ->join('mahasiswa', 'reqDokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->where('users.jurusan_id', $jid)
            ->where('reqDokumen.status', 'pending')
            ->count();

        $announcements = DB::table('announcement')
            ->where('status', 'active')
            ->where('sumber', 'jurusan')
            ->orderByDesc('is_penting')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        $requestTerbaru = DB::table('reqDokumen')
            ->join('mahasiswa', 'reqDokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->where('users.jurusan_id', $jid)
            ->select('reqDokumen.*', 'mahasiswa.nama as nama_mahasiswa', 'mahasiswa.npm')
            ->orderByDesc('reqDokumen.created_at')
            ->limit(5)
            ->get();

        $hariMap = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu',
        ];
        $hariIni = $hariMap[now()->format('l')] ?? now()->format('l');

        $jadwalHariIni = DB::table('jadwal')
            ->join('dosen', 'jadwal.dosen_id', '=', 'dosen.id')
            ->join('users', 'dosen.user_id', '=', 'users.id')
            ->leftJoin('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
            ->leftJoin('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->where('users.jurusan_id', $jid)
            ->where('jadwal.hari', $hariIni)
            ->select(
                'jadwal.*',
                'matakuliah.namaMk as nama_matkul',
                'dosen.nama as nama_dosen',
                'kelas.kodeKelas'
            )
            ->orderBy('jadwal.jam')
            ->get();

        $jadwalMingguIni = DB::table('jadwal')
            ->join('dosen', 'jadwal.dosen_id', '=', 'dosen.id')
            ->join('users', 'dosen.user_id', '=', 'users.id')
            ->leftJoin('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
            ->leftJoin('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->where('users.jurusan_id', $jid)
            ->orderByRaw("CASE jadwal.hari
                WHEN 'Senin'   THEN 1
                WHEN 'Selasa'  THEN 2
                WHEN 'Rabu'    THEN 3
                WHEN 'Kamis'   THEN 4
                WHEN 'Jumat'   THEN 5
                WHEN 'Sabtu'   THEN 6
                ELSE 7 END, jadwal.jam")
            ->select('jadwal.*', 'matakuliah.namaMk as nama_matkul', 'dosen.nama as nama_dosen', 'kelas.kodeKelas as kode_kelas')
            ->limit(10)
            ->get();

        $unreadNotifCount = $this->unreadNotif();

        return view('jurusan.dashboard', compact(
            'totalMahasiswa', 'jadwalAktif', 'totalMatakuliah', 'totalDosen',
            'pendingRequest', 'announcements', 'requestTerbaru', 'jadwalHariIni',
            'jadwalMingguIni', 'unreadNotifCount'
        ));
    }


    /*
    |==========================================================================
    | PROFIL
    |==========================================================================
    */

    public function profil()
    {
        return view('jurusan.profil', ['user' => auth()->user()]);
    }

    public function updateProfil(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if ($request->filled('password')) {
            \Illuminate\Support\Facades\DB::table('users')
                ->where('id', $user->id)
                ->update(['password' => bcrypt($request->password)]);
        }

        return redirect()->route('jurusan.profil')->with('success', 'Password berhasil diperbarui.');
    }


    /*
    |==========================================================================
    | NOTIFIKASI
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
            ->orderByDesc('notification_users.created_at')
            ->paginate(15)
            ->withQueryString();

        $unreadNotifCount = 0;

        return view('jurusan.notifikasi', compact('notifikasi', 'unreadNotifCount'));
    }


    /*
    |==========================================================================
    | ANNOUNCEMENT — CRUD
    |==========================================================================
    */

    public function announcement(Request $request)
    {
        $search = $request->input('search', '');
        $filter = $request->input('filter', '');

        $query = DB::table('announcement')
            ->where('sumber', 'jurusan')
            ->whereIn('status', ['active', 'inactive']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'ilike', "%{$search}%")
                  ->orWhere('message', 'ilike', "%{$search}%");
            });
        }

        if ($filter === 'penting') {
            $query->where('is_penting', true);
        } elseif ($filter === 'active') {
            $query->where('status', 'active');
        } elseif ($filter === 'inactive') {
            $query->where('status', 'inactive');
        }

        $announcements = $query
            ->orderByDesc('is_penting')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $unreadNotifCount = $this->unreadNotif();

        return view('jurusan.announcement.index', compact('announcements', 'unreadNotifCount', 'search', 'filter'));
    }

    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'subject'    => 'required|string|max:200',
            'message'    => 'required|string',
            'is_penting' => 'nullable|boolean',
        ]);

        DB::table('announcement')->insert([
            'subject'    => $request->subject,
            'message'    => $request->message,
            'status'     => 'active',
            'is_penting' => $request->boolean('is_penting'),
            'sumber'     => 'jurusan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('jurusan.announcement.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function editAnnouncement(int $id)
    {
        $ann = DB::table('announcement')
            ->where('id', $id)
            ->where('sumber', 'jurusan')
            ->first();

        abort_if(!$ann, 404);

        return response()->json($ann);
    }

    public function updateAnnouncement(Request $request, int $id)
    {
        $request->validate([
            'subject'    => 'required|string|max:200',
            'message'    => 'required|string',
            'is_penting' => 'nullable|boolean',
            'status'     => 'required|in:active,inactive',
        ]);

        DB::table('announcement')
            ->where('id', $id)
            ->where('sumber', 'jurusan')
            ->update([
                'subject'    => $request->subject,
                'message'    => $request->message,
                'is_penting' => $request->boolean('is_penting'),
                'status'     => $request->status,
                'updated_at' => now(),
            ]);

        return redirect()->route('jurusan.announcement.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroyAnnouncement(int $id)
    {
        DB::table('announcement')
            ->where('id', $id)
            ->where('sumber', 'jurusan')
            ->update(['status' => 'deleted', 'updated_at' => now()]);

        return redirect()->route('jurusan.announcement.index')->with('success', 'Pengumuman berhasil dihapus.');
    }


    /*
    |==========================================================================
    | DOKUMEN — daftar dokumen mahasiswa di jurusan ini
    |==========================================================================
    */

    public function dokumen(Request $request)
    {
        $jid    = $this->jurusanId();
        $search = $request->input('search', '');
        $filter = $request->input('filter', '');

        $query = DB::table('dokumen')
            ->join('mahasiswa', 'dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->where('users.jurusan_id', $jid)
            ->whereNull('dokumen.deleted_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('mahasiswa.nama', 'ilike', "%{$search}%")
                  ->orWhere('mahasiswa.npm', 'ilike', "%{$search}%")
                  ->orWhere('dokumen.namaDkmn', 'ilike', "%{$search}%")
                  ->orWhere('dokumen.noDkmn', 'ilike', "%{$search}%");
            });
        }

        if ($filter) {
            $query->where('dokumen.status', $filter);
        }

        $dokumen = $query
            ->select('dokumen.*', 'mahasiswa.nama as nama_mahasiswa', 'mahasiswa.npm')
            ->orderByDesc('dokumen.created_at')
            ->paginate(15)
            ->withQueryString();

        $unreadNotifCount = $this->unreadNotif();

        return view('jurusan.dokumen.index', compact('dokumen', 'unreadNotifCount', 'search', 'filter'));
    }

    public function showDokumen(int $id)
    {
        $jid = $this->jurusanId();

        $dok = DB::table('dokumen')
            ->join('mahasiswa', 'dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->where('dokumen.id', $id)
            ->where('users.jurusan_id', $jid)
            ->whereNull('dokumen.deleted_at')
            ->select('dokumen.*', 'mahasiswa.nama as nama_mahasiswa', 'mahasiswa.npm')
            ->first();

        abort_if(!$dok, 404);

        return response()->json($dok);
    }

    public function serveDokumen(int $id)
    {
        $jid = $this->jurusanId();

        $dok = DB::table('dokumen')
            ->join('mahasiswa', 'dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->where('dokumen.id', $id)
            ->where('users.jurusan_id', $jid)
            ->whereNull('dokumen.deleted_at')
            ->select('dokumen.id')
            ->first();

        abort_if(!$dok, 403);

        $file = DB::table('fileDetail')
            ->where('dokumen_id', $id)
            ->orderByDesc('created_at')
            ->first();

        abort_if(!$file, 404, 'File tidak tersedia.');

        $storage = Storage::disk('local');
        abort_if(!$storage->exists($file->path), 404, 'File tidak ditemukan.');

        return $storage->download($file->path, basename($file->path));
    }

    public function updateStatusDokumen(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $jid = $this->jurusanId();

        $dok = DB::table('dokumen')
            ->join('mahasiswa', 'dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->where('dokumen.id', $id)
            ->where('users.jurusan_id', $jid)
            ->whereNull('dokumen.deleted_at')
            ->select('dokumen.*', 'users.id as user_id', 'mahasiswa.nama as nama_mahasiswa')
            ->first();

        abort_if(!$dok, 403);

        DB::table('dokumen')
            ->where('id', $id)
            ->update(['status' => $request->status, 'updated_at' => now()]);

        $pesanMap = [
            'approved' => "Dokumen '{$dok->namaDkmn}' Anda telah diverifikasi oleh Jurusan.",
            'rejected' => "Dokumen '{$dok->namaDkmn}' Anda ditolak oleh Jurusan. Silakan upload ulang.",
        ];

        if (isset($pesanMap[$request->status])) {
            $this->kirimNotifMahasiswa($dok->user_id, 'Verifikasi Dokumen', $pesanMap[$request->status]);
        }

        return back()->with('success', 'Status dokumen berhasil diperbarui.');
    }


    /*
    |==========================================================================
    | REQUEST DOKUMEN
    |==========================================================================
    */

    public function indexReqDocument(Request $request)
    {
        $jid    = $this->jurusanId();
        $search = $request->input('search', '');
        $filter = $request->input('filter', '');

        $query = DB::table('reqDokumen')
            ->join('mahasiswa', 'reqDokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->where('users.jurusan_id', $jid);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('mahasiswa.nama', 'ilike', "%{$search}%")
                  ->orWhere('mahasiswa.npm', 'ilike', "%{$search}%")
                  ->orWhere('reqDokumen.tipeDkmn', 'ilike', "%{$search}%");
            });
        }

        if ($filter) {
            $query->where('reqDokumen.status', $filter);
        }

        $requests = $query
            ->select('reqDokumen.*', 'mahasiswa.nama as nama_mahasiswa', 'mahasiswa.npm')
            ->orderByDesc('reqDokumen.created_at')
            ->paginate(15)
            ->withQueryString();

        $unreadNotifCount = $this->unreadNotif();

        return view('jurusan.request.index', compact('requests', 'unreadNotifCount', 'search', 'filter'));
    }

    public function showReqDocument(int $id)
    {
        $jid = $this->jurusanId();

        $req = DB::table('reqDokumen')
            ->join('mahasiswa', 'reqDokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->where('reqDokumen.id', $id)
            ->where('users.jurusan_id', $jid)
            ->select('reqDokumen.*', 'mahasiswa.nama as nama_mahasiswa', 'mahasiswa.npm')
            ->first();

        abort_if(!$req, 404);

        return response()->json($req);
    }

    public function updateReqDokumen(Request $request, int $id)
    {
        $request->validate([
            'status'  => 'required|in:pending,approved,rejected',
            'message' => 'nullable|string|max:500',
        ]);

        $jid = $this->jurusanId();

        $req = DB::table('reqDokumen')
            ->join('mahasiswa', 'reqDokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->where('reqDokumen.id', $id)
            ->where('users.jurusan_id', $jid)
            ->select('reqDokumen.*', 'users.id as user_id', 'mahasiswa.nama as nama_mahasiswa')
            ->first();

        abort_if(!$req, 403);

        $updateData = ['status' => $request->status, 'updated_at' => now()];
        if ($request->filled('message')) {
            $updateData['keterangan'] = $request->message;
        }

        DB::table('reqDokumen')->where('id', $id)->update($updateData);

        $pesanNotif = match($request->status) {
            'approved' => "Permintaan dokumen '{$req->tipeDkmn}' Anda telah disetujui. Silakan unduh.",
            'rejected' => "Permintaan dokumen '{$req->tipeDkmn}' tidak dapat diproses." . ($request->message ? " Alasan: {$request->message}" : ''),
            default    => "Status permintaan dokumen Anda telah diperbarui.",
        };

        $this->kirimNotifMahasiswa($req->user_id, 'Update Request Dokumen', $pesanNotif);

        return back()->with('success', 'Status request berhasil diperbarui.');
    }

    public function uploadDokumen(Request $request, int $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $jid = $this->jurusanId();

        $req = DB::table('reqDokumen')
            ->join('mahasiswa', 'reqDokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->where('reqDokumen.id', $id)
            ->where('users.jurusan_id', $jid)
            ->select('reqDokumen.*', 'mahasiswa.nama as nama_mahasiswa', 'users.id as user_id')
            ->first();

        abort_if(!$req, 403);

        $file = $request->file('file');
        $path = $file->store('dokumen/jurusan/' . $req->mahasiswa_id, 'local');

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
            ->update(['status' => 'approved', 'updated_at' => now()]);

        $this->kirimNotifMahasiswa(
            $req->user_id,
            'Dokumen Jurusan Siap',
            "Dokumen '{$req->tipeDkmn}' dari Jurusan sudah siap. Silakan unduh di menu Request."
        );

        return back()->with('success', 'Dokumen berhasil diupload dan mahasiswa telah diberitahu.');
    }

    public function uploadReqDokumen(Request $request, int $id)
    {
        return $this->uploadDokumen($request, $id);
    }
}
