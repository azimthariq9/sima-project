<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Dosen;
use App\Services\DosenService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dosen\createDosenRequest;
use App\Http\Requests\Dosen\updateDosenRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DosenController extends Controller
{
    protected DosenService $dosenService;

    public function __construct(DosenService $dosenService)
    {
        $this->dosenService = $dosenService;
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW PAGE OF DOSEN
    |--------------------------------------------------------------------------
    */
     public function dashboard()
    {
        return view('dosen.dashboard');
    }

    public function profil()
    {
        return view('dosen.profil');
    }

    public function jadwal()
    {
        return view('dosen.jadwal');
    }

    public function announcement()
    {
        return view('dosen.announcement');
    }

    public function notifikasi()
    {
        return view('dosen.notifikasi');
    }

    public function analytics()
    {
        return view('dosen.analytics');
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $dosen = $this->dosenService->getAll();

        return response()->json([
            'success' => true,
            'message' => 'Dosen retrieved successfully',
            'data'    => $dosen,
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

        if ($request->filled('nidn')) {
            $filters['nidn'] = $request->nidn;
        }

        $sortBy        = $request->get('sort_by', 'id');
        $sortDirection = $request->get('sort_direction', 'desc');

        $dosen = $this->dosenService->getAll($filters);

        return response()->json([
            'success' => true,
            'data'    => $dosen,
            'flash'   => [
                'type'    => 'success',
                'message' => 'Dosen retrieved successfully',
                'theme'   => 'amazon',
                'timeout' => 5000,
            ],
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW SPECIFIC DOSEN
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        try {
            $dosen = $this->dosenService->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => $dosen->load('user'),
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
    | STORE DOSEN
    | Digunakan oleh admin jurusan untuk menambah data dosen
    | (User dengan role dosen sudah dibuat via UserController)
    |--------------------------------------------------------------------------
    */
    public function store(createDosenRequest $request)
    {
        try {
            $maker = Auth::user();
            $dosen = $this->dosenService->create($maker, $request->validated());

            return response()->json([
                'success' => true,
                'data'    => $dosen,
                'flash'   => [
                    'type'    => 'success',
                    'message' => 'Dosen created successfully',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Create dosen failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => [
                    'type'    => 'error',
                    'message' => 'Dosen creation failed',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE DOSEN
    | Bisa digunakan oleh admin jurusan ATAU dosen itu sendiri (update profile)
    |--------------------------------------------------------------------------
    */
    public function update(updateDosenRequest $request, $id)
    {
        try {
            $maker = Auth::user();

            Log::info('Updating dosen', [
                'dosen_id' => $id,
                'maker_id' => $maker->id,
                'data'     => $request->validated(),
            ]);

            $updated = $this->dosenService->update($maker, $id, $request->validated());

            return response()->json([
                'success' => true,
                'data'    => $updated,
                'flash'   => [
                    'type'    => 'success',
                    'message' => 'Dosen updated successfully',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Update dosen failed', [
                'dosen_id' => $id,
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => [
                    'type'    => 'error',
                    'message' => 'Dosen update failed',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE DOSEN
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        try {
            $maker = Auth::user();
            $this->dosenService->delete($maker, $id);

            return response()->json([
                'success' => true,
                'data'    => [],
                'flash'   => [
                    'type'    => 'success',
                    'message' => 'Dosen deleted successfully',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Delete dosen failed', [
                'dosen_id' => $id,
                'error'    => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => [
                    'type'    => 'error',
                    'message' => 'Dosen deletion failed',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 500);
        }
    }
}