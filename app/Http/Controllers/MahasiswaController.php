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
                ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
                ->join('mahasiswa_kelas', 'mahasiswa_kelas.kelas_id', '=', 'kelas.id')
                ->leftJoin('dosen', 'jadwal.dosen_id', '=', 'dosen.id')
                ->where('mahasiswa_kelas.mahasiswa_id', $student_id)
                ->where('jadwal.hari', $hariIni)
                ->select(
                    'jadwal.id',
                    'jadwal.hari',
                    DB::raw("REPLACE(SPLIT_PART(jadwal.jam, ' - ', 1), '.', ':') as jam_mulai"),
                    DB::raw("REPLACE(SPLIT_PART(jadwal.jam, ' - ', 2), '.', ':') as jam_selesai"),
                    'jadwal.ruangan',
                    DB::raw("CASE WHEN matakuliah.jurusan_id = 1 THEN 'kln' WHEN matakuliah.jurusan_id = 2 THEN 'bipa' ELSE 'perkuliahan' END as tipe_kelas"),
                    'matakuliah.namaMk as mata_kuliah',
                    'dosen.nama as dosen'
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
        $unreadNotifCount = $this->unreadNotifCount();

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
            $semuaJadwal = DB::table('jadwal')
                ->join('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
                ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
                ->join('mahasiswa_kelas', 'mahasiswa_kelas.kelas_id', '=', 'kelas.id')
                ->leftJoin('dosen', 'jadwal.dosen_id', '=', 'dosen.id')
                ->where('mahasiswa_kelas.mahasiswa_id', $student_id)
                ->select(
                    'jadwal.id',
                    'jadwal.hari',
                    DB::raw("REPLACE(SPLIT_PART(jadwal.jam, ' - ', 1), '.', ':') as jam_mulai"),
                    DB::raw("REPLACE(SPLIT_PART(jadwal.jam, ' - ', 2), '.', ':') as jam_selesai"),
                    'jadwal.ruangan',
                    DB::raw("CASE WHEN matakuliah.jurusan_id = 1 THEN 'kln' WHEN matakuliah.jurusan_id = 2 THEN 'bipa' ELSE 'perkuliahan' END as tipe_kelas"),
                    'matakuliah.namaMk as mata_kuliah',
                    'dosen.nama as dosen'
                )
                ->get()->unique('id')->values();

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
                ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
                ->join('mahasiswa_kelas', 'mahasiswa_kelas.kelas_id', '=', 'kelas.id')
                ->where('mahasiswa_kelas.mahasiswa_id', $student_id)
                ->select(
                    'jadwal.matakuliah_id as course_id',
                    DB::raw('MAX(jadwal."totalSesi") as total_sesi')
                )
                ->groupBy('jadwal.matakuliah_id')
                ->get()
                ->keyBy('course_id');
        }

        $unreadNotifCount = $this->unreadNotifCount();

        return view('mahasiswa.jadwal', [
            'todaySchedules'    => $todaySchedules,
            'weeklySchedules'   => $weeklySchedules,
            'attendanceSummary' => $attendanceSummary,
            'totalSesiPerMk'    => $totalSesiPerMk,
            'hariIni'           => $hariIni,
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

        $unreadNotifCount = $this->unreadNotifCount();

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


    public function kehadiran(Request $request)
    {
        $mahasiswa  = $this->getMahasiswa();
        $student_id = $mahasiswa->id ?? null;
        $tipe       = $request->input('tipe', 'semua');

        $kelas = collect();

        if ($student_id) {
            // kelas tidak punya matakuliah_id — harus lewat jadwal
            $query = DB::table('mahasiswa_kelas')
                ->join('kelas', 'mahasiswa_kelas.kelas_id', '=', 'kelas.id')
                ->join('jadwal', 'jadwal.kelas_id', '=', 'kelas.id')
                ->join('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
                ->leftJoin('dosen', 'jadwal.dosen_id', '=', 'dosen.id')
                ->leftJoin('jadwal_mahasiswa', function ($join) use ($student_id) {
                    $join->on('jadwal_mahasiswa.jadwal_id', '=', 'jadwal.id')
                         ->where('jadwal_mahasiswa.mahasiswa_id', $student_id);
                })
                ->where('mahasiswa_kelas.mahasiswa_id', $student_id)
                ->groupBy(
                    'kelas.id',
                    DB::raw('kelas."kodeKelas"'),
                    DB::raw('matakuliah."namaMk"'),
                    'matakuliah.jurusan_id'
                )
                ->selectRaw('
                    kelas.id as kelas_id,
                    kelas."kodeKelas",
                    matakuliah."namaMk",
                    matakuliah.jurusan_id,
                    CASE WHEN matakuliah.jurusan_id = 1 THEN \'kln\'
                         WHEN matakuliah.jurusan_id = 2 THEN \'bipa\'
                         ELSE \'perkuliahan\' END as tipe_kelas,
                    MIN(dosen.nama) as dosen,
                    MAX(jadwal."totalSesi") as total_sesi,
                    SUM(CASE WHEN jadwal_mahasiswa.status = \'present\' THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN jadwal_mahasiswa.status = \'excused\' THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN jadwal_mahasiswa.status = \'absent\'  THEN 1 ELSE 0 END) as absen
                ');

            if ($tipe !== 'semua') {
                $jurusanId = match($tipe) {
                    'kln'  => 1, 'bipa' => 2, default => null,
                };
                if ($jurusanId) {
                    $query->where('matakuliah.jurusan_id', $jurusanId);
                } else {
                    $query->where('matakuliah.jurusan_id', '>', 2);
                }
            }

            $kelas = $query->get();
        }

        $unreadNotifCount = $this->unreadNotifCount();

        return view('mahasiswa.kehadiran.index', compact('kelas', 'tipe', 'unreadNotifCount'));
    }

    public function kehadiranDetail(int $kelasId)
    {
        $mahasiswa  = $this->getMahasiswa();
        $student_id = $mahasiswa->id ?? null;

        abort_if(!$student_id, 403);

        $kelasInfo = DB::table('mahasiswa_kelas')
            ->join('kelas', 'mahasiswa_kelas.kelas_id', '=', 'kelas.id')
            ->join('jadwal', 'jadwal.kelas_id', '=', 'kelas.id')
            ->join('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
            ->leftJoin('dosen', 'jadwal.dosen_id', '=', 'dosen.id')
            ->where('mahasiswa_kelas.mahasiswa_id', $student_id)
            ->where('kelas.id', $kelasId)
            ->groupBy(
                'kelas.id',
                DB::raw('kelas."kodeKelas"'),
                DB::raw('matakuliah."namaMk"'),
                'matakuliah.jurusan_id'
            )
            ->selectRaw('
                kelas.id as kelas_id,
                kelas."kodeKelas",
                matakuliah."namaMk",
                matakuliah.jurusan_id,
                MIN(dosen.nama) as dosen,
                MAX(jadwal."totalSesi") as total_sesi
            ')
            ->first();

        abort_if(!$kelasInfo, 403);

        $sesiList = DB::table('jadwal_mahasiswa')
            ->join('jadwal', 'jadwal_mahasiswa.jadwal_id', '=', 'jadwal.id')
            ->where('jadwal.kelas_id', $kelasId)
            ->where('jadwal_mahasiswa.mahasiswa_id', $student_id)
            ->selectRaw('
                jadwal_mahasiswa.sesi,
                jadwal_mahasiswa.status,
                jadwal_mahasiswa."tglSesi",
                jadwal.hari,
                jadwal.jam,
                jadwal.ruangan
            ')
            ->orderBy('jadwal_mahasiswa.sesi')
            ->get();

        $unreadNotifCount = $this->unreadNotifCount();

        return view('mahasiswa.kehadiran.detail', compact('kelasInfo', 'sesiList', 'unreadNotifCount'));
    }


    /*
    |==========================================================================
    | PROFIL
    |==========================================================================
    */

    public function getProfile()
    {
        $user      = Auth::user();
        $mahasiswa = $this->getMahasiswa();

        $unreadNotifCount = $this->unreadNotifCount();

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

    public function dokumenPage()
    {
        $mahasiswa   = $this->getMahasiswa();
        $mahasiswaId = $mahasiswa->id ?? 0;

        $dokumen = DB::table('dokumen')
            ->where('mahasiswa_id', $mahasiswaId)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get();

        $recentRequests = DB::table('reqDokumen')
            ->where('mahasiswa_id', $mahasiswaId)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(fn($r) => [
                'code'   => 'REQ-' . str_pad($r->id, 3, '0', STR_PAD_LEFT),
                'name'   => str_replace('_', ' ', $r->namaDkmn ?? $r->tipeDkmn ?? '-'),
                'status' => $r->status,
                'date'   => \Carbon\Carbon::parse($r->created_at)->format('d M Y'),
            ]);

        $unreadNotifCount = $this->unreadNotifCount();

        return view('mahasiswa.dokumen.index', compact('dokumen', 'recentRequests', 'unreadNotifCount'));
    }

    public function storeDokumen(Request $request)
    {
        $request->validate([
            'tipeDkmn'  => 'required|string',
            'noDkmn'    => 'required|string|max:100',
            'tglTerbit' => 'required|date',
            'tglKdlwrs' => 'required|date|after:tglTerbit',
            'penerbit'  => 'required|in:KLN,IMIGRASI,KEPENDUDUKAN',
            'file'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'tglKdlwrs.after' => 'Tanggal berlaku harus setelah tanggal terbit.',
            'penerbit.in'     => 'Pilih penerbit yang valid.',
        ]);

        $mahasiswa = $this->getMahasiswa();
        if (!$mahasiswa) {
            return back()->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $namaDkmn = str_replace('_', ' ', $request->tipeDkmn);

        $dokumenId = DB::table('dokumen')->insertGetId([
            'mahasiswa_id' => $mahasiswa->id,
            'tipeDkmn'     => $request->tipeDkmn,
            'namaDkmn'     => $namaDkmn,
            'penerbit'     => $request->penerbit,
            'noDkmn'       => $request->noDkmn,
            'tglTerbit'    => $request->tglTerbit,
            'tglKdlwrs'    => $request->tglKdlwrs,
            'status'       => 'pending',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('dokumen', 'local');
            DB::table('fileDetail')->insert([
                'dokumen_id' => $dokumenId,
                'path'       => $path,
                'mimeType'   => $request->file('file')->getClientMimeType(),
                'fileSize'   => $request->file('file')->getSize(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('mahasiswa.dokumen.index')
            ->with('success', 'Dokumen berhasil diupload. KLN akan segera memverifikasi.');
    }

    public function downloadDokumen(int $id)
    {
        $mahasiswa = $this->getMahasiswa();
        $dok = DB::table('dokumen')
            ->where('id', $id)
            ->where('mahasiswa_id', $mahasiswa->id ?? 0)
            ->whereNull('deleted_at')
            ->first();

        abort_if(!$dok, 403);

        $file = DB::table('fileDetail')
            ->where('dokumen_id', $id)
            ->orderByDesc('created_at')
            ->first();

        abort_if(!$file, 404, 'File tidak tersedia.');

        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('local');
        abort_if(!$storage->exists($file->path), 404, 'File tidak ditemukan di server.');

        return $storage->download($file->path, basename($file->path));
    }

    public function updateDokumen(Request $request, int $id)
    {
        $request->validate([
            'tipeDkmn'  => 'required|string',
            'noDkmn'    => 'required|string|max:100',
            'tglTerbit' => 'required|date',
            'tglKdlwrs' => 'required|date|after:tglTerbit',
            'penerbit'  => 'required|in:KLN,IMIGRASI,KEPENDUDUKAN',
            'file'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'tglKdlwrs.after' => 'Tanggal berlaku harus setelah tanggal terbit.',
        ]);

        $mahasiswa = $this->getMahasiswa();
        if (!$mahasiswa) {
            return back()->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $dok = DB::table('dokumen')
            ->where('id', $id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereNull('deleted_at')
            ->first();

        abort_if(!$dok, 403);

        // Jika status approved → reset ke pending (butuh verifikasi ulang KLN)
        $newStatus = $dok->status === 'approved' ? 'pending' : $dok->status;
        $namaDkmn  = str_replace('_', ' ', $request->tipeDkmn);

        DB::table('dokumen')->where('id', $id)->update([
            'tipeDkmn'  => $request->tipeDkmn,
            'namaDkmn'  => $namaDkmn,
            'penerbit'  => $request->penerbit,
            'noDkmn'    => $request->noDkmn,
            'tglTerbit' => $request->tglTerbit,
            'tglKdlwrs' => $request->tglKdlwrs,
            'status'    => $newStatus,
            'updated_at'=> now(),
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('dokumen', 'local');
            DB::table('fileDetail')->insert([
                'dokumen_id' => $id,
                'path'       => $path,
                'mimeType'   => $request->file('file')->getClientMimeType(),
                'fileSize'   => $request->file('file')->getSize(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $msg = $newStatus === 'pending' && $dok->status === 'approved'
            ? 'Dokumen diperbarui dan dikembalikan ke status pending (perlu verifikasi ulang KLN).'
            : 'Dokumen berhasil diperbarui.';

        return redirect()->route('mahasiswa.dokumen.index')->with('success', $msg);
    }

    public function destroyDokumen(int $id)
    {
        $mahasiswa = $this->getMahasiswa();
        if (!$mahasiswa) {
            return back()->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $dok = DB::table('dokumen')
            ->where('id', $id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereNull('deleted_at')
            ->first();

        abort_if(!$dok, 403);

        // Soft delete — tidak boleh hapus dokumen yang sudah approved
        if ($dok->status === 'approved') {
            return back()->with('error', 'Dokumen yang sudah diverifikasi tidak dapat dihapus.');
        }

        DB::table('dokumen')->where('id', $id)->update([
            'deleted_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('mahasiswa.dokumen.index')->with('success', 'Dokumen berhasil dihapus.');
    }

    public function createRequest()
    {
        $mahasiswaId = $this->getMahasiswa()->id ?? 0;

        $recentRequests = DB::table('reqDokumen')
            ->where('mahasiswa_id', $mahasiswaId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($r) => [
                'code'   => 'REQ-' . str_pad($r->id, 3, '0', STR_PAD_LEFT),
                'name'   => str_replace('_', ' ', $r->namaDkmn),
                'status' => $r->status,
                'date'   => \Carbon\Carbon::parse($r->created_at)->format('d M Y'),
            ]);

        return view('mahasiswa.request.create', compact('recentRequests'));
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
            ->where('status', 'active')
            ->orderByDesc('is_penting')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $unreadNotifCount = $this->unreadNotifCount();

        return view('mahasiswa.announcement', compact('announcements', 'unreadNotifCount'));
    }

    public function announcementShow(int $id)
    {
        $ann = DB::table('announcement')
            ->where('id', $id)
            ->where('status', 'active')
            ->first();

        abort_if(!$ann, 404);

        $files = DB::table('announcement_files')
            ->where('announcement_id', $id)
            ->selectRaw('id, announcement_id, path, "originalName", "mimeType", size')
            ->get();

        $author = DB::table('users')
            ->where('id', $ann->user_id)
            ->value('email');

        $unreadNotifCount = $this->unreadNotifCount();

        return view('mahasiswa.announcement-show', compact('ann', 'files', 'author', 'unreadNotifCount'));
    }

    public function announcementFile(int $id, int $fileId)
    {
        $file = DB::table('announcement_files')
            ->where('id', $fileId)
            ->where('announcement_id', $id)
            ->selectRaw('id, path, "originalName", "mimeType", size')
            ->first();

        abort_if(!$file, 404);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('local');

        if (!$storage->exists($file->path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return $storage->download($file->path, $file->originalName ?? basename($file->path));
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

        DB::table('notification_mahasiswa')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'updated_at' => now()]);

        return view('mahasiswa.notifikasi', compact('notifications', 'unreadCount'));
    }

    public function markNotifRead()
    {
        $mahasiswa = $this->getMahasiswa();
        if (!$mahasiswa) return redirect()->route('mahasiswa.notifikasi');

        DB::table('notification_mahasiswa')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'updated_at' => now()]);

        return redirect()->route('mahasiswa.notifikasi');
    }

    public function markOneNotifRead(int $notifId)
    {
        $mahasiswa = $this->getMahasiswa();
        if (!$mahasiswa) return redirect()->route('mahasiswa.notifikasi');

        DB::table('notification_mahasiswa')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('notification_id', $notifId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'updated_at' => now()]);

        return redirect()->route('mahasiswa.notifikasi');
    }

    private function unreadNotifCount(): int
    {
        $mahasiswa = $this->getMahasiswa();
        if (!$mahasiswa) return 0;

        return DB::table('notification_mahasiswa')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('is_read', false)
            ->count();
    }
}
