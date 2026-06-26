<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * JurusanSeeder — seed semua program studi Gunadarma.
 * Idempotent: skip jika namaJurusan sudah ada.
 * Jalankan: php artisan db:seed --class=JurusanSeeder
 */
class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $jurusan = [
            'KLN',
            'BIPA',
            'S3 Ilmu Komunikasi',
            'S3 Ilmu Ekonomi',
            'S3 Ilmu Psikologi',
            'S3 Teknologi Informasi',
            'S2 Teknik Sipil',
            'S2 Teknik Mesin',
            'S2 Teknik Industri dan Manajemen',
            'S2 Teknik Elektro',
            'S2 Sastra Inggris',
            'S2 Psikologi Profesi',
            'S2 Psikologi',
            'S2 Manajemen Sistem Informasi',
            'S2 Manajemen',
            'S2 Ilmu Komunikasi',
            'S2 Arsitektur',
            'S1 Teknik Sipil (Kampus Kabupaten Penajam Paser Utara)',
            'S1 Teknik Sipil',
            'S1 Teknik Mesin',
            'S1 Teknik Informatika',
            'S1 Teknik Industri',
            'S1 Teknik Elektro',
            'S1 Sistem Komputer',
            'S1 Sistem Informasi (Kampus Kabupaten Penajam Paser Utara)',
            'S1 Sistem Informasi',
            'S1 Sastra Tiongkok',
            'S1 Sastra Inggris',
            'S1 Psikologi (Kampus Kabupaten Penajam Paser Utara)',
            'S1 Psikologi',
            'S1 Pariwisata',
            'S1 Manajemen (Kampus Kabupaten Penajam Paser Utara)',
            'S1 Manajemen',
            'S1 Kedokteran',
            'S1 Kebidanan',
            'S1 Informatika (Kampus Kabupaten Penajam Paser Utara)',
            'S1 Informatika',
            'S1 Ilmu Komunikasi (Kampus Kabupaten Penajam Paser Utara)',
            'S1 Ilmu Komunikasi',
            'S1 Farmasi',
            'S1 Ekonomi Syariah',
            'S1 Desain Interior',
            'S1 Arsitektur (Kampus Kabupaten Penajam Paser Utara)',
            'S1 Arsitektur',
            'S1 Akuntansi (Kampus Kabupaten Penajam Paser Utara)',
            'S1 Akuntansi',
            'S1 Agroteknologi',
            'D3 Teknik Komputer',
            'D3 Manajemen Pemasaran',
            'D3 Manajemen Keuangan',
            'D3 Manajemen Informatika',
            'D3 Akuntansi',
            'Pendidikan Profesi Psikologi',
            'Pendidikan Profesi Dokter',
            'Pendidikan Profesi Bidan',
        ];

        $existing = DB::table('jurusan')->pluck('namaJurusan')->all();

        $toInsert = [];
        foreach ($jurusan as $nama) {
            if (!in_array($nama, $existing)) {
                $toInsert[] = ['namaJurusan' => $nama, 'created_at' => $now, 'updated_at' => $now];
            }
        }

        if (!empty($toInsert)) {
            DB::table('jurusan')->insert($toInsert);
            $this->command->info('JurusanSeeder: inserted ' . count($toInsert) . ' jurusan.');
        } else {
            $this->command->info('JurusanSeeder: semua jurusan sudah ada, skip.');
        }
    }
}
