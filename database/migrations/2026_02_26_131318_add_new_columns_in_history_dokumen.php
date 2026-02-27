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
        Schema::table('historyDokumen', function (Blueprint $table) {
            $table->string('action'); // UPLOAD, UPDATE, VERIFY, REJECT, REVISION
            $table->string('status_from')->nullable(); // status sebelum perubahan
            $table->string('status_to')->nullable(); // status setelah perubahan
            $table->json('metadata')->nullable(); // data tambahan (file info, dll)
            $table->index(['dokumen_id', 'created_at']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historyDokumen', function (Blueprint $table) {
            //
        });
    }
};
