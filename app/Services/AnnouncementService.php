<?php
// app/Services/AnnouncementService.php

namespace App\Services;

use App\Models\Announcement;
use App\Traits\LogsActivityTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use app\Enums\Status;
use app\Enums\Role;
use Illuminate\Support\Facades\Log;
class AnnouncementService extends BaseService
{
    use LogsActivityTrait;
    
    public function __construct(Announcement $announcement)
    {
        parent::__construct($announcement);
    }
    
    public function getAll(array $filters = [], int $perPage = 15):LengthAwarePaginator
    {
        $query = Announcement::with(['targetRoles', 'createdBy']);
        
        if (isset($filters['is_published'])) {
            $query->where('is_published', $filters['is_published']);
        }
        
        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }
        
        return $query->latest()->paginate($perPage);
    }
    
    public function create($maker, array $data): Announcement
    {
        DB::beginTransaction();
        
        try {
            // 1. Data announcement
            $announcementData = [
                'user_id' => $maker->id,  // creator
                'subject' => $data['subject'],
                'message' => $data['message'],
                'status' => $data['status'] ?? Status::PENDING->value,
            ];
            
            // 2. Create announcement
            $announcement = parent::create($maker, $announcementData);
            
            // 3. Attach mahasiswa (penerima) - INI SATU-SATUNYA RELASI
            if (isset($data['mahasiswa_ids']) && is_array($data['mahasiswa_ids'])) {
                $announcement->mahasiswa()->attach($data['mahasiswa_ids']);
                
                Log::info('Mahasiswa attached', [
                    'announcement_id' => $announcement->id,
                    'mahasiswa_ids' => $data['mahasiswa_ids']
                ]);
            }
            
            
            DB::commit();
            
            // Load relasi untuk response
            return $announcement->load('mahasiswa', 'user');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating announcement: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function publish($maker, int $id): Announcement
    {
        $announcement = $this->update($maker, $id, [
            'is_published' => true,
            'published_at' => now()
        ]);
        
        $this->logActivity('PUBLISH', $announcement, "Mempublikasikan pengumuman: {$announcement->title}", $maker);
        
        return $announcement;
    }

    /**
     * Validate and return status
     */
    private function validateStatus($status): string
    {
        $validStatuses = array_column(Status::cases(), 'value');
        
        if ($status && in_array($status, $validStatuses)) {
            return $status;
        }
        
        return Status::PENDING->value; // default
    }
}