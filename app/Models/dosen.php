<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class dosen extends Model
{
    protected $table = 'dosen';

    protected $guarded = [];

    // public function jurusan()
    // {
    //     return $this->belongsTo(jurusan::class, 'jurusan_id');
    // }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'dosen_id');
    }
}
