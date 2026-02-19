@extends('layouts.sima')

@section('page_title', 'Dashboard Jurusan')
@section('page_subtitle', 'Pengelolaan akademik mahasiswa asing — Program Studi')

@section('main_content')

{{-- ═══════════════════════════════════════════ --}}
{{-- STAT ROW                                     --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row">

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--1">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--blue"></div>
            <div class="sima-stat__icon sima-stat__icon--blue">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="sima-stat__label">Mahasiswa Aktif</div>
            <div class="sima-stat__value" data-count="40">40</div>
            <div class="sima-stat__delta sima-stat__delta--up">
                <i class="fas fa-arrow-up"></i> +5 mahasiswa baru
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--2">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--emerald"></div>
            <div class="sima-stat__icon sima-stat__icon--emerald">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="sima-stat__label">Jadwal Aktif</div>
            <div class="sima-stat__value" data-count="6">6</div>
            <div class="sima-stat__delta sima-stat__delta--flat">
                <i class="fas fa-circle" style="font-size:7px"></i> Minggu ini
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--3">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--amber"></div>
            <div class="sima-stat__icon sima-stat__icon--amber">
                <i class="fas fa-bullhorn"></i>
            </div>
            <div class="sima-stat__label">Pengumuman Baru</div>
            <div class="sima-stat__value" data-count="2">2</div>
            <div class="sima-stat__delta sima-stat__delta--flat">Belum dipublish</div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--4">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--purple"></div>
            <div class="sima-stat__icon sima-stat__icon--purple">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="sima-stat__label">Rata-rata Kehadiran</div>
            <div class="sima-stat__value" data-count="84">84</div>
            <div class="sima-stat__delta sima-stat__delta--up">
                <i class="fas fa-arrow-up"></i> <span style="font-size:11px">%</span> +3% bulan ini
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- MAIN CONTENT                                 --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row">

    {{-- ── Absensi Today ──────────────────── --}}
    <div class="col-md-7 mb-4 sima-fade sima-fade--5">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Absensi Hari Ini</h5>
                    <div class="sima-card__subtitle">{{ date('l, d F Y') }}</div>
                </div>
                <a href="#" class="sima-card__action"><i class="fas fa-plus"></i> Input Absensi</a>
            </div>
            <div class="sima-card__body" style="padding:0">

                <table class="sima-table">
                    <thead>
                        <tr>
                            <th>Mata Kuliah</th>
                            <th>Jam</th>
                            <th>Ruang</th>
                            <th>Hadir</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $classes = [
                            ['Matematika Lanjut',     '08:00–10:00', 'Gd.4 R.201', '18/20', 'selesai'],
                            ['Struktur Data',          '10:00–12:00', 'Gd.4 R.201', '16/20', 'selesai'],
                            ['Pemrograman Web',        '13:00–15:00', 'Lab 3',       '—',     'berlangsung'],
                            ['Basis Data',             '15:00–17:00', 'Gd.2 R.105', '—',     'terjadwal'],
                        ];
                        @endphp

                        @foreach($classes as $c)
                        <tr>
                            <td style="font-weight:500">{{ $c[0] }}</td>
                            <td style="font-family:var(--font-mono);font-size:12px;color:var(--sima-text-muted);white-space:nowrap">{{ $c[1] }}</td>
                            <td style="font-size:12px;color:var(--sima-text-secondary)">{{ $c[2] }}</td>
                            <td style="font-family:var(--font-mono);font-size:13px;font-weight:600">{{ $c[3] }}</td>
                            <td>
                                @if($c[4]=='selesai')
                                    <span class="sima-badge sima-badge--emerald"><i class="fas fa-check-circle"></i> Selesai</span>
                                @elseif($c[4]=='berlangsung')
                                    <span class="sima-badge sima-badge--blue" style="animation:pulse 2s infinite">
                                        <i class="fas fa-circle" style="font-size:7px"></i> Berlangsung
                                    </span>
                                @else
                                    <span class="sima-badge sima-badge--grey"><i class="fas fa-clock"></i> Terjadwal</span>
                                @endif
                            </td>
                            <td>
                                @if($c[4]!='terjadwal')
                                    <a href="#" style="font-size:12px;color:var(--sima-blue);font-weight:500">Detail</a>
                                @else
                                    <span style="font-size:12px;color:var(--sima-text-muted)">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>

            </div>
        </div>
    </div>

    {{-- ── Kehadiran Per Mahasiswa ─────────── --}}
    <div class="col-md-5 mb-4 sima-fade sima-fade--6">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Monitoring Kehadiran</h5>
                    <div class="sima-card__subtitle">Semester ini · Mahasiswa kritis</div>
                </div>
                <a href="#" class="sima-card__action">Semua</a>
            </div>
            <div class="sima-card__body">

                @php
                $students = [
                    ['Ahmad R.',    75, 'red'],
                    ['Bui Thi L.', 78, 'red'],
                    ['Park J.',    82, 'amber'],
                    ['John D.',    85, 'amber'],
                    ['Yuki T.',    90, 'emerald'],
                    ['Li X.M.',    94, 'emerald'],
                ];
                $colorMap = [
                    'red'     => ['#DC2626','#FEF2F2'],
                    'amber'   => ['#D97706','#FFFBEB'],
                    'emerald' => ['#059669','#ECFDF5'],
                ];
                @endphp

                @foreach($students as $s)
                @php $c = $colorMap[$s[2]]; @endphp
                <div style="margin-bottom:14px">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:6px">
                        <div class="sima-avatar" style="background:{{ $c[1] }};color:{{ $c[0] }};font-size:11px">
                            {{ strtoupper(substr($s[0],0,1)) }}{{ strtoupper(substr(explode(' ',$s[0])[1]??'X',0,1)) }}
                        </div>
                        <div style="flex:1">
                            <div style="font-size:13px;font-weight:500;color:var(--sima-text-primary)">{{ $s[0] }}</div>
                        </div>
                        <div style="font-family:var(--font-mono);font-size:13px;font-weight:600;color:{{ $c[0] }}">{{ $s[1] }}%</div>
                    </div>
                    <div class="sima-progress">
                        <div class="sima-progress__bar" style="width:{{ $s[1] }}%;background:{{ $c[0] }}"></div>
                    </div>
                </div>
                @endforeach

                <div style="padding:12px;background:var(--sima-red-soft);border-radius:10px;border:1px solid #FED7D7;margin-top:6px">
                    <div style="font-size:12px;color:var(--sima-red);font-weight:500">
                        <i class="fas fa-exclamation-triangle"></i>
                        2 mahasiswa di bawah batas minimum (80%)
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- BOTTOM ROW: Schedule Management             --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row">

    <div class="col-md-6 mb-4 sima-fade sima-fade--7">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Jadwal Minggu Ini</h5>
                    <div class="sima-card__subtitle">18–22 Februari 2026</div>
                </div>
                <a href="#" class="sima-card__action"><i class="fas fa-plus"></i> Tambah Jadwal</a>
            </div>
            <div class="sima-card__body">

                @php
                $week = [
                    ['Senin',   'Matematika Lanjut',  '08:00', 'Gd.4 R.201'],
                    ['Selasa',  'Struktur Data',        '10:00', 'Gd.4 R.201'],
                    ['Rabu',    'Pemrograman Web',      '13:00', 'Lab 3'],
                    ['Kamis',   'Basis Data',           '08:00', 'Gd.2 R.105'],
                    ['Jumat',   'Algoritma',            '09:00', 'Gd.3 R.302'],
                    ['Sabtu',   'Seminar',              '10:00', 'Aula Utama'],
                ];
                $dayColors = ['#2563EB','#0D9488','#7C3AED','#D97706','#DC2626','#059669'];
                @endphp

                @foreach($week as $i => $w)
                <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--sima-border-soft)">
                    <div style="min-width:58px;padding:4px 0;text-align:center;background:{{ $dayColors[$i] }}18;border-radius:8px;border:1px solid {{ $dayColors[$i] }}30">
                        <div style="font-size:9px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:{{ $dayColors[$i] }}">{{ $w[0] }}</div>
                    </div>
                    <div style="flex:1">
                        <div style="font-size:13px;font-weight:500;color:var(--sima-text-primary)">{{ $w[1] }}</div>
                        <div style="font-size:11px;color:var(--sima-text-muted)"><i class="fas fa-map-marker-alt"></i> {{ $w[3] }}</div>
                    </div>
                    <div style="font-family:var(--font-mono);font-size:11px;color:var(--sima-text-muted)">{{ $w[2] }}</div>
                </div>
                @endforeach

            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4 sima-fade sima-fade--8">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Pengumuman Jurusan</h5>
                    <div class="sima-card__subtitle">Draft &amp; terkirim</div>
                </div>
                <a href="#" class="sima-card__action"><i class="fas fa-plus"></i> Buat Baru</a>
            </div>
            <div class="sima-card__body">

                <div class="sima-announce">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px">
                        <div class="sima-announce__title">📅 Jadwal UTS Semester Genap</div>
                        <span class="sima-badge sima-badge--emerald">Published</span>
                    </div>
                    <div class="sima-announce__body">UTS akan dilaksanakan 10–21 Maret 2026. Kartu ujian dapat diambil mulai 3 Maret.</div>
                    <div class="sima-announce__meta">
                        <i class="fas fa-users"></i> 40 mahasiswa
                        <span>· 1 hari lalu</span>
                    </div>
                </div>

                <div class="sima-announce" style="border-color:var(--sima-border);background:#FFFBEB">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px">
                        <div class="sima-announce__title">📋 Bimbingan Skripsi Gelombang 2</div>
                        <span class="sima-badge sima-badge--amber">Draft</span>
                    </div>
                    <div class="sima-announce__body">Pendaftaran bimbingan skripsi gelombang 2 akan dibuka...</div>
                    <div class="sima-announce__meta">
                        <i class="fas fa-edit"></i> Draft
                        <span>· Belum dipublish</span>
                        <a href="#" style="color:var(--sima-blue);margin-left:auto">Edit →</a>
                    </div>
                </div>

                <div class="sima-announce" style="border-color:var(--sima-border);background:#FFFBEB">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px">
                        <div class="sima-announce__title">🎓 Wisuda April 2026</div>
                        <span class="sima-badge sima-badge--amber">Draft</span>
                    </div>
                    <div class="sima-announce__body">Informasi pendaftaran wisuda untuk lulusan semester ini...</div>
                    <div class="sima-announce__meta">
                        <i class="fas fa-edit"></i> Draft
                        <span>· Belum dipublish</span>
                        <a href="#" style="color:var(--sima-blue);margin-left:auto">Edit →</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

@section('page_js')
<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}
</style>
@endsection

@endsection
