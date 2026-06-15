<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration ini sengaja dikosongkan.
// Tabel 'announcements' (plural) dihapus — sistem pengumuman
// menggunakan tabel 'announcement' (singular) yang sudah ada
// beserta model, service, dan controller-nya.
return new class extends Migration
{
    public function up(): void {}

    public function down(): void {}
};
