<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\status;
use App\Enums\role;

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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => role::class,
            'status' => status::class,
        ];
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
