<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivityLog
{
    /**
     * Record an activity to the log table.
     *
     * @param  string       $aksi          Human-readable description of the action
     * @param  string|null  $loggableType  Entity type (e.g. 'jadwal', 'dokumen', 'users')
     * @param  int|null     $loggableId    Entity ID
     * @param  int|null     $userId        Override actor; defaults to Auth::id()
     */
    public static function record(
        string $aksi,
        ?string $loggableType = null,
        ?int $loggableId = null,
        ?int $userId = null
    ): void {
        try {
            DB::table('log')->insert([
                'user_id'       => $userId ?? Auth::id(),
                'loggable_type' => $loggableType,
                'loggable_id'   => $loggableId,
                'aksi'          => $aksi,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        } catch (\Throwable) {
            // Logging must never break the main flow
        }
    }
}
