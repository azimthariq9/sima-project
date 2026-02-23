<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotifMhs extends Model
{
    protected $table = 'notification_mahasiswa';

    protected $guarded = [];

    protected $casts = [
        'is_read' => 'boolean',
    ];
    
    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }
}
