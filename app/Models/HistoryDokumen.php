<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryDokumen extends Model
{
    protected $table = 'history_dokumen';

    protected $guarded = [];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function dokumen()
    {
        return $this->belongsTo(Dokumen::class, 'dokumen_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
