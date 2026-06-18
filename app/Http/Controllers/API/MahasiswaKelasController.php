<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Services\MahasiswaKelasService;
use App\Http\Controllers\Controller;
use App\Http\Requests\MahasiswaKelas\AddMahasiswaKelasRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MahasiswaKelasController extends Controller
{
    protected MahasiswaKelasService $mahasiswaKelasService;

    public function __construct(MahasiswaKelasService $mahasiswaKelasService)
    {
        $this->mahasiswaKelasService = $mahasiswaKelasService;
    }

    /*
    |--------------------------------------------------------------------------
    | GET KELAS DENGAN SEMUA MAHASISWANYA
    |--------------------------------------------------------------------------
    */
    public function getMahasiswa($kelasId)
    {
        try {
            $data = $this->mahasiswaKelasService->getByKelas($kelasId);

            return response()->json([
                'success' => true,
                'data'    => $data,
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
    | ADD MAHASISWA KE KELAS
    | Yang boleh add: admin jurusan atau admin bipa (sesuai jurusan masing-masing)
    |--------------------------------------------------------------------------
    */
    public function addMahasiswa(AddMahasiswaKelasRequest $request, $kelasId)
    {
        try {
            $maker  = Auth::user();
            $result = $this->mahasiswaKelasService->addMahasiswaToKelas(
                $maker,
                $kelasId,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'data'    => $result,
                'flash'   => [
                    'type'    => 'success',
                    'message' => 'Mahasiswa added to kelas successfully',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Add mahasiswa to kelas failed', [
                'kelas_id' => $kelasId,
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => [
                    'type'    => 'error',
                    'message' => 'Failed to add mahasiswa to kelas',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE MAHASISWA DARI KELAS
    |--------------------------------------------------------------------------
    */
    public function removeMahasiswa($kelasId, $mahasiswaId)
    {
        try {
            $maker = Auth::user();
            $this->mahasiswaKelasService->removeMahasiswaFromKelas($maker, $kelasId, $mahasiswaId);

            return response()->json([
                'success' => true,
                'data'    => [],
                'flash'   => [
                    'type'    => 'success',
                    'message' => 'Mahasiswa removed from kelas successfully',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Remove mahasiswa from kelas failed', [
                'kelas_id'     => $kelasId,
                'mahasiswa_id' => $mahasiswaId,
                'error'        => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => [
                    'type'    => 'error',
                    'message' => 'Failed to remove mahasiswa from kelas',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 500);
        }
    }
}