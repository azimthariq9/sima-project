@extends('layouts.sima')

@section('page_title',   $kelasInfo->namaMk ?? 'Detail Kehadiran')
@section('page_section', 'Mahasiswa')
@section('page_subtitle','Detail absensi per sesi')

@section('main_content')

@php
$statusMap = [
    'present'     => ['label' => 'Hadir',        'cls' => 'sima-badge--green',  'ico' => 'fa-circle-check'],
    'excused'     => ['label' => 'Izin',          'cls' => 'sima-badge--amber',  'ico' => 'fa-clock'],
    'absent'      => ['label' => 'Tidak Hadir',   'cls' => 'sima-badge--red',    'ico' => 'fa-circle-xmark'],
    'belum hadir' => ['label' => 'Belum Dicatat', 'cls' => 'sima-badge--grey',   'ico' => 'fa-minus'],
];

$totalSesi = max(1, $kelasInfo->total_sesi ?? 1);
$hadir     = $sesiList->where('status', 'present')->count();
$izin      = $sesiList->where('status', 'excused')->count();
$absen     = $sesiList->where('status', 'absent')->count();
$pct       = round(($hadir + $izin) / $totalSesi * 100);
$barColor  = $pct >= 75 ? '#22c55e' : ($pct >= 50 ? '#f59e0b' : '#ef4444');

$badgeMap = ['kln' => 'sima-badge--teal', 'bipa' => 'sima-badge--purple', 'perkuliahan' => 'sima-badge--blue'];
$tipe     = match($kelasInfo->jurusan_id ?? 0) { 1 => 'kln', 2 => 'bipa', default => 'perkuliahan' };
$tipeCls  = $badgeMap[$tipe];
@endphp

{{-- Back + info header --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:16px" class="sima-fade">
    <a href="{{ route('mahasiswa.kehadiran') }}" class="sima-btn sima-btn--sm sima-btn--outline">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
    <div>
        <div style="font-size:15px;font-weight:700;color:var(--c-text-1)">{{ $kelasInfo->namaMk }}</div>
        <div style="font-size:12px;color:var(--c-text-3)">
            {{ $kelasInfo->kodeKelas }} · {{ $kelasInfo->dosen ?? '-' }}
            <span class="sima-badge {{ $tipeCls }}" style="margin-left:6px;font-size:10px">{{ strtoupper($tipe) }}</span>
        </div>
    </div>
</div>

<div class="row g-3">

    {{-- Stat cards --}}
    <div class="col-12 sima-fade">
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px">
            @foreach([
                ['Hadir',        $hadir, '#22c55e', '#ecfdf5', 'fa-circle-check'],
                ['Izin',         $izin,  '#f59e0b', '#fffbeb', 'fa-clock'],
                ['Tidak Hadir',  $absen, '#ef4444', '#fef2f2', 'fa-circle-xmark'],
                ['Persentase',   $pct.'%', $barColor, '#f8fafc', 'fa-chart-pie'],
            ] as [$label, $val, $color, $bg, $ico])
            <div style="background:{{ $bg }};border:1px solid {{ $color }}22;border-radius:12px;padding:14px 16px">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                    <i class="fas {{ $ico }}" style="color:{{ $color }};font-size:14px"></i>
                    <span style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:{{ $color }}">{{ $label }}</span>
                </div>
                <div style="font-size:22px;font-weight:800;color:{{ $color }}">{{ $val }}</div>
                @if($label === 'Persentase')
                    <div style="height:4px;background:rgba(0,0,0,.07);border-radius:99px;margin-top:8px;overflow:hidden">
                        <div style="height:100%;width:{{ $pct }}%;background:{{ $color }};border-radius:99px"></div>
                    </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Tabel sesi --}}
    <div class="col-12 sima-fade sima-fade--1">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Rekap per Sesi</h5>
                    <div class="sima-card__subtitle">Total {{ $totalSesi }} sesi · {{ $sesiList->count() }} tercatat</div>
                </div>
            </div>

            @if($sesiList->isEmpty())
                <div class="sima-card__body" style="text-align:center;padding:40px;color:var(--c-text-3)">
                    <i class="fas fa-clipboard-list" style="font-size:32px;opacity:.3;display:block;margin-bottom:10px"></i>
                    <div style="font-size:13.5px">Belum ada data kehadiran tercatat</div>
                </div>
            @else
                <div class="sima-card__body" style="padding:0">
                    <table class="sima-table">
                        <thead>
                            <tr>
                                <th style="width:80px">Sesi</th>
                                <th style="width:120px">Tanggal</th>
                                <th>Hari &amp; Jam</th>
                                <th>Ruangan</th>
                                <th style="width:130px">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sesiList as $sesi)
                                @php
                                    $st   = $sesi->status ?? 'belum hadir';
                                    $info = $statusMap[$st] ?? $statusMap['belum hadir'];
                                    $tgl  = $sesi->tglSesi ? \Carbon\Carbon::parse($sesi->tglSesi)->format('d M Y') : '-';
                                    $jam  = $sesi->jam ?? '-';
                                @endphp
                                <tr>
                                    <td>
                                        <span style="font-family:var(--f-mono);font-size:12px;font-weight:700;color:var(--c-text-3)">
                                            #{{ $sesi->sesi }}
                                        </span>
                                    </td>
                                    <td style="font-size:12.5px">{{ $tgl }}</td>
                                    <td>
                                        <div style="font-weight:600;font-size:13px;color:var(--c-text-1)">{{ $sesi->hari ?? '-' }}</div>
                                        <div style="font-size:11.5px;color:var(--c-text-3);font-family:var(--f-mono)">{{ $jam }}</div>
                                    </td>
                                    <td style="font-size:13px">{{ $sesi->ruangan ?? '-' }}</td>
                                    <td>
                                        <span class="sima-badge {{ $info['cls'] }}">
                                            <i class="fas {{ $info['ico'] }}"></i> {{ $info['label'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>

@endsection
