<?php
// app/Http/Controllers/API/Mahasiswa/DokumenController.php

namespace App\Http\Controllers\API\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use App\Services\DokumenService;
use App\Http\Requests\Dokumen\createDokumenRequest;
use App\Http\Requests\FileDetail\createFileDetailRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Log;

class DokumenController extends Controller
{
    use ApiResponseTrait;
    
    protected $dokumenService;
    
    public function __construct(DokumenService $dokumenService)
    {
        $this->dokumenService = $dokumenService;
    }
    
    /**
     * Upload dokumen baru
     */
    public function store(createDokumenRequest $request)
    {
        try {
            $user = Auth::user();
            
            // Pastikan mahasiswa_id sesuai dengan user yang login
            $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
            
            if (!$mahasiswa) {
                return $this->errorResponse('Data mahasiswa tidak ditemukan', 404);
            }
            
            // Validasi file
            if (!$request->hasFile('file')) {
                return $this->errorResponse('File tidak ditemukan', 422);
            }
            
            $file = $request->file('file');
            
            // Siapkan data dokumen
            $dokumenData = [
                'mahasiswa_id' => $mahasiswa->id,
                'tipeDkmn' => $request->tipeDkmn,
                'namaDkmn' => $request->namaDkmn,
                'penerbit' => $request->penerbit,
                'noDkmn' => $request->noDkmn,
                'tglTerbit' => $request->tglTerbit,
                'tglkdlwrs' => $request->tglkdlwrs,
                'status' => 'pending', // Default status pending
            ];
            
            // Upload dokumen
            $dokumen = $this->dokumenService->uploadDokumen(
                $dokumenData, 
                $file,
                $request->reqDokumen_id ?? null
            );
            
            return $this->successResponse([
                'dokumen' => [
                    'id' => $dokumen->id,
                    'nama' => $dokumen->namaDkmn,
                    'tipe' => $dokumen->tipeDkmn,
                    'status' => $dokumen->status,
                    'file' => [
                        'path' => $dokumen->fileDetail->first()->path ?? null,
                        'size' => $dokumen->fileDetail->first()->size ?? null,
                        'mime_type' => $dokumen->fileDetail->first()->mimeType ?? null,
                    ]
                ]
            ], 'Dokumen berhasil diupload', 201);
            
        } catch (\Exception $e) {
            Log::error('Error uploading dokumen: ' . $e->getMessage());
            return $this->errorResponse('Gagal upload dokumen', 500, $e->getMessage());
        }
    }
    
    /**
     * Get all dokumen for logged in mahasiswa
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
            
            if (!$mahasiswa) {
                return $this->errorResponse('Data mahasiswa tidak ditemukan', 404);
            }
            
            $filters = $request->only(['tipeDkmn', 'status', 'per_page']);
            $dokumen = $this->dokumenService->getByMahasiswa($mahasiswa->id, $filters);
            
            return $this->paginatedResponse($dokumen, 'Dokumen retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil dokumen', 500, $e->getMessage());
        }
    }
    
    /**
     * Download dokumen
     */
    public function download($id)
    {
        try {
            $user = Auth::user();
            $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
            
            // Pastikan dokumen milik mahasiswa ini
            $dokumen = $this->dokumenService->findOrFail($id);
            
            if ($dokumen->mahasiswa_id !== $mahasiswa->id) {
                return $this->errorResponse('Anda tidak memiliki akses ke dokumen ini', 403);
            }
            
            return $this->dokumenService->downloadDokumen($id);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal download dokumen', 500, $e->getMessage());
        }
    }
}