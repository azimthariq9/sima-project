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
        Schema::table('fileDetail', function (Blueprint $table) {
            $table->foreignId('reqDokumen_id')->constrained('reqDokumen')->nullable()->onDelete('set null');
            $table->unsignedBigInteger('dokumen_id')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
