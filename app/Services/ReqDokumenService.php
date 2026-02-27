<?php
// app/Services/ReqDokumenService.php

namespace App\Services;

use App\Models\ReqDokumen;
use App\Models\Notification;
use App\Traits\LogsActivityTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Enums\status;
use App\Enums\Role;
use Illuminate\Http\UploadedFile;
use App\Traits\FileValidationTrait;


class ReqDokumenService extends BaseService
{
    use FileValidationTrait, LogsActivityTrait;
    
    protected $notificationService;
    
    public function __construct(ReqDokumen $reqDokumen, NotificationService $notificationService)
    {
        parent::__construct($reqDokumen);
        $this->notificationService = $notificationService;
    }
    
    /**
     * Create request dokumen dari mahasiswa
     */
    public function createRequest($mahasiswa, array $data): ReqDokumen
    {
        DB::beginTransaction();
        
        try {
            $maker = null;
            // 1. Buat request dokumen
            $reqData = [
                'mahasiswa_id' => $mahasiswa->id,
                // 'user_id' => $mahasiswa->user_id,
                // 'tipeDkmn' => $data['tipeDkmn'],
                // 'namaDkmn' => $data['namaDkmn'],
                // 'keterangan' => $data['keterangan'] ?? null,
                'message' => $data['message'] ?? null,
                'status' => status::PENDING->value,
            ];
            
            $reqDokumen = $this->create($maker, $reqData);
            
            // 2. Kirim notifikasi ke admin KLN
            $this->sendNotificationToAdmin($reqDokumen, $mahasiswa);
            
            // // 3. Log activity
            // $this->logActivity(
            //     'CREATE_REQUEST',
            //     $reqDokumen,
            //     "Mahasiswa {$mahasiswa->nama} request dokumen {$reqDokumen->tipeDkmn}",
            //     auth()->user()
            // );
            
            DB::commit();
            
            return $reqDokumen->load(['mahasiswa', 'user']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating reqDokumen: ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Kirim notifikasi ke semua admin KLN
     */
    private function sendNotificationToAdmin(ReqDokumen $reqDokumen, $mahasiswa)
    {
            // Cari semua user dengan role KLN dan ambil ID-nya
        $adminIds = \App\Models\User::where('role', Role::KLN->value)
            ->pluck('id')
            ->toArray();
        
        // Buat notifikasi (asumsi sudah ada di database)
        $notificationId = 1; // Ganti dengan ID notifikasi yang sesuai
        
        // Kirim ke semua admin
        if (!empty($adminIds)) {
            $this->notificationService->sendToUsers($notificationId, $adminIds);
        }
    }
    
    /**
     * Get all requests untuk admin KLN
     */
    public function getForAdmin(array $filters = [])
    {
        $query = $this->model->with(['mahasiswa', 'user', 'fileDetail'])
            ->latest();
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['tipeDkmn'])) {
            $query->where('tipeDkmn', $filters['tipeDkmn']);
        }
        
        if (isset($filters['mahasiswa_id'])) {
            $query->where('mahasiswa_id', $filters['mahasiswa_id']);
        }
        
        if (isset($filters['search'])) {
            $query->whereHas('mahasiswa', function($q) use ($filters) {
                $q->where('nama', 'like', "%{$filters['search']}%")
                  ->orWhere('npm', 'like', "%{$filters['search']}%");
            });
        }
        
        return $query->paginate($filters['per_page'] ?? 15);
    }
    
    /**
     * Get requests untuk mahasiswa tertentu
     */
    public function getForMahasiswa(int $mahasiswaId, array $filters = [])
    {
        $query = $this->model->with(['fileDetail'])
            ->where('mahasiswa_id', $mahasiswaId)
            ->latest();
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        return $query->paginate($filters['per_page'] ?? 15);
    }
    
    /**
     * Update status request oleh admin
     */
    public function updateStatus(int $id, array $data, $admin): ReqDokumen
    {
        DB::beginTransaction();
        
        try {
            $reqDokumen = $this->findOrFail($id);
            $oldStatus = $reqDokumen->status;
            $oldStatusString = $oldStatus instanceof \App\Enums\status 
                ? $oldStatus->value 
                : (string) $oldStatus;
            
            // Update data
            $updateData = [
                'status' => $data['status'],
            ];
            
            if (isset($data['catatan'])) {
                $updateData['catatan'] = $data['catatan'];
            }
            
            $reqDokumen->update($updateData);
            // Kirim notifikasi ke mahasiswa
            // Kirim notifikasi ke mahasiswa
            if (isset($data['notification_id'])) {
                $this->sendNotificationToMahasiswa($reqDokumen->mahasiswa_id, $data['notification_id']);
            }
            
            // // Log activity
            // $this->logActivity(
            //     'UPDATE_STATUS',
            //     $reqDokumen,
            //     "Admin {$admin->name} update status request dari {$oldStatus} ke {$status}",
            //     $admin
            // );
            
            DB::commit();
            
            return $reqDokumen->fresh(['mahasiswa', 'user']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating reqDokumen status: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Kirim notifikasi ke mahasiswa
     */
    private function sendNotificationToMahasiswa(int $id)
    {
        // $messages = [
        //     'processing' => 'Request dokumen sedang diproses',
        //     'ready' => 'Dokumen sudah siap, silakan cek dan download',
        //     'completed' => 'Request dokumen telah selesai',
        //     'rejected' => 'Request dokumen ditolak',
        // ];
        
        // $message = $messages[$newStatus] ?? "Status request berubah dari {$oldStatus} ke {$newStatus}";
        
        $notificationId= 1;
        
        $this->notificationService->sendToMahasiswa($notificationId, [$id]);
    }
    
    /**
     * Proses request selesai (dokumen sudah diupload)
     */
    public function markAsCompleted(int $id, $admin): ReqDokumen
    {
        DB::beginTransaction();
        
        try {
            $reqDokumen = $this->findOrFail($id);
            $oldStatus = $reqDokumen->status;
            
            $reqDokumen->update([
                'status' => status::APPROVED->value,
            ]);
            
            // Log activity
            $this->logActivity(
                'COMPLETE_REQUEST',
                $reqDokumen,
                "Admin {$admin->name} menyelesaikan request dokumen {$reqDokumen->tipeDkmn}",
                $admin
            );
            
            DB::commit();
            
            return $reqDokumen->fresh();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completing reqDokumen: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
 * Upload dokumen untuk request (menyelesaikan request)
 * Method ini akan mengupdate reqDokumen dan membuat fileDetail
 */
    public function completeRequestWithFile(int $id, UploadedFile $file, array $data, $admin): ReqDokumen
    {
        DB::beginTransaction();
        
        try {
            // 1. Cari request
            $reqDokumen = $this->findOrFail($id);
            
            if ($reqDokumen->status === \App\Enums\status::APPROVED->value) {
                throw new \Exception('Request sudah selesai');
            }
            
            // 2. Validasi file
            $this->validatePdfFile($file, 2); // Perlu di-import trait
            
            // 3. Generate path dan simpan file
            $fileDetailService = app(\App\Services\FileDetailService::class);
            
            // Simpan file dan dapatkan fileDetail
            $fileDetail = $fileDetailService->uploadForReqDokumen(
                $file,
                $reqDokumen->id,
                $data
            );
            
            // 4. Update status request jadi completed
            $reqDokumen->update([
                'status' => \App\Enums\status::APPROVED->value,
                'namaDkmn' => $file->getClientOriginalName(), // Simpan nama file di reqDokumen
                'tipeDkmn' => $data['tipeDkmn']
            ]);
            
            // // 5. Log activity
            // $this->logActivity(
            //     'COMPLETE_REQUEST',
            //     $reqDokumen,
            //     "Admin {$admin->name} menyelesaikan request dokumen {$reqDokumen->tipeDkmn}",
            //     $admin
            // );
            
            DB::commit();
            
            return $reqDokumen->load('fileDetail');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completing request with file: ' . $e->getMessage());
            throw $e;
        }
    }
}