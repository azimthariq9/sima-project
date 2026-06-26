<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\JurusanService;
use App\Http\Requests\Jurusan\createJurusanRequest;
use App\Http\Requests\Jurusan\updateJurusanRequest;
use App\Models\User;
use App\Models\dosen;
use App\Enums\Role;
use App\Services\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JurusanController extends Controller
{
    protected JurusanService $jurusanService;

    public function __construct(JurusanService $jurusanService)
    {
        $this->jurusanService = $jurusanService;
    }

    /* ── PAGE METHODS (return view dengan data server-side) ─────────────────── */

    private function unreadNotif(): int
    {
        return DB::table('notification_users')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
    }

    public function index() { return view('jurusan.dashboard'); }

    public function profil()
    {
        return view('jurusan.profil', ['user' => Auth::user()]);
    }

    public function updateProfil(Request $request)
    {
        $request->validate([
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if ($request->filled('password')) {
            DB::table('users')->where('id', $user->id)->update(['password' => bcrypt($request->password)]);
        }

        return redirect()->route('jurusan.profil')->with('success', 'Password berhasil diperbarui.');
    }

    public function mahasiswaPage(Request $request)
    {
        $jid    = Auth::user()->jurusan_id;
        $search = $request->input('search', '');

        $query = User::where('role', 'mahasiswa')
            ->where('jurusan_id', $jid)
            ->with(['mahasiswa', 'mahasiswa.kelas']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'ilike', "%{$search}%")
                  ->orWhereHas('mahasiswa', function ($m) use ($search) {
                      $m->where('nama', 'ilike', "%{$search}%")
                        ->orWhere('npm',  'ilike', "%{$search}%");
                  });
            });
        }

        $mahasiswa        = $query->latest()->paginate(15)->withQueryString();
        $unreadNotifCount = $this->unreadNotif();

        return view('jurusan.mahasiswa.index', compact('mahasiswa', 'search', 'unreadNotifCount'));
    }

    public function dosenPage(Request $request)
    {
        $jid    = Auth::user()->jurusan_id;
        $search = $request->input('search', '');

        $query = dosen::with('user')
            ->whereHas('user', function ($q) use ($jid) {
                $q->where('jurusan_id', $jid);
            });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama',    'ilike', "%{$search}%")
                  ->orWhere('nidn',   'ilike', "%{$search}%")
                  ->orWhere('kodeDos','ilike', "%{$search}%");
            });
        }

        $dosens           = $query->latest()->paginate(15)->withQueryString();
        $unreadNotifCount = $this->unreadNotif();

        return view('jurusan.dosen.index', compact('dosens', 'search', 'unreadNotifCount'));
    }

    public function matakuliahPage(Request $request)
    {
        $jid    = Auth::user()->jurusan_id;
        $search = $request->input('search', '');

        $query = DB::table('matakuliah')->where('jurusan_id', $jid);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('namaMk', 'ilike', "%{$search}%")
                  ->orWhere('kodeMk', 'ilike', "%{$search}%");
            });
        }
        $matakuliah       = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $unreadNotifCount = $this->unreadNotif();

        return view('jurusan.matakuliah.index', compact('matakuliah', 'search', 'unreadNotifCount'));
    }

    public function kelasPage(Request $request)
    {
        $jid    = Auth::user()->jurusan_id;
        $search = $request->input('search', '');

        $query = DB::table('kelas')
            ->join('jurusan', 'kelas.jurusan_id', '=', 'jurusan.id')
            ->where('kelas.jurusan_id', $jid);
        if ($search) {
            $query->where('kelas.kodeKelas', 'ilike', "%{$search}%");
        }
        $kelas = $query
            ->selectRaw('kelas.*, jurusan."namaJurusan", (SELECT COUNT(*) FROM mahasiswa_kelas mk WHERE mk.kelas_id = kelas.id) as mahasiswa_count')
            ->orderByDesc('kelas.created_at')
            ->paginate(15)->withQueryString();
        $unreadNotifCount = $this->unreadNotif();

        return view('jurusan.kelas.index', compact('kelas', 'search', 'unreadNotifCount'));
    }

    public function jadwalPage(Request $request)
    {
        $jid    = Auth::user()->jurusan_id;
        $search = $request->input('search', '');

        $query = DB::table('jadwal')
            ->join('dosen as d', 'jadwal.dosen_id', '=', 'd.id')
            ->join('users as u', 'd.user_id', '=', 'u.id')
            ->leftJoin('matakuliah', 'jadwal.matakuliah_id', '=', 'matakuliah.id')
            ->leftJoin('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->where('u.jurusan_id', $jid);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('matakuliah.namaMk', 'ilike', "%{$search}%")
                  ->orWhere('d.nama', 'ilike', "%{$search}%")
                  ->orWhere('kelas.kodeKelas', 'ilike', "%{$search}%");
            });
        }

        $jadwal = $query->select(
                'jadwal.*',
                'matakuliah.namaMk as nama_matkul',
                'd.nama as nama_dosen',
                'kelas.kodeKelas'
            )
            ->orderByRaw("CASE jadwal.hari
                WHEN 'Senin'   THEN 1 WHEN 'Selasa'  THEN 2 WHEN 'Rabu'    THEN 3
                WHEN 'Kamis'   THEN 4 WHEN 'Jumat'   THEN 5 WHEN 'Sabtu'   THEN 6
                ELSE 7 END, jadwal.jam")
            ->paginate(15)->withQueryString();

        $unreadNotifCount = $this->unreadNotif();

        return view('jurusan.jadwal.index', compact('jadwal', 'search', 'unreadNotifCount'));
    }

    /*
    |--------------------------------------------------------------------------
    | MAHASISWA DATA
    | Query: User dengan role mahasiswa dan jurusan_id sama dengan admin login
    | Load relasi mahasiswa agar nama, npm, dll tersedia
    |--------------------------------------------------------------------------
    */
    public function getMahasiswaData(Request $request)
    {
        $jurusanId = Auth::user()->jurusan_id;

        $query = User::where('role', Role::MAHASISWA)
            ->where('jurusan_id', $jurusanId)
            ->with([
                'mahasiswa',
                'mahasiswa.kelas', // kelas yang diikuti mahasiswa
            ]);

        // Filter search nama/npm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhereHas('mahasiswa', function ($m) use ($search) {
                      $m->where('nama', 'like', "%{$search}%")
                        ->orWhere('npm',  'like', "%{$search}%");
                  });
            });
        }

        $perPage   = $request->get('per_page', 15);
        $mahasiswa = $query->latest()->paginate($perPage);

        return response()->json([
            'success'    => true,
            'data'       => $mahasiswa->items(),
            'pagination' => [
                'current_page' => $mahasiswa->currentPage(),
                'last_page'    => $mahasiswa->lastPage(),
                'per_page'     => $mahasiswa->perPage(),
                'total'        => $mahasiswa->total(),
            ],
            'flash' => [
                'type'    => 'success',
                'message' => 'Mahasiswa retrieved successfully',
                'theme'   => 'amazon',
                'timeout' => 5000,
            ],
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW MAHASISWA DETAIL
    | Return user + relasi mahasiswa + kelas yang diikuti
    |--------------------------------------------------------------------------
    */
    public function showMahasiswa($id)
    {
        try {
            $user = User::where('jurusan_id', Auth::user()->jurusan_id)
                ->with([
                    'mahasiswa',
                    'mahasiswa.kelas',
                    'mahasiswa.kelas.jadwal',
                    'mahasiswa.kelas.jadwal.matakuliah',
                    'mahasiswa.kelas.jadwal.dosen',
                ])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => $user,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak ditemukan',
            ], 404);
        }
    }

    /*
    |==========================================================================
    | JURUSAN CRUD (diakses oleh KLN)
    |==========================================================================
    */

    public function jurusanIndex()
    {
        $jurusan = $this->jurusanService->getAll();
        return response()->json(['success' => true, 'data' => $jurusan], 200);
    }

    public function getJurusan(Request $request)
    {
        $filters = [];
        if ($request->filled('namaJurusan')) {
            $filters['namaJurusan'] = $request->namaJurusan;
        }

        $jurusan = $this->jurusanService->getAll($filters);

        return response()->json([
            'success'    => true,
            'data'       => $jurusan->items(),
            'pagination' => [
                'current_page' => $jurusan->currentPage(),
                'last_page'    => $jurusan->lastPage(),
                'per_page'     => $jurusan->perPage(),
                'total'        => $jurusan->total(),
            ],
        ], 200);
    }

    public function showJurusan($id)
    {
        try {
            return response()->json(['success' => true, 'data' => $this->jurusanService->findOrFail($id)], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function storeJurusan(createJurusanRequest $request)
    {
        try {
            $jurusan = $this->jurusanService->create(Auth::user(), $request->validated());
            ActivityLog::record("Membuat jurusan: {$jurusan->namaJurusan}", 'jurusan', $jurusan->id);
            return response()->json([
                'success' => true, 'data' => $jurusan,
                'flash'   => ['type' => 'success', 'message' => 'Jurusan created successfully', 'theme' => 'amazon', 'timeout' => 5000],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Create jurusan failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateJurusan(updateJurusanRequest $request, $id)
    {
        try {
            $updated = $this->jurusanService->update(Auth::user(), $id, $request->validated());
            ActivityLog::record("Memperbarui jurusan #{$id}", 'jurusan', (int) $id);
            return response()->json([
                'success' => true, 'data' => $updated,
                'flash'   => ['type' => 'success', 'message' => 'Jurusan updated successfully', 'theme' => 'amazon', 'timeout' => 5000],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Update jurusan failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroyJurusan($id)
    {
        try {
            $this->jurusanService->delete(Auth::user(), $id);
            ActivityLog::record("Menghapus jurusan #{$id}", 'jurusan', (int) $id);
            return response()->json([
                'success' => true, 'data' => [],
                'flash'   => ['type' => 'success', 'message' => 'Jurusan deleted successfully', 'theme' => 'amazon', 'timeout' => 5000],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Delete jurusan failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
 * Preview kelas by kodeKelas + tahunAjar
 * Dipakai oleh form tambah jadwal untuk validasi realtime
 */
public function previewKelas(Request $request)
{
    $kelas = \App\Models\kelas::where('kodeKelas', $request->kodeKelas)
        ->where('tahunAjar', $request->tahunAjar)
        ->first();

    if (!$kelas) {
        return response()->json(['found' => false]);
    }

    return response()->json([
        'found'     => true,
        'id'        => $kelas->id,
        'kodeKelas' => $kelas->kodeKelas,
        'tahunAjar' => $kelas->tahunAjar,
    ]);
}

/**
 * Preview matakuliah by kodeMk
 */
public function previewMatakuliah(Request $request)
{
    $mk = \App\Models\Matakuliah::where('kodeMk', $request->kodeMk)
        ->where('jurusan_id', auth()->user()->jurusan_id) // scope ke jurusan
        ->first();

    if (!$mk) {
        return response()->json(['found' => false]);
    }

    return response()->json([
        'found'  => true,
        'id'     => $mk->id,
        'kodeMk' => $mk->kodeMk,
        'namaMk' => $mk->namaMk,
    ]);
}

/**
 * Preview dosen by kodeDos atau nidn
 */
public function previewDosen(Request $request)
{
    $dosen = \App\Models\dosen::where('kodeDos', $request->kodeDos)
        ->orWhere('nidn', $request->kodeDos)
        ->whereHas('user', fn($q) => $q->where('jurusan_id', auth()->user()->jurusan_id))
        ->first();

    if (!$dosen) {
        return response()->json(['found' => false]);
    }

    return response()->json([
        'found'   => true,
        'id'      => $dosen->id,
        'nama'    => $dosen->nama,
        'kodeDos' => $dosen->kodeDos,
        'nidn'    => $dosen->nidn,
    ]);
}
}

