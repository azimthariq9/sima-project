<?php
// app/Services/MahasiswaService.php

namespace App\Services;

use App\Models\Mahasiswa;
use App\Models\User;
use App\Traits\LogsActivityTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class MahasiswaService extends BaseService
{
    use LogsActivityTrait;
    
    protected $userService;
    
    public function __construct(Mahasiswa $mahasiswa, UserService $userService)
    {
        parent::__construct($mahasiswa);
        $this->userService = $userService;
    }
    
    /**
     * Get mahasiswa by user ID
     */
    public function getByUserId(int $userId): ?Mahasiswa
    {
        return $this->model->where('user_id', $userId)->first();
    }
    
    /**
     * Get mahasiswa by user ID or fail
     */
    public function getByUserIdOrFail(int $userId): Mahasiswa
    {
        $mahasiswa = $this->getByUserId($userId);
        
        if (!$mahasiswa) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                "Mahasiswa not found for user ID: {$userId}"
            );
        }
        
        return $mahasiswa;
    }
    
    /**
     * Update profile mahasiswa
     */
    public function updateProfile(int $userId, array $data): Mahasiswa
    {
        DB::beginTransaction();
        
         try {
            // 1. Cari mahasiswa
            $mahasiswa = $this->getByUserIdOrFail($userId);
            Log::info('Mahasiswa found:', ['id' => $mahasiswa->id, 'current_data' => $mahasiswa->toArray()]);
            
            // 2. Simpan data lama
            $oldData = $mahasiswa->toArray();
            
            // 3. Update data mahasiswa
            $mahasiswa->update($data);
            Log::info('After update:', $mahasiswa->toArray());
            
            // 4. Refresh model
            $mahasiswa->refresh();
            Log::info('After refresh:', $mahasiswa->toArray());
            
            
            // // 5. Log activity
            // $this->logActivity(
            //     'UPDATE_PROFILE',
            //     $mahasiswa,
            //     "Mahasiswa {$mahasiswa->nama} mengupdate profile",
            //     auth()->user()
            // );
            
            DB::commit();
            
            return $mahasiswa->load('user');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating mahasiswa profile: ' . $e->getMessage(), [
                'user_id' => $userId,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * Get profile lengkap mahasiswa
     */
    public function getProfile(int $userId): ?Mahasiswa
    {
        return $this->model
            ->with(['user', 'dokumen' => function($q) {
                $q->with('fileDetail')->latest();
            }])
            ->where('user_id', $userId)
            ->first();
    }
    
    /**
     * Get dashboard stats untuk mahasiswa
     */
    public function getDashboardStats(int $userId): array
    {
        $mahasiswa = $this->getByUserIdOrFail($userId);
        
        return [
            'profile' => [
                'nama' => $mahasiswa->nama,
                'npm' => $mahasiswa->npm,
                'noWa' => $mahasiswa->noWa,
                'warNeg' => $mahasiswa->warNeg,
                'status_profile' => $mahasiswa->user?->status_profile ?? 'pending',
            ],
            'dokumen_stats' => [
                'total' => $mahasiswa->dokumen()->count(),
                'pending' => $mahasiswa->dokumen()->where('status', 'pending')->count(),
                'approved' => $mahasiswa->dokumen()->where('status', 'approved')->count(),
                'revision' => $mahasiswa->dokumen()->where('status', 'revision')->count(),
                'expired' => $mahasiswa->dokumen()
                    ->where('tglkdlwrs', '<', now())
                    ->count(),
            ],
            'recent_dokumen' => $mahasiswa->dokumen()
                ->with('fileDetail')
                ->latest()
                ->take(5)
                ->get(),
        ];
    }
    
    /**
     * Cek kelengkapan profile
     */
    public function checkProfileCompletion(int $userId): array
    {
        $mahasiswa = $this->getByUserIdOrFail($userId);
        
        $requiredFields = ['nama', 'npm', 'noWa', 'tglLahir', 'warNeg', 'alamatAsal', 'alamatIndo'];
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            if (empty($mahasiswa->$field)) {
                $missingFields[] = $field;
            }
        }
        
        $completionPercentage = 100 - (count($missingFields) / count($requiredFields) * 100);
        
        return [
            'is_complete' => count($missingFields) === 0,
            'completion_percentage' => round($completionPercentage),
            'missing_fields' => $missingFields,
            'filled_fields' => array_diff($requiredFields, $missingFields),
        ];
    }
}