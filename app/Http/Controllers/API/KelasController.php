<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\kelas;
use App\Services\KelasService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Kelas\createKelasRequest;
use App\Http\Requests\Kelas\updateKelasRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class KelasController extends Controller
{
    protected KelasService $kelasService;

    public function __construct(KelasService $kelasService)
    {
        $this->kelasService = $kelasService;
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $kelas = $this->kelasService->getAll();
        return response()->json([
            'success' => true,
            'message' => 'Kelas retrieved successfully',
            'data'    => $kelas,
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | GET DATA — AJAX table, dengan pagination
    |--------------------------------------------------------------------------
    */
    public function getData(Request $request)
    {
        $filters = [];
        if ($request->filled('kode')) {
            $filters['kode'] = $request->kode;
        }

        $kelas = $this->kelasService->getAll($filters);

        return response()->json([
            'success'    => true,
            'data'       => $kelas->items(),       // array data halaman ini
            'pagination' => [
                'current_page' => $kelas->currentPage(),
                'last_page'    => $kelas->lastPage(),
                'per_page'     => $kelas->perPage(),
                'total'        => $kelas->total(),
            ],
            'flash' => [
                'type'    => 'success',
                'message' => 'Kelas retrieved successfully',
                'theme'   => 'amazon',
                'timeout' => 5000,
            ],
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    | Model Kelas: hasMany(Jadwal), belongsToMany(Mahasiswa)
    | TIDAK ada relasi langsung ke matakuliah/dosen di model Kelas
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        try {
            $kelas = kelas::with(['mahasiswa', 'mahasiswa.user'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => $kelas,
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
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(createKelasRequest $request)
    {
        try {
            $maker = Auth::user();
            $kelas = $this->kelasService->create($maker, $request->validated());

            return response()->json([
                'success' => true,
                'data'    => $kelas,
                'flash'   => [
                    'type'    => 'success',
                    'message' => 'Kelas created successfully',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Create kelas failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => ['type' => 'error', 'message' => 'Kelas creation failed', 'theme' => 'amazon', 'timeout' => 5000],
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(updateKelasRequest $request, $id)
    {
        try {
            $maker   = Auth::user();
            $updated = $this->kelasService->update($maker, $id, $request->validated());

            return response()->json([
                'success' => true,
                'data'    => $updated,
                'flash'   => ['type' => 'success', 'message' => 'Kelas updated successfully', 'theme' => 'amazon', 'timeout' => 5000],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Update kelas failed', ['kelas_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => ['type' => 'error', 'message' => 'Kelas update failed', 'theme' => 'amazon', 'timeout' => 5000],
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        try {
            $maker = Auth::user();
            $this->kelasService->delete($maker, $id);

            return response()->json([
                'success' => true,
                'data'    => [],
                'flash'   => ['type' => 'success', 'message' => 'Kelas deleted successfully', 'theme' => 'amazon', 'timeout' => 5000],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Delete kelas failed', ['kelas_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => ['type' => 'error', 'message' => 'Kelas deletion failed', 'theme' => 'amazon', 'timeout' => 5000],
            ], 500);
        }
    }
}