<?php

namespace App\Traits;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

trait LogsActivityTrait
{
    protected function logActivity($action, $model, $description, $maker, $user)
    {
        $user = $user;
        $maker = $maker;
        $description1 = $this->generateDescription($action, $model);
        
        $logData = [
            'user_id' => $maker->id,
            'mahasiswa_id' => $model->mahasiswa->id ?? null,
            'dosen_id' => $model->dosen->id ?? null,
            'kelas_id' => $model->kelas_id ?? null,
            'jadwal_id' => $model->jadwal_id ?? null,
            'matakuliah_id' => $model->matakuliah_id ?? null,
            'jurusan_id' => $model->jurusan_id ?? null,
            'notification_id' => $model->notification_id ?? null,
            'announcement_id' => $model->announcement_id ?? null,
            'aksi' => $description,
        ];
        return Log::create($logData);
    }

    protected function logCustomActivity($action, $description)
    {
        $user = Auth::user();
        $logData = [
            'user_id' => $user->id,
            'mahasiswa_id' => $user->mahasiswa->id ?? null,
            'dosen_id' => $user->dosen->id ?? null,
            'kelas_id' => $model->kelas_id ?? null,
            'jadwal_id' => $model->jadwal_id ?? null,
            'matakuliah_id' => $model->matakuliah_id ?? null,
            'jurusan_id' => $model->jurusan_id ?? null,
            'notification_id' => $model->notification_id ?? null,
            'announcement_id' => $model->announcement_id ?? null,
            'aksi' => $description,
        ];
        return Log::create($logData);
    }

    private function generateDescription($action, $model)
    {
        $userName = Auth::user()->name;
        $modelName = class_basename($model);
        
        switch ($action) {
            case 'CREATE':
                return "Admin {$userName} membuat {$modelName} baru dengan ID {$model->id}";
            case 'UPDATE':
                return "Admin {$userName} mengupdate {$modelName} dengan ID {$model->id}";
            case 'DELETE':
                return "Admin {$userName} menghapus {$modelName} dengan ID {$model->id}";
            case 'UPDATE_STATUS':
                return "Admin {$userName} mengupdate status {$modelName} dengan ID {$model->id}";
            default:
                return "Admin {$userName} melakukan {$action} pada {$modelName} ID {$model->id}";
        }
    }
}
