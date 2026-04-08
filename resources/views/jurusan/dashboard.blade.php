@extends('layouts.sima')

@section('page_title',    'Dashboard Jurusan')
@section('page_section',  'ADMIN JURUSAN')
@section('page_subtitle', 'Pengelolaan akademik — Selamat datang, ' . (auth()->user()->name ?? 'Admin Jurusan'))

@section('main_content')

{{-- ═══════════════════════════════════════════════════
     STAT CARDS
     ═══════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    <div class="col-6 col-md-3 sima-fade sima-fade--1">
        <a href="{{ route('jurusan.mahasiswa.page') }}" class="sima-stat sima-stat--blue d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--blue">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="sima-stat__label">Mahasiswa Aktif</div>
            <span class="sima-stat__value" data-count="{{ $totalMahasiswa ?? 40 }}">{{ $totalMahasiswa ?? 40 }}</span>
            <div class="sima-stat__delta sima-stat__delta--up">
                <i class="fas fa-arrow-up"></i> +5 mahasiswa baru
            </div>
        </a>
    </div>

    <div class="col-6 col-md-3 sima-fade sima-fade--2">
        <a href="{{ route('jurusan.jadwal.page') }}" class="sima-stat sima-stat--teal d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--teal">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="sima-stat__label">Jadwal Aktif</div>
            <span class="sima-stat__value" data-count="{{ $jadwalAktif ?? 6 }}">{{ $jadwalAktif ?? 6 }}</span>
            <div class="sima-stat__delta sima-stat__delta--flat">
                <i class="fas fa-circle" style="font-size:7px"></i> Minggu ini
            </div>
        </a>
    </div>

    <div class="col-6 col-md-3 sima-fade sima-fade--3">
        <a href="{{ route('jurusan.matakuliah.page') }}" class="sima-stat sima-stat--amber d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--amber">
                <i class="fas fa-book"></i>
            </div>
            <div class="sima-stat__label">Mata Kuliah</div>
            <span class="sima-stat__value" data-count="{{ $totalMatakuliah ?? 12 }}">{{ $totalMatakuliah ?? 12 }}</span>
            <div class="sima-stat__delta sima-stat__delta--flat">Semester ini</div>
        </a>
    </div>

    <div class="col-6 col-md-3 sima-fade sima-fade--4">
        <a href="{{ route('jurusan.dosen.page') }}" class="sima-stat sima-stat--purple d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--purple">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="sima-stat__label">Dosen Aktif</div>
            <span class="sima-stat__value" data-count="{{ $totalDosen ?? 8 }}">{{ $totalDosen ?? 8 }}</span>
            <div class="sima-stat__delta sima-stat__delta--up">
                <i class="fas fa-arrow-up"></i> Rata-rata kehadiran 84%
            </div>
        </a>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════
     ROW 2 — Absensi Hari Ini + Monitoring Kehadiran
     ═══════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- ── ABSENSI HARI INI ──────────────────────── --}}
    <div class="col-md-7 sima-fade sima-fade--5">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Absensi Hari Ini</h5>
                    <div class="sima-card__subtitle">{{ now()->translatedFormat('l, d F Y') }}</div>
                </div>
                <a href="{{ route('jurusan.jadwal.page') }}" class="sima-card__action">
                    <i class="fas fa-calendar-alt"></i> Lihat Jadwal
                </a>
            </div>
            <div style="overflow-x:auto">
                @php
                $classes = [
                    ['Matematika Lanjut',  '08:00–10:00', 'Gd.4 R.201', '18/20', 'selesai'],
                    ['Struktur Data',      '10:00–12:00', 'Gd.4 R.201', '16/20', 'selesai'],
                    ['Pemrograman Web',    '13:00–15:00', 'Lab 3',      '—',     'berlangsung'],
                    ['Basis Data',         '15:00–17:00', 'Gd.2 R.105', '—',     'terjadwal'],
                ];
                @endphp
                <table class="sima-table">
                    <thead>
                        <tr>
                            <th>Mata Kuliah</th>
                            <th>Jam</th>
                            <th>Ruang</th>
                            <th>Hadir</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classes as $c)
                        <tr>
                            <td style="font-weight:500">{{ $c[0] }}</td>
                            <td style="font-family:var(--f-mono);font-size:12px;color:var(--c-text-3);white-space:nowrap">{{ $c[1] }}</td>
                            <td style="font-size:12px;color:var(--c-text-2)">{{ $c[2] }}</td>
                            <td style="font-family:var(--f-mono);font-size:13px;font-weight:600">{{ $c[3] }}</td>
                            <td>
                                @if($c[4] === 'selesai')
                                    <span class="sima-badge sima-badge--green">
                                        <i class="fas fa-check-circle"></i> Selesai
                                    </span>
                                @elseif($c[4] === 'berlangsung')
                                    <span class="sima-badge sima-badge--blue" style="animation:pulse 2s infinite">
                                        <i class="fas fa-circle" style="font-size:7px"></i> Berlangsung
                                    </span>
                                @else
                                    <span class="sima-badge sima-badge--grey">
                                        <i class="fas fa-clock"></i> Terjadwal
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── MONITORING KEHADIRAN ──────────────────── --}}
    <div class="col-md-5 sima-fade sima-fade--6">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Monitoring Kehadiran</h5>
                    <div class="sima-card__subtitle">Semester ini · Mahasiswa kritis di bawah 80%</div>
                </div>
                <a href="{{ route('jurusan.mahasiswa.page') }}" class="sima-card__action">Semua</a>
            </div>
            <div class="sima-card__body">
                @php
                $students = [
                    ['Ahmad R.',   75],
                    ['Bui Thi L.', 78],
                    ['Park J.',    82],
                    ['John D.',    85],
                    ['Yuki T.',    90],
                    ['Li X.M.',    94],
                ];
                @endphp

                @foreach($students as $s)
                @php
                $color = $s[1] < 80 ? '#DC2626' : ($s[1] < 85 ? '#D97706' : '#059669');
                $bg    = $s[1] < 80 ? '#FEF2F2' : ($s[1] < 85 ? '#FFFBEB' : '#ECFDF5');
                $init  = strtoupper(substr($s[0], 0, 1)) . strtoupper(substr(explode(' ', $s[0])[1] ?? 'X', 0, 1));
                @endphp
                <div style="margin-bottom:14px">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:6px">
                        <div class="sima-avatar"
                             style="width:30px;height:30px;font-size:10px;border-radius:8px;
                                    background:{{ $bg }};color:{{ $color }}">
                            {{ $init }}
                        </div>
                        <div style="flex:1;font-size:13px;font-weight:500;color:var(--c-text-1)">{{ $s[0] }}</div>
                        <div style="font-family:var(--f-mono);font-size:13px;font-weight:600;color:{{ $color }}">
                            {{ $s[1] }}%
                        </div>
                    </div>
                    <div class="sima-prog">
                        <div class="sima-prog__bar" data-w="{{ $s[1] }}" style="background:{{ $color }}"></div>
                    </div>
                </div>
                @endforeach

                <div style="padding:12px;background:var(--c-red-lt);border-radius:10px;
                            border:1px solid rgba(220,38,38,.2);margin-top:6px">
                    <div style="font-size:12px;color:var(--c-red);font-weight:500">
                        <i class="fas fa-exclamation-triangle"></i>
                        2 mahasiswa di bawah batas minimum (80%)
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════
     ROW 3 — Jadwal Minggu Ini + Pengumuman
     ═══════════════════════════════════════════════════ --}}
<div class="row g-3">

    {{-- ── JADWAL MINGGU INI ────────────────────── --}}
    <div class="col-md-6 sima-fade sima-fade--5">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Jadwal Minggu Ini</h5>
                    <div class="sima-card__subtitle">{{ now()->startOfWeek()->format('d') }}–{{ now()->endOfWeek()->format('d F Y') }}</div>
                </div>
                <a href="{{ route('jurusan.jadwal.page') }}" class="sima-card__action">
                    <i class="fas fa-plus"></i> Tambah Jadwal
                </a>
            </div>
            <div class="sima-card__body">
                @php
                $week = [
                    ['Senin',  'Matematika Lanjut', '08:00', 'Gd.4 R.201'],
                    ['Selasa', 'Struktur Data',      '10:00', 'Gd.4 R.201'],
                    ['Rabu',   'Pemrograman Web',    '13:00', 'Lab 3'],
                    ['Kamis',  'Basis Data',         '08:00', 'Gd.2 R.105'],
                    ['Jumat',  'Algoritma',          '09:00', 'Gd.3 R.302'],
                    ['Sabtu',  'Seminar',            '10:00', 'Aula Utama'],
                ];
                $dayColors = ['#2563EB','#0D9488','#7C3AED','#D97706','#DC2626','#059669'];
                @endphp

                @foreach($week as $i => $w)
                <div style="display:flex;align-items:center;gap:12px;padding:10px 0;
                            border-bottom:1px solid var(--c-border-soft)">
                    <div style="min-width:58px;padding:4px 0;text-align:center;
                                background:{{ $dayColors[$i] }}18;border-radius:8px;
                                border:1px solid {{ $dayColors[$i] }}30">
                        <div style="font-size:9px;font-weight:700;letter-spacing:.08em;
                                    text-transform:uppercase;color:{{ $dayColors[$i] }}">
                            {{ $w[0] }}
                        </div>
                    </div>
                    <div style="flex:1">
                        <div style="font-size:13px;font-weight:500;color:var(--c-text-1)">{{ $w[1] }}</div>
                        <div style="font-size:11px;color:var(--c-text-3)">
                            <i class="fas fa-map-marker-alt"></i> {{ $w[3] }}
                        </div>
                    </div>
                    <div style="font-family:var(--f-mono);font-size:11px;color:var(--c-text-3)">{{ $w[2] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── PENGUMUMAN JURUSAN ───────────────────── --}}
    <div class="col-md-6 sima-fade sima-fade--6">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Pengumuman Jurusan</h5>
                    <div class="sima-card__subtitle">Draft &amp; terkirim</div>
                </div>
                <a href="#" class="sima-card__action">
                    <i class="fas fa-plus"></i> Buat Baru
                </a>
            </div>
            <div class="sima-card__body">

                <div class="sima-announce">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px">
                        <div class="sima-announce__title">📅 Jadwal UTS Semester Genap</div>
                        <span class="sima-badge sima-badge--green">Published</span>
                    </div>
                    <div class="sima-announce__body">
                        UTS akan dilaksanakan 10–21 Maret 2026. Kartu ujian dapat diambil mulai 3 Maret.
                    </div>
                    <div class="sima-announce__meta">
                        <i class="fas fa-users"></i> 40 mahasiswa
                        <span>· 1 hari lalu</span>
                    </div>
                </div>

                <div class="sima-announce" style="background:var(--c-amber-lt);border-color:rgba(217,119,6,.2)">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px">
                        <div class="sima-announce__title">📋 Bimbingan Skripsi Gelombang 2</div>
                        <span class="sima-badge sima-badge--amber">Draft</span>
                    </div>
                    <div class="sima-announce__body">
                        Pendaftaran bimbingan skripsi gelombang 2 akan dibuka...
                    </div>
                    <div class="sima-announce__meta">
                        <i class="fas fa-edit"></i> Draft
                        <span>· Belum dipublish</span>
                        <a href="#" style="color:var(--c-blue);margin-left:auto">Edit →</a>
                    </div>
                </div>

                <div class="sima-announce" style="background:var(--c-amber-lt);border-color:rgba(217,119,6,.2)">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px">
                        <div class="sima-announce__title">🎓 Wisuda April 2026</div>
                        <span class="sima-badge sima-badge--amber">Draft</span>
                    </div>
                    <div class="sima-announce__body">
                        Informasi pendaftaran wisuda untuk lulusan semester ini...
                    </div>
                    <div class="sima-announce__meta">
                        <i class="fas fa-edit"></i> Draft
                        <span>· Belum dipublish</span>
                        <a href="#" style="color:var(--c-blue);margin-left:auto">Edit →</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection

@section('page_js')
<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50%       { opacity: .6; }
}
</style>
@endsection