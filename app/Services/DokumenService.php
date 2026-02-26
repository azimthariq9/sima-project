<?php
// app/Services/DokumenService.php

namespace App\Services;

use App\Models\Dokumen;
use App\Traits\LogsActivityTrait;
use App\Traits\FileValidationTrait;
use App\Services\FileDetailService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;


class DokumenService extends BaseService
{
    use FileValidationTrait,LogsActivityTrait;

    protected $fileDetailService;

    public function __construct(
        Dokumen $dokumen,
        FileDetailService $fileDetailService

    )
    {
        parent::__construct($dokumen);
        $this->fileDetailService = $fileDetailService;
    }
    
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Dokumen::with(['user', 'user.mahasiswa'])
            ->when(isset($filters['status']), function ($q) use ($filters) {
                $q->where('status', $filters['status']);
            })
            ->when(isset($filters['user_id']), function ($q) use ($filters) {
                $q->where('user_id', $filters['user_id']);
            })
            ->when(isset($filters['jenis_dokumen']), function ($q) use ($filters) {
                $q->where('jenis_dokumen', $filters['jenis_dokumen']);
            });
            
        return $query->latest()->paginate($perPage);
    }
    
    public function updateStatus(int $id, string $status, ?string $catatan = null): Dokumen
    {
        DB::beginTransaction();
        
        try {
            $maker = auth()->user();
            $dokumen = $this->findOrFail($id);
            $oldStatus = $dokumen->status;
            
            $dokumen->update([
                'status' => $status,
                'catatan_revisi' => $status === 'revisi' ? $catatan : $dokumen->catatan_revisi,
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);
            
            $this->logActivity('UPDATE_STATUS', $dokumen, 
                "Mengupdate status dokumen {$dokumen->jenis_dokumen} dari {$oldStatus} menjadi {$status}", $maker, $dokumen
            );
            
            DB::commit();
            return $dokumen->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

     /**
     * Upload dokumen baru dengan file
     */
    public function uploadDokumen(array $dokumenData, UploadedFile $file, ?int $reqDokumenId = null): Dokumen
    {
        DB::beginTransaction();
        
        try {
            // 1. Validasi file
            $this->validatePdfFile($file, 2); // max 2MB
            
            // 2. Create dokumen
            $dokumen = $this->create($dokumenData);
            
            // 3. Upload file dan simpan detail
            $fileDetail = $this->fileDetailService->uploadForDokumen(
                $file, 
                $dokumen->id, 
                $reqDokumenId
            );
            
            // 4. Log activity
            $this->logActivity('UPLOAD', $dokumen, 
                "Upload dokumen {$dokumen->namaDkmn} untuk mahasiswa ID: {$dokumen->mahasiswa_id}"
            );
            
            DB::commit();
            
            return $dokumen->load('fileDetail');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error uploading dokumen: ' . $e->getMessage(), [
                'dokumen_data' => $dokumenData,
                'file_name' => $file->getClientOriginalName(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * Update dokumen dengan file baru (opsional)
     */
    public function updateWithFile(int $id, array $dokumenData, ?UploadedFile $file = null, ?int $reqDokumenId = null): Dokumen
    {
        DB::beginTransaction();
        
        try {
            $dokumen = $this->findOrFail($id);
            
            // 1. Update dokumen data
            $dokumen->update($dokumenData);
            
            // 2. Jika ada file baru, upload dan replace
            if ($file) {
                $this->validatePdfFile($file, 2);
                
                // Hapus file lama
                $this->fileDetailService->deleteForDokumen($dokumen->id);
                
                // Upload file baru
                $this->fileDetailService->uploadForDokumen($file, $dokumen->id, $reqDokumenId);
            }
            
            $this->logActivity('UPDATE', $dokumen, "Update dokumen {$dokumen->namaDkmn}");
            
            DB::commit();
            
            return $dokumen->load('fileDetail');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating dokumen: ' . $e->getMessage());
            throw $e;
        }
    }

        /**
     * Get dokumen by mahasiswa
     */
    public function getByMahasiswa(int $mahasiswaId, array $filters = [])
    {
        $query = Dokumen::with('fileDetail')
            ->where('mahasiswa_id', $mahasiswaId);
        
        if (isset($filters['tipeDkmn'])) {
            $query->where('tipeDkmn', $filters['tipeDkmn']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

     /**
     * Download file dokumen
     */
    public function downloadDokumen(int $id)
    {
        $dokumen = $this->findOrFail($id);
        $fileDetail = $dokumen->fileDetail()->latest()->first();
        
        if (!$fileDetail || !Storage::disk('local')->exists($fileDetail->path)) {
            throw new \Exception('File tidak ditemukan');
        }
        
        $this->logActivity('DOWNLOAD', $dokumen, "Download dokumen {$dokumen->namaDkmn}");
        
        return Storage::disk('local')->download(
            $fileDetail->path,
            $this->generateDownloadFilename($dokumen)
        );
    }
    
    /**
     * Generate filename untuk download
     */
    protected function generateDownloadFilename(Dokumen $dokumen): string
    {
        $nama = str_replace(' ', '_', $dokumen->namaDkmn);
        $tgl = now()->format('Y-m-d');
        
        return "{$nama}_{$tgl}.pdf";
    }

    
}