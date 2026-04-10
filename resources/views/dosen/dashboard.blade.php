@extends('layouts.sima')

@section('page_title',    'Dashboard')
@section('page_section',  'DOSEN')
@section('page_subtitle', 'Selamat datang, ' . (auth()->user()->dosen->nama ?? auth()->user()->email))

@section('main_content')

{{-- ═══════════════════════════════════════════════════
     JADWAL HARI INI
     ═══════════════════════════════════════════════════ --}}
<div class="row g-3">
    <div class="col-12">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Jadwal Mengajar Hari Ini</h5>
                    <div class="sima-card__subtitle">
                        {{ $hariId }}, {{ now()->translatedFormat('d F Y') }}
                    </div>
                </div>
                <a href="{{ route('dosen.jadwal') }}" class="sima-card__action">
                    <i class="fas fa-calendar-alt"></i> Semua Jadwal
                </a>
            </div>

            @if($jadwalHariIni->isEmpty())
                <div style="padding:48px;text-align:center;">
                    <div style="font-size:40px;margin-bottom:12px;">📭</div>
                    <div style="font-size:14px;color:var(--c-text-3);font-weight:500">
                        Tidak ada jadwal mengajar hari ini
                    </div>
                    <div style="font-size:12px;color:var(--c-text-3);margin-top:4px">
                        Nikmati hari Anda!
                    </div>
                </div>
            @else
                <div class="sima-card__body" style="padding:0">
                    @php
                    $hariColor = [
                        'Senin'  => '#2563EB', 'Selasa' => '#0D9488',
                        'Rabu'   => '#7C3AED', 'Kamis'  => '#D97706',
                        'Jumat'  => '#DC2626', 'Sabtu'  => '#059669',
                    ];
                    @endphp

                    @foreach($jadwalHariIni as $j)
                    @php $color = $hariColor[$j->hari] ?? '#888'; @endphp
                    <a href="{{ route('dosen.jadwal.detail', $j->id) }}"
                       style="display:flex;align-items:center;gap:16px;padding:18px 20px;
                              border-bottom:1px solid var(--c-border-soft);
                              text-decoration:none;transition:background .13s;"
                       onmouseover="this.style.background='var(--c-surface-2)'"
                       onmouseout="this.style.background='transparent'">

                        {{-- Jam badge --}}
                        <div style="min-width:80px;text-align:center;padding:8px 6px;
                                    background:{{ $color }}18;border-radius:10px;
                                    border:1px solid {{ $color }}30;">
                            <div style="font-family:var(--f-mono);font-size:13px;
                                        font-weight:700;color:{{ $color }}">
                                {{ $j->jam }}
                            </div>
                            <div style="font-size:9px;font-weight:600;color:{{ $color }};
                                        text-transform:uppercase;letter-spacing:.06em;margin-top:2px">
                                {{ $j->hari }}
                            </div>
                        </div>

                        {{-- Info --}}
                        <div style="flex:1;min-width:0">
                            <div style="font-size:14px;font-weight:600;color:var(--c-text-1)">
                                {{ $j->matakuliah->namaMk ?? '-' }}
                            </div>
                            <div style="font-size:12px;color:var(--c-text-3);margin-top:3px;
                                        display:flex;align-items:center;gap:12px">
                                <span><i class="fas fa-door-open"></i> Kelas {{ $j->kelas->kodeKelas ?? '-' }}</span>
                                <span><i class="fas fa-map-marker-alt"></i> {{ $j->ruangan }}</span>
                                <span><i class="fas fa-layer-group"></i> {{ $j->totalSesi }} sesi total</span>
                            </div>
                        </div>

                        {{-- CTA --}}
                        <div style="flex-shrink:0">
                            <span class="sima-btn sima-btn--blue sima-btn--sm">
                                <i class="fas fa-clipboard-check"></i> Input Kehadiran
                            </span>
                        </div>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@endsection