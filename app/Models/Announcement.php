<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table = 'announcement';

    protected $guarded = [];

    protected $casts = [
        'status' => 'string',
    ];

    public function mahasiswa()
    {
        return $this->belongsToMany(Mahasiswa::class, 'announcement_mahasiswa', 'announcement_id', 'mahasiswa_id');
    }
}
