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

    public function NotifUsers()
    {
        return $this->hasMany(NotifUsers::class, 'notification_id');
    }
    public function notifMhs()
    {
        return $this->hasMany(NotifMhs::class, 'notification_id');
    }
    
}
