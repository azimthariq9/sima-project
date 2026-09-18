<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->string('tahunMasuk', 30)->nullable()->after('masaAktif');
            $table->boolean('isOnline')->default(false)->after('tahunMasuk');
            $table->string('fotoProfil')->nullable()->after('isOnline');
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropColumn(['tahunMasuk', 'isOnline', 'fotoProfil']);
        });
    }
};
