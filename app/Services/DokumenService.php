<?php
// app/Services/DokumenService.php

namespace App\Services;

use App\Models\Dokumen;
use App\Models\User;
use App\Traits\LogsActivityTrait;
use App\Traits\FileValidationTrait;
use App\Services\FileDetailService;
use App\Enums\Status;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DokumenService extends BaseService
{
    use FileValidationTrait,LogsActivityTrait;

    protected $fileDetailService;
    protected $historyDokumenService;

    public function __construct(
        Dokumen $dokumen,
        FileDetailService $fileDetailService,
        HistoryDokumenService $historyDokumenService

    )
    {
        parent::__construct($dokumen);
        $this->fileDetailService = $fileDetailService;
        $this->historyDokumenService = $historyDokumenService;

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
                "Mengupdate status dokumen {$dokumen->jenis_dokumen} dari {$oldStatus} menjadi {$status}", $maker);
            
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
    public function uploadOrUpdateDokumen(array $dokumenData, UploadedFile $file, ?int $reqDokumenId = null): Dokumen
    {
        DB::beginTransaction();
        
        try {
            // 1. Validasi file
            $this->validatePdfFile($file, 2);
            $maker = Auth::user();
            
            // 2. Cek apakah sudah ada dokumen dengan kriteria tertentu
            $existingDokumen = $this->findExistingDokumen(
                $dokumenData['mahasiswa_id'],
                $dokumenData['tipeDkmn'] ?? null,
                $reqDokumenId
            );
            
            // 3. Jika sudah ada, lakukan UPDATE
            if ($existingDokumen) {
                Log::info('Existing dokumen found, performing update', [
                    'dokumen_id' => $existingDokumen->id,
                    'old_file' => $existingDokumen->namaDkmn
                ]);
                
                $result = $this->updateExistingDokumen(
                    $existingDokumen,
                    $dokumenData,
                    $file,
                    $reqDokumenId,
                    $maker
                );
            } 
            // 4. Jika belum ada, lakukan CREATE
            else {
                Log::info('No existing dokumen, performing create');
                
                $result = $this->createNewDokumen(
                    $dokumenData,
                    $file,
                    $reqDokumenId,
                    $maker
                );
            }
            
            DB::commit();
            return $result->load('fileDetail', 'history');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in uploadOrUpdateDokumen: ' . $e->getMessage(), [
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
    /**
     * Create dokumen baru
     */
    private function createNewDokumen(array $dokumenData, UploadedFile $file, ?int $reqDokumenId, $maker): Dokumen
    {
        // 1. Set default status untuk dokumen baru
        $dokumenData['status'] = $dokumenData['status'] ?? 'pending';
        
        // 2. Create dokumen
        $dokumen = $this->create($maker, $dokumenData);
        
        // 3. Upload file
        $this->fileDetailService->uploadForDokumen(
            $file, 
            $dokumen->id, 
            $reqDokumenId
        );
        
        // 4. Catat history UPLOAD
        $mahasiswa = $dokumen->mahasiswa;
        $this->historyDokumenService->logUpload($dokumen, $mahasiswa, [
            'file_size' => $file->getSize(),
            'file_name' => $file->getClientOriginalName(),
            'reqDokumen_id' => $reqDokumenId,
            'action_by' => $maker->name,
            'action_by_id' => $maker->id
        ]);
        
        return $dokumen;
    }

    /**
     * Update dokumen yang sudah ada
     */
    private function updateExistingDokumen(Dokumen $dokumen, array $dokumenData, UploadedFile $newFile, ?int $reqDokumenId, $maker): Dokumen
    {
            $oldStatus = $dokumen->status;

            $oldStatusString = $oldStatus instanceof \App\Enums\status 
            ? $oldStatus->value 
            : (string) $oldStatus;



        $oldNama = $dokumen->namaDkmn;
        $oldFileDetails = $dokumen->fileDetail()->latest()->first();
        
        // 1. Update data dokumen (kecuali field tertentu)
        $updateData = array_merge($dokumenData, [
            'status' => 'pending' // Reset status jadi pending karena upload ulang
        ]);
        $dokumen->update($updateData);
        
        // 2. Soft delete file lama
        $this->fileDetailService->softDeleteForDokumen($dokumen->id);
        
        // 3. Upload file baru
        $this->fileDetailService->uploadForDokumen(
            $newFile, 
            $dokumen->id, 
            $reqDokumenId
        );
        
        // 4. Catat history UPDATE
        $mahasiswa = $dokumen->mahasiswa;
        $this->historyDokumenService->logUpdate($dokumen, $mahasiswa, $oldStatusString, [
            'old_file' => [
                'name' => $oldNama,
                'id' => $oldFileDetails?->id,
                'path' => $oldFileDetails?->path
            ],
            'new_file' => [
                'name' => $dokumen->namaDkmn,
                'size' => $newFile->getSize(),
                'mime' => $newFile->getMimeType()
            ],
            'has_new_file' => true,
            'reason' => 'Upload ulang oleh mahasiswa',
            'action_by' => $maker->name,
            'action_by_id' => $maker->id
        ]);
        
        return $dokumen;
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
        $maker = Auth::user();
        
        if (!$fileDetail || !Storage::disk('local')->exists($fileDetail->path)) {
            throw new \Exception('File tidak ditemukan');
        }
        
        $this->logActivity('DOWNLOAD', $dokumen, "Download dokumen {$dokumen->namaDkmn}",$maker);
        
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

    /**
     * Verifikasi dokumen oleh admin
     */
    public function verifyDokumen(int $id, User $admin, string $action, ?string $message = null): Dokumen
    {
        DB::beginTransaction();
        
        try {
            $dokumen = $this->findOrFail($id);
            
            // Update status berdasarkan action
            switch ($action) {
                case 'APPROVE':
                    $dokumen->status = 'approved';
                    break;
                case 'REJECT':
                    $dokumen->status = 'rejected';
                    break;
                case 'REVISION':
                    $dokumen->status = 'revision';
                    break;
                case 'VERIFY':
                    $dokumen->status = 'verified';
                    break;
                default:
                    throw new \InvalidArgumentException("Invalid action: {$action}");
            }
            
            $dokumen->save();
            
            // Catat history verifikasi
            $this->historyDokumenService->logVerification(
                $dokumen, 
                $admin, 
                $action, 
                $message,
                ['ip_address' => request()->ip()]
            );
            
            DB::commit();
            
            return $dokumen->load('histories');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error verifying dokumen: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Soft delete dokumen
     */
    public function softDeleteDokumen(int $id, $actor, bool $isMahasiswa = true): bool
    {
        DB::beginTransaction();
        
        try {
            $dokumen = $this->findOrFail($id);
            
            // Catat history sebelum delete
            $this->historyDokumenService->logSoftDelete($dokumen, $actor, $isMahasiswa);
            
            // Soft delete file details
            $this->fileDetailService->softDeleteForDokumen($dokumen->id);
            
            // Soft delete dokumen
            $result = $dokumen->delete();
            
            DB::commit();
            
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error soft deleting dokumen: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get dokumen dengan history
     */
    public function getWithHistory(int $id): Dokumen
    {
        return $this->model->with([
            'mahasiswa', 
            'fileDetail' => function($q) {
                $q->withTrashed(); // termasuk yang soft deleted
            },
            'histories' => function($q) {
                $q->with(['mahasiswa', 'user'])->latest();
            }
        ])->withTrashed()->findOrFail($id);
    }
    
    /**
     * Restore dokumen yang soft deleted
     */
    public function restoreDokumen(int $id, User $admin): Dokumen
    {
        DB::beginTransaction();
        
        try {
            $dokumen = $this->model->withTrashed()->findOrFail($id);
            $maker = null;
            // Restore dokumen
            $dokumen->restore();
            
            // Restore file details
            FileDetailService::withTrashed()->where('dokumen_id', $id)->restore();
            
            // Catat history restore
            $this->historyDokumenService->create($maker,[
                'dokumen_id' => $dokumen->id,
                'mahasiswa_id' => $dokumen->mahasiswa_id,
                'user_id' => $admin->id,
                'action' => 'RESTORE',
                'status_from' => 'deleted',
                'status_to' => $dokumen->status,
                'message' => "Dokumen direstore oleh admin {$admin->name}",
            ]);
            
            DB::commit();
            
            return $dokumen;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error restoring dokumen: ' . $e->getMessage());
            throw $e;
        }
    }

        /**
     * Cari dokumen yang sudah ada
     */
    private function findExistingDokumen(int $mahasiswaId, ?string $tipeDkmn = null, ?int $reqDokumenId = null): ?Dokumen
    {
        $query = Dokumen::where('mahasiswa_id', $mahasiswaId)
            ->whereNull('deleted_at'); // hanya yang tidak terhapus
        
        // Cari berdasarkan tipe dokumen jika ada
        if ($tipeDkmn) {
            $query->where('tipeDkmn', $tipeDkmn);
        }
        
        // Cari berdasarkan reqDokumen_id jika ada
        if ($reqDokumenId) {
            // Cek melalui relasi fileDetail
            $query->whereHas('fileDetail', function($q) use ($reqDokumenId) {
                $q->where('reqDokumen_id', $reqDokumenId);
            });
        }
        
        // Prioritaskan yang statusnya pending/revision (bukan approved)
        $dokumen = $query->latest()->first();
        
        return $dokumen;
    }
}