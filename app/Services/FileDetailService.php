<?php
// app/Services/FileDetailService.php

namespace App\Services;

use App\Models\FileDetail;
use App\Traits\FileValidationTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class FileDetailService extends BaseService
{
    use FileValidationTrait;
    
    protected $disk = 'local'; // untuk testing, nanti bisa ubah ke 'public' atau 's3'
    protected $folder = 'dokumen';
    
    public function __construct(FileDetail $fileDetail)
    {
        parent::__construct($fileDetail);
    }
     /**
     * Soft delete semua file untuk dokumen
     */
    public function softDeleteForDokumen(int $dokumenId): void
    {
        $fileDetails = $this->model->where('dokumen_id', $dokumenId)->get();
        
        foreach ($fileDetails as $fileDetail) {
            $fileDetail->delete(); // soft delete
        }
    }
    /**
     * Upload file untuk dokumen tertentu
     */
    public function uploadForDokumen(UploadedFile $file, int $dokumenId, ?int $reqDokumenId = null): FileDetail
    {
        $maker = Auth::user();
        // 1. Validasi file
        $this->validatePdfFile($file, 2);
        
        // 2. Generate nama file unik
        $filename = $this->generateFileName($file, "dokumen_{$dokumenId}");
        
        // 3. Tentukan path folder berdasarkan mahasiswa/dokumen
        $path = $this->getFolderPath($dokumenId) . '/' . $filename;
        
        // 4. Simpan file ke storage
        Storage::disk($this->disk)->put($path, file_get_contents($file));
        
        // 5. Simpan record ke database
        $fileDetailData = [
            'path' => $path,
            'mimeType' => $file->getMimeType(),
            'fileSize' => $file->getSize(),
            'dokumen_id' => $dokumenId,
            'reqDokumen_id' => $reqDokumenId,
        ];
        
        return $this->create($maker,$fileDetailData);
    }
    
    /**
     * Hapus semua file untuk dokumen
     */
    public function deleteForDokumen(int $dokumenId): void
    {
        $fileDetails = $this->model->where('dokumen_id', $dokumenId)->get();
        
        foreach ($fileDetails as $fileDetail) {
            // Hapus file dari storage
            if (Storage::disk($this->disk)->exists($fileDetail->path)) {
                Storage::disk($this->disk)->delete($fileDetail->path);
            }
            
            // Hapus record
            $fileDetail->delete();
        }
    }
    
    /**
     * Get folder path berdasarkan dokumen
     */
    protected function getFolderPath(int $dokumenId): string
    {
        // Format: dokumen/tahun/bulan/tanggal/dokumen_id
        $date = now();
        
        return sprintf(
            '%s/%s/%s/%s/%d',
            $this->folder,
            $date->format('Y'),
            $date->format('m'),
            $date->format('d'),
            $dokumenId
        );
    }
    
    /**
     * Get URL untuk file (jika public)
     */
    public function getFileUrl(int $fileDetailId): ?string
    {
        $fileDetail = $this->findOrFail($fileDetailId);
        
        if ($this->disk === 'public') {
            return Storage::disk($this->disk)->url($fileDetail->path);
        }
        
        return null; // untuk local disk, tidak ada URL public
    }

    /**
     * Upload file untuk reqDokumen
     */
    public function uploadForReqDokumen(UploadedFile $file, int $reqDokumenId, array $data = []): FileDetail
    {
        $maker = null;
        // 1. Validasi file PDF
        $this->validatePdfFile($file, 2);
        
        // 2. Generate nama file unik
        $filename = $this->generateFileName($file, "req_{$reqDokumenId}");
        
        // 3. Tentukan path
        $path = $this->getFolderPathForReq($reqDokumenId) . '/' . $filename;
        
        // 4. Simpan file ke storage
        Storage::disk($this->disk)->put($path, file_get_contents($file));
        
        // 5. Simpan record ke database
        $fileDetailData = [
            'path' => $path,
            'mimeType' => $file->getMimeType(),
            'fileSize' => $file->getSize(),
            'reqDokumen_id' => $reqDokumenId,
        ];
        
        return $this->create($maker, $fileDetailData);
    }

    /**
     * Get folder path untuk reqDokumen
     */
    protected function getFolderPathForReq(int $reqDokumenId): string
    {
        $date = now();
        
        return sprintf(
                    'req_dokumen/%s/%s/%s/%d',
            $date->format('Y'),
            $date->format('m'),
            $date->format('d'),
            $reqDokumenId
        );
    }
}