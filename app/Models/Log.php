<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'log';

    protected $fillable = [
        'user_id',
        'mahasiswa_id',
        'dosen_id',
        'kelas_id',
        'jadwal_id',
        'matakuliah_id',
        'jurusan_id',
        'notification_id',
        'announcement_id',
        'aksi',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

     public function getActionAttribute($value)
    {
        return strtoupper($value);
    }

     public function setActionAttribute($value)
    {
        $this->attributes['action'] = strtoupper($value);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }
    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }
    public function kelas()
    {
        return $this->belongsTo(kelas::class, 'kelas_id');
    }
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }
    public function matakuliah()
    {
        return $this->belongsTo(Matakuliah::class, 'matakuliah_id');
    }
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }
    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }
    public function announcement()
    {
        return $this->belongsTo(Announcement::class, 'announcement_id');
    }
}
