<?php

namespace App\Traits;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

trait LogsActivityTrait
{
    /*
    |--------------------------------------------------------------------------
    | logActivity
    |
    | Aturan sederhana:
    |  - Hanya baca direct attribute ($model->someColumn) — JANGAN akses relasi
    |  - Relasi belongsToMany mengembalikan Collection, bukan Model → crash
    |  - Kolom FK (kelas_id, jadwal_id, dst) sudah ada langsung di model jika
    |    memang ada relasinya, jadi cukup baca atribut langsung
    |--------------------------------------------------------------------------
    */
    protected function logActivity(string $action, Model $model, string $description, $maker): Log
    {
        $modelName  = strtolower(class_basename($model));
        $foreignKey = $modelName . '_id'; // contoh: kelas → kelas_id

        $logData = [
            'user_id' => $maker->id,
            'aksi'    => $description,
        ];

        // Auto-assign FK model yang sedang di-log (cek dulu kolomnya ada di fillable Log)
        $fillable = (new Log())->getFillable();
        if (in_array($foreignKey, $fillable) && isset($model->id)) {
            $logData[$foreignKey] = $model->id;
        }

        // Assign FK lain yang ada LANGSUNG sebagai kolom di model (bukan relasi)
        // Gunakan getRawOriginal atau getAttribute agar tidak trigger relasi
        $directFkColumns = [
            'mahasiswa_id',
            'dosen_id',
            'kelas_id',
            'jadwal_id',
            'matakuliah_id',
            'jurusan_id',
            'notification_id',
            'announcement_id',
        ];

        foreach ($directFkColumns as $col) {
            // Skip kalau sudah di-set oleh auto-assign di atas
            if (isset($logData[$col])) {
                continue;
            }
            // Cek apakah kolom ini memang ada sebagai attribute (bukan relasi)
            if (in_array($col, $fillable) && array_key_exists($col, $model->getAttributes())) {
                $logData[$col] = $model->getAttribute($col);
            }
        }

        return Log::create($logData);
    }

    /*
    |--------------------------------------------------------------------------
    | logCustomActivity
    | Untuk log aktivitas tanpa model spesifik (contoh: login, logout)
    |--------------------------------------------------------------------------
    */
    protected function logCustomActivity(string $description, $maker): Log
    {
        return Log::create([
            'user_id' => $maker->id,
            'aksi'    => $description,
        ]);
    }
}