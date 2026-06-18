<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * SimaSeeder — data demo untuk tampilan.
 * Jalankan: php artisan db:seed --class=SimaSeeder
 * Idempotent: aman dijalankan berulang kali (cek by email/npm/subject).
 */
class SimaSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // helper: firstOrCreate via DB::table, return id
        $oc = fn(string $t, array $w, array $e = []) =>
            DB::table($t)->where($w)->value('id')
            ?? DB::table($t)->insertGetId(array_merge($w, $e, ['created_at' => $now, 'updated_at' => $now]));

        // unique string pendek
        $u = fn(string $prefix = '') => $prefix . strtoupper(substr(uniqid(), -6));

        // ══════════════════════════════════════════════════════════════
        // JURUSAN — ambil yang sudah ada
        // ══════════════════════════════════════════════════════════════
        $jurusanAll = DB::table('jurusan')->orderBy('id')->get();
        if ($jurusanAll->isEmpty()) {
            $this->command->error('Tabel jurusan kosong.');
            return;
        }
        $klnJurId  = optional($jurusanAll->first(fn($j) => stripos($j->namaJurusan, 'KLN') !== false
                                                         || stripos($j->namaJurusan, 'Internasional') !== false))->id
                     ?? $jurusanAll->first()->id;
        $bipaJurId = optional($jurusanAll->first(fn($j) => stripos($j->namaJurusan, 'BIPA') !== false
                                                         || stripos($j->namaJurusan, 'Indonesia') !== false))->id
                     ?? $jurusanAll->skip(1)->first()->id ?? $klnJurId;
        $others    = $jurusanAll->whereNotIn('id', [$klnJurId, $bipaJurId])->values();
        $tiJurId   = optional($others->get(0))->id ?? $klnJurId;
        $siJurId   = optional($others->get(1))->id ?? $bipaJurId;

        $this->command->info("Jurusan — KLN:{$klnJurId} BIPA:{$bipaJurId} TI:{$tiJurId} SI:{$siJurId}");

        // ══════════════════════════════════════════════════════════════
        // USER KLN (sender notifikasi & pengumuman)
        // ══════════════════════════════════════════════════════════════
        $klnUserId = $oc('users', ['email' => 'kln.demo@seed.test'], [
            'role' => 'kln', 'password' => Hash::make('password'),
            'status' => 'active', 'profile_completed' => true,
        ]);

        // ══════════════════════════════════════════════════════════════
        // DOSEN (10 dosen)
        // ══════════════════════════════════════════════════════════════
        $dosenDef = [
            ['email' => 'dos.bipa1@seed.test', 'nama' => 'Dr. Ahmad Fauzi M.Pd.',      'jur' => $bipaJurId],
            ['email' => 'dos.bipa2@seed.test', 'nama' => 'Siti Rahayu S.Pd.',          'jur' => $bipaJurId],
            ['email' => 'dos.bipa3@seed.test', 'nama' => 'Dr. Hendra Wijaya M.Hum.',   'jur' => $bipaJurId],
            ['email' => 'dos.ti1@seed.test',   'nama' => 'Prof. Budi Santoso Ph.D.',   'jur' => $tiJurId],
            ['email' => 'dos.ti2@seed.test',   'nama' => 'Ir. Dewi Kusuma M.T.',       'jur' => $tiJurId],
            ['email' => 'dos.ti3@seed.test',   'nama' => 'Rudi Hartono S.Kom. M.T.',   'jur' => $tiJurId],
            ['email' => 'dos.si1@seed.test',   'nama' => 'Dr. Lestari Putri M.M.',     'jur' => $siJurId],
            ['email' => 'dos.si2@seed.test',   'nama' => 'Agus Prasetyo S.T. M.Sc.',   'jur' => $siJurId],
            ['email' => 'dos.kln1@seed.test',  'nama' => 'Mr. James Anderson',         'jur' => $klnJurId],
            ['email' => 'dos.kln2@seed.test',  'nama' => 'Ms. Emily Chen Ph.D.',       'jur' => $klnJurId],
        ];

        $dosenIds = [];
        foreach ($dosenDef as $d) {
            $uid = $oc('users', ['email' => $d['email']], [
                'role' => 'dosen', 'password' => Hash::make('password'),
                'status' => 'active', 'profile_completed' => true,
                'jurusan_id' => $d['jur'],
            ]);
            // cek apakah sudah ada dosen untuk user ini
            $did = DB::table('dosen')->where('user_id', $uid)->value('id')
                ?? DB::table('dosen')->insertGetId([
                    'nidn'       => $u('N'),
                    'nama'       => $d['nama'],
                    'kodeDos'    => $u('D'),
                    'user_id'    => $uid,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            $dosenIds[] = $did;
        }
        [$dosBipa1, $dosBipa2, $dosBipa3, $dosTi1, $dosTi2, $dosTi3, $dosSi1, $dosSi2, $dosKln1, $dosKln2] = $dosenIds;

        // ══════════════════════════════════════════════════════════════
        // MATAKULIAH
        // ══════════════════════════════════════════════════════════════
        $mkBipa1 = $oc('matakuliah', ['kodeMk' => 'BIPA-101'], ['namaMk' => 'Bahasa Indonesia Dasar',       'sks' => 3, 'keterangan' => 'Wajib',   'jurusan_id' => $bipaJurId]);
        $mkBipa2 = $oc('matakuliah', ['kodeMk' => 'BIPA-201'], ['namaMk' => 'Bahasa Indonesia Menengah',    'sks' => 3, 'keterangan' => 'Wajib',   'jurusan_id' => $bipaJurId]);
        $mkBipa3 = $oc('matakuliah', ['kodeMk' => 'BIPA-301'], ['namaMk' => 'Bahasa Indonesia Lanjut',      'sks' => 3, 'keterangan' => 'Pilihan', 'jurusan_id' => $bipaJurId]);
        $mkTi1   = $oc('matakuliah', ['kodeMk' => 'LEC-BD01'], ['namaMk' => 'Basis Data',                  'sks' => 3, 'keterangan' => 'Wajib',   'jurusan_id' => $tiJurId]);
        $mkTi2   = $oc('matakuliah', ['kodeMk' => 'LEC-PW01'], ['namaMk' => 'Pemrograman Web',             'sks' => 3, 'keterangan' => 'Wajib',   'jurusan_id' => $tiJurId]);
        $mkTi3   = $oc('matakuliah', ['kodeMk' => 'LEC-RP01'], ['namaMk' => 'Rekayasa Perangkat Lunak',    'sks' => 3, 'keterangan' => 'Wajib',   'jurusan_id' => $tiJurId]);
        $mkSi1   = $oc('matakuliah', ['kodeMk' => 'LEC-AS01'], ['namaMk' => 'Analisis Sistem Informasi',   'sks' => 3, 'keterangan' => 'Wajib',   'jurusan_id' => $siJurId]);
        $mkKln1  = $oc('matakuliah', ['kodeMk' => 'KLN-101'],  ['namaMk' => 'International Communication', 'sks' => 2, 'keterangan' => 'Wajib',   'jurusan_id' => $klnJurId]);
        $mkKln2  = $oc('matakuliah', ['kodeMk' => 'KLN-201'],  ['namaMk' => 'Cross-Cultural Studies',      'sks' => 3, 'keterangan' => 'Wajib',   'jurusan_id' => $klnJurId]);

        // ══════════════════════════════════════════════════════════════
        // KELAS
        // ══════════════════════════════════════════════════════════════
        $kelBipaA = $oc('kelas', ['kodeKelas' => 'BIPA-2025A'], ['tahunAjar' => '2025/2026']);
        $kelBipaB = $oc('kelas', ['kodeKelas' => 'BIPA-2025B'], ['tahunAjar' => '2025/2026']);
        $kelTiA   = $oc('kelas', ['kodeKelas' => 'LEC-2025A'],  ['tahunAjar' => '2025/2026']);
        $kelSiA   = $oc('kelas', ['kodeKelas' => 'LEC-2025B'],  ['tahunAjar' => '2025/2026']);
        $kelKlnA  = $oc('kelas', ['kodeKelas' => 'KLN-2025A'],  ['tahunAjar' => '2025/2026']);

        // ══════════════════════════════════════════════════════════════
        // JADWAL
        // ══════════════════════════════════════════════════════════════
        $jadwalDef = [
            [$kelBipaA, $mkBipa1, $dosBipa1, 'Senin',  '08:00', 'R.BIPA-01', 16],
            [$kelBipaA, $mkBipa2, $dosBipa1, 'Rabu',   '10:00', 'R.BIPA-01', 16],
            [$kelBipaA, $mkBipa3, $dosBipa2, 'Jumat',  '09:00', 'R.BIPA-01', 14],
            [$kelBipaB, $mkBipa1, $dosBipa2, 'Selasa', '08:00', 'R.BIPA-02', 16],
            [$kelBipaB, $mkBipa2, $dosBipa3, 'Kamis',  '10:00', 'R.BIPA-02', 16],
            [$kelTiA,   $mkTi1,  $dosTi1,   'Senin',  '07:30', 'R.LEC-201', 16],
            [$kelTiA,   $mkTi2,  $dosTi2,   'Kamis',  '09:00', 'R.LEC-202', 16],
            [$kelTiA,   $mkTi3,  $dosTi3,   'Jumat',  '13:00', 'R.LEC-301', 14],
            [$kelSiA,   $mkSi1,  $dosSi1,   'Selasa', '10:00', 'R.LEC-101', 16],
            [$kelSiA,   $mkTi2,  $dosSi2,   'Rabu',   '13:00', 'R.LEC-102', 16],
            [$kelKlnA,  $mkKln1, $dosKln1,  'Rabu',   '08:00', 'R.KLN-01',  12],
            [$kelKlnA,  $mkKln2, $dosKln2,  'Jumat',  '13:00', 'R.KLN-01',  14],
        ];

        $jadwalIds = [];
        foreach ($jadwalDef as [$kId, $mkId, $dId, $hari, $jam, $ruangan, $sesi]) {
            $jadwalIds[] = DB::table('jadwal')
                ->where(['kelas_id' => $kId, 'matakuliah_id' => $mkId])
                ->value('id')
                ?? DB::table('jadwal')->insertGetId([
                    'kelas_id' => $kId, 'matakuliah_id' => $mkId, 'dosen_id' => $dId,
                    'hari' => $hari, 'jam' => $jam, 'ruangan' => $ruangan,
                    'totalSesi' => $sesi, 'tahunAjar' => '2025/2026',
                    'created_at' => $now, 'updated_at' => $now,
                ]);
        }

        // ══════════════════════════════════════════════════════════════
        // MAHASISWA (12 mhs, berbagai negara)
        // ══════════════════════════════════════════════════════════════
        $mhsDef = [
            ['email' => 'mhs.bipa1@seed.test', 'npm' => 'BIPA-001', 'nama' => 'Takeshi Yamamoto', 'neg' => 'Jepang',    'jur' => $bipaJurId],
            ['email' => 'mhs.bipa2@seed.test', 'npm' => 'BIPA-002', 'nama' => 'Liu Wei',          'neg' => 'Tiongkok',  'jur' => $bipaJurId],
            ['email' => 'mhs.bipa3@seed.test', 'npm' => 'BIPA-003', 'nama' => 'Kim Min-jun',      'neg' => 'Korea',     'jur' => $bipaJurId],
            ['email' => 'mhs.bipa4@seed.test', 'npm' => 'BIPA-004', 'nama' => 'Amara Diallo',     'neg' => 'Senegal',   'jur' => $bipaJurId],
            ['email' => 'mhs.ti1@seed.test',   'npm' => 'LEC-001',  'nama' => 'Carlos Mendoza',   'neg' => 'Meksiko',   'jur' => $tiJurId],
            ['email' => 'mhs.ti2@seed.test',   'npm' => 'LEC-002',  'nama' => 'Fatima Al-Rashid', 'neg' => 'Arab Saudi','jur' => $tiJurId],
            ['email' => 'mhs.ti3@seed.test',   'npm' => 'LEC-003',  'nama' => 'Hans Müller',      'neg' => 'Jerman',    'jur' => $tiJurId],
            ['email' => 'mhs.si1@seed.test',   'npm' => 'LEC-004',  'nama' => 'Nguyen Thi Lan',   'neg' => 'Vietnam',   'jur' => $siJurId],
            ['email' => 'mhs.si2@seed.test',   'npm' => 'LEC-005',  'nama' => 'Ahmed Hassan',     'neg' => 'Mesir',     'jur' => $siJurId],
            ['email' => 'mhs.kln1@seed.test',  'npm' => 'KLN-001',  'nama' => 'Priya Sharma',     'neg' => 'India',     'jur' => $klnJurId],
            ['email' => 'mhs.kln2@seed.test',  'npm' => 'KLN-002',  'nama' => 'Ivan Petrov',      'neg' => 'Rusia',     'jur' => $klnJurId],
            ['email' => 'mhs.kln3@seed.test',  'npm' => 'KLN-003',  'nama' => 'Sophie Dubois',    'neg' => 'Prancis',   'jur' => $klnJurId],
        ];

        // build map by npm
        $mhs = [];
        foreach ($mhsDef as $m) {
            $uid = $oc('users', ['email' => $m['email']], [
                'role' => 'mahasiswa', 'password' => Hash::make('password'),
                'status' => 'active', 'profile_completed' => true,
                'jurusan_id' => $m['jur'],
            ]);
            $mid = DB::table('mahasiswa')->where('user_id', $uid)->value('id')
                ?? DB::table('mahasiswa')->insertGetId([
                    'npm'        => $m['npm'],
                    'nama'       => $m['nama'],
                    'noWa'       => $u('+62'),
                    'tglLahir'   => '2000-06-15',
                    'warNeg'     => $m['neg'],
                    'alamatAsal' => 'Luar Negeri',
                    'alamatIndo' => 'Jakarta Pusat',
                    'user_id'    => $uid,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            $mhs[$m['npm']] = ['id' => $mid, 'uid' => $uid, 'npm' => $m['npm']];
        }

        // ── MAHASISWA_KELAS ───────────────────────────────────────────
        $kelasMhs = [
            [$kelBipaA, 'BIPA-001'], [$kelBipaA, 'BIPA-002'], [$kelBipaA, 'BIPA-003'],
            [$kelBipaB, 'BIPA-002'], [$kelBipaB, 'BIPA-004'], [$kelBipaB, 'BIPA-003'],
            [$kelTiA,   'LEC-001'],  [$kelTiA,   'LEC-002'],  [$kelTiA,   'LEC-003'],
            [$kelSiA,   'LEC-004'],  [$kelSiA,   'LEC-005'],  [$kelSiA,   'LEC-001'],
            [$kelKlnA,  'KLN-001'],  [$kelKlnA,  'KLN-002'],  [$kelKlnA,  'KLN-003'],
            [$kelKlnA,  'BIPA-001'], [$kelKlnA,  'BIPA-002'],
        ];
        foreach ($kelasMhs as [$kId, $npm]) {
            if (!isset($mhs[$npm])) continue;
            DB::table('mahasiswa_kelas')->updateOrInsert(
                ['kelas_id' => $kId, 'mahasiswa_id' => $mhs[$npm]['id']],
                ['created_at' => $now, 'updated_at' => $now]
            );
        }

        // ── JADWAL_MAHASISWA (kehadiran sample) ───────────────────────
        $tglSesi  = ['2026-02-02','2026-02-09','2026-02-16','2026-02-23','2026-03-02','2026-03-09','2026-03-16','2026-03-23'];
        $patterns = [
            ['present','present','present','present','present','absent', 'present','present'],
            ['present','present','absent', 'present','excused','present','present','present'],
            ['present','absent', 'present','excused','present','present','absent', 'present'],
        ];
        $kehadiranDef = [
            $jadwalIds[0]  => ['BIPA-001','BIPA-002','BIPA-003'],
            $jadwalIds[5]  => ['LEC-001', 'LEC-002', 'LEC-003'],
            $jadwalIds[10] => ['KLN-001', 'KLN-002', 'KLN-003'],
        ];
        foreach ($kehadiranDef as $jadwalId => $npms) {
            foreach ($npms as $i => $npm) {
                if (!isset($mhs[$npm])) continue;
                foreach ($patterns[$i % count($patterns)] as $si => $status) {
                    DB::table('jadwal_mahasiswa')->updateOrInsert(
                        ['jadwal_id' => $jadwalId, 'mahasiswa_id' => $mhs[$npm]['id'], 'sesi' => $si + 1],
                        ['status' => $status, 'tglSesi' => $tglSesi[$si], 'created_at' => $now, 'updated_at' => $now]
                    );
                }
            }
        }

        // ══════════════════════════════════════════════════════════════
        // DOKUMEN (expired, expiring, valid, diproses)
        // ══════════════════════════════════════════════════════════════
        // TipeDok valid: KITAS, KITAP, Paspor, KTP, Polis_Asuransi, Foto_Profil,
        //                Surat_Keterangan, Surat_Izin, Surat_Tugas, Surat_Undangan, Surat_Pernyataan
        // Penerbit valid: KLN, IMIGRASI, KEPENDUDUKAN
        // Status valid  : approved, pending, rejected, active, inactive
        $dokDef = [
            ['npm' => 'BIPA-001', 'tipe' => 'KITAS',             'nama' => 'KITAS Mahasiswa',     'penerbit' => 'IMIGRASI', 'terbit' => '2023-01-01', 'kdlwrs' => '2024-12-31', 'status' => 'approved'],
            ['npm' => 'LEC-001',  'tipe' => 'KITAS',             'nama' => 'KITAS Mahasiswa',     'penerbit' => 'IMIGRASI', 'terbit' => '2023-06-01', 'kdlwrs' => '2025-01-15', 'status' => 'approved'],
            ['npm' => 'KLN-002',  'tipe' => 'Paspor',            'nama' => 'Paspor',              'penerbit' => 'KLN',      'terbit' => '2020-03-10', 'kdlwrs' => '2025-03-10', 'status' => 'approved'],
            ['npm' => 'BIPA-002', 'tipe' => 'KITAS',             'nama' => 'KITAS Mahasiswa',     'penerbit' => 'IMIGRASI', 'terbit' => '2024-07-01', 'kdlwrs' => date('Y-m-d', strtotime('+10 days')), 'status' => 'approved'],
            ['npm' => 'LEC-002',  'tipe' => 'KITAP',             'nama' => 'KITAP Mahasiswa',     'penerbit' => 'IMIGRASI', 'terbit' => '2024-09-01', 'kdlwrs' => date('Y-m-d', strtotime('+20 days')), 'status' => 'approved'],
            ['npm' => 'KLN-001',  'tipe' => 'Paspor',            'nama' => 'Paspor',              'penerbit' => 'KLN',      'terbit' => '2021-05-10', 'kdlwrs' => date('Y-m-d', strtotime('+25 days')), 'status' => 'approved'],
            ['npm' => 'BIPA-003', 'tipe' => 'KITAS',             'nama' => 'KITAS Mahasiswa',     'penerbit' => 'IMIGRASI', 'terbit' => '2025-01-15', 'kdlwrs' => '2026-12-31', 'status' => 'approved'],
            ['npm' => 'BIPA-004', 'tipe' => 'KITAS',             'nama' => 'KITAS Mahasiswa',     'penerbit' => 'IMIGRASI', 'terbit' => '2025-03-01', 'kdlwrs' => '2027-03-01', 'status' => 'approved'],
            ['npm' => 'LEC-003',  'tipe' => 'KITAS',             'nama' => 'KITAS Mahasiswa',     'penerbit' => 'IMIGRASI', 'terbit' => '2025-02-01', 'kdlwrs' => '2027-02-01', 'status' => 'approved'],
            ['npm' => 'LEC-004',  'tipe' => 'Paspor',            'nama' => 'Paspor',              'penerbit' => 'KLN',      'terbit' => '2025-04-01', 'kdlwrs' => '2026-10-01', 'status' => 'approved'],
            ['npm' => 'LEC-005',  'tipe' => 'Paspor',            'nama' => 'Paspor',              'penerbit' => 'KLN',      'terbit' => '2022-08-15', 'kdlwrs' => '2032-08-15', 'status' => 'approved'],
            ['npm' => 'KLN-003',  'tipe' => 'KITAS',             'nama' => 'KITAS Mahasiswa',     'penerbit' => 'IMIGRASI', 'terbit' => '2024-11-01', 'kdlwrs' => '2026-11-01', 'status' => 'approved'],
            ['npm' => 'BIPA-001', 'tipe' => 'Polis_Asuransi',   'nama' => 'Asuransi Kesehatan',  'penerbit' => 'KLN',      'terbit' => '2026-01-01', 'kdlwrs' => '2028-01-01', 'status' => 'pending'],
            ['npm' => 'KLN-002',  'tipe' => 'Surat_Keterangan', 'nama' => 'Surat Keterangan',    'penerbit' => 'KLN',      'terbit' => '2026-03-01', 'kdlwrs' => '2028-03-01', 'status' => 'pending'],
        ];
        foreach ($dokDef as $d) {
            if (!isset($mhs[$d['npm']])) continue;
            $mId = $mhs[$d['npm']]['id'];
            // cek by mahasiswa_id + tipe + nama agar idempoten
            DB::table('dokumen')->updateOrInsert(
                ['mahasiswa_id' => $mId, 'tipeDkmn' => $d['tipe'], 'namaDkmn' => $d['nama']],
                [
                    'penerbit'   => $d['penerbit'],
                    'noDkmn'     => $u('DOK'),
                    'tglTerbit'  => $d['terbit'],
                    'tglKdlwrs'  => $d['kdlwrs'],
                    'status'     => $d['status'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        // ══════════════════════════════════════════════════════════════
        // REQUEST DOKUMEN
        // ══════════════════════════════════════════════════════════════
        $reqDef = [
            ['npm' => 'BIPA-001', 'tipe' => 'KITAS',             'nama' => 'Perpanjangan KITAS',          'status' => 'pending',  'msg' => 'KITAS saya akan expired bulan depan, mohon segera diproses'],
            ['npm' => 'LEC-001',  'tipe' => 'KITAS',             'nama' => 'Pembuatan KITAS Baru',        'status' => 'pending',  'msg' => 'Membutuhkan KITAS untuk keperluan penelitian'],
            ['npm' => 'KLN-001',  'tipe' => 'Surat_Keterangan',  'nama' => 'Surat Keterangan Aktif',     'status' => 'pending',  'msg' => 'Diperlukan untuk keperluan bank'],
            ['npm' => 'BIPA-003', 'tipe' => 'Surat_Keterangan',  'nama' => 'Surat Keterangan Beasiswa',  'status' => 'pending',  'msg' => 'Dibutuhkan untuk pengajuan beasiswa'],
            ['npm' => 'LEC-004',  'tipe' => 'KITAP',             'nama' => 'Perpanjangan KITAP',         'status' => 'pending',  'msg' => 'Studi diperpanjang 1 semester'],
            ['npm' => 'KLN-003',  'tipe' => 'KITAS',             'nama' => 'KITAS Mahasiswa Baru',       'status' => 'pending',  'msg' => 'Baru tiba di Indonesia, perlu KITAS'],
            ['npm' => 'LEC-003',  'tipe' => 'KITAS',             'nama' => 'Perpanjangan KITAS',         'status' => 'approved', 'msg' => 'Mohon diproses sebelum akhir bulan'],
            ['npm' => 'KLN-002',  'tipe' => 'KITAS',             'nama' => 'Pembaruan KITAS',            'status' => 'approved', 'msg' => 'KITAS lama habis di bulan depan'],
            ['npm' => 'BIPA-002', 'tipe' => 'Surat_Pernyataan',  'nama' => 'Surat Pernyataan Domisili',  'status' => 'rejected', 'msg' => 'Perlu surat domisili untuk perubahan alamat'],
            ['npm' => 'LEC-002',  'tipe' => 'Paspor',            'nama' => 'Perpanjangan Paspor',        'status' => 'rejected', 'msg' => 'Paspor akan habis masa berlakunya'],
        ];
        foreach ($reqDef as $r) {
            if (!isset($mhs[$r['npm']])) continue;
            DB::table('reqDokumen')->updateOrInsert(
                ['mahasiswa_id' => $mhs[$r['npm']]['id'], 'tipeDkmn' => $r['tipe'], 'namaDkmn' => $r['nama']],
                [
                    'user_id'    => $mhs[$r['npm']]['uid'],
                    'status'     => $r['status'],
                    'message'    => $r['msg'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        // ══════════════════════════════════════════════════════════════
        // ANNOUNCEMENT (12 pengumuman KLN)
        // ══════════════════════════════════════════════════════════════
        $annDef = [
            ['subject' => 'Orientasi Mahasiswa Internasional 2025/2026',                 'status' => 'active',   'penting' => true,  'msg' => 'Seluruh mahasiswa internasional diwajibkan hadir pada kegiatan orientasi tanggal 5 Juli 2026 pukul 08.00 WIB di Aula Utama Kampus. Harap membawa dokumen identitas dan kartu mahasiswa.'],
            ['subject' => 'Jadwal Validasi Dokumen Keimigrasian Semester Genap',         'status' => 'active',   'penting' => false, 'msg' => 'KLN membuka jadwal validasi dokumen keimigrasian setiap Senin dan Rabu pukul 09.00–12.00 di Gedung KLN Lantai 2. Harap membawa dokumen asli dan fotokopinya.'],
            ['subject' => 'Pengumuman Libur Idul Fitri 1447 H',                          'status' => 'active',   'penting' => false, 'msg' => 'Kampus libur dari tanggal 28 Maret hingga 6 April 2026. Perkuliahan dimulai kembali pada tanggal 7 April 2026.'],
            ['subject' => 'Perpanjangan KITAS Mahasiswa Batch Mei 2026',                 'status' => 'active',   'penting' => true,  'msg' => 'Mahasiswa yang KITAS-nya berakhir Juni–Agustus 2026 segera ajukan perpanjangan ke KLN paling lambat 30 April 2026. Terlambat dapat mengakibatkan overstay.'],
            ['subject' => 'Program Beasiswa KLN Exchange 2026',                          'status' => 'active',   'penting' => false, 'msg' => 'KLN membuka pendaftaran program beasiswa exchange ke universitas mitra di Jepang, Korea, dan Eropa. Pendaftaran 1 April – 31 Mei 2026.'],
            ['subject' => 'Perubahan Prosedur Permohonan Surat Keterangan',              'status' => 'active',   'penting' => false, 'msg' => 'Mulai 1 Maret 2026, permohonan surat keterangan dilakukan online via portal mahasiswa. Permohonan langsung ke KLN tidak lagi dilayani.'],
            ['subject' => 'Sosialisasi Sistem SIMA untuk Mahasiswa Internasional',       'status' => 'active',   'penting' => false, 'msg' => 'Sosialisasi penggunaan SIMA pada 12 Juli 2026 pukul 13.00 WIB via Zoom. Link akan dikirimkan melalui email mahasiswa.'],
            ['subject' => 'Pembaruan Data Kontak Darurat Mahasiswa',                     'status' => 'active',   'penting' => true,  'msg' => 'Seluruh mahasiswa internasional wajib memperbarui data kontak darurat via portal SIMA paling lambat 30 Juni 2026.'],
            ['subject' => '[DRAFT] Panduan Mahasiswa Baru Internasional TA 2026/2027',   'status' => 'draft',    'penting' => false, 'msg' => 'Draft panduan ini sedang dalam tahap finalisasi. Akan dipublish sebelum penerimaan mahasiswa baru.'],
            ['subject' => '[DRAFT] Kebijakan Kehadiran Kelas untuk Mahasiswa Exchange',  'status' => 'draft',    'penting' => false, 'msg' => 'Draft kebijakan baru mengenai kehadiran minimum dan prosedur izin absen untuk mahasiswa program exchange.'],
            ['subject' => 'Penutupan Layanan KLN selama Audit Internal',                 'status' => 'inactive', 'penting' => false, 'msg' => 'Layanan KLN ditutup sementara pada 15–17 Januari 2026 untuk audit internal. Tidak ada layanan validasi dokumen selama periode ini.'],
            ['subject' => 'Informasi: Perubahan Nomor Hotline KLN',                      'status' => 'inactive', 'penting' => false, 'msg' => 'Nomor hotline KLN telah berubah. Nomor baru: 021-XXXX-YYYY. Nomor lama tidak aktif terhitung 1 Februari 2026.'],
        ];
        foreach ($annDef as $a) {
            DB::table('announcement')->updateOrInsert(
                ['subject' => $a['subject'], 'user_id' => $klnUserId],
                ['message' => $a['msg'], 'status' => $a['status'], 'sumber' => 'kln',
                 'is_penting' => $a['penting'], 'created_at' => $now, 'updated_at' => $now]
            );
        }

        // ══════════════════════════════════════════════════════════════
        // NOTIFIKASI BROADCAST
        // ══════════════════════════════════════════════════════════════
        $broadcastDef = [
            ['subject' => 'Selamat Datang di SIMA!',                            'type' => 'broadcast',    'targets' => array_keys($mhs),                              'readPct' => 90],
            ['subject' => 'Reminder: Perpanjangan KITAS Segera',                'type' => 'broadcast',    'targets' => ['BIPA-001','BIPA-002','LEC-001','LEC-002'],    'readPct' => 60],
            ['subject' => 'Dokumen Anda Telah Divalidasi',                      'type' => 'document',     'targets' => ['LEC-003','KLN-002'],                          'readPct' => 100],
            ['subject' => 'Pengumuman: Jadwal Validasi Dokumen Baru',           'type' => 'announcement', 'targets' => array_keys($mhs),                              'readPct' => 70],
            ['subject' => 'Akun Anda Telah Diaktifkan',                         'type' => 'account',      'targets' => ['BIPA-003','BIPA-004','LEC-004','LEC-005'],    'readPct' => 80],
            ['subject' => 'Reminder: Lengkapi Profil Anda',                     'type' => 'broadcast',    'targets' => ['BIPA-004','LEC-005','KLN-003'],               'readPct' => 33],
        ];
        foreach ($broadcastDef as $b) {
            $notifId = DB::table('notification')
                ->where('subject', $b['subject'])->where('type', $b['type'])->value('id')
                ?? DB::table('notification')->insertGetId([
                    'subject' => $b['subject'], 'message' => 'Pesan demo: ' . $b['subject'],
                    'status' => 'active', 'type' => $b['type'], 'sender_id' => $klnUserId,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            foreach ($b['targets'] as $i => $npm) {
                if (!isset($mhs[$npm])) continue;
                $isRead = ($i / count($b['targets']) * 100) < $b['readPct'];
                DB::table('notification_mahasiswa')->updateOrInsert(
                    ['notification_id' => $notifId, 'mahasiswa_id' => $mhs[$npm]['id']],
                    ['is_read' => $isRead, 'created_at' => $now, 'updated_at' => $now]
                );
            }
        }

        $this->command->info('SimaSeeder selesai — '
            . count($mhs) . ' mhs, ' . count($dosenDef) . ' dosen, ' . count($jadwalIds) . ' jadwal, '
            . count($dokDef) . ' dok, ' . count($reqDef) . ' req, '
            . count($annDef) . ' ann, ' . count($broadcastDef) . ' broadcast');
    }
}
