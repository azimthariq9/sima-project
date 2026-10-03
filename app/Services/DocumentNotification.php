<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentNotification
{
    public function student(int $studentId, string $subject, string $message): void
    {
        $id = $this->create($subject, $message);
        DB::table('notification_mahasiswa')->insert([
            'notification_id' => $id, 'mahasiswa_id' => $studentId,
            'is_read' => false, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function kln(string $subject, string $message): void
    {
        $users = DB::table('users')->whereRaw('LOWER(role) = ?', ['kln'])->pluck('id');
        if ($users->isEmpty()) {
            return;
        }
        $id = $this->create($subject, $message);
        DB::table('notification_users')->insert($users->map(fn ($userId) => [
            'notification_id' => $id, 'user_id' => $userId,
            'is_read' => false, 'created_at' => now(), 'updated_at' => now(),
        ])->all());
    }

    private function create(string $subject, string $message): int
    {
        return DB::table('notification')->insertGetId([
            'subject' => $subject, 'message' => Str::limit($message, 250),
            'type' => 'document', 'sender_id' => Auth::id(),
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}
