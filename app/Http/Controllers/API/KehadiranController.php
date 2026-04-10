<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Services\KehadiranService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Kehadiran\createKehadiranRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class KehadiranController extends Controller
{
    protected KehadiranService $kehadiranService;

    public function __construct(KehadiranService $kehadiranService)
    {
        $this->kehadiranService = $kehadiranService;
    }

    /*
    |--------------------------------------------------------------------------
    | STORE BULK KEHADIRAN
    | Dosen submit semua kehadiran mahasiswa dalam satu request
    | Body: { jadwal_id, sesi, tglSesi, jam (opsional), kehadiran: [{mahasiswa_id, status}] }
    |--------------------------------------------------------------------------
    */
    public function storeBulk(Request $request)
    {
        $request->validate([
            'jadwal_id'             => ['required', 'exists:jadwal,id'],
            'sesi'                  => ['required', 'integer', 'min:1', 'max:16'],
            'tglSesi'               => ['required', 'date'],
            'jam'                   => ['nullable', 'string', 'max:20'],
            'kehadiran'             => ['required', 'array', 'min:1'],
            'kehadiran.*.mahasiswa_id' => ['required', 'exists:mahasiswa,id'],
            'kehadiran.*.status'    => ['required', 'in:present,absent,excused'],
        ]);

        try {
            $maker  = Auth::user();
            $result = $this->kehadiranService->storeBulk($maker, $request->all());

            return response()->json([
                'success' => true,
                'data'    => $result,
                'flash'   => [
                    'type'    => 'success',
                    'message' => 'Kehadiran berhasil disimpan',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Store bulk kehadiran failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => [
                    'type'    => 'error',
                    'message' => 'Gagal menyimpan kehadiran',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | GET KEHADIRAN BY JADWAL
    | Untuk lihat rekap kehadiran di jadwal tertentu
    |--------------------------------------------------------------------------
    */
    public function getByJadwal($jadwalId)
    {
        try {
            $data = $this->kehadiranService->getByJadwal($jadwalId);

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
    | UPDATE JAM JADWAL
    | Dosen bisa update jam mengajar (pindah jadwal)
    |--------------------------------------------------------------------------
    */
    public function updateJam(Request $request, $jadwalId)
    {
        $request->validate([
            'jam' => ['required', 'string', 'max:20'],
        ]);

        try {
            $maker  = Auth::user();
            $result = $this->kehadiranService->updateJamJadwal($maker, $jadwalId, $request->jam);

            return response()->json([
                'success' => true,
                'data'    => $result,
                'flash'   => [
                    'type'    => 'success',
                    'message' => 'Jam jadwal berhasil diupdate',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Update jam jadwal failed', [
                'jadwal_id' => $jadwalId,
                'error'     => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => [
                    'type'    => 'error',
                    'message' => 'Gagal update jam jadwal',
                    'theme'   => 'amazon',
                    'timeout' => 5000,
                ],
            ], 500);
        }
    }
}