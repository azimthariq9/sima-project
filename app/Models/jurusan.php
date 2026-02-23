<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class jurusan extends Model
{
    protected $table = 'jurusan';

    protected $guarded = [];
    public function dosen()
    {
        return $this->hasMany(dosen::class, 'jurusan_id');
    }
    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class, 'jurusan_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
