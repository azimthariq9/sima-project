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
    
    public function create($maker,array $data): Announcement
    {
        DB::beginTransaction();
        
        try {
            // Set default values
            $announcementData = [
                'users_id' => $maker->id, // created by
                'subject' => $data['subject'],
                'message' => $data['message'],
                'status' => $this->validateStatus($data['status'] ?? null),
            ];
            
            // Create announcement
            $announcement = parent::create($maker, $announcementData);
            
            // Sync mahasiswa relations (many-to-many)
            if (isset($data['mahasiswa_ids']) && is_array($data['mahasiswa_ids'])) {
                $announcement->mahasiswa()->sync($data['mahasiswa_ids']);
            }
            
            // // Sync user relations if needed (many-to-many)
            // if (isset($data['user_ids']) && is_array($data['user_ids'])) {
            //     $announcement->users()->sync($data['user_ids']);
            // } elseif (isset($data['mahasiswa_ids'])) {
            //     // Jika hanya ada mahasiswa_ids, bisa ambil user_id dari mahasiswa
            //     $userIds = \App\Models\Mahasiswa::whereIn('id', $data['mahasiswa_ids'])
            //         ->pluck('user_id')
            //         ->toArray();
            //     $announcement->users()->sync($userIds);
            // }
            
            // Log activity
            $this->logActivity('CREATE', $announcement, 
                "Membuat pengumuman: {$announcement->subject}", 
                $maker, 
                $announcement
            );
            
            DB::commit();
            
            // Load relations untuk response
            return $announcement->load(['mahasiswa', 'users']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating announcement: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
        
    }
    
    public function publish($maker, int $id): Announcement
    {
        $announcement = $this->update($maker, $id, [
            'is_published' => true,
            'published_at' => now()
        ]);
        
        $this->logActivity('PUBLISH', $announcement, "Mempublikasikan pengumuman: {$announcement->title}", $maker, $announcement);
        
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