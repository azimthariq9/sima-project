@extends('layouts.sima')

@section('page_title', 'Dashboard BIPA')
@section('page_subtitle', 'Bahasa Indonesia bagi Penutur Asing — Pusat Bahasa Gunadarma')

@section('main_content')

{{-- ═══════════════════════════════════════════ --}}
{{-- STAT ROW                                     --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row">

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--1">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--teal"></div>
            <div class="sima-stat__icon sima-stat__icon--teal">
                <i class="fas fa-language"></i>
            </div>
            <div class="sima-stat__label">Peserta BIPA Aktif</div>
            <div class="sima-stat__value" data-count="56">56</div>
            <div class="sima-stat__delta sima-stat__delta--up">
                <i class="fas fa-arrow-up"></i> +12 semester ini
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--2">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--blue"></div>
            <div class="sima-stat__icon sima-stat__icon--blue">
                <i class="fas fa-book-open"></i>
            </div>
            <div class="sima-stat__label">Kelas Aktif</div>
            <div class="sima-stat__value" data-count="4">4</div>
            <div class="sima-stat__delta sima-stat__delta--flat">A1 · A2 · B1 · B2</div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--3">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--emerald"></div>
            <div class="sima-stat__icon sima-stat__icon--emerald">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="sima-stat__label">Absensi Hari Ini</div>
            <div class="sima-stat__value" data-count="42">42</div>
            <div class="sima-stat__delta sima-stat__delta--up">
                <i class="fas fa-arrow-up"></i> 75% kehadiran
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--4">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--amber"></div>
            <div class="sima-stat__icon sima-stat__icon--amber">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="sima-stat__label">Rata-rata Skor Ujian</div>
            <div class="sima-stat__value" data-count="78">78</div>
            <div class="sima-stat__delta sima-stat__delta--up">
                <i class="fas fa-arrow-up"></i> +4 poin dari bulan lalu
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- LEVEL CARDS                                  --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row" style="margin-bottom:4px">
    <div class="col-12 sima-fade sima-fade--4">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px">
            <h5 style="font-family:var(--font-display);font-size:18px;font-weight:600;margin:0;color:var(--sima-text-primary)">Status Per Level</h5>
            <div style="flex:1;height:1px;background:var(--sima-border-soft)"></div>
        </div>
    </div>
</div>

<div class="row">

    @php
    $levels = [
        ['A1', 'Pemula',        16, 18, '#0D9488', '#F0FDFA', 'Dasar percakapan'],
        ['A2', 'Dasar',         14, 15, '#2563EB', '#EFF6FF', 'Komunikasi harian'],
        ['B1', 'Menengah',      15, 16, '#7C3AED', '#F5F3FF', 'Topik akademis'],
        ['B2', 'Atas-Menengah', 11, 12, '#D97706', '#FFFBEB', 'Menulis ilmiah'],
    ];
    @endphp

    @foreach($levels as $l)
    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--{{ $loop->index + 5 }}">
        <div class="sima-card h-100" style="border-top:3px solid {{ $l[4] }}">
            <div class="sima-card__body">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px">
                    <div>
                        <div style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;background:{{ $l[5] }};border-radius:12px;font-family:var(--font-display);font-size:18px;font-weight:700;color:{{ $l[4] }};margin-bottom:8px">
                            {{ $l[0] }}
                        </div>
                        <div style="font-size:13px;font-weight:600;color:var(--sima-text-primary)">{{ $l[0] }} — {{ $l[1] }}</div>
                        <div style="font-size:11px;color:var(--sima-text-muted)">{{ $l[6] }}</div>
                    </div>
                </div>

                <div style="display:flex;justify-content:space-between;margin-bottom:8px">
                    <span style="font-size:12px;color:var(--sima-text-secondary)">Peserta</span>
                    <span style="font-family:var(--font-mono);font-size:12px;font-weight:600;color:{{ $l[4] }}">{{ $l[2] }}/{{ $l[3] }}</span>
                </div>
                <div class="sima-progress" style="margin-bottom:12px">
                    <div class="sima-progress__bar" style="width:{{ round($l[2]/$l[3]*100) }}%;background:{{ $l[4] }}"></div>
                </div>

                <div style="display:flex;gap:8px">
                    <a href="#" style="flex:1;text-align:center;padding:7px;background:{{ $l[5] }};color:{{ $l[4] }};border-radius:8px;font-size:12px;font-weight:500;text-decoration:none">
                        <i class="fas fa-clipboard"></i> Absensi
                    </a>
                    <a href="#" style="flex:1;text-align:center;padding:7px;background:var(--sima-surface-2);color:var(--sima-text-secondary);border-radius:8px;font-size:12px;font-weight:500;text-decoration:none;border:1px solid var(--sima-border-soft)">
                        <i class="fas fa-chart-bar"></i> Laporan
                    </a>
                </div>

            </div>
        </div>
    </div>
    @endforeach

</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- MAIN CONTENT ROW                             --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row">

    {{-- ── Schedule BIPA ──────────────────── --}}
    <div class="col-md-6 mb-4 sima-fade sima-fade--5">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Jadwal BIPA Hari Ini</h5>
                    <div class="sima-card__subtitle">{{ date('l, d F Y') }}</div>
                </div>
                <a href="#" class="sima-card__action"><i class="fas fa-plus"></i> Input Jadwal</a>
            </div>
            <div class="sima-card__body">

                @php
                $schedule = [
                    ['A1', 'Pemula',        '08:00–10:00', 'Ruang B.01', 16, '#0D9488', 'selesai'],
                    ['B1', 'Menengah',      '10:00–12:00', 'Ruang B.02', 15, '#7C3AED', 'selesai'],
                    ['A2', 'Dasar',         '13:00–15:00', 'Ruang B.01', 14, '#2563EB', 'berlangsung'],
                    ['B2', 'Atas-Menengah', '15:00–17:00', 'Ruang B.03', 11, '#D97706', 'terjadwal'],
                ];
                @endphp

                @foreach($schedule as $s)
                <div style="display:flex;gap:14px;align-items:center;padding:14px 0;border-bottom:1px solid var(--sima-border-soft)">
                    <div style="width:40px;height:40px;border-radius:10px;background:{{ $s[5] }}18;display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-size:14px;font-weight:700;color:{{ $s[5] }};flex-shrink:0">
                        {{ $s[0] }}
                    </div>
                    <div style="flex:1">
                        <div style="font-size:13px;font-weight:500;color:var(--sima-text-primary)">BIPA {{ $s[0] }} — {{ $s[1] }}</div>
                        <div style="font-size:11px;color:var(--sima-text-muted)">
                            <i class="fas fa-clock"></i> {{ $s[2] }}
                            &nbsp;·&nbsp;
                            <i class="fas fa-map-marker-alt"></i> {{ $s[3] }}
                        </div>
                    </div>
                    <div style="text-align:right">
                        <div style="font-family:var(--font-mono);font-size:12px;font-weight:600;color:var(--sima-text-primary);margin-bottom:3px">{{ $s[4] }} peserta</div>
                        @if($s[6]=='selesai')
                            <span class="sima-badge sima-badge--emerald" style="font-size:10px"><i class="fas fa-check"></i> Selesai</span>
                        @elseif($s[6]=='berlangsung')
                            <span class="sima-badge sima-badge--blue" style="font-size:10px"><i class="fas fa-circle" style="font-size:6px"></i> Live</span>
                        @else
                            <span class="sima-badge sima-badge--grey" style="font-size:10px"><i class="fas fa-clock"></i> Terjadwal</span>
                        @endif
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>

    {{-- ── Top Students & Progress ─────────── --}}
    <div class="col-md-6 mb-4 sima-fade sima-fade--6">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Progres Peserta Unggulan</h5>
                    <div class="sima-card__subtitle">Semester Genap 2025/2026</div>
                </div>
            </div>
            <div class="sima-card__body">

                @php
                $top = [
                    ['Park Jisoo',       'KR', 'B2', 96, '#D97706', '🇰🇷'],
                    ['Nguyen Thi Huong', 'VN', 'B1', 92, '#7C3AED', '🇻🇳'],
                    ['Yuki Tanaka',       'JP', 'B1', 89, '#7C3AED', '🇯🇵'],
                    ['Ahmad Rahimov',     'TJ', 'A2', 85, '#2563EB', '🇹🇯'],
                    ['Amara Diallo',      'SN', 'A1', 81, '#0D9488', '🇸🇳'],
                    ['Li Xiao Ming',      'CN', 'A2', 79, '#2563EB', '🇨🇳'],
                ];
                @endphp

                @foreach($top as $i => $s)
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px">
                    <div style="font-family:var(--font-display);font-size:18px;font-weight:700;color:{{ $i < 3 ? ['#C4973A','#94A3B8','#CD7F32'][$i] : 'var(--sima-border)' }};min-width:24px;text-align:center">
                        {{ $i + 1 }}
                    </div>
                    <div style="font-size:18px">{{ $s[5] }}</div>
                    <div style="flex:1">
                        <div style="display:flex;justify-content:space-between;margin-bottom:5px">
                            <div>
                                <span style="font-size:13px;font-weight:500;color:var(--sima-text-primary)">{{ $s[0] }}</span>
                                <span class="sima-badge" style="margin-left:6px;background:{{ $s[4] }}18;color:{{ $s[4] }};font-size:9px;padding:2px 7px">{{ $s[2] }}</span>
                            </div>
                            <span style="font-family:var(--font-mono);font-size:12px;font-weight:700;color:{{ $s[4] }}">{{ $s[3] }}%</span>
                        </div>
                        <div class="sima-progress">
                            <div class="sima-progress__bar" style="width:{{ $s[3] }}%;background:{{ $s[4] }}"></div>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- BOTTOM: Announcements + Quick Stats         --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row">

    <div class="col-md-8 mb-4 sima-fade sima-fade--7">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Pengumuman BIPA</h5>
                    <div class="sima-card__subtitle">Informasi untuk peserta kelas bahasa</div>
                </div>
                <a href="#" class="sima-card__action"><i class="fas fa-plus"></i> Post Baru</a>
            </div>
            <div class="sima-card__body">

                <div class="sima-announce">
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                        <div class="sima-announce__title">🎉 Pembukaan Kelas BIPA B2 Semester Genap</div>
                        <span class="sima-badge sima-badge--emerald">Published</span>
                    </div>
                    <div class="sima-announce__body">Kelas BIPA tingkat B2 resmi dibuka untuk semester genap. Peserta yang lolos seleksi harap konfirmasi kehadiran sebelum 25 Februari 2026.</div>
                    <div class="sima-announce__meta">
                        <i class="fas fa-users"></i> 11 peserta
                        <span>· 2 hari lalu</span>
                    </div>
                </div>

                <div class="sima-announce">
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                        <div class="sima-announce__title">📝 Ujian Akhir BIPA A1 — 28 Februari</div>
                        <span class="sima-badge sima-badge--emerald">Published</span>
                    </div>
                    <div class="sima-announce__body">Ujian akhir kelas A1 dijadwalkan 28 Februari 2026 pukul 09.00–11.00 di Ruang BIPA B.01. Materi: percakapan, menulis, dan membaca.</div>
                    <div class="sima-announce__meta">
                        <i class="fas fa-users"></i> 16 peserta
                        <span>· 4 hari lalu</span>
                        <span class="sima-badge sima-badge--amber">Penting</span>
                    </div>
                </div>

                <div class="sima-announce" style="background:#FFFBEB;border-color:var(--sima-border)">
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                        <div class="sima-announce__title">🗣️ Kegiatan Percakapan — Cultural Night</div>
                        <span class="sima-badge sima-badge--amber">Draft</span>
                    </div>
                    <div class="sima-announce__body">Kegiatan Cultural Night akan diadakan...</div>
                    <div class="sima-announce__meta">
                        <i class="fas fa-edit"></i> Draft
                        <a href="#" style="color:var(--sima-blue);margin-left:auto">Edit →</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4 sima-fade sima-fade--8">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <h5 class="sima-card__title">Ringkasan Bulan Ini</h5>
            </div>
            <div class="sima-card__body">

                @php
                $summary = [
                    ['Total Sesi Kelas',    '48', 'fas fa-book',          '#2563EB'],
                    ['Sesi Selesai',         '36', 'fas fa-check-circle',   '#059669'],
                    ['Peserta Lulus',         '8',  'fas fa-graduation-cap', '#C4973A'],
                    ['Absensi Rata-rata',    '78%', 'fas fa-chart-pie',      '#0D9488'],
                    ['Materi Upload',        '12',  'fas fa-file-pdf',       '#7C3AED'],
                    ['Tugas Dikumpulkan',   '145',  'fas fa-paper-plane',    '#D97706'],
                ];
                @endphp

                @foreach($summary as $s)
                <div style="display:flex;align-items:center;gap:14px;padding:11px 0;border-bottom:1px solid var(--sima-border-soft)">
                    <div style="width:34px;height:34px;border-radius:9px;background:{{ $s[3] }}18;display:flex;align-items:center;justify-content:center;color:{{ $s[3] }};font-size:14px;flex-shrink:0">
                        <i class="{{ $s[2] }}"></i>
                    </div>
                    <div style="flex:1;font-size:12px;color:var(--sima-text-secondary)">{{ $s[0] }}</div>
                    <div style="font-family:var(--font-display);font-size:18px;font-weight:600;color:var(--sima-text-primary)">{{ $s[1] }}</div>
                </div>
                @endforeach

            </div>
        </div>
    </div>

</div>

@endsection
