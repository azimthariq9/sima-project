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
        return view('dosen.profil');
    }

    public function updateProfil(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:100',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user  = Auth::user();
        $dosen = $user->dosen;

        if ($dosen) {
            $dosen->update(['nama' => $request->nama]);
        }

        if ($request->filled('password')) {
            $user->update(['password' => bcrypt($request->password)]);
        }

        return redirect()->route('dosen.profil')->with('success', 'Profil berhasil diperbarui.');
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

        $riwayatSesi = DB::table('jadwal_mahasiswa')
            ->where('jadwal_id', $jadwalId)
            ->select(
                'sesi',
                'tglSesi',
                DB::raw('COUNT(*) as total'),
                DB::raw("COUNT(*) FILTER (WHERE status = 'present') as hadir")
            )
            ->groupBy('sesi', 'tglSesi')
            ->orderBy('sesi')
            ->get();

        return view('dosen.jadwal.detail', compact('jadwal', 'mahasiswa', 'sesiTerisi', 'riwayatSesi'));
    }


    /*
    |--------------------------------------------------------------------------
    | JADWAL SESI DETAIL — edit kehadiran per sesi
    |--------------------------------------------------------------------------
    */
    public function jadwalSesiDetail($jadwalId, $sesi)
    {
        $dosen = Auth::user()->dosen;

        $jadwal = Jadwal::with([
                'kelas.mahasiswa.user',
                'matakuliah',
            ])
            ->where('dosen_id', $dosen->id)
            ->findOrFail($jadwalId);

        $mahasiswa = $jadwal->kelas->mahasiswa ?? collect();

        $kehadiranSesi = Kehadiran::where('jadwal_id', $jadwalId)
            ->where('sesi', $sesi)
            ->get()
            ->keyBy('mahasiswa_id');

        $tglSesi = optional($kehadiranSesi->first())->tglSesi;

        return view('dosen.jadwal.sesi', compact('jadwal', 'mahasiswa', 'sesi', 'kehadiranSesi', 'tglSesi'));
    }


    /*
    |--------------------------------------------------------------------------
    | ANNOUNCEMENT
    |--------------------------------------------------------------------------
    */
    public function announcement()
    {
        $announcements = DB::table('announcement')
            ->where('status', 'active')
            ->orderByDesc('is_penting')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $unreadNotifCount = DB::table('notification_users')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('dosen.announcement', compact('announcements', 'unreadNotifCount'));
    }


    /*
    |--------------------------------------------------------------------------
    | NOTIFIKASI
    |--------------------------------------------------------------------------
    */
    public function notifikasi()
    {
        $user = Auth::user();

        // Mark all as read
        DB::table('notification_users')
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'updated_at' => now()]);

        $notif = DB::table('notification_users')
            ->join('notification', 'notification_users.notification_id', '=', 'notification.id')
            ->where('notification_users.user_id', $user->id)
            ->select(
                'notification.id',
                'notification.subject',
                'notification.message',
                'notification.type',
                'notification_users.is_read',
                'notification_users.created_at as received_at'
            )
            ->orderByDesc('notification_users.created_at')
            ->paginate(15)
            ->withQueryString();

        $unreadNotifCount = 0;

        return view('dosen.notifikasi', compact('notif', 'unreadNotifCount'));
    }


    /*
    |--------------------------------------------------------------------------
    | ANALYTICS — ringkasan statistik kehadiran dari jadwal_mahasiswa
    |--------------------------------------------------------------------------
    */
    public function analytics()
    {
        $dosen   = Auth::user()->dosen;
        $dosenId = $dosen->id ?? null;

        $totalKelas      = 0;
        $totalMahasiswa  = 0;
        $totalSesiTerisi = 0;
        $rekapPerKelas   = collect();

        if ($dosenId) {
            $totalKelas = DB::table('jadwal')
                ->where('dosen_id', $dosenId)
                ->count();

            $totalMahasiswa = DB::table('mahasiswa_kelas')
                ->join('jadwal', 'mahasiswa_kelas.kelas_id', '=', 'jadwal.kelas_id')
                ->where('jadwal.dosen_id', $dosenId)
                ->distinct('mahasiswa_kelas.mahasiswa_id')
                ->count('mahasiswa_kelas.mahasiswa_id');

            // Sesi unik yang sudah punya data kehadiran
            $totalSesiTerisi = DB::table('jadwal_mahasiswa')
                ->join('jadwal', 'jadwal_mahasiswa.jadwal_id', '=', 'jadwal.id')
                ->where('jadwal.dosen_id', $dosenId)
                ->select('jadwal_mahasiswa.jadwal_id', 'jadwal_mahasiswa.sesi')
                ->distinct()
                ->get()
                ->count();

            $rekapPerKelas = DB::table('jadwal')
                ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
                ->join('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
                ->leftJoin('jadwal_mahasiswa', 'jadwal_mahasiswa.jadwal_id', '=', 'jadwal.id')
                ->where('jadwal.dosen_id', $dosenId)
                ->groupBy(
                    'jadwal.id', 'kelas.kodeKelas', 'matakuliah.namaMk',
                    'jadwal.totalSesi', 'jadwal.hari', 'jadwal.jam'
                )
                ->selectRaw('
                    jadwal.id,
                    kelas."kodeKelas",
                    matakuliah."namaMk",
                    jadwal."totalSesi",
                    jadwal.hari,
                    jadwal.jam,
                    COUNT(DISTINCT jadwal_mahasiswa.mahasiswa_id)
                        FILTER (WHERE jadwal_mahasiswa.status IS NOT NULL) as total_mahasiswa,
                    COUNT(DISTINCT jadwal_mahasiswa.sesi)
                        FILTER (WHERE jadwal_mahasiswa.status IN (\'present\', \'absent\', \'excused\')) as sesi_terisi,
                    ROUND(
                        100.0 * COUNT(*) FILTER (WHERE jadwal_mahasiswa.status = \'present\') /
                        NULLIF(COUNT(*) FILTER (WHERE jadwal_mahasiswa.status IN (\'present\', \'absent\', \'excused\')), 0),
                    1) as pct_hadir
                ')
                ->get();
        }

        $unreadNotifCount = DB::table('notification_users')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('dosen.analytics', compact(
            'totalKelas', 'totalMahasiswa', 'totalSesiTerisi',
            'rekapPerKelas', 'unreadNotifCount'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | START / CLOSE ABSENSI — fitur lama dihapus, redirect saja
    |--------------------------------------------------------------------------
    */
    public function startAttendance(Request $request)
    {
        return redirect()->route('dosen.analytics');
    }

    public function closeAttendance(Request $request)
    {
        return redirect()->route('dosen.analytics');
    }
}
