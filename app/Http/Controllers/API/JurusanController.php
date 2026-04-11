<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\JurusanService;
use App\Http\Requests\Jurusan\createJurusanRequest;
use App\Http\Requests\Jurusan\updateJurusanRequest;
use App\Models\User;
use App\Enums\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class JurusanController extends Controller
{
    protected JurusanService $jurusanService;

    public function __construct(JurusanService $jurusanService)
    {
        $this->jurusanService = $jurusanService;
    }

    /* ── PAGE METHODS (return view) ─────────────────── */

    public function index()         { return view('jurusan.dashboard'); }
    public function dosenPage()     { return view('jurusan.dosen.index'); }
    public function matakuliahPage(){ return view('jurusan.matakuliah.index'); }
    public function kelasPage()     { return view('jurusan.kelas.index'); }
    public function jadwalPage()    { return view('jurusan.jadwal.index'); }
    public function mahasiswaPage() { return view('jurusan.mahasiswa.index'); }

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
            return response()->json([
                'success' => true, 'data' => [],
                'flash'   => ['type' => 'success', 'message' => 'Jurusan deleted successfully', 'theme' => 'amazon', 'timeout' => 5000],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Delete jurusan failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}