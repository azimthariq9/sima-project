<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Jadwal;
use App\Models\Kehadiran;

class DosenController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    public function dashboard()
    {
        $dosen = Auth::user()->dosen;

        $hariMap = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu',
        ];
        $hariId = $hariMap[now()->format('l')] ?? now()->format('l');

        $jadwalHariIni = collect();
        if ($dosen) {
            $jadwalHariIni = Jadwal::with(['kelas', 'matakuliah'])
                ->where('dosen_id', $dosen->id)
                ->where('hari', $hariId)
                ->get();
        }

        return view('dosen.dashboard', compact('jadwalHariIni', 'hariId'));
    }


    /*
    |--------------------------------------------------------------------------
    | PROFIL
    |--------------------------------------------------------------------------
    */
    public function profil()
    {
        return view('dosen.profil', ['user' => Auth::user()]);
    }


    /*
    |--------------------------------------------------------------------------
    | JADWAL — daftar semua jadwal dosen
    |--------------------------------------------------------------------------
    */
    public function jadwal()
    {
        $dosen  = Auth::user()->dosen;

        $jadwal = collect();
        if ($dosen) {
            $jadwal = Jadwal::with(['kelas', 'matakuliah'])
                ->where('dosen_id', $dosen->id)
                ->orderByRaw("CASE hari
                    WHEN 'Senin'   THEN 1
                    WHEN 'Selasa'  THEN 2
                    WHEN 'Rabu'    THEN 3
                    WHEN 'Kamis'   THEN 4
                    WHEN 'Jumat'   THEN 5
                    WHEN 'Sabtu'   THEN 6
                    ELSE 7 END")
                ->get();
        }

        return view('dosen.jadwal.index', compact('jadwal'));
    }


    /*
    |--------------------------------------------------------------------------
    | JADWAL DETAIL — mahasiswa di kelas + form kehadiran
    |--------------------------------------------------------------------------
    */
    public function jadwalDetail($jadwalId)
    {
        $dosen  = Auth::user()->dosen;

        $jadwal = Jadwal::with([
                'kelas.mahasiswa.user',
                'matakuliah',
            ])
            ->where('dosen_id', $dosen->id)
            ->findOrFail($jadwalId);

        $mahasiswa  = $jadwal->kelas->mahasiswa ?? collect();

        $sesiTerisi = Kehadiran::where('jadwal_id', $jadwalId)
            ->distinct()
            ->pluck('sesi')
            ->toArray();

        return view('dosen.jadwal.detail', compact('jadwal', 'mahasiswa', 'sesiTerisi'));
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

        return view('dosen.announcement', ['announcement' => $data]);
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

        return view('dosen.notifikasi', ['notifikasi' => $notif]);
    }


    /*
    |--------------------------------------------------------------------------
    | ANALYTICS
    |--------------------------------------------------------------------------
    */
    public function analytics()
    {
        $dosen_id = Auth::id();

        $total_kelas     = DB::table('jadwal')->where('dosen_id', $dosen_id)->count();
        $total_mahasiswa = DB::table('mahasiswa_kelas')->count();
        $total_absen     = DB::table('attendance_records')->count();
        $total_session   = DB::table('attendances_sessions')->count();

        $rata_kehadiran = $total_session > 0
            ? round(($total_absen / ($total_session * 30)) * 100)
            : 0;

        $hariMap = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu',
        ];
        $hari = $hariMap[now()->format('l')] ?? now()->format('l');

        $jadwal_hari_ini = DB::table('jadwal')
            ->join('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
            ->select('jadwal.*', 'matakuliah.namaMk as matakuliah')
            ->where('jadwal.dosen_id', $dosen_id)
            ->where('jadwal.hari', $hari)
            ->get();

        $semua_matakuliah = DB::table('jadwal')
            ->join('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
            ->select('matakuliah.id as course_id', 'matakuliah.namaMk')
            ->where('jadwal.dosen_id', $dosen_id)
            ->groupBy('matakuliah.id', 'matakuliah.namaMk')
            ->get();

        $active_attendance = DB::table('attendances_sessions')
            ->where('lecturer_id', $dosen_id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->first();

        $students_present = collect();
        if ($active_attendance) {
            $students_present = DB::table('attendance_records')
                ->join('mahasiswa', 'attendance_records.student_id', '=', 'mahasiswa.id')
                ->select('mahasiswa.nama', 'attendance_records.checkin_time', 'attendance_records.status')
                ->where('attendance_records.attendance_session_id', $active_attendance->id)
                ->orderBy('attendance_records.checkin_time', 'asc')
                ->get();
        }

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

        $meeting_progress = DB::table('attendances_sessions')
            ->where('lecturer_id', $dosen_id)
            ->count();

        $chart_data = DB::table('attendances_sessions')
            ->select(
                'meeting_number',
                DB::raw('(SELECT COUNT(*) FROM attendance_records WHERE attendance_session_id = attendances_sessions.id) as hadir')
            )
            ->where('lecturer_id', $dosen_id)
            ->orderBy('meeting_number', 'asc')
            ->limit(16)
            ->get();

        return view('dosen.analytics', compact(
            'total_kelas', 'total_mahasiswa', 'rata_kehadiran',
            'jadwal_hari_ini', 'semua_matakuliah', 'active_attendance',
            'students_present', 'attendance_history', 'meeting_progress', 'chart_data'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | START ABSENSI
    |--------------------------------------------------------------------------
    */
    public function startAttendance(Request $request)
    {
        $dosen_id = Auth::id();
        $code     = strtoupper(substr(md5(uniqid(rand(), true)), 0, 5));

        DB::table('attendances_sessions')
            ->where('lecturer_id', $dosen_id)
            ->where('status', 'active')
            ->update(['status' => 'closed']);

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
        DB::table('attendances_sessions')
            ->where('id', $request->session_id)
            ->where('lecturer_id', Auth::id())
            ->update(['status' => 'closed']);

        return redirect()->route('dosen.analytics');
    }
}
