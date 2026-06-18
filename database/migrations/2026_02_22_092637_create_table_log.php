<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('users_id')->constrained('users')->onDelete('cascade')->nullable();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade')->nullable();
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade')->nullable();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade')->nullable();
            $table->foreignId('jadwal_id')->constrained('jadwal')->onDelete('cascade')->nullable();
            $table->foreignId('matakuliah_id')->constrained('matakuliah')->onDelete('cascade')->nullable();
            $table->foreignId('jurusan_id')->constrained('jurusan')->onDelete('cascade')->nullable();
            $table->foreignId('notification_id')->constrained('notification')->onDelete('cascade')->nullable();
            $table->foreignId('announcement_id')->constrained('announcement')->onDelete('cascade')->nullable();
            $table->string('aksi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log');
    }
};
