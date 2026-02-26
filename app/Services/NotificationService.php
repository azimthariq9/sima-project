<?php
// app/Services/NotificationService.php

namespace App\Services;

use App\Models\Notification;
use App\Traits\LogsActivityTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationService extends BaseService
{
    use LogsActivityTrait;
    
    public function __construct(Notification $notification)
    {
        parent::__construct($notification);
    }
    
    public function getAll(array $filters = [], int $perPage = 15):LengthAwarePaginator
    {
        $query = Notification::with(['targetUsers', 'createdBy']);
        
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }
        
        return $query->latest()->paginate($perPage);
    }
    
    public function create($maker,array $data): Notification
    {
        $data['created_by'] = $maker->id;
        $notification = parent::create($maker,$data);
        
        // Attach target users if specified
        if (isset($data['target_roles']) && is_array($data['target_roles'])) {
            $userIds = \App\Models\User::whereIn('role', $data['target_roles'])->pluck('id');
            $notification->targetUsers()->attach($userIds);
        }
        
        $this->logActivity('CREATE', $notification, "Membuat notifikasi: {$notification->title}", $maker);
        
        return $notification->load('targetUsers');
    }
    
    public function update($maker, int $id, array $data): Notification
    {
        $notification = parent::update($maker, $id, $data);
        
        if (isset($data['target_roles'])) {
            $notification->targetUsers()->detach();
            $userIds = \App\Models\User::whereIn('role', $data['target_roles'])->pluck('id');
            $notification->targetUsers()->attach($userIds);
        }
        
        $this->logActivity('UPDATE', $notification, "Mengupdate notifikasi: {$notification->title}", $maker);
        
        return $notification->load('targetUsers');
    }

    public function sendToUsers(int $notification_id, array $user_ids): void
    {
        $notification = $this->findOrFail($notification_id);
        $notification->users()->attach($user_ids, [
        'is_read' => false,
        'created_at' => now(),
        'updated_at' => now()
        ]);
        
        // Log activity for each user
        foreach ($user_ids as $userId) {
            $this->logActivity('SEND', $notification, "Mengirim notifikasi '{$notification->title}' ke user ID {$userId}", null);
        }
    }
}