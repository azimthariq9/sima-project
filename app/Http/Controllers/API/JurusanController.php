<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\JurusanService;
use App\Http\Requests\Jurusan\createJurusanRequest;
use App\Http\Requests\Jurusan\updateJurusanRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class JurusanController extends Controller
{
    protected JurusanService $jurusanService;

    public function __construct(JurusanService $jurusanService)
    {
        $this->jurusanService = $jurusanService;
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD (view page — untuk admin jurusan)
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        return view('jurusan.dashboard');
    }

    /*
    |--------------------------------------------------------------------------
    | DOSEN PAGE
    |--------------------------------------------------------------------------
    */
    public function dosenPage()
    {
        return view('jurusan.dosen.index');
    }

    /*
    |--------------------------------------------------------------------------
    | MATAKULIAH PAGE
    |--------------------------------------------------------------------------
    */
    public function matakuliahPage()
    {
        return view('jurusan.matakuliah.index');
    }

    /*
    |--------------------------------------------------------------------------
    | KELAS PAGE
    |--------------------------------------------------------------------------
    */
    public function kelasPage()
    {
        return view('jurusan.kelas.index');
    }

    /*
    |--------------------------------------------------------------------------
    | JADWAL PAGE
    |--------------------------------------------------------------------------
    */
    public function jadwalPage()
    {
        return view('jurusan.jadwal.index');
    }    
    /*
    |--------------------------------------------------------------------------
    | MAHASISWA PAGE
    |--------------------------------------------------------------------------
    */
    public function mahasiswaPage()
    {
        return view('jurusan.mahasiswa.index');
    }



    /*
    |==========================================================================
    | JURUSAN CRUD
    | Dikelola oleh KLN (superAdmin) — route ada di kln routes
    |==========================================================================
    */

    /*
    |--------------------------------------------------------------------------
    | INDEX — semua jurusan (untuk halaman management KLN)
    |--------------------------------------------------------------------------
    */
    public function jurusanIndex()
    {
        $jurusan = $this->jurusanService->getAll();

        return response()->json([
            'success' => true,
            'message' => 'Jurusan retrieved successfully',
            'data'    => $jurusan,
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | GET DATA — untuk AJAX table dengan filter & sort
    |--------------------------------------------------------------------------
    */
    public function getJurusan(Request $request)
    {
        $filters = [];

        if ($request->filled('namaJurusan')) {
            $filters['namaJurusan'] = $request->namaJurusan;
        }

        $jurusan = $this->jurusanService->getAll($filters);

        return response()->json([
            'success' => true,
            'data'    => $jurusan,
            'flash'   => [
                'type'    => 'success',
                'message' => 'Jurusan retrieved successfully',
                'theme'   => 'amazon',
                'timeout' => 5000,
            ],
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW — detail jurusan tertentu
    |--------------------------------------------------------------------------
    */
    public function showJurusan($id)
    {
        try {
            $jurusan = $this->jurusanService->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => $jurusan,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | STORE — tambah jurusan baru
    |--------------------------------------------------------------------------
    */
    public function storeJurusan(createJurusanRequest $request)
    {
        try {
            $maker   = Auth::user();
            $jurusan = $this->jurusanService->create($maker, $request->validated());

            return response()->json([
                'success' => true,
                'data'    => $jurusan,
                'flash'   => [
                    'type'    => 'success',
                    'message' => 'Jurusan created successfully',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Create jurusan failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => [
                    'type'    => 'error',
                    'message' => 'Jurusan creation failed',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE — edit nama jurusan
    |--------------------------------------------------------------------------
    */
    public function updateJurusan(updateJurusanRequest $request, $id)
    {
        try {
            $maker = Auth::user();

            Log::info('Updating jurusan', [
                'jurusan_id' => $id,
                'maker_id'   => $maker->id,
                'data'       => $request->validated(),
            ]);

            $updated = $this->jurusanService->update($maker, $id, $request->validated());

            return response()->json([
                'success' => true,
                'data'    => $updated,
                'flash'   => [
                    'type'    => 'success',
                    'message' => 'Jurusan updated successfully',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Update jurusan failed', [
                'jurusan_id' => $id,
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => [
                    'type'    => 'error',
                    'message' => 'Jurusan update failed',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY — hapus jurusan
    | Hati-hati: cascade ke user, dosen, mahasiswa, kelas, dll
    |--------------------------------------------------------------------------
    */
    public function destroyJurusan($id)
    {
        try {
            $maker = Auth::user();
            $this->jurusanService->delete($maker, $id);

            return response()->json([
                'success' => true,
                'data'    => [],
                'flash'   => [
                    'type'    => 'success',
                    'message' => 'Jurusan deleted successfully',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Delete jurusan failed', [
                'jurusan_id' => $id,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => [
                    'type'    => 'error',
                    'message' => 'Jurusan deletion failed',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 500);
        }
    }
}