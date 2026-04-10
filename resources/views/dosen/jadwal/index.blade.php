@extends('layouts.sima')

@section('page_title',    'Jadwal & Kelas')
@section('page_section',  'DOSEN')
@section('page_subtitle', 'Daftar jadwal mengajar dan kelas yang Anda ampu')

@section('main_content')

@php
$hariColor = [
    'Senin'  => ['color' => '#2563EB', 'bg' => '#EFF6FF'],
    'Selasa' => ['color' => '#0D9488', 'bg' => '#F0FDFA'],
    'Rabu'   => ['color' => '#7C3AED', 'bg' => '#F5F3FF'],
    'Kamis'  => ['color' => '#D97706', 'bg' => '#FFFBEB'],
    'Jumat'  => ['color' => '#DC2626', 'bg' => '#FEF2F2'],
    'Sabtu'  => ['color' => '#059669', 'bg' => '#ECFDF5'],
];
$hariUrut = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$grouped  = $jadwal->groupBy('hari');
@endphp

@if($jadwal->isEmpty())
    <div class="sima-card">
        <div style="padding:64px;text-align:center;">
            <div style="font-size:48px;margin-bottom:16px;">📅</div>
            <div style="font-size:15px;font-weight:600;color:var(--c-text-1)">Belum ada jadwal mengajar</div>
            <div style="font-size:13px;color:var(--c-text-3);margin-top:6px">
                Hubungi admin jurusan untuk informasi jadwal Anda
            </div>
        </div>
    </div>
@else
    {{-- ── STAT RINGKAS ────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="sima-stat sima-stat--blue">
                <div class="sima-stat__icon sima-stat__icon--blue">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="sima-stat__label">Total Jadwal</div>
                <span class="sima-stat__value">{{ $jadwal->count() }}</span>
                <div class="sima-stat__delta sima-stat__delta--flat">Semester ini</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="sima-stat sima-stat--teal">
                <div class="sima-stat__icon sima-stat__icon--teal">
                    <i class="fas fa-door-open"></i>
                </div>
                <div class="sima-stat__label">Kelas Diampu</div>
                <span class="sima-stat__value">{{ $jadwal->unique('kelas_id')->count() }}</span>
                <div class="sima-stat__delta sima-stat__delta--flat">Kelas aktif</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="sima-stat sima-stat--purple">
                <div class="sima-stat__icon sima-stat__icon--purple">
                    <i class="fas fa-book"></i>
                </div>
                <div class="sima-stat__label">Mata Kuliah</div>
                <span class="sima-stat__value">{{ $jadwal->unique('matakuliah_id')->count() }}</span>
                <div class="sima-stat__delta sima-stat__delta--flat">Berbeda</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="sima-stat sima-stat--amber">
                <div class="sima-stat__icon sima-stat__icon--amber">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="sima-stat__label">Total Sesi</div>
                <span class="sima-stat__value">{{ $jadwal->sum('totalSesi') }}</span>
                <div class="sima-stat__delta sima-stat__delta--flat">Sesi semester ini</div>
            </div>
        </div>
    </div>

    {{-- ── JADWAL PER HARI ─────────────────────────────── --}}
    @foreach($hariUrut as $hari)
        @if($grouped->has($hari))
        @php $jList = $grouped[$hari]; $c = $hariColor[$hari]; @endphp
        <div class="sima-card sima-fade" style="margin-bottom:16px;">
            <div class="sima-card__header" style="padding-bottom:14px;
                 border-bottom:1px solid var(--c-border-soft);">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:8px;height:8px;border-radius:50%;background:{{ $c['color'] }}"></div>
                    <h5 class="sima-card__title" style="margin:0">{{ $hari }}</h5>
                    <span class="sima-badge" style="background:{{ $c['bg'] }};color:{{ $c['color'] }}">
                        {{ $jList->count() }} jadwal
                    </span>
                </div>
            </div>

            @foreach($jList as $j)
            <a href="{{ route('dosen.jadwal.detail', $j->id) }}"
               style="display:flex;align-items:center;gap:16px;padding:16px 20px;
                      border-bottom:1px solid var(--c-border-soft);text-decoration:none;
                      transition:background .13s;"
               onmouseover="this.style.background='var(--c-surface-2)'"
               onmouseout="this.style.background='transparent'">

                {{-- Jam --}}
                <div style="min-width:90px;font-family:var(--f-mono);font-size:13px;
                            font-weight:600;color:{{ $c['color'] }}">
                    {{ $j->jam }}
                </div>

                {{-- Matakuliah --}}
                <div style="flex:1;min-width:0">
                    <div style="font-size:13px;font-weight:600;color:var(--c-text-1)">
                        {{ $j->matakuliah->namaMk ?? '-' }}
                    </div>
                    <div style="font-size:11.5px;color:var(--c-text-3);margin-top:3px;
                                display:flex;gap:14px;flex-wrap:wrap">
                        <span>
                            <i class="fas fa-tag" style="font-size:10px"></i>
                            {{ $j->matakuliah->kodeMk ?? '-' }}
                        </span>
                        <span>
                            <i class="fas fa-door-open" style="font-size:10px"></i>
                            Kelas {{ $j->kelas->kodeKelas ?? '-' }}
                        </span>
                        <span>
                            <i class="fas fa-map-marker-alt" style="font-size:10px"></i>
                            {{ $j->ruangan }}
                        </span>
                        <span>
                            <i class="fas fa-layer-group" style="font-size:10px"></i>
                            {{ $j->totalSesi }} sesi
                        </span>
                    </div>
                </div>

                {{-- Arrow --}}
                <div style="color:var(--c-text-4);flex-shrink:0">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    @endforeach
@endif

@endsection