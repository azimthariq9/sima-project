@extends('layouts.sima')

@section('page_title',   'Analitik Kehadiran')
@section('page_section', 'Mahasiswa')
@section('page_subtitle','Rekap dan statistik kehadiran Anda')

@section('main_content')

{{-- ═══ ROW 1 — Stat Cards ═══ --}}
<div class="row g-3 mb-4">

    <div class="col-6 col-md-3 sima-fade sima-fade--1">
        <div class="sima-stat sima-stat--green">
            <div class="sima-stat__icon sima-stat__icon--green"><i class="fas fa-circle-check"></i></div>
            <div class="sima-stat__label">Total Hadir</div>
            <span class="sima-stat__value">{{ $totalHadir }}</span>
        </div>
    </div>

    <div class="col-6 col-md-3 sima-fade sima-fade--2">
        <div class="sima-stat sima-stat--amber">
            <div class="sima-stat__icon sima-stat__icon--amber"><i class="fas fa-clock-rotate-left"></i></div>
            <div class="sima-stat__label">Izin / Excused</div>
            <span class="sima-stat__value">{{ $totalTelat }}</span>
        </div>
    </div>

    <div class="col-6 col-md-3 sima-fade sima-fade--3">
        <div class="sima-stat sima-stat--red">
            <div class="sima-stat__icon sima-stat__icon--red"><i class="fas fa-circle-xmark"></i></div>
            <div class="sima-stat__label">Alpha</div>
            <span class="sima-stat__value">{{ $totalAlpha }}</span>
        </div>
    </div>

    <div class="col-6 col-md-3 sima-fade sima-fade--4">
        <div class="sima-stat sima-stat--blue">
            <div class="sima-stat__icon sima-stat__icon--blue"><i class="fas fa-chart-pie"></i></div>
            <div class="sima-stat__label">% Kehadiran</div>
            <span class="sima-stat__value">{{ $persentaseHadir }}%</span>
        </div>
    </div>

</div>

{{-- ═══ ROW 2 — Rekap per Mata Kuliah ═══ --}}
<div class="row g-3">

    <div class="col-lg-7 sima-fade sima-fade--1">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Rekap per Mata Kuliah</h5>
                    <div class="sima-card__subtitle">Berdasarkan data kehadiran tercatat</div>
                </div>
            </div>
            <div class="sima-card__body" style="padding:0">

                @forelse($rekapPerMk as $mk)
                @php
                    $total = max($mk['total'], 1);
                    $pct   = round(($mk['hadir'] / $total) * 100);
                    $color = $pct >= 75 ? 'var(--c-green)' : ($pct >= 50 ? 'var(--c-amber)' : 'var(--c-red)');
                @endphp
                <div style="display:flex;align-items:center;gap:14px;padding:14px 20px;border-bottom:1px solid var(--c-border-soft)">
                    <div style="min-width:0;flex:1">
                        <div style="font-size:13px;font-weight:600;color:var(--c-text-1);margin-bottom:5px">
                            {{ $mk['namaMk'] }}
                        </div>
                        <div style="height:6px;background:rgba(0,0,0,.06);border-radius:999px;overflow:hidden">
                            <div style="height:100%;width:{{ $pct }}%;background:{{ $color }};border-radius:999px;transition:width .5s ease"></div>
                        </div>
                        <div style="font-size:11px;color:var(--c-text-3);margin-top:4px">
                            <i class="fas fa-circle-check" style="color:var(--c-green)"></i> {{ $mk['hadir'] }} hadir
                            &nbsp;·&nbsp;
                            <i class="fas fa-clock" style="color:var(--c-amber)"></i> {{ $mk['telat'] }} izin
                            &nbsp;·&nbsp;
                            <i class="fas fa-circle-xmark" style="color:var(--c-red)"></i> {{ $mk['alpha'] }} alpha
                            &nbsp;·&nbsp; {{ $mk['total'] }} total sesi
                        </div>
                    </div>
                    <div style="font-family:var(--f-mono);font-size:15px;font-weight:700;color:{{ $color }};min-width:44px;text-align:right">
                        {{ $pct }}%
                    </div>
                </div>
                @empty
                <div style="padding:48px 0;text-align:center">
                    <div style="font-size:32px;margin-bottom:10px;opacity:.3">📋</div>
                    <div style="font-size:13px;color:var(--c-text-3)">Belum ada data kehadiran tercatat</div>
                </div>
                @endforelse

            </div>
        </div>
    </div>

    {{-- Riwayat 20 sesi terakhir --}}
    <div class="col-lg-5 sima-fade sima-fade--2">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Riwayat Sesi</h5>
                    <div class="sima-card__subtitle">20 kehadiran terakhir</div>
                </div>
            </div>
            <div class="sima-card__body" style="padding:0">

                @forelse($history->take(20) as $rec)
                @php
                    $smap = [
                        'present' => ['label' => 'Hadir',   'cls' => 'green',  'ico' => 'fa-circle-check'],
                        'excused' => ['label' => 'Izin',    'cls' => 'amber',  'ico' => 'fa-clock'],
                        'absent'  => ['label' => 'Alpha',   'cls' => 'red',    'ico' => 'fa-circle-xmark'],
                    ];
                    $s = $smap[$rec->status] ?? $smap['present'];
                @endphp
                <div style="display:flex;align-items:center;gap:12px;padding:10px 18px;border-bottom:1px solid var(--c-border-soft)">
                    <div style="width:28px;height:28px;border-radius:7px;display:grid;place-items:center;font-size:11px;
                                background:var(--c-{{ $s['cls'] }}-lt);color:var(--c-{{ $s['cls'] }});flex-shrink:0">
                        <i class="fas {{ $s['ico'] }}"></i>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:12.5px;font-weight:500;color:var(--c-text-1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            {{ $rec->namaMk }}
                        </div>
                        <div style="font-size:10.5px;color:var(--c-text-3)">
                            Sesi ke-{{ $rec->meeting_number }}
                            @if($rec->checkin_time)
                                · {{ \Carbon\Carbon::parse($rec->checkin_time)->format('d M Y') }}
                            @endif
                        </div>
                    </div>
                    <span class="sima-badge sima-badge--{{ $s['cls'] }}" style="flex-shrink:0">
                        {{ $s['label'] }}
                    </span>
                </div>
                @empty
                <div style="padding:48px 0;text-align:center">
                    <div style="font-size:32px;margin-bottom:10px;opacity:.3">📭</div>
                    <div style="font-size:13px;color:var(--c-text-3)">Belum ada riwayat kehadiran</div>
                </div>
                @endforelse

            </div>
        </div>
    </div>

</div>

@endsection
