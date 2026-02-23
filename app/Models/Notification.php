<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notification';

    protected $guarded = [];

    protected $cast =[
        'status' => 'string',
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
