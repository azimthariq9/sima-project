<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_imigrasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained()->cascadeOnDelete();

            $table->string('passport_number')->nullable();
            $table->date('passport_expired')->nullable();
            $table->string('visa')->nullable();
            $table->date('visa_expired')->nullable();
            $table->string('file')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_imigrasi');
    }
};