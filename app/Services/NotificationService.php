<?php
// app/Services/NotificationService.php

namespace App\Services;

use App\Models\Notification;
use App\Traits\LogsActivityTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        /**
     * Kirim notifikasi ke multiple users (hanya attach, tidak create baru)
     */
    public function sendToUsers(int $notification_id, array $user_ids): void
    {
        if (empty($user_ids)) {
            return; // Tidak ada user tujuan
        }
        
        DB::beginTransaction();
        
        try {
            $notification = $this->findOrFail($notification_id);
            
            // Attach users ke notification
            $notification->users()->attach($user_ids, [
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // // Log activity
            // $this->logActivity(
            //     'SEND_NOTIFICATION', 
            //     $notification, 
            //     "Mengirim notifikasi '{$notification->title}' ke " . count($user_ids) . " users",
            //     auth()->user()
            // );
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error sending notification: ' . $e->getMessage());
            throw $e;
        }
    }
        /**
     * Kirim notifikasi ke single user
     */
    public function sendToUser(int $notification_id, int $user_id): void
    {
        $this->sendToUsers($notification_id, [$user_id]);
    }

      /**
     * Kirim notifikasi ke multiple mahasiswa(hanya attach, tidak create baru)
     */
    public function sendToMahasiswa(int $notification_id, array $mahasiswa_ids): void
    {
        if (empty($mahasiswa_ids)) {
            return; // Tidak ada user tujuan
        }
        
        DB::beginTransaction();
        
        try {
            $notification = $this->findOrFail($notification_id);
            
            // Attach users ke notification
            $notification->mahasiswa()->attach($mahasiswa_ids, [
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // // Log activity
            // $this->logActivity(
            //     'SEND_NOTIFICATION', 
            //     $notification, 
            //     "Mengirim notifikasi '{$notification->title}' ke " . count($user_ids) . " users",
            //     auth()->user()
            // );
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error sending notification: ' . $e->getMessage());
            throw $e;
        }
    }
}