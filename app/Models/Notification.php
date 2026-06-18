<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\status;
class Notification extends Model
{
    protected $table = 'notification';

    protected $guarded = [];

    protected $casts = [
        'status' => status::class,
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'notification_users', 'notification_id', 'user_id')->withPivot('is_read')->withTimestamps();;
    }
    public function mahasiswa()
    {
        return $this->belongsToMany(Mahasiswa::class, 'notification_mahasiswa', 'notification_id', 'mahasiswa_id')->withPivot('is_read')->withTimestamps();;
    }
    
}
