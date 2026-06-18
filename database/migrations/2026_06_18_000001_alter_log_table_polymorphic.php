<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop FK constraints by their actual names in DB
        $constraints = [
            'log_users_id_foreign',
            'log_mahasiswa_id_foreign',
            'log_dosen_id_foreign',
            'log_kelas_id_foreign',
            'log_jadwal_id_foreign',
            'log_matakuliah_id_foreign',
            'log_jurusan_id_foreign',
            'log_notification_id_foreign',
            'log_announcement_id_foreign',
        ];
        foreach ($constraints as $constraint) {
            \Illuminate\Support\Facades\DB::statement(
                "ALTER TABLE \"log\" DROP CONSTRAINT IF EXISTS \"{$constraint}\""
            );
        }

        Schema::table('log', function (Blueprint $table) {
            $table->dropColumn([
                'user_id',
                'mahasiswa_id',
                'dosen_id',
                'kelas_id',
                'jadwal_id',
                'matakuliah_id',
                'jurusan_id',
                'notification_id',
                'announcement_id',
            ]);

            // Who performed the action
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // What entity was acted upon (polymorphic)
            $table->string('loggable_type')->nullable()->after('user_id');
            $table->unsignedBigInteger('loggable_id')->nullable()->after('loggable_type');
        });
    }

    public function down(): void
    {
        Schema::table('log', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'loggable_type', 'loggable_id']);

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->nullable();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade')->nullable();
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade')->nullable();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade')->nullable();
            $table->foreignId('jadwal_id')->constrained('jadwal')->onDelete('cascade')->nullable();
            $table->foreignId('matakuliah_id')->constrained('matakuliah')->onDelete('cascade')->nullable();
            $table->foreignId('jurusan_id')->constrained('jurusan')->onDelete('cascade')->nullable();
            $table->foreignId('notification_id')->constrained('notification')->onDelete('cascade')->nullable();
            $table->foreignId('announcement_id')->constrained('announcement')->onDelete('cascade')->nullable();
        });
    }
};
