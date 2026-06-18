<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MahasiswaController extends Controller
{
    /*
    |==========================================================================
    | HELPER PRIVATE
    |==========================================================================
    */

    /**
     * Ambil mahasiswa row berdasarkan user yang sedang login.
     * Konsisten dipakai di semua method agar tidak duplikasi query.
     */
    private function getMahasiswa()
    {
        return DB::table('mahasiswa')
            ->where('user_id', Auth::id())
            ->first();
    }


    /*
    |==========================================================================
    | COMPLETE PROFILE
    |==========================================================================
    */

    public function completeProfile()
    {
        $mahasiswa = $this->getMahasiswa();

        // profile_completed ada di tabel users, bukan mahasiswa
        if (Auth::user()->profile_completed) {
            return redirect()->route('mahasiswa.dashboard');
        }

        return view('mahasiswa.complete-profile', compact('mahasiswa'));
    }

    public function storeCompleteProfile(Request $request)
    {
        $mahasiswa   = $this->getMahasiswa();
        $mahasiswaId = $mahasiswa->id ?? 0;

        $request->validate([
            'nama'       => 'required|string|max:255',
            'npm'        => ['required', 'string', 'max:20', Rule::unique('mahasiswa', 'npm')->ignore($mahasiswaId)],
            'noWa'       => ['nullable', 'string', 'max:20', Rule::unique('mahasiswa', 'noWa')->ignore($mahasiswaId)],
            'tglLahir'   => 'nullable|date',
            'warNeg'     => 'nullable|string|max:100',
            'alamatAsal' => 'nullable|string|max:500',
            'alamatIndo' => 'nullable|string|max:500',
            'password'   => 'nullable|string|min:8|confirmed',
        ], [
            'npm.unique' => 'NPM ini sudah digunakan oleh mahasiswa lain.',
            'noWa.unique' => 'Nomor WhatsApp ini sudah digunakan oleh mahasiswa lain.',
        ]);

        // Update data mahasiswa dengan nama kolom DB yang benar (camelCase)
        DB::table('mahasiswa')
            ->where('user_id', Auth::id())
            ->update([
                'nama'       => $request->nama,
                'npm'        => $request->npm,
                'noWa'       => $request->noWa,
                'tglLahir'   => $request->tglLahir,
                'warNeg'     => $request->warNeg,
                'alamatAsal' => $request->alamatAsal,
                'alamatIndo' => $request->alamatIndo,
                'updated_at' => now(),
            ]);

        // profile_completed ada di users, bukan mahasiswa
        DB::table('users')
            ->where('id', Auth::id())
            ->update(['profile_completed' => true, 'updated_at' => now()]);

        if ($request->filled('password')) {
            DB::table('users')
                ->where('id', Auth::id())
                ->update(['password' => bcrypt($request->password), 'updated_at' => now()]);
        }

        return redirect()->route('mahasiswa.dashboard')
            ->with('success', 'Profil berhasil dilengkapi. Selamat datang!');
    }


    /*
    |==========================================================================
    | DASHBOARD
    |==========================================================================
    */

    public function dashboard()
    {
        $mahasiswa  = $this->getMahasiswa();
        $student_id = $mahasiswa->id ?? null;

        /*
        |----------------------------------------------------------------------
        | History 10 kehadiran terakhir (dari jadwal_mahasiswa)
        |----------------------------------------------------------------------
        */
        $history = collect();
        $totalHadir = 0;
        $totalTelat = 0;
        $totalAlpha = 0;

        if ($student_id) {
            $history = DB::table('jadwal_mahasiswa')
                ->join('jadwal', 'jadwal_mahasiswa.jadwal_id', '=', 'jadwal.id')
                ->join('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
                ->where('jadwal_mahasiswa.mahasiswa_id', $student_id)
                ->whereIn('jadwal_mahasiswa.status', ['present', 'absent', 'excused'])
                ->select(
                    'matakuliah.namaMk',
                    'jadwal_mahasiswa.sesi as meeting_number',
                    'jadwal_mahasiswa.status',
                    'jadwal_mahasiswa.tglSesi as checkin_time'
                )
                ->orderByDesc('jadwal_mahasiswa.tglSesi')
                ->limit(10)
                ->get();

            $allStatuses = DB::table('jadwal_mahasiswa')
                ->where('mahasiswa_id', $student_id)
                ->whereIn('status', ['present', 'absent', 'excused'])
                ->pluck('status');

            $totalHadir = $allStatuses->filter(fn($s) => $s === 'present')->count();
            $totalTelat = $allStatuses->filter(fn($s) => $s === 'excused')->count();
            $totalAlpha = $allStatuses->filter(fn($s) => $s === 'absent')->count();
        }

        /*
        |----------------------------------------------------------------------
        | Pengumuman terbaru (3) — kolom: subject, message (bukan judul/isi)
        |----------------------------------------------------------------------
        */
        $announcements = DB::table('announcement')
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        /*
        |----------------------------------------------------------------------
        | Jadwal hari ini — kolom jam adalah varchar "09.30 - 11.30"
        |----------------------------------------------------------------------
        */
        $hariIni = now()->locale('id')->translatedFormat('l');
        $jadwalHariIni = collect();

        if ($student_id) {
            $jadwalHariIni = DB::table('jadwal')
                ->join('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
                ->join('jadwal_mahasiswa', 'jadwal_mahasiswa.jadwal_id', '=', 'jadwal.id')
                ->where('jadwal_mahasiswa.mahasiswa_id', $student_id)
                ->where('jadwal.hari', $hariIni)
                ->select(
                    'jadwal.id',
                    'jadwal.hari',
                    DB::raw("REPLACE(SPLIT_PART(jadwal.jam, ' - ', 1), '.', ':') as jam_mulai"),
                    DB::raw("REPLACE(SPLIT_PART(jadwal.jam, ' - ', 2), '.', ':') as jam_selesai"),
                    'jadwal.ruangan',
                    DB::raw("CASE WHEN matakuliah.jurusan_id = 1 THEN 'kln' WHEN matakuliah.jurusan_id = 2 THEN 'bipa' ELSE 'kuliah' END as jenis"),
                    DB::raw('NULL::text as kelas'),
                    'matakuliah.namaMk as mata_kuliah'
                )
                ->orderBy('jadwal.jam')
                ->get()
                ->unique('id')
                ->values();
        }

        /*
        |----------------------------------------------------------------------
        | Notifikasi yang belum dibaca
        |----------------------------------------------------------------------
        */
        $unreadNotifCount = $student_id
            ? DB::table('notification_mahasiswa')
                ->where('mahasiswa_id', $student_id)
                ->where('is_read', false)
                ->count()
            : 0;

        // active_attendance & sudah_absen tidak ada di schema baru, pass null/false
        $active_attendance = null;
        $sudah_absen       = false;

        return view('mahasiswa.dashboard', compact(
            'mahasiswa',
            'active_attendance',
            'sudah_absen',
            'history',
            'totalHadir',
            'totalTelat',
            'totalAlpha',
            'announcements',
            'jadwalHariIni',
            'unreadNotifCount'
        ));
    }


    /*
    |==========================================================================
    | SUBMIT ABSENSI
    | Route: POST mahasiswa/absensi/submit -> mahasiswa.absensi.submit
    | (Tambahkan route ini di web.php jika belum ada)
    |==========================================================================
    */

    public function submitAbsensi(Request $request)
    {
        $request->validate([
            'attendance_code' => 'required|string|max:10',
        ]);

        $mahasiswa  = $this->getMahasiswa();
        $student_id = $mahasiswa->id ?? Auth::id();

        $session = DB::table('attendances_sessions')
            ->where('attendance_code', strtoupper($request->attendance_code))
            ->where('status', 'active')
            ->first();

        if (!$session) {
            return back()->with('error', 'Kode absensi tidak valid atau sesi sudah ditutup.');
        }

        if (now()->gt($session->end_time)) {
            return back()->with('error', 'Waktu absensi sudah berakhir.');
        }

        $sudah_absen = DB::table('attendance_records')
            ->where('attendance_session_id', $session->id)
            ->where('student_id', $student_id)
            ->exists();

        if ($sudah_absen) {
            return back()->with('error', 'Kamu sudah melakukan absensi untuk sesi ini.');
        }

        // Lebih dari 10 menit sejak sesi dibuka = terlambat
        $status = now()->diffInMinutes($session->start_time) > 10
            ? 'late'
            : 'present';

        DB::table('attendance_records')->insert([
            'attendance_session_id' => $session->id,
            'student_id'            => $student_id,
            'status'                => $status,
            'checkin_time'          => now(),
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);

        $pesan = $status === 'late'
            ? 'Absensi berhasil, namun kamu tercatat terlambat.'
            : 'Absensi berhasil! Kamu tercatat hadir tepat waktu.';

        return back()->with('success', $pesan);
    }


    /*
    |==========================================================================
    | JADWAL
    |==========================================================================
    | BUG FIX: $hariIniId dan $tipe tidak pernah didefinisikan di versi lama.
    */

    public function jadwal(Request $request)
    {
        $mahasiswa  = $this->getMahasiswa();
        $student_id = $mahasiswa->id ?? null;

        $tipe = $request->get('tipe', 'semua');

        $hariUrutan = [
            'Senin'  => 1, 'Selasa' => 2, 'Rabu'   => 3,
            'Kamis'  => 4, 'Jumat'  => 5, 'Sabtu'  => 6, 'Minggu' => 7,
        ];

        $hariIni = now()->locale('id')->translatedFormat('l');

        $todaySchedules  = collect();
        $weeklySchedules = collect();
        $attendanceSummary = collect();
        $totalSesiPerMk    = collect();

        if ($student_id) {
            /*
            |------------------------------------------------------------------
            | Query jadwal — kolom jam adalah varchar "09.30 - 11.30"
            | tipe_kelas diturunkan dari matakuliah.jurusan_id
            |------------------------------------------------------------------
            */
            $query = DB::table('jadwal')
                ->join('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
                ->join('jadwal_mahasiswa', 'jadwal_mahasiswa.jadwal_id', '=', 'jadwal.id')
                ->leftJoin('dosen', 'jadwal.dosen_id', '=', 'dosen.id')
                ->where('jadwal_mahasiswa.mahasiswa_id', $student_id)
                ->select(
                    'jadwal.id',
                    'jadwal.hari',
                    DB::raw("REPLACE(SPLIT_PART(jadwal.jam, ' - ', 1), '.', ':') as jam_mulai"),
                    DB::raw("REPLACE(SPLIT_PART(jadwal.jam, ' - ', 2), '.', ':') as jam_selesai"),
                    'jadwal.ruangan',
                    DB::raw("CASE WHEN matakuliah.jurusan_id = 1 THEN 'kln' WHEN matakuliah.jurusan_id = 2 THEN 'bipa' ELSE 'perkuliahan' END as tipe_kelas"),
                    'matakuliah.namaMk as mata_kuliah',
                    'dosen.nama as dosen'
                );

            if ($tipe !== 'semua') {
                if ($tipe === 'kln') {
                    $query->where('matakuliah.jurusan_id', 1);
                } elseif ($tipe === 'bipa') {
                    $query->where('matakuliah.jurusan_id', 2);
                } else {
                    // perkuliahan / kuliah
                    $query->where('matakuliah.jurusan_id', '>', 2);
                }
            }

            $semuaJadwal = $query->get()->unique('id')->values();

            $todaySchedules = $semuaJadwal
                ->filter(fn($j) => $j->hari === $hariIni)
                ->values();

            $weeklySchedules = $semuaJadwal
                ->sortBy(fn($j) => $hariUrutan[$j->hari] ?? 99)
                ->values();

            /*
            |------------------------------------------------------------------
            | Summary kehadiran per mata kuliah (dari jadwal_mahasiswa)
            |------------------------------------------------------------------
            */
            $attendanceSummary = DB::table('jadwal_mahasiswa')
                ->join('jadwal', 'jadwal_mahasiswa.jadwal_id', '=', 'jadwal.id')
                ->join('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
                ->where('jadwal_mahasiswa.mahasiswa_id', $student_id)
                ->select(
                    'matakuliah.namaMk',
                    'matakuliah.id as course_id',
                    DB::raw("SUM(CASE WHEN jadwal_mahasiswa.status = 'present' THEN 1 ELSE 0 END) as hadir"),
                    DB::raw("SUM(CASE WHEN jadwal_mahasiswa.status = 'excused' THEN 1 ELSE 0 END) as terlambat"),
                    DB::raw("SUM(CASE WHEN jadwal_mahasiswa.status = 'absent'  THEN 1 ELSE 0 END) as alpha")
                )
                ->groupBy('matakuliah.namaMk', 'matakuliah.id')
                ->get();

            $totalSesiPerMk = DB::table('jadwal')
                ->join('jadwal_mahasiswa', 'jadwal_mahasiswa.jadwal_id', '=', 'jadwal.id')
                ->where('jadwal_mahasiswa.mahasiswa_id', $student_id)
                ->select(
                    'jadwal.matakuliah_id as course_id',
                    DB::raw('MAX(jadwal.totalSesi) as total_sesi')
                )
                ->groupBy('jadwal.matakuliah_id')
                ->get()
                ->keyBy('course_id');
        }

        $unreadNotifCount = $student_id
            ? DB::table('notification_mahasiswa')
                ->where('mahasiswa_id', $student_id)
                ->where('is_read', false)
                ->count()
            : 0;

        return view('mahasiswa.jadwal', [
            'todaySchedules'    => $todaySchedules,
            'weeklySchedules'   => $weeklySchedules,
            'attendanceSummary' => $attendanceSummary,
            'totalSesiPerMk'    => $totalSesiPerMk,
            'hariIni'           => $hariIni,
            'activeTipe'        => $tipe,
            'unreadNotifCount'  => $unreadNotifCount,
        ]);
    }


    /*
    |==========================================================================
    | ANALYTICS
    |==========================================================================
    */

    public function analytics()
    {
        $mahasiswa  = $this->getMahasiswa();
        $student_id = $mahasiswa->id ?? null;

        $history    = collect();
        $rekapPerMk = collect();
        $totalHadir = $totalTelat = $totalAlpha = $totalSemua = 0;
        $persentaseHadir = 0;

        if ($student_id) {
            $history = DB::table('jadwal_mahasiswa')
                ->join('jadwal', 'jadwal_mahasiswa.jadwal_id', '=', 'jadwal.id')
                ->join('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
                ->where('jadwal_mahasiswa.mahasiswa_id', $student_id)
                ->whereIn('jadwal_mahasiswa.status', ['present', 'absent', 'excused'])
                ->select(
                    'matakuliah.namaMk',
                    'matakuliah.id as course_id',
                    'jadwal_mahasiswa.sesi as meeting_number',
                    'jadwal_mahasiswa.status',
                    'jadwal_mahasiswa.tglSesi as checkin_time'
                )
                ->orderByDesc('jadwal_mahasiswa.tglSesi')
                ->get();

            $rekapPerMk = $history->groupBy('namaMk')->map(function ($items) {
                return [
                    'namaMk' => $items->first()->namaMk,
                    'hadir'  => $items->where('status', 'present')->count(),
                    'telat'  => $items->where('status', 'excused')->count(),
                    'alpha'  => $items->where('status', 'absent')->count(),
                    'total'  => $items->count(),
                ];
            })->values();

            $totalHadir = $history->where('status', 'present')->count();
            $totalTelat = $history->where('status', 'excused')->count();
            $totalAlpha = $history->where('status', 'absent')->count();
            $totalSemua = $history->count();

            $persentaseHadir = $totalSemua > 0
                ? round(($totalHadir + $totalTelat) / $totalSemua * 100, 1)
                : 0;
        }

        $unreadNotifCount = $student_id
            ? DB::table('notification_mahasiswa')
                ->where('mahasiswa_id', $student_id)
                ->where('is_read', false)
                ->count()
            : 0;

        return view('mahasiswa.analytics', compact(
            'history',
            'rekapPerMk',
            'totalHadir',
            'totalTelat',
            'totalAlpha',
            'totalSemua',
            'persentaseHadir',
            'unreadNotifCount'
        ));
    }


    /*
    |==========================================================================
    | PROFIL
    |==========================================================================
    | BUG FIX: View sebelumnya 'mahasiswa.profile', diseragamkan ke 'mahasiswa.profil'
    */

    public function getProfile()
    {
        $user      = Auth::user();
        $mahasiswa = $this->getMahasiswa();

        $unreadNotifCount = $mahasiswa
            ? DB::table('notification_mahasiswa')
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('is_read', false)
                ->count()
            : 0;

        return view('mahasiswa.profile', compact(
            'user',
            'mahasiswa',
            'unreadNotifCount'
        ));
    }

    public function updateProfile(Request $request)
    {
        $mahasiswa   = $this->getMahasiswa();
        $mahasiswaId = $mahasiswa->id ?? 0;

        $request->validate([
            'nama'       => 'required|string|max:255',
            'noWa'       => ['nullable', 'string', 'max:20', Rule::unique('mahasiswa', 'noWa')->ignore($mahasiswaId)],
            'tglLahir'   => 'nullable|date',
            'warNeg'     => 'nullable|string|max:100',
            'alamatAsal' => 'nullable|string|max:500',
            'alamatIndo' => 'nullable|string|max:500',
            'password'   => 'nullable|string|min:8|confirmed',
        ], [
            'noWa.unique' => 'Nomor WhatsApp ini sudah digunakan oleh mahasiswa lain.',
        ]);

        // Kolom DB menggunakan camelCase sesuai migrasi
        DB::table('mahasiswa')
            ->where('user_id', Auth::id())
            ->update([
                'nama'       => $request->nama,
                'noWa'       => $request->noWa,
                'tglLahir'   => $request->tglLahir,
                'warNeg'     => $request->warNeg,
                'alamatAsal' => $request->alamatAsal,
                'alamatIndo' => $request->alamatIndo,
                'updated_at' => now(),
            ]);

        if ($request->filled('password')) {
            DB::table('users')
                ->where('id', Auth::id())
                ->update(['password' => bcrypt($request->password), 'updated_at' => now()]);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }


    /*
    |==========================================================================
    | REQUEST DOKUMEN
    |==========================================================================
    */

    public function createRequest()
    {
        // Jenis dokumen yang bisa di-request sesuai spek
        $jenisDokumen = [
            'kln'       => ['Surat Keterangan Aktif', 'Surat Rekomendasi', 'Surat Sponsor'],
            'jurusan'   => ['Transkrip Nilai', 'Surat Keterangan Lulus', 'KRS'],
            'bipa'      => ['Sertifikat BIPA', 'Laporan Kemajuan Bahasa'],
            'gunadarma' => ['Surat Domisili Kampus', 'Kartu Mahasiswa'],
        ];

        $riwayat = DB::table('request_dokumen')
            ->where('mahasiswa_id', $this->getMahasiswa()->id ?? 0)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('mahasiswa.request.create', compact('jenisDokumen', 'riwayat'));
    }

    // BUG FIX: Route mahasiswa.request.quick (bukan duplikat mahasiswa.request.store)
    public function storeRequest(Request $request)
    {
        $request->validate([
            'jenis_dokumen' => 'required|string|max:100',
            'req_bagian'    => 'required|string|in:kln,jurusan,bipa,gunadarma',
            'deskripsi'     => 'nullable|string|max:1000',
        ]);

        $mahasiswa = $this->getMahasiswa();

        DB::table('request_dokumen')->insert([
            'mahasiswa_id'     => $mahasiswa->id,
            'jenis_dokumen'    => $request->jenis_dokumen,
            'req_bagian'       => $request->req_bagian,
            'deskripsi'        => $request->deskripsi,
            'status'           => 'pending',
            'tanggal_pengajuan'=> now(),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        return redirect()
            ->route('mahasiswa.request.index')
            ->with('success', 'Permintaan dokumen berhasil dikirim.');
    }


    /*
    |==========================================================================
    | ANNOUNCEMENT
    |==========================================================================
    | BUG FIX: Method ini tidak ada di versi lama (Route::view static).
    */

    public function announcement()
    {
        $announcements = DB::table('announcement')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $mhsId = DB::table('mahasiswa')->where('user_id', Auth::id())->value('id');
        $unreadNotifCount = $mhsId
            ? DB::table('notification_mahasiswa')
                ->where('mahasiswa_id', $mhsId)
                ->where('is_read', false)
                ->count()
            : 0;

        return view('mahasiswa.announcement', compact('announcements', 'unreadNotifCount'));
    }

    public function announcementShow(int $id)
    {
        return redirect()->route('mahasiswa.announcement');
    }


    /*
    |==========================================================================
    | NOTIFIKASI
    |==========================================================================
    | BUG FIX: Method ini tidak ada di versi lama (Route::view static).
    */

    public function notifikasi()
    {
        $mahasiswa = $this->getMahasiswa();
        if (!$mahasiswa) return redirect()->route('mahasiswa.dashboard');

        // Ambil dulu sebelum mark read — supaya unread bisa di-render
        $notifications = DB::table('notification_mahasiswa')
            ->join('notification', 'notification_mahasiswa.notification_id', '=', 'notification.id')
            ->where('notification_mahasiswa.mahasiswa_id', $mahasiswa->id)
            ->select(
                'notification.id',
                'notification.subject',
                'notification.message',
                'notification.type',
                'notification_mahasiswa.is_read',
                'notification_mahasiswa.created_at as received_at'
            )
            ->orderBy('notification_mahasiswa.created_at', 'desc')
            ->get();

        $unreadCount = $notifications->where('is_read', false)->count();

        // Mark semua unread → read
        DB::table('notification_mahasiswa')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'updated_at' => now()]);

        return view('mahasiswa.notifikasi', compact('notifications', 'unreadCount'));
    }

    public function markNotifRead()
    {
        $mahasiswa = $this->getMahasiswa();
        if (!$mahasiswa) return response()->json(['success' => false], 403);

        DB::table('notification_mahasiswa')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'updated_at' => now()]);

        return response()->json(['success' => true]);
    }
}
