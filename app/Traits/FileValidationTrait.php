<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

trait FileValidationTrait
{
    /**
     * Validate PDF file
     */
    protected function validatePdfFile(UploadedFile $file, int $maxSizeMB = 2): void
    {
        $maxSizeBytes = $maxSizeMB * 1024 * 1024;
        
        // Cek mime type
        if ($file->getMimeType() !== 'application/pdf') {
            throw ValidationException::withMessages([
                'file' => "File harus berformat PDF. Mime type: {$file->getMimeType()}"
            ]);
        }
        
        // Cek ekstensi
        if ($file->getClientOriginalExtension() !== 'pdf') {
            throw ValidationException::withMessages([
                'file' => 'File harus berekstensi .pdf'
            ]);
        }
        
        // Cek ukuran
        if ($file->getSize() > $maxSizeBytes) {
            throw ValidationException::withMessages([
                'file' => "Ukuran file maksimal {$maxSizeMB}MB"
            ]);
        }
    }
    
    /**
     * Get file details from uploaded file
     */
    protected function getFileDetails(UploadedFile $file, ?int $dokumenId = null, ?int $reqDokumenId = null): array
    {
        return [
            'path' => '', // akan diisi setelah upload
            'mimeType' => $file->getMimeType(),
            'size' => $file->getSize(),
            'dokumen_id' => $dokumenId,
            'reqDokumen_id' => $reqDokumenId,
        ];
    }
    
    /**
     * Generate unique filename
     */
    protected function generateFileName(UploadedFile $file, string $prefix = 'dokumen'): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $random = uniqid();
        $extension = $file->getClientOriginalExtension();
        
        return "{$prefix}_{$timestamp}_{$random}.{$extension}";
    }
}
