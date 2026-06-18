<?php

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

            $reqData = [
                'user_id' => $mahasiswa->user_id,
                'mahasiswa_id' => $mahasiswa->id,
                'tipeDkmn' => $data['tipeDkmn'],
                'namaDkmn' => $data['tipeDkmn'],
                'message' => $data['message'] ?? null,
                'status' => status::PENDING->value,
            ];

            $reqDokumen = $this->model->create($reqData);

            $this->sendNotificationToAdmin($reqDokumen, $mahasiswa);

            DB::commit();

            return $reqDokumen->fresh();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Kirim notifikasi ke semua admin KLN
     */
    private function sendNotificationToAdmin(ReqDokumen $reqDokumen, $mahasiswa)
    {
        $adminIds = \App\Models\User::where('role', Role::KLN->value)
            ->pluck('id')
            ->toArray();

        $notificationId = 1;

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
            $query->whereHas('mahasiswa', function ($q) use ($filters) {
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

            $updateData = [
                'status' => $data['status'],
            ];

            if (isset($data['catatan'])) {
                $updateData['catatan'] = $data['catatan'];
            }

            $reqDokumen->update($updateData);

            if (isset($data['notification_id'])) {
                $this->sendNotificationToMahasiswa($reqDokumen->mahasiswa_id);
            }

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
        $notificationId = 1;

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

            $reqDokumen->update([
                'status' => status::APPROVED->value,
            ]);

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
     */
    public function completeRequestWithFile(int $id, UploadedFile $file, array $data, $admin): ReqDokumen
    {
        DB::beginTransaction();

        try {

            $reqDokumen = $this->findOrFail($id);

            if ($reqDokumen->status === status::APPROVED->value) {
                throw new \Exception('Request sudah selesai');
            }

            $this->validatePdfFile($file, 2);

            $fileDetailService = app(\App\Services\FileDetailService::class);

            $fileDetailService->uploadForReqDokumen(
                $file,
                $reqDokumen->id,
                $data
            );

            $reqDokumen->update([
                'status' => status::APPROVED->value,
                'namaDkmn' => $file->getClientOriginalName(),
                'tipeDkmn' => $data['tipeDkmn']
            ]);

            DB::commit();

            return $reqDokumen->load('fileDetail');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completing request with file: ' . $e->getMessage());
            throw $e;
        }
    }
}