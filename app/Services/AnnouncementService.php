<?php
// app/Services/AnnouncementService.php

namespace App\Services;

use App\Models\Announcement;
use App\Traits\LogsActivityTrait;
use Illuminate\Pagination\LengthAwarePaginator;

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
        $data['created_by'] = $maker->id;
        $announcement = parent::create($maker,$data);
        
        $this->logActivity('CREATE', $announcement, "Membuat pengumuman: {$announcement->title}", $maker, $announcement);
        
        return $announcement;
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
}