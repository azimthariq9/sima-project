<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    /*
    |==========================================================================
    | HELPER PRIVATE
    |==========================================================================
    */

    /**
     * Jenis jadwal yang boleh dikelola per role.
     * Dipakai untuk filter dan validasi store/update.
     */
    private array $roleJenis = [
        'kln'     => 'KLN',
        'jurusan' => 'Jurusan',
        'bipa'    => 'BIPA',
    ];

    /**
     * Deteksi role yang sedang login dan kembalikan jenis jadwalnya.
     */
    private function detectJenis(): string
    {
        $role = strtolower(
            Auth::user()->role instanceof \App\Enums\Role
                ? Auth::user()->role->value
                : Auth::user()->role
        );

        return $this->roleJenis[$role] ?? 'KLN';
    }

    /**
     * Query jadwal lengkap dengan join.
     * Bisa difilter per jenis atau semua.
     */
    private function queryJadwal(?string $jenis = null)
    {
        $q = DB::table('jadwal')
            ->leftJoin('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
            ->leftJoin('users as dosen_user', 'jadwal.dosen_id', '=', 'dosen_user.id')
            ->select(
                'jadwal.id',
                'jadwal.hari',
                'jadwal.jam_mulai',
                'jadwal.jam_selesai',
                'jadwal.ruangan',
                'jadwal.kelas',
                'jadwal.jenis',
                'jadwal.created_at',
                'matakuliah.id   as matakuliah_id',
                'matakuliah.namaMk as nama_matkul',
                'dosen_user.name as nama_dosen'
            )
            ->orderByRaw("FIELD(jadwal.hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
            ->orderBy('jadwal.jam_mulai');

        if ($jenis) {
            $q->where('jadwal.jenis', $jenis);
        }

        return $q;
    }

    /**
     * Hitung ringkasan jadwal per hari untuk chart/summary.
     */
    private function summaryPerHari(string $jenis): array
    {
        return DB::table('jadwal')
            ->where('jenis', $jenis)
            ->select('hari', DB::raw('COUNT(*) as total'))
            ->groupBy('hari')
            ->pluck('total', 'hari')
            ->toArray();
    }

    private function unreadNotif(): int
    {
        return DB::table('notifikasi')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
    }

    private function allMatakuliah()
    {
        return DB::table('matakuliah')->orderBy('namaMk')->get();
    }

    private function allDosen()
    {
        return DB::table('users')
            ->where('role', 'DOSEN')
            ->orderBy('name')
            ->select('id', 'name')
            ->get();
    }


    /*
    |==========================================================================
    | INDEX — KLN
    | Route: GET kln/schedule -> kln.schedule.index
    |==========================================================================
    */

    public function index(Request $request)
    {
        $jenis      = 'KLN';
        $filterHari = $request->get('hari');
        $search     = $request->get('search');

        $query = $this->queryJadwal($jenis);

        if ($filterHari) {
            $query->where('jadwal.hari', $filterHari);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('matakuliah.namaMk', 'like', "%{$search}%")
                  ->orWhere('jadwal.ruangan', 'like', "%{$search}%")
                  ->orWhere('dosen_user.name', 'like', "%{$search}%");
            });
        }

        $jadwals     = $query->paginate(15)->withQueryString();
        $summary     = $this->summaryPerHari($jenis);
        $matakuliahs = $this->allMatakuliah();
        $dosens      = $this->allDosen();
        $totalJadwal = DB::table('jadwal')->where('jenis', $jenis)->count();

        $unreadNotifCount = $this->unreadNotif();

        return view('kln.schedule', compact(
            'jadwals',
            'summary',
            'matakuliahs',
            'dosens',
            'totalJadwal',
            'filterHari',
            'search',
            'unreadNotifCount'
        ));
    }


    /*
    |==========================================================================
    | INDEX — JURUSAN
    | Route: GET jurusan/schedule -> jurusan.schedule.index
    |==========================================================================
    */

    public function indexJurusan(Request $request)
    {
        $jenis      = 'Jurusan';
        $filterHari = $request->get('hari');
        $search     = $request->get('search');

        $query = $this->queryJadwal($jenis);

        if ($filterHari) {
            $query->where('jadwal.hari', $filterHari);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('matakuliah.namaMk', 'like', "%{$search}%")
                  ->orWhere('jadwal.ruangan', 'like', "%{$search}%")
                  ->orWhere('dosen_user.name', 'like', "%{$search}%");
            });
        }

        $jadwals     = $query->paginate(15)->withQueryString();
        $summary     = $this->summaryPerHari($jenis);
        $matakuliahs = $this->allMatakuliah();
        $dosens      = $this->allDosen();
        $totalJadwal = DB::table('jadwal')->where('jenis', $jenis)->count();

        $unreadNotifCount = $this->unreadNotif();

        return view('jurusan.schedule', compact(
            'jadwals',
            'summary',
            'matakuliahs',
            'dosens',
            'totalJadwal',
            'filterHari',
            'search',
            'unreadNotifCount'
        ));
    }


    /*
    |==========================================================================
    | INDEX — BIPA
    | Route: GET bipa/schedule -> bipa.schedule.index
    |==========================================================================
    */

    public function indexBipa(Request $request)
    {
        $jenis      = 'BIPA';
        $filterHari = $request->get('hari');
        $search     = $request->get('search');

        $query = $this->queryJadwal($jenis);

        if ($filterHari) {
            $query->where('jadwal.hari', $filterHari);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('matakuliah.namaMk', 'like', "%{$search}%")
                  ->orWhere('jadwal.ruangan', 'like', "%{$search}%")
                  ->orWhere('dosen_user.name', 'like', "%{$search}%");
            });
        }

        $jadwals     = $query->paginate(15)->withQueryString();
        $summary     = $this->summaryPerHari($jenis);
        $matakuliahs = $this->allMatakuliah();
        $dosens      = $this->allDosen();
        $totalJadwal = DB::table('jadwal')->where('jenis', $jenis)->count();

        $unreadNotifCount = $this->unreadNotif();

        return view('bipa.schedule', compact(
            'jadwals',
            'summary',
            'matakuliahs',
            'dosens',
            'totalJadwal',
            'filterHari',
            'search',
            'unreadNotifCount'
        ));
    }


    /*
    |==========================================================================
    | SHOW
    | Route: GET kln/schedule/{id} -> kln.schedule.show
    |==========================================================================
    */

    public function show(int $id)
    {
        $jadwal = DB::table('jadwal')
            ->leftJoin('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
            ->leftJoin('users as dosen_user', 'jadwal.dosen_id', '=', 'dosen_user.id')
            ->where('jadwal.id', $id)
            ->select(
                'jadwal.*',
                'matakuliah.namaMk as nama_matkul',
                'dosen_user.name   as nama_dosen'
            )
            ->first();

        if (!$jadwal) {
            return back()->with('error', 'Jadwal tidak ditemukan.');
        }

        // Mahasiswa yang terdaftar di jadwal ini
        $mahasiswaTerdaftar = DB::table('jadwal_mahasiswa')
            ->join('mahasiswa', 'jadwal_mahasiswa.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('jadwal_mahasiswa.jadwal_id', $id)
            ->select('mahasiswa.id', 'mahasiswa.nama', 'mahasiswa.npm')
            ->get();

        return response()->json([
            'jadwal'              => $jadwal,
            'mahasiswaTerdaftar'  => $mahasiswaTerdaftar,
        ]);
    }


    /*
    |==========================================================================
    | STORE  (dipakai KLN, JURUSAN, BIPA — jenis ditentukan dari form)
    | Route: POST kln/schedule         -> kln.schedule.store
    |        POST jurusan/schedule     -> jurusan.schedule.store
    |        POST bipa/schedule        -> bipa.schedule.store
    |==========================================================================
    */

    public function store(Request $request)
    {
        $request->validate([
            'matakuliah_id' => 'required|integer|exists:matakuliah,id',
            'dosen_id'      => 'required|integer|exists:users,id',
            'jenis'         => 'required|in:KLN,Jurusan,BIPA',
            'hari'          => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai'     => 'required|date_format:H:i',
            'jam_selesai'   => 'required|date_format:H:i|after:jam_mulai',
            'ruangan'       => 'required|string|max:100',
            'kelas'         => 'required|string|max:50',
        ]);

        // Cek bentrok jadwal: ruangan + hari + waktu overlap
        $bentrok = DB::table('jadwal')
            ->where('hari', $request->hari)
            ->where('ruangan', $request->ruangan)
            ->where(function ($q) use ($request) {
                $q->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                  ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('jam_mulai', '<=', $request->jam_mulai)
                         ->where('jam_selesai', '>=', $request->jam_selesai);
                  });
            })
            ->exists();

        if ($bentrok) {
            return back()
                ->withInput()
                ->with('error', 'Ruangan sudah dipakai pada hari dan jam yang sama.');
        }

        DB::table('jadwal')->insert([
            'matakuliah_id' => $request->matakuliah_id,
            'dosen_id'      => $request->dosen_id,
            'jenis'         => $request->jenis,
            'hari'          => $request->hari,
            'jam_mulai'     => $request->jam_mulai,
            'jam_selesai'   => $request->jam_selesai,
            'ruangan'       => $request->ruangan,
            'kelas'         => $request->kelas,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // Tentukan redirect berdasarkan jenis
        $redirectRoute = match($request->jenis) {
            'Jurusan' => 'jurusan.schedule.index',
            'BIPA'    => 'bipa.schedule.index',
            default   => 'kln.schedule.index',
        };

        return redirect()
            ->route($redirectRoute)
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }


    /*
    |==========================================================================
    | UPDATE
    | Route: PATCH kln/schedule/{id}     -> kln.schedule.update
    |        PATCH jurusan/schedule/{id} -> jurusan.schedule.update
    |        PATCH bipa/schedule/{id}    -> bipa.schedule.update
    |==========================================================================
    */

    public function update(Request $request, int $id)
    {
        $jadwal = DB::table('jadwal')->where('id', $id)->first();

        if (!$jadwal) {
            return back()->with('error', 'Jadwal tidak ditemukan.');
        }

        $request->validate([
            'matakuliah_id' => 'required|integer|exists:matakuliah,id',
            'dosen_id'      => 'required|integer|exists:users,id',
            'hari'          => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai'     => 'required|date_format:H:i',
            'jam_selesai'   => 'required|date_format:H:i|after:jam_mulai',
            'ruangan'       => 'required|string|max:100',
            'kelas'         => 'required|string|max:50',
        ]);

        // Cek bentrok, kecualikan jadwal yang sedang diedit
        $bentrok = DB::table('jadwal')
            ->where('id', '!=', $id)
            ->where('hari', $request->hari)
            ->where('ruangan', $request->ruangan)
            ->where(function ($q) use ($request) {
                $q->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                  ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('jam_mulai', '<=', $request->jam_mulai)
                         ->where('jam_selesai', '>=', $request->jam_selesai);
                  });
            })
            ->exists();

        if ($bentrok) {
            return back()
                ->withInput()
                ->with('error', 'Ruangan sudah dipakai pada hari dan jam yang sama.');
        }

        DB::table('jadwal')
            ->where('id', $id)
            ->update([
                'matakuliah_id' => $request->matakuliah_id,
                'dosen_id'      => $request->dosen_id,
                'hari'          => $request->hari,
                'jam_mulai'     => $request->jam_mulai,
                'jam_selesai'   => $request->jam_selesai,
                'ruangan'       => $request->ruangan,
                'kelas'         => $request->kelas,
                'updated_at'    => now(),
            ]);

        $redirectRoute = match($jadwal->jenis) {
            'Jurusan' => 'jurusan.schedule.index',
            'BIPA'    => 'bipa.schedule.index',
            default   => 'kln.schedule.index',
        };

        return redirect()
            ->route($redirectRoute)
            ->with('success', 'Jadwal berhasil diperbarui.');
    }


    /*
    |==========================================================================
    | DESTROY
    | Route: DELETE kln/schedule/{id}     -> kln.schedule.destroy
    |        DELETE jurusan/schedule/{id} -> jurusan.schedule.destroy
    |        DELETE bipa/schedule/{id}    -> bipa.schedule.destroy
    |==========================================================================
    */

    public function destroy(int $id)
    {
        $jadwal = DB::table('jadwal')->where('id', $id)->first();

        if (!$jadwal) {
            return back()->with('error', 'Jadwal tidak ditemukan.');
        }

        // Hapus relasi jadwal_mahasiswa terlebih dahulu
        DB::table('jadwal_mahasiswa')->where('jadwal_id', $id)->delete();

        DB::table('jadwal')->where('id', $id)->delete();

        $redirectRoute = match($jadwal->jenis) {
            'Jurusan' => 'jurusan.schedule.index',
            'BIPA'    => 'bipa.schedule.index',
            default   => 'kln.schedule.index',
        };

        return redirect()
            ->route($redirectRoute)
            ->with('success', 'Jadwal berhasil dihapus.');
    }
}
