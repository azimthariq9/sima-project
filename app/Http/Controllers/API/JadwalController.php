<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Services\JadwalService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Jadwal\createJadwalRequest;
use App\Http\Requests\Jadwal\updateJadwalRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class JadwalController extends Controller
{
    protected JadwalService $jadwalService;

    public function __construct(JadwalService $jadwalService)
    {
        $this->jadwalService = $jadwalService;
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $jadwal = $this->jadwalService->getAll();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal retrieved successfully',
            'data'    => $jadwal,
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

        if ($request->filled('hari')) {
            $filters['hari'] = $request->hari;
        }

        if ($request->filled('kelas_id')) {
            $filters['kelas_id'] = $request->kelas_id;
        }

        if ($request->filled('dosen_id')) {
            $filters['dosen_id'] = $request->dosen_id;
        }

        $jadwal = $this->jadwalService->getAll($filters);

        return response()->json([
            'success' => true,
            'data'    => $jadwal,
            'flash'   => [
                'type'    => 'success',
                'message' => 'Jadwal retrieved successfully',
                'theme'   => 'amazon',
                'timeout' => 5000,
            ],
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW SPECIFIC JADWAL
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        try {
            $jadwal = $this->jadwalService->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => $jadwal->load(['kelas', 'dosen', 'matakuliah']),
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
    | STORE JADWAL
    |--------------------------------------------------------------------------
    */
    public function store(createJadwalRequest $request)
    {
        try {
            $maker  = Auth::user();
            $jadwal = $this->jadwalService->create($maker, $request->validated());

            return response()->json([
                'success' => true,
                'data'    => $jadwal,
                'flash'   => [
                    'type'    => 'success',
                    'message' => 'Jadwal created successfully',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Create jadwal failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => [
                    'type'    => 'error',
                    'message' => 'Jadwal creation failed',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE JADWAL
    |--------------------------------------------------------------------------
    */
    public function update(updateJadwalRequest $request, $id)
    {
        try {
            $maker = Auth::user();

            Log::info('Updating jadwal', [
                'jadwal_id' => $id,
                'maker_id'  => $maker->id,
                'data'      => $request->validated(),
            ]);

            $updated = $this->jadwalService->update($maker, $id, $request->validated());

            return response()->json([
                'success' => true,
                'data'    => $updated,
                'flash'   => [
                    'type'    => 'success',
                    'message' => 'Jadwal updated successfully',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Update jadwal failed', [
                'jadwal_id' => $id,
                'error'     => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => [
                    'type'    => 'error',
                    'message' => 'Jadwal update failed',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE JADWAL
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        try {
            $maker = Auth::user();
            $this->jadwalService->delete($maker, $id);

            return response()->json([
                'success' => true,
                'data'    => [],
                'flash'   => [
                    'type'    => 'success',
                    'message' => 'Jadwal deleted successfully',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Delete jadwal failed', [
                'jadwal_id' => $id,
                'error'     => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => [
                    'type'    => 'error',
                    'message' => 'Jadwal deletion failed',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 500);
        }
    }
}