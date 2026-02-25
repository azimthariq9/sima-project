<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\Status;
use App\Enums\Role;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => Role::class,
        'status' => Status::class,
        ];
    
    // Accessor untuk mendapatkan role sebagai string
    public function getRoleStringAttribute(): string
    {
        return $this->role instanceof Role ? $this->role->value : $this->role;
    }
    
    // Accessor untuk mendapatkan status_profile sebagai string
    public function getStatusProfileStringAttribute(): string
    {
        return $this->status_profile instanceof Status 
            ? $this->status_profile->value 
            : $this->status_profile;
    }
    
    // Mutator untuk role - terima string atau enum
    public function setRoleAttribute($value)
    {
        if ($value instanceof Role) {
            $this->attributes['role'] = $value->value;
        } else {
            $this->attributes['role'] = $value;
        }
    }
    
    // Mutator untuk status_profile
    public function setStatusProfileAttribute($value)
    {
        if ($value instanceof Status) {
            $this->attributes['status_profile'] = $value->value;
        } else {
            $this->attributes['status_profile'] = $value;
        }
    }
 
    public function dosen()
    {
        return $this->hasOne(dosen::class, 'user_id');
    }
    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class, 'user_id');
    }
    public function announcement()
    {
        return $this->hasMany(Announcement::class, 'user_id');
    }
    public function historyDokumen()
    {
        return $this->hasMany(HistoryDokumen::class, 'user_id');
    }
    public function notification()
    {
        return $this->belongsToMany(Notification::class, 'notification_users', 'user_id', 'notification_id')->withPivot('is_read')->withTimestamps();
    }
    public function jurusan()
    {
        return $this->belongsTo(jurusan::class, 'jurusan_id');
    }
}
