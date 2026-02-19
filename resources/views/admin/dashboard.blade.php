@extends('layouts.sima')

@section('page_title', 'Dashboard Admin')
@section('page_subtitle', 'Sistem Informasi Mahasiswa Asing — Universitas Gunadarma')

@section('main_content')

{{-- ═══════════════════════════════════════════ --}}
{{-- SYSTEM HEALTH BANNER                        --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="sima-alert sima-alert--teal sima-fade sima-fade--1" style="margin-bottom:20px">
    <i class="fas fa-server" style="font-size:16px;flex-shrink:0"></i>
    <div style="flex:1">
        <strong>System Status: Operational</strong>
        &nbsp;—&nbsp; Semua layanan berjalan normal.
        Database · API · Storage &nbsp;✓
    </div>
    <div style="font-family:var(--font-mono);font-size:11px;color:var(--sima-teal);white-space:nowrap">
        Uptime: 99.98%
    </div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- TOP STAT ROW                                 --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row">

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--1">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--gold"></div>
            <div class="sima-stat__icon sima-stat__icon--gold">
                <i class="fas fa-users"></i>
            </div>
            <div class="sima-stat__label">Total Mahasiswa Asing</div>
            <div class="sima-stat__value" data-count="120">120</div>
            <div class="sima-stat__delta sima-stat__delta--up">
                <i class="fas fa-arrow-up"></i> +8 dari semester lalu
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--2">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--blue"></div>
            <div class="sima-stat__icon sima-stat__icon--blue">
                <i class="fas fa-user-cog"></i>
            </div>
            <div class="sima-stat__label">Total Pengguna Sistem</div>
            <div class="sima-stat__value" data-count="148">148</div>
            <div class="sima-stat__delta sima-stat__delta--flat">Semua role aktif</div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--3">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--teal"></div>
            <div class="sima-stat__icon sima-stat__icon--teal">
                <i class="fas fa-globe"></i>
            </div>
            <div class="sima-stat__label">Negara Terdaftar</div>
            <div class="sima-stat__value" data-count="14">14</div>
            <div class="sima-stat__delta sima-stat__delta--up">
                <i class="fas fa-arrow-up"></i> +2 negara baru
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--4">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--red"></div>
            <div class="sima-stat__icon sima-stat__icon--red">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="sima-stat__label">Dokumen Kritis</div>
            <div class="sima-stat__value" data-count="13">13</div>
            <div class="sima-stat__delta sima-stat__delta--down">
                <i class="fas fa-exclamation"></i> 5 expired · 8 expiring
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- SECONDARY STATS                              --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row">

    <div class="col-md-2 col-sm-6 mb-4 sima-fade sima-fade--3">
        <div class="sima-stat" style="padding:18px">
            <div class="sima-stat__icon sima-stat__icon--blue" style="width:36px;height:36px;font-size:14px;border-radius:9px;margin-bottom:10px">
                <i class="fas fa-university"></i>
            </div>
            <div class="sima-stat__label">Jurusan</div>
            <div class="sima-stat__value" style="font-size:28px" data-count="8">8</div>
        </div>
    </div>

    <div class="col-md-2 col-sm-6 mb-4 sima-fade sima-fade--4">
        <div class="sima-stat" style="padding:18px">
            <div class="sima-stat__icon sima-stat__icon--teal" style="width:36px;height:36px;font-size:14px;border-radius:9px;margin-bottom:10px">
                <i class="fas fa-language"></i>
            </div>
            <div class="sima-stat__label">Peserta BIPA</div>
            <div class="sima-stat__value" style="font-size:28px" data-count="56">56</div>
        </div>
    </div>

    <div class="col-md-2 col-sm-6 mb-4 sima-fade sima-fade--5">
        <div class="sima-stat" style="padding:18px">
            <div class="sima-stat__icon sima-stat__icon--purple" style="width:36px;height:36px;font-size:14px;border-radius:9px;margin-bottom:10px">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="sima-stat__label">Dokumen Hari Ini</div>
            <div class="sima-stat__value" style="font-size:28px" data-count="11">11</div>
        </div>
    </div>

    <div class="col-md-2 col-sm-6 mb-4 sima-fade sima-fade--6">
        <div class="sima-stat" style="padding:18px">
            <div class="sima-stat__icon sima-stat__icon--emerald" style="width:36px;height:36px;font-size:14px;border-radius:9px;margin-bottom:10px">
                <i class="fas fa-check-double"></i>
            </div>
            <div class="sima-stat__label">Divalidasi</div>
            <div class="sima-stat__value" style="font-size:28px" data-count="3">3</div>
        </div>
    </div>

    <div class="col-md-2 col-sm-6 mb-4 sima-fade sima-fade--7">
        <div class="sima-stat" style="padding:18px">
            <div class="sima-stat__icon sima-stat__icon--amber" style="width:36px;height:36px;font-size:14px;border-radius:9px;margin-bottom:10px">
                <i class="fas fa-bell"></i>
            </div>
            <div class="sima-stat__label">Notifikasi Terkirim</div>
            <div class="sima-stat__value" style="font-size:28px" data-count="47">47</div>
        </div>
    </div>

    <div class="col-md-2 col-sm-6 mb-4 sima-fade sima-fade--8">
        <div class="sima-stat" style="padding:18px">
            <div class="sima-stat__icon sima-stat__icon--gold" style="width:36px;height:36px;font-size:14px;border-radius:9px;margin-bottom:10px">
                <i class="fas fa-star"></i>
            </div>
            <div class="sima-stat__label">Mahasiswa Beasiswa</div>
            <div class="sima-stat__value" style="font-size:28px" data-count="32">32</div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- MAIN CONTENT                                 --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row">

    {{-- ── User Management ───────────────── --}}
    <div class="col-md-8 mb-4 sima-fade sima-fade--5">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Manajemen Pengguna</h5>
                    <div class="sima-card__subtitle">Semua akun aktif dalam sistem</div>
                </div>
                <a href="#" class="sima-card__action"><i class="fas fa-plus"></i> Tambah User</a>
            </div>
            <div class="sima-card__body" style="padding:0">
                <table class="sima-table">
                    <thead>
                        <tr>
                            <th>Pengguna</th>
                            <th>Role</th>
                            <th>Jurusan / Unit</th>
                            <th>Status</th>
                            <th>Login Terakhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $users = [
                            ['Dr. Hendro Susanto',   'jurusan', 'Teknik Informatika',  'active',   '19 Feb 2026'],
                            ['Siti Rahayu, S.E.',    'kln',     'KLN Pusat',           'active',   '19 Feb 2026'],
                            ['Budi Santoso, M.T.',   'jurusan', 'Sistem Informasi',    'active',   '18 Feb 2026'],
                            ['Ahmad Fauzi',          'bipa',    'Pusat Bahasa',        'active',   '18 Feb 2026'],
                            ['Park Jisoo',           'mahasiswa','Korea Selatan',      'active',   '19 Feb 2026'],
                            ['John Doe',             'mahasiswa','Uzbekistan',         'inactive', '12 Feb 2026'],
                        ];
                        $roleBadge = [
                            'jurusan'  => ['purple','JURUSAN'],
                            'kln'      => ['teal','KLN'],
                            'bipa'     => ['amber','BIPA'],
                            'mahasiswa'=> ['blue','MAHASISWA'],
                            'admin'    => ['gold','ADMIN'],
                        ];
                        @endphp

                        @foreach($users as $u)
                        @php $rb = $roleBadge[$u[1]] ?? ['grey','USER']; @endphp
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <div class="sima-avatar" style="background:linear-gradient(135deg,#0D1B2A,#2563EB);color:white;font-size:11px">
                                        {{ strtoupper(substr($u[0],0,1)) }}{{ strtoupper(substr(explode(' ',$u[0])[1]??'',0,1)) }}
                                    </div>
                                    <div style="font-size:13px;font-weight:500;color:var(--sima-text-primary)">{{ $u[0] }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="sima-badge sima-badge--{{ $rb[0] }}" style="font-size:10px">{{ $rb[1] }}</span>
                            </td>
                            <td style="font-size:12px;color:var(--sima-text-secondary)">{{ $u[2] }}</td>
                            <td>
                                @if($u[3]=='active')
                                    <span class="sima-badge sima-badge--emerald" style="font-size:10px"><i class="fas fa-circle" style="font-size:6px"></i> Aktif</span>
                                @else
                                    <span class="sima-badge sima-badge--grey" style="font-size:10px"><i class="fas fa-circle" style="font-size:6px"></i> Nonaktif</span>
                                @endif
                            </td>
                            <td style="font-family:var(--font-mono);font-size:11px;color:var(--sima-text-muted)">{{ $u[4] }}</td>
                            <td>
                                <div style="display:flex;gap:6px">
                                    <a href="#" style="padding:4px 10px;background:var(--sima-blue-soft);color:var(--sima-blue);border-radius:6px;font-size:11px;font-weight:500;text-decoration:none">Edit</a>
                                    <a href="#" style="padding:4px 10px;background:var(--sima-red-soft);color:var(--sima-red);border-radius:6px;font-size:11px;font-weight:500;text-decoration:none">Hapus</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Role Distribution + System Info ─ --}}
    <div class="col-md-4 mb-4 sima-fade sima-fade--6">

        {{-- Role Distribution --}}
        <div class="sima-card" style="margin-bottom:16px">
            <div class="sima-card__header">
                <h5 class="sima-card__title">Distribusi Role</h5>
            </div>
            <div class="sima-card__body">

                @php
                $roles = [
                    ['Mahasiswa',  120, 'blue',   '#2563EB'],
                    ['KLN Staff',    5, 'teal',   '#0D9488'],
                    ['Jurusan',     16, 'purple', '#7C3AED'],
                    ['BIPA',         4, 'amber',  '#D97706'],
                    ['Admin',        3, 'gold',   '#C4973A'],
                ];
                $totalU = array_sum(array_column($roles,'1'));
                @endphp

                @foreach($roles as $r)
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                    <div style="width:8px;height:8px;border-radius:50%;background:{{ $r[3] }};flex-shrink:0"></div>
                    <div style="flex:1;font-size:12px;color:var(--sima-text-secondary)">{{ $r[0] }}</div>
                    <div style="font-family:var(--font-mono);font-size:12px;font-weight:600;color:var(--sima-text-primary)">{{ $r[1] }}</div>
                    <div style="min-width:40px;text-align:right;font-size:11px;color:var(--sima-text-muted)">{{ round($r[1]/$totalU*100) }}%</div>
                </div>
                <div class="sima-progress" style="margin-bottom:10px">
                    <div class="sima-progress__bar" style="width:{{ round($r[1]/$totalU*100) }}%;background:{{ $r[3] }}"></div>
                </div>
                @endforeach

            </div>
        </div>

        {{-- System Info --}}
        <div class="sima-card">
            <div class="sima-card__header">
                <h5 class="sima-card__title">Info Sistem</h5>
            </div>
            <div class="sima-card__body">
                @php
                $info = [
                    ['Versi SIMA',     'v2.4.1',         'fas fa-code-branch', '#2563EB'],
                    ['Laravel',        'v11.x',          'fas fa-server',      '#059669'],
                    ['PHP',            '8.3',            'fas fa-code',        '#7C3AED'],
                    ['Database',       'MySQL 8.0',      'fas fa-database',    '#0D9488'],
                    ['Storage Used',   '2.4 GB / 50 GB', 'fas fa-hdd',        '#D97706'],
                    ['Last Backup',    'Hari ini 03:00', 'fas fa-save',        '#C4973A'],
                ];
                @endphp
                @foreach($info as $s)
                <div style="display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px solid var(--sima-border-soft)">
                    <div style="width:28px;height:28px;border-radius:7px;background:{{ $s[3] }}15;color:{{ $s[3] }};display:flex;align-items:center;justify-content:center;font-size:11px;flex-shrink:0">
                        <i class="{{ $s[2] }}"></i>
                    </div>
                    <div style="flex:1;font-size:12px;color:var(--sima-text-secondary)">{{ $s[0] }}</div>
                    <div style="font-family:var(--font-mono);font-size:11px;font-weight:600;color:var(--sima-text-primary)">{{ $s[1] }}</div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- BOTTOM ROW: Jurusan + Global Monitoring     --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row">

    <div class="col-md-6 mb-4 sima-fade sima-fade--7">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Manajemen Jurusan</h5>
                    <div class="sima-card__subtitle">8 program studi terdaftar</div>
                </div>
                <a href="#" class="sima-card__action"><i class="fas fa-plus"></i> Tambah</a>
            </div>
            <div class="sima-card__body" style="padding:0">
                <table class="sima-table">
                    <thead>
                        <tr>
                            <th>Program Studi</th>
                            <th>Mahasiswa</th>
                            <th>Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $prodi = [
                            ['Teknik Informatika',  28, 86],
                            ['Sistem Informasi',    22, 82],
                            ['Manajemen',           18, 79],
                            ['Akuntansi',           16, 88],
                            ['Teknik Industri',     14, 84],
                            ['Psikologi',            8, 91],
                            ['Ilmu Komputer',        8, 83],
                            ['Komunikasi',           6, 77],
                        ];
                        @endphp
                        @foreach($prodi as $p)
                        <tr>
                            <td style="font-weight:500;font-size:13px">{{ $p[0] }}</td>
                            <td style="font-family:var(--font-mono);font-size:13px;font-weight:600;color:var(--sima-blue)">{{ $p[1] }}</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px">
                                    <div class="sima-progress" style="width:80px">
                                        <div class="sima-progress__bar" style="width:{{ $p[2] }}%;background:{{ $p[2] >= 85 ? 'var(--sima-emerald)' : ($p[2] >= 80 ? 'var(--sima-amber)' : 'var(--sima-red)') }}"></div>
                                    </div>
                                    <span style="font-family:var(--font-mono);font-size:11px;color:var(--sima-text-muted)">{{ $p[2] }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4 sima-fade sima-fade--8">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Monitoring Global</h5>
                    <div class="sima-card__subtitle">Rekapitulasi seluruh unit</div>
                </div>
            </div>
            <div class="sima-card__body">

                {{-- Module Status --}}
                @php
                $modules = [
                    ['KLN',     'Monitoring & Validasi',   '120 mahasiswa aktif',  'teal',   'fa-check-circle'],
                    ['Jurusan', 'Absensi & Jadwal',        '40 mahasiswa, 6 kelas','purple', 'fa-check-circle'],
                    ['BIPA',    'Kelas Bahasa Indonesia',  '56 peserta, 4 kelas',  'amber',  'fa-check-circle'],
                    ['SIMA',    'Sistem Notifikasi',       '47 notif terkirim',    'blue',   'fa-check-circle'],
                ];
                $moduleColors = ['teal'=>'#0D9488','purple'=>'#7C3AED','amber'=>'#D97706','blue'=>'#2563EB'];
                @endphp

                @foreach($modules as $m)
                @php $color = $moduleColors[$m[3]]; @endphp
                <div style="display:flex;gap:14px;padding:14px;border-radius:12px;background:{{ $color }}08;border:1px solid {{ $color }}18;margin-bottom:10px">
                    <div style="width:40px;height:40px;border-radius:10px;background:{{ $color }}18;color:{{ $color }};display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0">
                        <i class="fas {{ $m[4] }}"></i>
                    </div>
                    <div style="flex:1">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:3px">
                            <span style="font-size:13px;font-weight:600;color:var(--sima-text-primary)">{{ $m[0] }}</span>
                            <span class="sima-badge sima-badge--emerald" style="font-size:9px"><i class="fas fa-circle" style="font-size:5px"></i> Online</span>
                        </div>
                        <div style="font-size:12px;color:var(--sima-text-secondary)">{{ $m[1] }}</div>
                        <div style="font-size:11px;color:var(--sima-text-muted)">{{ $m[2] }}</div>
                    </div>
                </div>
                @endforeach

                {{-- Quick Admin Actions --}}
                <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--sima-border-soft)">
                    <div style="font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--sima-text-muted);margin-bottom:12px">Aksi Cepat Admin</div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                        <a href="#" style="padding:8px 14px;background:var(--sima-navy);color:white;border-radius:8px;font-size:12px;font-weight:500;text-decoration:none"><i class="fas fa-download"></i> Export Data</a>
                        <a href="#" style="padding:8px 14px;background:var(--sima-red-soft);color:var(--sima-red);border-radius:8px;font-size:12px;font-weight:500;text-decoration:none"><i class="fas fa-sync"></i> Backup DB</a>
                        <a href="#" style="padding:8px 14px;background:var(--sima-gold-pale);color:var(--sima-gold);border-radius:8px;font-size:12px;font-weight:500;text-decoration:none"><i class="fas fa-cogs"></i> Settings</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection
