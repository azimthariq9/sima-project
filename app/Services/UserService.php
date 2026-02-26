<?php
// app/Services/UserService.php

namespace App\Services;

use App\Models\User;
use App\Traits\LogsActivityTrait;
use App\Services\NotificationService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use app\Enums\Status;
use app\Enums\Role;
use Illuminate\Support\Facades\Log;

class UserService extends BaseService
{
    use LogsActivityTrait;
    
    protected $NotificationService;
    
    public function __construct(
        User $user, 
        NotificationService $notificationService
    ){
        parent::__construct($user);
        $this->NotificationService = $notificationService;
    }
    
    /**
     * Get all users with filters
     */
    public function getAll(array $filters = [], int $perPage = 15):LengthAwarePaginator
    {
         $query = User::with(['mahasiswa', 'dosen']);
        
        if (isset($filters['role'])) {
            // PERBAIKAN: Jika role dikirim sebagai string, konversi ke value
            $roleValue = $filters['role'];
            // Jika role adalah enum object, ambil value-nya
            if ($roleValue instanceof Role) {
                $roleValue = $roleValue->value;
            }
            $query->where('role', $roleValue);
        }
        
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%")
                  ->orWhere('nim_nip', 'like', "%{$filters['search']}%");
            });
        }
        
        if (isset($filters['status'])) {
            $statusValue = $filters['status'];
            if ($statusValue instanceof Status) {
                $statusValue = $statusValue->value;
            }
            $query->where('status', $statusValue);
        }
        
        return $query->latest()->paginate($perPage);
    }
    
    /**
     * Create new user
     */
    public function create($maker, array $data): User
    {
        DB::beginTransaction();
        
        try {

            $userData = [
            'email' => $data['email'],
            'password' => ($data['password']),
            'role' => $data['role'],
            'status' => $data['status'],
            'jurusan_id' => $data['jurusan_id'],
            ];

            if (isset($userData['role'])) {
                if ($userData['role'] instanceof Role) {
                    $userData['role'] = $userData['role']->value;
                }
                // Pastikan role valid
                $validRoles = array_column(Role::cases(), 'value');
                if (!in_array($userData['role'], $validRoles)) {
                    throw new \InvalidArgumentException("Invalid role: {$userData['role']}");
                }
            }

            if (!isset($userData['password']) || empty($userData['password'])) {
                $userData['password'] = 'password123';
            }
            $userData['password'] = Hash::make($userData['password']);


            if (isset($userData['status'])) {
            // Cek apakah nilai yang dikirim valid
            $validStatuses = array_column(Status::cases(), 'value');
            if (!in_array($userData['status'], $validStatuses)) {
                // Jika tidak valid, set default
                $userData['status'] = Status::PENDING->value;
                }
            }
            
            $newUser = parent::create($maker, $userData);


                // Debug: lihat nilai role
            Log::info('Role user setelah create:', [
                'role' => $newUser->role,
                'role_value' => $newUser->role instanceof Role ? $newUser->role->value : $newUser->role,
                // 'role_string' => (string) $newUser->role
            ]);
            
            // Create related record based on role
            $userRole = $newUser->role instanceof Role ? $newUser->role->value : $newUser->role;
            

            //MAHASISWA
            $mahasiswaData = [
                'user_id' => $newUser->id,
                'npm' => $data['mahasiswa']['npm'] ?? null,
                'nama' => $data['mahasiswa']['nama'] ?? null,
            ];

            if ($userRole === Role::MAHASISWA->value) {
                // Cek apakah data mahasiswa ada
                if (!isset($data['mahasiswa']) || !is_array($data['mahasiswa'])) {
                    throw new \InvalidArgumentException("Data mahasiswa harus dikirim untuk role mahasiswa");
                }
                
                
                // Create mahasiswa
                $newUser->mahasiswa()->create($mahasiswaData);
                
                Log::info('Mahasiswa created', ['user_id' => $newUser->id]);
                
            } elseif ($userRole === Role::DOSEN->value) {
                // Cek apakah data dosen ada
                if (!isset($data['dosen']) || !is_array($data['dosen'])) {
                    throw new \InvalidArgumentException("Data dosen harus dikirim untuk role dosen");
                }
                
                //DOSEN
                $dosenData = [
                    'user_id' => $newUser->id,
                    'nama' => $data['dosen']['nama'] ?? null,
                    'nidn' => $data['dosen']['nidn'] ?? null,
                    'kodeDos' => $data['dosen']['kodeDos'] ?? null,
                ];
                
                // Create dosen
                $newUser->dosen()->create($dosenData);
                
                Log::info('Dosen created', ['user_id' => $newUser->id]);
                
            } elseif ($userRole === Role::JURUSAN->value || $userRole === Role::BIPA->value || $userRole === Role::KLN->value) {
                // Admin roles tidak punya relasi tambahan
                Log::info('Admin user created', ['role' => $userRole]);
                
            } else {
                // Jika role tidak dikenal
                DB::rollBack();
                throw new \InvalidArgumentException("Role '{$userRole}' tidak dikenal");
            }
            
            $this->logActivity('CREATE', $newUser, "Membuat user baru: {$userData['email']} dengan role {$userRole},", $maker);
            
            DB::commit();
            return $newUser->load(['mahasiswa', 'dosen']);
        
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating user: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * Update user
     */
    public function update($maker, int $id, array $data): User
    {
        DB::beginTransaction();
        
        try {
            $user = $this->findOrFail($id);
            
            if (isset($data['password']) && $data['password']) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            
            $user->update($data);
            
            // Update related record
            if ($user->role === 'mahasiswa' && isset($data['mahasiswa'])) {
                $user->mahasiswa()->updateOrCreate(
                    ['user_id' => $user->id],
                    $data['mahasiswa']
                );
            } elseif ($user->role === 'dosen' && isset($data['dosen'])) {
                $user->dosen()->updateOrCreate(
                    ['user_id' => $user->id],
                    $data['dosen']
                );
            }
            
            $this->logActivity('UPDATE', $user, "Mengupdate user: {$user->name}", $maker);
            
            DB::commit();
            return $user->fresh(['mahasiswa', 'dosen']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Update profile status
     */
    public function updateProfileStatus($maker, int $id, string $status, int $notification_id): User
    {
        DB::beginTransaction();
        
        try {
            if (isset($status)) {
            // Cek apakah nilai yang dikirim valid
            $validStatuses = array_column(Status::cases(), 'value');
            if (!in_array($status, $validStatuses)) {
                // Jika tidak valid, set default
                $status = Status::PENDING->value;
                }
            }
            $user = $this->findOrFail($id);
            $user->update(['status' => $status]);
            $this->NotificationService->sendToUsers($notification_id, [$user->id]);

            $this->logActivity('UPDATE_STATUS', $user, "Mengupdate status user {$user->name} menjadi {$status}", $maker);
            
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Get mahasiswa list for verification
     */
    public function getMahasiswaForVerification(array $filters = [])
    {
        return User::where('role', 'mahasiswa')
            ->with('mahasiswa')
            ->when(isset($filters['status_profile']), function ($q) use ($filters) {
                $q->where('status_profile', $filters['status_profile']);
            })
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }
}