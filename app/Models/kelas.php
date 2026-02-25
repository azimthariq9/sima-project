<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class kelas extends Model
{
    protected $table = 'kelas';

    protected $guarded = [];

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'kelas_id');
    }
    public function mahasiswa()
    {
        return $this->belongsToMany(Mahasiswa::class, 'mahasiswa_kelas', 'kelas_id', 'mahasiswa_id')->withTimestamps();
    }
}
