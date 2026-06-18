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
        Schema::table('announcement', function (Blueprint $table) {
            $table->string('sumber')->default('kln')->after('status');
            $table->boolean('is_penting')->default(false)->after('sumber');
        });
    }

    public function down(): void
    {
        Schema::table('announcement', function (Blueprint $table) {
            $table->dropColumn(['sumber', 'is_penting']);
        });
    }
};
