<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('judul');
            $table->text('isi')->nullable();
            $table->string('sumber')->default('SIMA'); // KLN, JURUSAN, BIPA, dll
            $table->boolean('is_penting')->default(false);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['judul', 'isi', 'sumber', 'is_penting', 'user_id']);
        });
    }
};