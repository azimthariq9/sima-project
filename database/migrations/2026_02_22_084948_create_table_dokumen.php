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
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained()->onDelete('cascade');
            $table->string('tipeDkmn');
            $table->string('namaDkmn');
            $table->string('penerbit');
            $table->string('noDkmn');
            $table->date('tglterbit');
            $table->date('tglKdlwrs');
            $table->string('status')->default('sedang diproses');
            $table->string('path');
            $table->string('mimeType');
            $table->integer('fileSize');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};
