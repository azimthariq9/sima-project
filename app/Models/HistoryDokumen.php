<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoryDokumen extends Model
{
    use SoftDeletes; // Opsional, kalau mau soft delete juga
    protected $table = 'historyDokumen';

    protected $guarded = [];
    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

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
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope untuk filter action
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }
    
    /**
     * Scope untuk dokumen tertentu
     */
    public function scopeForDokumen($query, $dokumenId)
    {
        return $query->where('dokumen_id', $dokumenId);
    }
}
