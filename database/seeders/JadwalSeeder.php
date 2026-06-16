<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // firstOrCreate via raw DB — returns existing id or inserts and returns new id
        $oc = function (string $table, array $where, array $extra = []) use ($now) {
            return DB::table($table)->where($where)->value('id')
                ?? DB::table($table)->insertGetId(
                    array_merge($where, $extra, ['created_at' => $now, 'updated_at' => $now])
                );
        };

        // ── JURUSAN (sudah ada, jangan insert) ───────────────────
        $klnJurId  = 1;
        $bipaJurId = 2;

        // Untuk Lecturers: ambil 2 jurusan dari DB yang bukan KLN/BIPA
        $lecJurIds = DB::table('jurusan')
            ->whereNotIn('id', [1, 2])
            ->orderBy('id')
            ->limit(2)
            ->pluck('id')
            ->toArray();

        if (count($lecJurIds) < 2) {
            $this->command->warn('Kurang dari 2 jurusan tersedia untuk Lecturers. Seeder dihentikan.');
            return;
        }

        [$tiJurId, $siJurId] = $lecJurIds;

        $this->command->info("Jurusan: KLN={$klnJurId}, BIPA={$bipaJurId}, Lecturers={$tiJurId},{$siJurId}");

        // ── USERS UNTUK JURUSAN ADMIN (agar Lecturers page bisa detect) ──
        $jurTiUid = $oc('users', ['email' => 'jurusan.ti@seed.test'], [
            'role' => 'jurusan', 'password' => Hash::make('password'),
            'status' => 'active', 'jurusan_id' => $tiJurId, 'profile_completed' => false,
        ]);
        DB::table('users')->where('id', $jurTiUid)->update(['jurusan_id' => $tiJurId]);

        $jurSiUid = $oc('users', ['email' => 'jurusan.si@seed.test'], [
            'role' => 'jurusan', 'password' => Hash::make('password'),
            'status' => 'active', 'jurusan_id' => $siJurId, 'profile_completed' => false,
        ]);
        DB::table('users')->where('id', $jurSiUid)->update(['jurusan_id' => $siJurId]);

        // ── USERS UNTUK DOSEN ─────────────────────────────────────
        $dosBipaU1 = $oc('users', ['email' => 'dos.bipa1@seed.test'], [
            'role' => 'DOSEN', 'password' => Hash::make('password'),
            'status' => 'active', 'profile_completed' => false,
        ]);
        $dosBipaU2 = $oc('users', ['email' => 'dos.bipa2@seed.test'], [
            'role' => 'DOSEN', 'password' => Hash::make('password'),
            'status' => 'active', 'profile_completed' => false,
        ]);
        $dosTiU1 = $oc('users', ['email' => 'dos.ti1@seed.test'], [
            'role' => 'DOSEN', 'password' => Hash::make('password'),
            'status' => 'active', 'profile_completed' => false,
        ]);
        $dosTiU2 = $oc('users', ['email' => 'dos.ti2@seed.test'], [
            'role' => 'DOSEN', 'password' => Hash::make('password'),
            'status' => 'active', 'profile_completed' => false,
        ]);
        $dosKlnU1 = $oc('users', ['email' => 'dos.kln1@seed.test'], [
            'role' => 'DOSEN', 'password' => Hash::make('password'),
            'status' => 'active', 'profile_completed' => false,
        ]);

        // ── DOSEN ─────────────────────────────────────────────────
        $dosBipa1 = $oc('dosen', ['nidn' => '0001010001'], ['nama' => 'Dr. Ahmad Fauzi M.Pd.',     'kodeDos' => 'DOS-BIPA-01', 'user_id' => $dosBipaU1]);
        $dosBipa2 = $oc('dosen', ['nidn' => '0001010002'], ['nama' => 'Siti Rahayu S.Pd.',         'kodeDos' => 'DOS-BIPA-02', 'user_id' => $dosBipaU2]);
        $dosTi1   = $oc('dosen', ['nidn' => '0002020001'], ['nama' => 'Prof. Budi Santoso Ph.D.',  'kodeDos' => 'DOS-TI-01',   'user_id' => $dosTiU1]);
        $dosTi2   = $oc('dosen', ['nidn' => '0002020002'], ['nama' => 'Ir. Dewi Kusuma M.T.',      'kodeDos' => 'DOS-TI-02',   'user_id' => $dosTiU2]);
        $dosKln1  = $oc('dosen', ['nidn' => '0003030001'], ['nama' => 'Mr. James Anderson',        'kodeDos' => 'DOS-KLN-01',  'user_id' => $dosKlnU1]);

        // ── MATAKULIAH ─────────────────────────────────────────────
        $mkBipa1 = $oc('matakuliah', ['kodeMk' => 'BIPA-101'], ['namaMk' => 'Bahasa Indonesia Dasar',        'sks' => 3, 'keterangan' => 'Wajib',   'jurusan_id' => $bipaJurId]);
        $mkBipa2 = $oc('matakuliah', ['kodeMk' => 'BIPA-201'], ['namaMk' => 'Bahasa Indonesia Menengah',     'sks' => 3, 'keterangan' => 'Wajib',   'jurusan_id' => $bipaJurId]);
        $mkBipa3 = $oc('matakuliah', ['kodeMk' => 'BIPA-301'], ['namaMk' => 'Bahasa Indonesia Lanjut',       'sks' => 3, 'keterangan' => 'Pilihan', 'jurusan_id' => $bipaJurId]);

        $mkTi1 = $oc('matakuliah', ['kodeMk' => 'LEC-BD-01'],  ['namaMk' => 'Basis Data',                   'sks' => 3, 'keterangan' => 'Wajib',   'jurusan_id' => $tiJurId]);
        $mkTi2 = $oc('matakuliah', ['kodeMk' => 'LEC-PW-01'],  ['namaMk' => 'Pemrograman Web',              'sks' => 3, 'keterangan' => 'Wajib',   'jurusan_id' => $tiJurId]);
        $mkTi3 = $oc('matakuliah', ['kodeMk' => 'LEC-RPL-01'], ['namaMk' => 'Rekayasa Perangkat Lunak',     'sks' => 3, 'keterangan' => 'Wajib',   'jurusan_id' => $tiJurId]);
        $mkSi1 = $oc('matakuliah', ['kodeMk' => 'LEC-ASI-01'], ['namaMk' => 'Analisis Sistem Informasi',    'sks' => 3, 'keterangan' => 'Wajib',   'jurusan_id' => $siJurId]);

        $mkKln1 = $oc('matakuliah', ['kodeMk' => 'KLN-101'], ['namaMk' => 'International Communication',   'sks' => 2, 'keterangan' => 'Wajib',   'jurusan_id' => $klnJurId]);
        $mkKln2 = $oc('matakuliah', ['kodeMk' => 'KLN-201'], ['namaMk' => 'Cross-Cultural Studies',        'sks' => 3, 'keterangan' => 'Wajib',   'jurusan_id' => $klnJurId]);

        // ── KELAS ──────────────────────────────────────────────────
        $kelBipaA = $oc('kelas', ['kodeKelas' => 'BIPA-2025A'], ['tahunAjar' => '2025/2026']);
        $kelBipaB = $oc('kelas', ['kodeKelas' => 'BIPA-2025B'], ['tahunAjar' => '2025/2026']);
        $kelTiA   = $oc('kelas', ['kodeKelas' => 'LEC-2025A'],  ['tahunAjar' => '2025/2026']);
        $kelSiA   = $oc('kelas', ['kodeKelas' => 'LEC-2025B'],  ['tahunAjar' => '2025/2026']);
        $kelKlnA  = $oc('kelas', ['kodeKelas' => 'KLN-2025A'],  ['tahunAjar' => '2025/2026']);

        // ── JADWAL ─────────────────────────────────────────────────
        // [kelas_id, matakuliah_id, dosen_id, hari, jam, ruangan, totalSesi]
        $jadwalRows = [
            // BIPA
            [$kelBipaA, $mkBipa1, $dosBipa1, 'Senin',  '08:00', 'R.BIPA-01', 16],
            [$kelBipaA, $mkBipa2, $dosBipa1, 'Rabu',   '10:00', 'R.BIPA-01', 16],
            [$kelBipaB, $mkBipa1, $dosBipa2, 'Selasa', '08:00', 'R.BIPA-02', 16],
            [$kelBipaB, $mkBipa3, $dosBipa2, 'Kamis',  '13:00', 'R.BIPA-02', 14],
            // Lecturers
            [$kelTiA, $mkTi1, $dosTi1, 'Senin',  '07:30', 'R.LEC-201', 16],
            [$kelTiA, $mkTi2, $dosTi2, 'Kamis',  '09:00', 'R.LEC-202', 16],
            [$kelTiA, $mkTi3, $dosTi1, 'Jumat',  '13:00', 'R.LEC-301', 14],
            [$kelSiA, $mkSi1, $dosTi2, 'Selasa', '10:00', 'R.LEC-101', 16],
            // KLN
            [$kelKlnA, $mkKln1, $dosKln1, 'Rabu',  '08:00', 'R.KLN-01', 12],
            [$kelKlnA, $mkKln2, $dosKln1, 'Jumat', '13:00', 'R.KLN-01', 14],
        ];

        $jadwalIds = [];
        foreach ($jadwalRows as [$kId, $mkId, $dId, $hari, $jam, $ruangan, $sesi]) {
            $jadwalIds[] = DB::table('jadwal')
                ->where(['kelas_id' => $kId, 'matakuliah_id' => $mkId])
                ->value('id')
                ?? DB::table('jadwal')->insertGetId([
                    'kelas_id'      => $kId,
                    'matakuliah_id' => $mkId,
                    'dosen_id'      => $dId,
                    'hari'          => $hari,
                    'jam'           => $jam,
                    'ruangan'       => $ruangan,
                    'totalSesi'     => $sesi,
                    'tahunAjar'     => '2025/2026',
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
        }

        // ── MAHASISWA ──────────────────────────────────────────────
        $mhsData = [
            ['email' => 'mhs1@seed.test', 'npm' => 'S-MHS-001', 'nama' => 'Takeshi Yamamoto', 'wa' => '+81901234001'],
            ['email' => 'mhs2@seed.test', 'npm' => 'S-MHS-002', 'nama' => 'Liu Wei',          'wa' => '+86139876002'],
            ['email' => 'mhs3@seed.test', 'npm' => 'S-MHS-003', 'nama' => 'Amara Diallo',     'wa' => '+22176543003'],
            ['email' => 'mhs4@seed.test', 'npm' => 'S-MHS-004', 'nama' => 'Carlos Mendoza',   'wa' => '+52551234004'],
        ];

        $mhsIds = [];
        foreach ($mhsData as $m) {
            $uid = $oc('users', ['email' => $m['email']], [
                'role' => 'MAHASISWA', 'password' => Hash::make('password'),
                'status' => 'active', 'profile_completed' => true,
            ]);
            $mhsIds[] = $oc('mahasiswa', ['npm' => $m['npm']], [
                'nama'       => $m['nama'],
                'noWa'       => $m['wa'],
                'tglLahir'   => '2000-06-15',
                'warNeg'     => 'Asing',
                'alamatAsal' => 'Luar Negeri',
                'alamatIndo' => 'Jakarta Pusat',
                'user_id'    => $uid,
            ]);
        }

        [$mhs1, $mhs2, $mhs3, $mhs4] = $mhsIds;

        // ── MAHASISWA_KELAS ────────────────────────────────────────
        $kelasMhs = [
            [$kelBipaA, $mhs1], [$kelBipaA, $mhs2], [$kelBipaA, $mhs3], [$kelBipaA, $mhs4],
            [$kelBipaB, $mhs2], [$kelBipaB, $mhs3],
            [$kelTiA,   $mhs1], [$kelTiA,   $mhs3], [$kelTiA,   $mhs4],
            [$kelSiA,   $mhs2], [$kelSiA,   $mhs4],
            [$kelKlnA,  $mhs1], [$kelKlnA,  $mhs2], [$kelKlnA,  $mhs3],
        ];
        foreach ($kelasMhs as [$kId, $mId]) {
            DB::table('mahasiswa_kelas')->updateOrInsert(
                ['kelas_id' => $kId, 'mahasiswa_id' => $mId],
                ['created_at' => $now, 'updated_at' => $now]
            );
        }

        // ── JADWAL_MAHASISWA (kehadiran untuk 3 jadwal) ────────────
        // jadwal index 0 = BIPA pertama, index 4 = Lecturers pertama, index 8 = KLN pertama
        $jadwalUntukKehadiran = [
            $jadwalIds[0] => [$mhs1, $mhs2, $mhs3, $mhs4], // BIPA-2025A — Bahasa Indonesia Dasar
            $jadwalIds[4] => [$mhs1, $mhs3, $mhs4],         // LEC-2025A  — Basis Data
            $jadwalIds[8] => [$mhs1, $mhs2, $mhs3],         // KLN-2025A  — International Communication
        ];

        foreach ($jadwalUntukKehadiran as $jadwalId => $peserta) {
            $this->seedKehadiran($jadwalId, $peserta, $now);
        }

        $this->command->info('JadwalSeeder selesai — ' . count($jadwalIds) . ' jadwal, ' . count($mhsIds) . ' mahasiswa.');
    }

    // ── HELPER: isi jadwal_mahasiswa dengan kehadiran bervariasi ──
    private function seedKehadiran(int $jadwalId, array $mhsIds, \Illuminate\Support\Carbon $now): void
    {
        $tglSesi = [
            '2026-02-02', '2026-02-09', '2026-02-16', '2026-02-23',
            '2026-03-02', '2026-03-09', '2026-03-16', '2026-03-23',
        ];

        // Tiap mahasiswa dapat pola berbeda
        $patterns = [
            ['present', 'present', 'present', 'present', 'present', 'absent',  'present', 'present'], // 7/8
            ['present', 'present', 'absent',  'present', 'excused', 'present', 'present', 'present'], // 6/8
            ['present', 'absent',  'present', 'excused', 'present', 'present', 'absent',  'present'], // 5/8
            ['absent',  'present', 'present', 'present', 'present', 'excused', 'present', 'present'], // 6/8
        ];

        foreach ($mhsIds as $i => $mId) {
            $pattern = $patterns[$i % count($patterns)];
            foreach ($pattern as $sesiIdx => $status) {
                DB::table('jadwal_mahasiswa')->updateOrInsert(
                    ['jadwal_id' => $jadwalId, 'mahasiswa_id' => $mId, 'sesi' => $sesiIdx + 1],
                    ['status' => $status, 'tglSesi' => $tglSesi[$sesiIdx], 'created_at' => $now, 'updated_at' => $now]
                );
            }
        }
    }
}
