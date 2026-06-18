<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notification', function (Blueprint $table) {
            $table->string('type')->default('broadcast')->after('status');
            $table->unsignedBigInteger('sender_id')->nullable()->after('type');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('notification', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
            $table->dropColumn(['type', 'sender_id']);
        });
    }
};
