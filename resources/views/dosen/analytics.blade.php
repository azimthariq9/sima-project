@extends('layouts.sima')

@section('page_title',    'Analytics')
@section('page_section',  'DOSEN')
@section('page_subtitle', 'Ringkasan statistik kehadiran mahasiswa')

@section('main_content')

{{-- ── STAT CARDS ─────────────────────────────────────────── --}}
<div class="row g-3 sima-fade" style="margin-bottom:16px">
    <div class="col-sm-4">
        <div class="sima-stat">
            <div class="sima-stat__icon" style="background:rgba(99,102,241,.1);color:#6366f1">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="sima-stat__body">
                <div class="sima-stat__label">Total Kelas Diajar</div>
                <div class="sima-stat__value">{{ $totalKelas }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="sima-stat">
            <div class="sima-stat__icon" style="background:rgba(5,150,105,.1);color:#059669">
                <i class="fas fa-users"></i>
            </div>
            <div class="sima-stat__body">
                <div class="sima-stat__label">Total Mahasiswa</div>
                <div class="sima-stat__value">{{ $totalMahasiswa }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="sima-stat">
            <div class="sima-stat__icon" style="background:rgba(245,158,11,.1);color:#d97706">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="sima-stat__body">
                <div class="sima-stat__label">Sesi Sudah Diisi</div>
                <div class="sima-stat__value">{{ $totalSesiTerisi }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ── REKAP PER KELAS ─────────────────────────────────────── --}}
<div class="sima-card sima-fade sima-fade--1">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Rekap Kehadiran per Kelas</h5>
            <div class="sima-card__subtitle">Berdasarkan data absensi yang telah diisi di Detail Jadwal</div>
        </div>
    </div>

    @if($rekapPerKelas->isEmpty())
        <div class="sima-card__body" style="text-align:center;padding:48px 20px;color:var(--c-text-3)">
            <i class="fas fa-chart-bar" style="font-size:36px;opacity:.3;display:block;margin-bottom:12px"></i>
            <div style="font-size:14px;font-weight:500;color:var(--c-text-2)">Belum ada data kehadiran</div>
            <div style="font-size:12.5px;margin-top:4px">
                Input kehadiran mahasiswa melalui menu <a href="{{ route('dosen.jadwal.index') }}"
                style="color:var(--c-accent)">Jadwal → Detail</a>.
            </div>
        </div>
    @else
        <div class="sima-card__body" style="padding:0">
            <table class="sima-table">
                <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Mata Kuliah</th>
                        <th>Jadwal</th>
                        <th style="text-align:center">Mhs</th>
                        <th style="text-align:center">Sesi Terisi</th>
                        <th style="text-align:center;width:120px">% Hadir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapPerKelas as $r)
                    @php
                        $pct    = $r->pct_hadir ?? 0;
                        $pctClr = $pct >= 75 ? '#22c55e' : ($pct >= 50 ? '#f59e0b' : '#ef4444');
                        $total  = $r->totalSesi ?? '—';
                        $terisi = $r->sesi_terisi ?? 0;
                    @endphp
                    <tr>
                        <td>
                            <span style="font-family:var(--f-mono);font-size:12px;font-weight:700;
                                         color:var(--c-accent);background:rgba(var(--c-accent-rgb),.08);
                                         padding:3px 8px;border-radius:6px">
                                {{ $r->kodeKelas }}
                            </span>
                        </td>
                        <td style="font-weight:600;font-size:13.5px">{{ $r->namaMk }}</td>
                        <td style="font-size:12.5px;color:var(--c-text-2)">
                            {{ $r->hari }} · {{ $r->jam }}
                        </td>
                        <td style="text-align:center;font-weight:600">{{ $r->total_mahasiswa ?? '—' }}</td>
                        <td style="text-align:center">
                            <span style="font-size:12.5px;font-weight:600;color:var(--c-text-1)">{{ $terisi }}</span>
                            <span style="font-size:11.5px;color:var(--c-text-3)">/ {{ $total }}</span>
                        </td>
                        <td style="text-align:center">
                            @if($r->sesi_terisi > 0)
                                <div style="display:flex;align-items:center;gap:8px;justify-content:center">
                                    <div style="flex:1;height:6px;background:var(--c-bg);border-radius:99px;overflow:hidden;max-width:56px">
                                        <div style="height:100%;width:{{ min($pct,100) }}%;background:{{ $pctClr }};border-radius:99px"></div>
                                    </div>
                                    <span style="font-size:12px;font-weight:700;color:{{ $pctClr }};min-width:36px">{{ $pct }}%</span>
                                </div>
                            @else
                                <span style="font-size:12px;color:var(--c-text-3)">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="sima-alert sima-alert--blue sima-fade sima-fade--2" style="margin-top:12px">
    <i class="fas fa-circle-info sima-alert__icon"></i>
    <div class="sima-alert__text" style="font-size:12.5px">
        Untuk mengisi absensi, buka <strong>Jadwal → Detail</strong> dan input status kehadiran per sesi untuk setiap mahasiswa.
    </div>
</div>

@endsection
