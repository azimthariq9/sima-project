<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        // Jika sudah complete, langsung ke dashboard
        if ($mahasiswa && $mahasiswa->profile_completed) {
            return redirect()->route('mahasiswa.dashboard');
        }

        return view('mahasiswa.complete-profile', compact('mahasiswa'));
    }

    public function storeCompleteProfile(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'npm'         => 'required|string|max:20',
            'warga_negara'=> 'required|string|max:100',
            'no_whatsapp' => 'required|string|max:20',
            'alamat_asal' => 'required|string|max:500',
            'alamat_indo' => 'required|string|max:500',
        ]);

        DB::table('mahasiswa')
            ->where('user_id', Auth::id())
            ->update([
                'nama'             => $request->nama,
                'npm'              => $request->npm,
                'warga_negara'     => $request->warga_negara,
                'no_whatsapp'      => $request->no_whatsapp,
                'alamat_asal'      => $request->alamat_asal,
                'alamat_indo'      => $request->alamat_indo,
                'profile_completed'=> true,
                'updated_at'       => now(),
            ]);

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
        $student_id = $mahasiswa->id ?? Auth::id();

        /*
        |----------------------------------------------------------------------
        | Sesi absensi aktif
        |----------------------------------------------------------------------
        */
        $active_attendance = DB::table('attendances_sessions')
            ->join('matakuliah', 'attendances_sessions.course_id', '=', 'matakuliah.id')
            ->where('attendances_sessions.status', 'active')
            ->select(
                'attendances_sessions.*',
                'matakuliah.namaMk as matakuliah'
            )
            ->orderBy('attendances_sessions.created_at', 'desc')
            ->first();

        /*
        |----------------------------------------------------------------------
        | Cek sudah absen atau belum
        |----------------------------------------------------------------------
        */
        $sudah_absen = false;
        if ($active_attendance) {
            $sudah_absen = DB::table('attendance_records')
                ->where('attendance_session_id', $active_attendance->id)
                ->where('student_id', $student_id)
                ->exists();
        }

        /*
        |----------------------------------------------------------------------
        | History 10 absensi terakhir
        |----------------------------------------------------------------------
        */
        $history = DB::table('attendance_records')
            ->join('attendances_sessions', 'attendance_records.attendance_session_id', '=', 'attendances_sessions.id')
            ->join('matakuliah', 'attendances_sessions.course_id', '=', 'matakuliah.id')
            ->where('attendance_records.student_id', $student_id)
            ->select(
                'matakuliah.namaMk',
                'attendances_sessions.meeting_number',
                'attendance_records.status',
                'attendance_records.checkin_time'
            )
            ->orderBy('attendance_records.checkin_time', 'desc')
            ->limit(10)
            ->get();

        /*
        |----------------------------------------------------------------------
        | Data chart absensi per pertemuan
        |----------------------------------------------------------------------
        */
        $chart = DB::table('attendance_records')
            ->join('attendances_sessions', 'attendance_records.attendance_session_id', '=', 'attendances_sessions.id')
            ->where('attendance_records.student_id', $student_id)
            ->select(
                'attendances_sessions.meeting_number',
                'attendance_records.status'
            )
            ->orderBy('attendances_sessions.meeting_number')
            ->get();

        /*
        |----------------------------------------------------------------------
        | Rekap kehadiran
        |----------------------------------------------------------------------
        */
        $totalHadir = $history->where('status', 'present')->count();
        $totalTelat = $history->where('status', 'late')->count();
        $totalAlpha = $history->where('status', 'absent')->count();

        /*
        |----------------------------------------------------------------------
        | Pengumuman terbaru (3)
        |----------------------------------------------------------------------
        */
        $announcements = DB::table('announcements')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        /*
        |----------------------------------------------------------------------
        | Jadwal hari ini
        |----------------------------------------------------------------------
        */
        $hariIni = now()->locale('id')->translatedFormat('l');
        $jadwalHariIni = DB::table('jadwal')
            ->join('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
            ->join('jadwal_mahasiswa', 'jadwal_mahasiswa.jadwal_id', '=', 'jadwal.id')
            ->where('jadwal_mahasiswa.mahasiswa_id', $student_id)
            ->where('jadwal.hari', $hariIni)
            ->select(
                'jadwal.id',
                'jadwal.hari',
                'jadwal.jam_mulai',
                'jadwal.jam_selesai',
                'jadwal.ruangan',
                'jadwal.jenis',
                'jadwal.kelas',
                'matakuliah.namaMk as mata_kuliah'
            )
            ->orderBy('jadwal.jam_mulai')
            ->get();

        /*
        |----------------------------------------------------------------------
        | Notifikasi yang belum dibaca
        |----------------------------------------------------------------------
        */
        $unreadNotifCount = DB::table('notifikasi')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('mahasiswa.dashboard', compact(
            'mahasiswa',
            'active_attendance',
            'sudah_absen',
            'history',
            'chart',
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
        $student_id = $mahasiswa->id ?? Auth::id();

        // BUG FIX: Filter tipe dari query param, default 'semua'
        $tipe = $request->get('tipe', 'semua');

        $hariUrutan = [
            'Senin'  => 1,
            'Selasa' => 2,
            'Rabu'   => 3,
            'Kamis'  => 4,
            'Jumat'  => 5,
            'Sabtu'  => 6,
            'Minggu' => 7,
        ];

        /*
        |----------------------------------------------------------------------
        | Query jadwal dengan filter tipe
        |----------------------------------------------------------------------
        */
        $query = DB::table('jadwal')
            ->join('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
            ->join('jadwal_mahasiswa', 'jadwal_mahasiswa.jadwal_id', '=', 'jadwal.id')
            ->where('jadwal_mahasiswa.mahasiswa_id', $student_id)
            ->select(
                'jadwal.id',
                'jadwal.hari',
                'jadwal.jam_mulai',    // BUG FIX: kolom ini sebelumnya tidak diselect
                'jadwal.jam_selesai',  // BUG FIX: kolom ini sebelumnya tidak diselect
                'jadwal.ruangan',
                'jadwal.jenis',
                'jadwal.kelas',
                'jadwal.dosen_id',
                'matakuliah.namaMk as mata_kuliah'
            );

        if ($tipe !== 'semua') {
            $query->where('jadwal.jenis', $tipe);
        }

        $semuaJadwal = $query->get();

        // BUG FIX: $hariIni dari locale Indonesia
        $hariIni = now()->locale('id')->translatedFormat('l');

        // BUG FIX: $hariIniId didefinisikan dari $hariUrutan
        $hariIniId = $hariUrutan[$hariIni] ?? 1;

        $todaySchedules = $semuaJadwal
            ->filter(fn($j) => $j->hari === $hariIni)
            ->values();

        $weeklySchedules = $semuaJadwal
            ->sortBy(fn($j) => $hariUrutan[$j->hari] ?? 99)
            ->values();

        /*
        |----------------------------------------------------------------------
        | Summary kehadiran per mata kuliah
        |----------------------------------------------------------------------
        */
        $attendanceSummary = DB::table('attendance_records')
            ->join('attendances_sessions', 'attendance_records.attendance_session_id', '=', 'attendances_sessions.id')
            ->join('matakuliah', 'attendances_sessions.course_id', '=', 'matakuliah.id')
            ->where('attendance_records.student_id', $student_id)
            ->select(
                'matakuliah.namaMk',
                'attendances_sessions.course_id',
                DB::raw('COUNT(*) as total_hadir'),
                DB::raw("SUM(CASE WHEN attendance_records.status='present' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN attendance_records.status='late'    THEN 1 ELSE 0 END) as telat"),
                DB::raw("SUM(CASE WHEN attendance_records.status='absent'  THEN 1 ELSE 0 END) as alpha")
            )
            ->groupBy('matakuliah.namaMk', 'attendances_sessions.course_id')
            ->get();

        $totalSesiPerMk = DB::table('attendances_sessions')
            ->select('course_id', DB::raw('COUNT(*) as total_sesi'))
            ->groupBy('course_id')
            ->get()
            ->keyBy('course_id');

        $unreadNotifCount = DB::table('notifikasi')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('mahasiswa.jadwal', [
            'todaySchedules'    => $todaySchedules,
            'weeklySchedules'   => $weeklySchedules,
            'attendanceSummary' => $attendanceSummary,
            'totalSesiPerMk'    => $totalSesiPerMk,
            'hariIni'           => $hariIni,
            'hariIniId'         => $hariIniId, // BUG FIX: sekarang didefinisikan
            'activeTipe'        => $tipe,       // BUG FIX: sekarang didefinisikan
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
        $student_id = $mahasiswa->id ?? Auth::id();

        $history = DB::table('attendance_records')
            ->join('attendances_sessions', 'attendance_records.attendance_session_id', '=', 'attendances_sessions.id')
            ->join('matakuliah', 'attendances_sessions.course_id', '=', 'matakuliah.id')
            ->where('attendance_records.student_id', $student_id)
            ->select(
                'matakuliah.namaMk',
                'matakuliah.id as course_id',
                'attendances_sessions.meeting_number',
                'attendance_records.status',
                'attendance_records.checkin_time'
            )
            ->orderBy('attendance_records.checkin_time', 'desc')
            ->get();

        // Rekap per mata kuliah
        $rekapPerMk = $history->groupBy('namaMk')->map(function ($items) {
            return [
                'namaMk'  => $items->first()->namaMk,
                'hadir'   => $items->where('status', 'present')->count(),
                'telat'   => $items->where('status', 'late')->count(),
                'alpha'   => $items->where('status', 'absent')->count(),
                'total'   => $items->count(),
            ];
        })->values();

        $totalHadir  = $history->where('status', 'present')->count();
        $totalTelat  = $history->where('status', 'late')->count();
        $totalAlpha  = $history->where('status', 'absent')->count();
        $totalSemua  = $history->count();

        $persentaseHadir = $totalSemua > 0
            ? round(($totalHadir + $totalTelat) / $totalSemua * 100, 1)
            : 0;

        $unreadNotifCount = DB::table('notifikasi')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

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

        // 🔥 FIX UTAMA
        $mahasiswa = \App\Models\Mahasiswa::where('user_id', $user->id)->first();

        // Dokumen keimigrasian
        $dokumenKitas = DB::table('dokumen_imigrasi')
            ->where('mahasiswa_id', $mahasiswa->id ?? 0)
            ->first();

        // Dokumen kependudukan
        $dokumenKtp = DB::table('dokumen_kependudukan')
            ->where('mahasiswa_id', $mahasiswa->id ?? 0)
            ->first();

        // Dokumen asuransi
        $dokumenAsuransi = DB::table('dokumen_asuransi')
            ->where('mahasiswa_id', $mahasiswa->id ?? 0)
            ->first();

        $unreadNotifCount = DB::table('notifikasi')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        // BUG FIX: 'mahasiswa.profil' (bukan 'mahasiswa.profile')
        return view('mahasiswa.profil', compact(
            'user',
            'mahasiswa',
            'dokumenKitas',
            'dokumenKtp',
            'dokumenAsuransi',
            'unreadNotifCount'
        ));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'no_whatsapp' => 'nullable|string|max:20',
            'alamat_asal' => 'nullable|string|max:500',
            'alamat_indo' => 'nullable|string|max:500',
        ]);

        DB::table('mahasiswa')
            ->where('user_id', Auth::id())
            ->update([
                'nama'        => $request->nama,
                'no_whatsapp' => $request->no_whatsapp,
                'alamat_asal' => $request->alamat_asal,
                'alamat_indo' => $request->alamat_indo,
                'updated_at'  => now(),
            ]);

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
        $announcements = DB::table('announcements')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $unreadNotifCount = DB::table('notifikasi')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('mahasiswa.announcement', compact('announcements', 'unreadNotifCount'));
    }


    /*
    |==========================================================================
    | NOTIFIKASI
    |==========================================================================
    | BUG FIX: Method ini tidak ada di versi lama (Route::view static).
    */

    public function notifikasi()
    {
        // Tandai semua notif sebagai sudah dibaca saat halaman dibuka
        DB::table('notifikasi')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'updated_at' => now()]);

        $notifikasi = DB::table('notifikasi')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('mahasiswa.notifikasi', compact('notifikasi'));
    }
}
