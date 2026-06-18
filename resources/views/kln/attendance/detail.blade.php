@extends('layouts.sima')

@section('page_title',    $mahasiswa->nama . ' — Kehadiran')
@section('page_section',  'KEHADIRAN')
@section('page_subtitle', 'Detail kehadiran per matakuliah')

@section('main_content')

{{-- ── BACK ─────────────────────────────────────────── --}}
<div class="mb-3">
    <a href="{{ route('kln.attendance') }}" class="sima-btn sima-btn--outline sima-btn--sm">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

{{-- ── MAHASISWA INFO CARD ──────────────────────────── --}}
<div class="sima-card mb-4" style="padding:20px 24px;">
    <div style="display:flex;align-items:flex-start;gap:16px;flex-wrap:wrap;">
        <div style="width:52px;height:52px;border-radius:50%;background:var(--c-accent);
                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fas fa-user" style="color:#fff;font-size:20px;"></i>
        </div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:18px;font-weight:700;font-family:var(--f-display);">
                {{ $mahasiswa->nama }}
            </div>
            <div style="font-size:13px;color:var(--c-text-3);margin-top:2px;">
                <code>{{ $mahasiswa->npm }}</code>
                @if($mahasiswa->namaJurusan)
                    &middot; {{ $mahasiswa->namaJurusan }}
                @endif
                &middot; {{ $mahasiswa->warNeg ?? '' }}
            </div>
            <div style="font-size:13px;color:var(--c-text-3);">{{ $mahasiswa->email }}</div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            @php
                $active = $mahasiswa->user_status === 'active';
            @endphp
            <span class="sima-badge {{ $active ? 'sima-badge--green' : 'sima-badge--red' }}">
                {{ $active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
    </div>
</div>

{{-- ── OVERALL STAT CARDS ───────────────────────────── --}}
@php
    if ($totalPct === null) {
        $overallBadge = 'sima-badge--amber'; $overallLabel = '—';
    } elseif ($totalPct >= 80) {
        $overallBadge = 'sima-badge--green'; $overallLabel = $totalPct . '%';
    } elseif ($totalPct >= 60) {
        $overallBadge = 'sima-badge--amber'; $overallLabel = $totalPct . '%';
    } else {
        $overallBadge = 'sima-badge--red'; $overallLabel = $totalPct . '%';
    }
@endphp
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--green">
            <div class="sima-stat__icon sima-stat__icon--green"><i class="fas fa-check"></i></div>
            <div class="sima-stat__label">Total Hadir</div>
            <div class="sima-stat__value">{{ $totalHadir }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--red">
            <div class="sima-stat__icon sima-stat__icon--red"><i class="fas fa-times"></i></div>
            <div class="sima-stat__label">Total Absen</div>
            <div class="sima-stat__value">{{ $totalAbsen }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--amber">
            <div class="sima-stat__icon sima-stat__icon--amber"><i class="fas fa-file-alt"></i></div>
            <div class="sima-stat__label">Total Izin</div>
            <div class="sima-stat__value">{{ $totalIzin }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sima-stat {{ $totalPct >= 80 ? 'sima-stat--green' : ($totalPct >= 60 ? 'sima-stat--amber' : 'sima-stat--red') }}"
             style="{{ $totalPct === null ? 'opacity:.6;' : '' }}">
            <div class="sima-stat__icon {{ $totalPct >= 80 ? 'sima-stat__icon--green' : ($totalPct >= 60 ? 'sima-stat__icon--amber' : 'sima-stat__icon--red') }}">
                <i class="fas fa-percent"></i>
            </div>
            <div class="sima-stat__label">Kehadiran Total</div>
            <div class="sima-stat__value">{{ $overallLabel }}</div>
        </div>
    </div>
</div>

{{-- ── PROGRESS BAR GLOBAL ──────────────────────────── --}}
@if($totalPct !== null)
@php $totalBerlangsung = $totalHadir + $totalAbsen + $totalIzin; @endphp
<div class="sima-card mb-4" style="padding:16px 24px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;font-size:13px;">
        <span class="fw-600">Progress Kehadiran Keseluruhan</span>
        <span style="color:var(--c-text-3);">{{ $totalHadir }} / {{ $totalBerlangsung }} sesi berlangsung</span>
    </div>
    <div style="height:10px;background:var(--c-border);border-radius:999px;overflow:hidden;">
        <div style="height:100%;border-radius:999px;transition:width .4s;
            width:{{ $totalPct }}%;
            background:{{ $totalPct >= 80 ? 'var(--c-green)' : ($totalPct >= 60 ? 'var(--c-amber)' : 'var(--c-red)') }};">
        </div>
    </div>
</div>
@endif

{{-- ── PER-JADWAL ACCORDION ─────────────────────────── --}}
<div class="sima-card">
    <div style="padding:16px 24px;border-bottom:1px solid var(--c-border);display:flex;align-items:center;gap:8px;">
        <h5 class="sima-card__title" style="margin:0;">Detail per Matakuliah</h5>
        <span class="sima-badge sima-badge--blue">{{ $jadwalList->count() }} jadwal</span>
    </div>

    @forelse($jadwalList as $j)
    @php
        $berlangsung = $j->hadir + $j->absen + $j->izin;
        if ($j->pct === null) {
            $jBadge = 'sima-badge--amber'; $jLabel = '—';
        } elseif ($j->pct >= 80) {
            $jBadge = 'sima-badge--green'; $jLabel = $j->pct . '%';
        } elseif ($j->pct >= 60) {
            $jBadge = 'sima-badge--amber'; $jLabel = $j->pct . '%';
        } else {
            $jBadge = 'sima-badge--red'; $jLabel = $j->pct . '%';
        }
        $sesiList = $sesiByJadwal[$j->jadwal_id] ?? collect();
    @endphp

    <div style="border-bottom:1px solid var(--c-border);" id="jadwal-block-{{ $j->jadwal_id }}">

        {{-- Row ringkasan jadwal --}}
        <div style="padding:14px 24px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;cursor:pointer;"
             onclick="toggleSesi({{ $j->jadwal_id }})">

            {{-- Info matakuliah --}}
            <div style="flex:1;min-width:180px;">
                <div class="fw-600" style="font-size:14px;">{{ $j->namaMk }}</div>
                <div style="font-size:12px;color:var(--c-text-3);display:flex;gap:10px;flex-wrap:wrap;margin-top:2px;">
                    <span><i class="fas fa-tag me-1"></i><code>{{ $j->kodeMk }}</code></span>
                    <span><i class="fas fa-door-open me-1"></i>{{ $j->kodeKelas }}</span>
                    @if($j->hari)
                    <span><i class="fas fa-calendar me-1"></i>
                        {{ $j->hari }},
                        {{ $j->jam ? str_replace(':', '.', $j->jam) : '' }}
                    </span>
                    @endif
                    @if($j->namaDosen)
                    <span><i class="fas fa-chalkboard-teacher me-1"></i>{{ $j->namaDosen }}</span>
                    @endif
                </div>
            </div>

            {{-- Badge hadir/absen/izin --}}
            <div style="display:flex;gap:6px;align-items:center;flex-shrink:0;">
                <span class="sima-badge sima-badge--green" title="Hadir">
                    <i class="fas fa-check me-1"></i>{{ $j->hadir }}
                </span>
                <span class="sima-badge sima-badge--red" title="Absen">
                    <i class="fas fa-times me-1"></i>{{ $j->absen }}
                </span>
                <span class="sima-badge sima-badge--amber" title="Izin">
                    <i class="fas fa-file-alt me-1"></i>{{ $j->izin }}
                </span>
                @if($j->belum > 0)
                <span class="sima-badge" style="background:var(--c-border);color:var(--c-text-2);" title="Belum hadir">
                    {{ $j->belum }}
                </span>
                @endif
            </div>

            {{-- % badge + progress --}}
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0;min-width:90px;">
                <span class="sima-badge {{ $jBadge }}">{{ $jLabel }}</span>
                @if($j->pct !== null)
                <div style="width:80px;height:4px;background:var(--c-border);border-radius:999px;overflow:hidden;">
                    <div style="height:100%;border-radius:999px;width:{{ min($j->pct,100) }}%;
                        background:{{ $j->pct >= 80 ? 'var(--c-green)' : ($j->pct >= 60 ? 'var(--c-amber)' : 'var(--c-red)') }};">
                    </div>
                </div>
                @endif
            </div>

            {{-- Expand chevron --}}
            <div style="flex-shrink:0;color:var(--c-text-3);">
                <i class="fas fa-chevron-down" id="chev-{{ $j->jadwal_id }}"
                   style="transition:transform .2s;"></i>
            </div>

        </div>{{-- end row ringkasan --}}

        {{-- Per-sesi accordion body --}}
        <div id="sesi-{{ $j->jadwal_id }}" style="display:none;background:var(--c-bg-2);padding:0 24px 16px;">
            @if($sesiList->isNotEmpty())
            <table style="width:100%;border-collapse:collapse;font-size:13px;margin-top:8px;">
                <thead>
                    <tr style="border-bottom:1px solid var(--c-border);">
                        <th style="padding:6px 10px;font-weight:600;color:var(--c-text-3);text-align:center;width:60px;">Sesi</th>
                        <th style="padding:6px 10px;font-weight:600;color:var(--c-text-3);">Tanggal</th>
                        <th style="padding:6px 10px;font-weight:600;color:var(--c-text-3);text-align:center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sesiList as $sesi)
                    @php
                        $statusMap = [
                            'present'     => ['label' => 'Hadir',  'cls' => 'sima-badge--green'],
                            'absent'      => ['label' => 'Absen',  'cls' => 'sima-badge--red'],
                            'excused'     => ['label' => 'Izin',   'cls' => 'sima-badge--amber'],
                            'belum hadir' => ['label' => 'Belum',  'cls' => ''],
                        ];
                        $sm = $statusMap[$sesi->status] ?? ['label' => $sesi->status, 'cls' => ''];
                    @endphp
                    <tr style="border-bottom:1px solid var(--c-border);">
                        <td style="padding:7px 10px;text-align:center;font-weight:600;">{{ $sesi->sesi }}</td>
                        <td style="padding:7px 10px;color:var(--c-text-3);">
                            {{ $sesi->tglSesi ? \Carbon\Carbon::parse($sesi->tglSesi)->isoFormat('D MMM YYYY') : '—' }}
                        </td>
                        <td style="padding:7px 10px;text-align:center;">
                            <span class="sima-badge {{ $sm['cls'] }}"
                                  style="{{ !$sm['cls'] ? 'background:var(--c-border);color:var(--c-text-2);' : '' }}">
                                {{ $sm['label'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div style="padding:12px 0;color:var(--c-text-3);font-size:13px;">
                Belum ada data sesi untuk matakuliah ini.
            </div>
            @endif
        </div>

    </div>{{-- end jadwal-block --}}
    @empty
    <div class="text-center text-muted py-5">
        <i class="fas fa-calendar-times fa-2x d-block mb-2" style="opacity:.3;"></i>
        Mahasiswa ini belum terdaftar di jadwal manapun.
    </div>
    @endforelse
</div>

@endsection

@push('page_js')
<script>
function toggleSesi(jadwalId) {
    const body  = document.getElementById('sesi-' + jadwalId);
    const chev  = document.getElementById('chev-' + jadwalId);
    const open  = body.style.display !== 'none';
    body.style.display = open ? 'none' : '';
    chev.style.transform = open ? '' : 'rotate(180deg)';
}
</script>
@endpush
