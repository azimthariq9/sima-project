<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotifUsers extends Model
{
    protected $table = 'notif_users';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }
}
