<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';

    protected $guarded = [];

    protected $casts = [
        'tglLahir' => 'date',
    ];

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'mahasiswa_kelas', 'mahasiswa_id', 'kelas_id')->withTimestamps();
    }
    public function jurusan()
    {
        return $this->belongsTo(jurusan::class, 'jurusan_id');
    }
    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class, 'mahasiswa_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function announcement()
    {
        return $this->belongsToMany(Announcement::class, 'announcement_mahasiswa', 'mahasiswa_id', 'announcement_id')->withTimestamps();
    }
    public function dokumen()
    {
        return $this->hasMany(Dokumen::class, 'mahasiswa_id');
    }
    public function log()
    {
        return $this->hasMany(Log::class, 'mahasiswa_id');
    }
    public function notification()
    {
        return $this->belongsToMany(Notification::class, 'notification_mahasiswa', 'mahasiswa_id', 'notification_id')->withPivot('is_read')->withTimestamps();
    }
}
