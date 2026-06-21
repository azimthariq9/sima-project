@extends('layouts.sima')

@section('page_title',   'Detail Kehadiran')
@section('page_section', 'Mahasiswa')
@section('page_subtitle','Rekap kehadiran per mata kuliah')

@section('main_content')

@php
$tipeOptions = ['semua' => 'Semua', 'perkuliahan' => 'Perkuliahan', 'bipa' => 'BIPA', 'kln' => 'KLN'];
$badgeMap    = ['kln' => 'sima-badge--teal', 'bipa' => 'sima-badge--purple', 'perkuliahan' => 'sima-badge--blue'];
@endphp

<div class="sima-card sima-fade">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Mata Kuliah yang Diikuti</h5>
            <div class="sima-card__subtitle">Klik detail untuk melihat rekap absensi per sesi</div>
        </div>

        {{-- Filter tipe --}}
        <div style="display:flex;gap:6px;flex-wrap:wrap">
            @foreach($tipeOptions as $val => $label)
                <a href="{{ route('mahasiswa.kehadiran', ['tipe' => $val]) }}"
                   class="sima-btn sima-btn--sm {{ $tipe === $val ? '' : 'sima-btn--outline' }}"
                   style="font-size:11.5px">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    @if($kelas->isEmpty())
        <div class="sima-card__body" style="text-align:center;padding:48px 20px;color:var(--c-text-3)">
            <i class="fas fa-inbox" style="font-size:36px;margin-bottom:12px;display:block;opacity:.35"></i>
            <div style="font-size:14px;font-weight:500">Belum ada mata kuliah terdaftar</div>
            <div style="font-size:12.5px;margin-top:4px">Hubungi KLN jika ada pertanyaan mengenai pendaftaran kelas.</div>
        </div>
    @else
        <div class="sima-card__body" style="padding:0">
            <table class="sima-table">
                <thead>
                    <tr>
                        <th>Mata Kuliah</th>
                        <th>Dosen</th>
                        <th>Tipe</th>
                        <th style="width:160px">Kehadiran</th>
                        <th style="width:100px">Status</th>
                        <th style="width:80px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kelas as $mk)
                        @php
                            $totalSesi  = max(1, $mk->total_sesi ?? 1);
                            $hadir      = $mk->hadir  ?? 0;
                            $izin       = $mk->izin   ?? 0;
                            $absen      = $mk->absen  ?? 0;
                            $tercatat   = $hadir + $izin + $absen;
                            $pct        = round(($hadir + $izin) / $totalSesi * 100);
                            $selesai    = $tercatat >= $totalSesi;
                            $barColor   = $pct >= 75 ? '#22c55e' : ($pct >= 50 ? '#f59e0b' : '#ef4444');
                            $tipeCls    = $badgeMap[$mk->tipe_kelas] ?? 'sima-badge--blue';
                            $tipeLabel  = strtoupper($mk->tipe_kelas ?? 'Perkuliahan');
                        @endphp
                        <tr>
                            <td>
                                <div style="font-weight:600;font-size:13.5px;color:var(--c-text-1)">{{ $mk->namaMk }}</div>
                                <div style="font-size:11px;color:var(--c-text-3);font-family:var(--f-mono);margin-top:1px">{{ $mk->kodeKelas }}</div>
                            </td>
                            <td style="font-size:13px">{{ $mk->dosen ?? '-' }}</td>
                            <td><span class="sima-badge {{ $tipeCls }}">{{ $tipeLabel }}</span></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px">
                                    <div style="flex:1;height:5px;background:rgba(0,0,0,.07);border-radius:99px;overflow:hidden">
                                        <div style="height:100%;width:{{ $pct }}%;background:{{ $barColor }};border-radius:99px;transition:width .5s"></div>
                                    </div>
                                    <span style="font-size:12px;font-weight:700;color:{{ $barColor }};min-width:34px">{{ $pct }}%</span>
                                </div>
                                <div style="font-size:11px;color:var(--c-text-3);margin-top:3px">
                                    {{ $hadir }} hadir · {{ $izin }} izin · {{ $absen }} absen · {{ $totalSesi }} sesi
                                </div>
                            </td>
                            <td>
                                @if($selesai)
                                    <span class="sima-badge sima-badge--grey"><i class="fas fa-circle-check"></i> Selesai</span>
                                @else
                                    <span class="sima-badge sima-badge--green"><i class="fas fa-circle" style="font-size:6px"></i> Aktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('mahasiswa.kehadiran.detail', $mk->kelas_id) }}"
                                   class="sima-btn sima-btn--sm sima-btn--outline"
                                   style="font-size:11.5px;padding:4px 12px">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
