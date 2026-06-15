<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DosenController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD DOSEN
    |--------------------------------------------------------------------------
    */
    public function dashboard()
    {
        $user     = Auth::user();
        $dosen_id = $user->id;

        $total_kelas = DB::table('jadwal')
            ->where('dosen_id', $dosen_id)
            ->count();

        $total_mahasiswa = DB::table('mahasiswa_kelas')->count();

        return view('dosen.dashboard', [
            'total_kelas'     => $total_kelas,
            'total_mahasiswa' => $total_mahasiswa,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PROFIL DOSEN
    |--------------------------------------------------------------------------
    */
    public function profil()
    {
        $user = Auth::user();

        return view('dosen.profil', [
            'user' => $user,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | JADWAL DOSEN
    |--------------------------------------------------------------------------
    */
    public function jadwal()
    {
        $user = Auth::user();

        $jadwal = DB::table('jadwal')
            ->join('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
            ->select('jadwal.*', 'matakuliah.namaMk as matakuliah')
            ->where('jadwal.dosen_id', $user->id)
            ->orderBy('jadwal.hari')
            ->get();

        return view('dosen.jadwal', [
            'jadwal' => $jadwal,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | ANNOUNCEMENT
    |--------------------------------------------------------------------------
    */
    public function announcement()
    {
        $data = DB::table('announcement')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dosen.announcement', [
            'announcement' => $data,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | NOTIFIKASI
    |--------------------------------------------------------------------------
    */
    public function notifikasi()
    {
        $user = Auth::user();

        $notif = DB::table('notification_users')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dosen.notifikasi', [
            'notifikasi' => $notif,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | ANALYTICS DOSEN — FIXED (was double return before)
    |--------------------------------------------------------------------------
    */
    public function analytics()
    {
        $user     = Auth::user();
        $dosen_id = $user->id;

        // ── STATS ─────────────────────────────────────────────────────────────
        $total_kelas = DB::table('jadwal')
            ->where('dosen_id', $dosen_id)
            ->count();

        $total_mahasiswa = DB::table('mahasiswa_kelas')->count();

        $total_absen   = DB::table('attendance_records')->count();
        $total_session = DB::table('attendances_sessions')->count();

        $rata_kehadiran = $total_session > 0
            ? round(($total_absen / ($total_session * 30)) * 100)
            : 0;

        // ── JADWAL HARI INI ───────────────────────────────────────────────────
        // translatedFormat('l') → "Sabtu", "Senin", dst.
        $hari = now()->translatedFormat('l');

        $jadwal_hari_ini = DB::table('jadwal')
            ->join('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
            ->select('jadwal.*', 'matakuliah.namaMk as matakuliah')
            ->where('jadwal.dosen_id', $dosen_id)
            ->where('jadwal.hari', $hari)
            ->get();

        // ── SEMUA MATAKULIAH DOSEN (untuk form Start Attendance) ─────────────
        $semua_matakuliah = DB::table('jadwal')
            ->join('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
            ->select('matakuliah.id as course_id', 'matakuliah.namaMk')
            ->where('jadwal.dosen_id', $dosen_id)
            ->groupBy('matakuliah.id', 'matakuliah.namaMk')
            ->get();

        // ── ACTIVE ATTENDANCE (kode absensi yang masih aktif) ─────────────────
        // INI YANG HILANG SEBELUMNYA — harus sebelum return view()
        $active_attendance = DB::table('attendances_sessions')
            ->where('lecturer_id', $dosen_id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->first();

        // ── DAFTAR MAHASISWA HADIR (live, berdasarkan sesi aktif) ─────────────
        $students_present = collect();
        if ($active_attendance) {
            $students_present = DB::table('attendance_records')
                ->join('mahasiswa', 'attendance_records.student_id', '=', 'mahasiswa.id')
                ->select('mahasiswa.nama', 'attendance_records.checkin_time', 'attendance_records.status')
                ->where('attendance_records.attendance_session_id', $active_attendance->id)
                ->orderBy('attendance_records.checkin_time', 'asc')
                ->get();
        }

        // ── HISTORY ABSENSI (5 terakhir + jumlah hadir) ─────────────────────
        $attendance_history = DB::table('attendances_sessions')
            ->join('matakuliah', 'attendances_sessions.course_id', '=', 'matakuliah.id')
            ->select(
                'attendances_sessions.*',
                'matakuliah.namaMk as matakuliah',
                DB::raw('(SELECT COUNT(*) FROM attendance_records WHERE attendance_session_id = attendances_sessions.id) as total_hadir')
            )
            ->where('attendances_sessions.lecturer_id', $dosen_id)
            ->orderBy('attendances_sessions.created_at', 'desc')
            ->limit(5)
            ->get();

        // ── PROGRESS PERTEMUAN ────────────────────────────────────────────────
        $meeting_progress = DB::table('attendances_sessions')
            ->where('lecturer_id', $dosen_id)
            ->count();

        // ── CHART DATA (per pertemuan) ────────────────────────────────────────
        $chart_data = DB::table('attendances_sessions')
            ->select(
                'meeting_number',
                DB::raw('(SELECT COUNT(*) FROM attendance_records WHERE attendance_session_id = attendances_sessions.id) as hadir')
            )
            ->where('lecturer_id', $dosen_id)
            ->orderBy('meeting_number', 'asc')
            ->limit(16)
            ->get();

        // ── RETURN VIEW — hanya satu return, semua variabel lengkap ──────────
        return view('dosen.analytics', [
            'total_kelas'        => $total_kelas,
            'total_mahasiswa'    => $total_mahasiswa,
            'rata_kehadiran'     => $rata_kehadiran,
            'jadwal_hari_ini'    => $jadwal_hari_ini,
            'semua_matakuliah'   => $semua_matakuliah,
            'active_attendance'  => $active_attendance,
            'students_present'   => $students_present,
            'attendance_history' => $attendance_history,
            'meeting_progress'   => $meeting_progress,
            'chart_data'         => $chart_data,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | START ABSENSI — GENERATE KODE
    |--------------------------------------------------------------------------
    */
    public function startAttendance(Request $request)
    {
        $dosen_id = Auth::id();

        // Generate kode unik 5 karakter
        $code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 5));

        // Tutup semua sesi aktif dosen ini dulu
        DB::table('attendances_sessions')
            ->where('lecturer_id', $dosen_id)
            ->where('status', 'active')
            ->update(['status' => 'closed']);

        // Buat sesi baru
        DB::table('attendances_sessions')->insert([
            'course_id'       => $request->course_id,
            'lecturer_id'     => $dosen_id,
            'meeting_number'  => $request->meeting_number ?? 1,
            'attendance_code' => $code,
            'date'            => now()->toDateString(),
            'start_time'      => now(),
            'end_time'        => now()->addMinutes(15),
            'status'          => 'active',
            'created_at'      => now(),
        ]);

        return redirect()->route('dosen.analytics');
    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE ABSENSI
    |--------------------------------------------------------------------------
    */
    public function closeAttendance(Request $request)
    {
        $dosen_id = Auth::id();

        DB::table('attendances_sessions')
            ->where('id', $request->session_id)
            ->where('lecturer_id', $dosen_id)
            ->update(['status' => 'closed']);

        return redirect()->route('dosen.analytics');
    }

}