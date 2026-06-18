<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Matakuliah;
use App\Services\MatakuliahService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Matakuliah\createMatakuliahRequest;
use App\Http\Requests\Matakuliah\updateMatakuliahRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MatakuliahController extends Controller
{
    protected MatakuliahService $matakuliahService;

    public function __construct(MatakuliahService $matakuliahService)
    {
        $this->matakuliahService = $matakuliahService;
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $matakuliah = $this->matakuliahService->getAll();

        return response()->json([
            'success' => true,
            'message' => 'Matakuliah retrieved successfully',
            'data'    => $matakuliah,
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

        if ($request->filled('namaMk')) {
            $filters['namaMk'] = $request->namaMk;
        }

        if ($request->filled('kode')) {
            $filters['kodeMk'] = $request->kodeMk;
        }

        $matakuliah = $this->matakuliahService->getAll($filters);

        return response()->json([
            'success' => true,
            'data'    => $matakuliah,
            'flash'   => [
                'type'    => 'success',
                'message' => 'Matakuliah retrieved successfully',
                'theme'   => 'amazon',
                'timeout' => 5000,
            ],
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW SPECIFIC MATAKULIAH
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        try {
            $matakuliah = $this->matakuliahService->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => $matakuliah,
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
    | STORE MATAKULIAH
    |--------------------------------------------------------------------------
    */
    public function store(createMatakuliahRequest $request)
    {
        try {
            $maker      = Auth::user();
            $matakuliah = $this->matakuliahService->create($maker, $request->validated());

            return response()->json([
                'success' => true,
                'data'    => $matakuliah,
                'flash'   => [
                    'type'    => 'success',
                    'message' => 'Matakuliah created successfully',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Create matakuliah failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => [
                    'type'    => 'error',
                    'message' => 'Matakuliah creation failed',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE MATAKULIAH
    |--------------------------------------------------------------------------
    */
    public function update(updateMatakuliahRequest $request, $id)
    {
        try {
            $maker = Auth::user();

            Log::info('Updating matakuliah', [
                'matakuliah_id' => $id,
                'maker_id'      => $maker->id,
                'data'          => $request->validated(),
            ]);

            $updated = $this->matakuliahService->update($maker, $id, $request->validated());

            return response()->json([
                'success' => true,
                'data'    => $updated,
                'flash'   => [
                    'type'    => 'success',
                    'message' => 'Matakuliah updated successfully',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Update matakuliah failed', [
                'matakuliah_id' => $id,
                'error'         => $e->getMessage(),
                'trace'         => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => [
                    'type'    => 'error',
                    'message' => 'Matakuliah update failed',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE MATAKULIAH
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        try {
            $maker = Auth::user();
            $this->matakuliahService->delete($maker, $id);

            return response()->json([
                'success' => true,
                'data'    => [],
                'flash'   => [
                    'type'    => 'success',
                    'message' => 'Matakuliah deleted successfully',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Delete matakuliah failed', [
                'matakuliah_id' => $id,
                'error'         => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => [
                    'type'    => 'error',
                    'message' => 'Matakuliah deletion failed',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 500);
        }
    }
}