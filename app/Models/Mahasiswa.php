<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';

    protected $guarded = [];

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'mahasiswa_kelas', 'mahasiswa_id', 'kelas_id');
    }
}
