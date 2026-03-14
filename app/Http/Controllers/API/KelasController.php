<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Kelas;
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
    | GET DATA (dengan filter & sort, untuk AJAX table)
    |--------------------------------------------------------------------------
    */
    public function getData(Request $request)
    {
        $filters = [];

        if ($request->filled('nama')) {
            $filters['nama'] = $request->nama;
        }

        if ($request->filled('kode')) {
            $filters['kode'] = $request->kode;
        }

        $kelas = $this->kelasService->getAll($filters);

        return response()->json([
            'success' => true,
            'data'    => $kelas,
            'flash'   => [
                'type'    => 'success',
                'message' => 'Kelas retrieved successfully',
                'theme'   => 'amazon',
                'timeout' => 5000,
            ],
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW SPECIFIC KELAS
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        try {
            $kelas = $this->kelasService->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => $kelas->load(['matakuliah', 'dosen', 'mahasiswaKelas.mahasiswa']),
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
    | STORE KELAS
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
            Log::error('Create kelas failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => [
                    'type'    => 'error',
                    'message' => 'Kelas creation failed',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE KELAS
    |--------------------------------------------------------------------------
    */
    public function update(updateKelasRequest $request, $id)
    {
        try {
            $maker = Auth::user();

            Log::info('Updating kelas', [
                'kelas_id' => $id,
                'maker_id' => $maker->id,
                'data'     => $request->validated(),
            ]);

            $updated = $this->kelasService->update($maker, $id, $request->validated());

            return response()->json([
                'success' => true,
                'data'    => $updated,
                'flash'   => [
                    'type'    => 'success',
                    'message' => 'Kelas updated successfully',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Update kelas failed', [
                'kelas_id' => $id,
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => [
                    'type'    => 'error',
                    'message' => 'Kelas update failed',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE KELAS
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
                'flash'   => [
                    'type'    => 'success',
                    'message' => 'Kelas deleted successfully',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Delete kelas failed', [
                'kelas_id' => $id,
                'error'    => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => [
                    'type'    => 'error',
                    'message' => 'Kelas deletion failed',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 500);
        }
    }
}