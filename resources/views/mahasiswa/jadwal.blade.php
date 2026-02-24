{{-- ═══════════════════════════════════════════════════
     JADWAL — resources/views/mahasiswa/jadwal.blade.php
     ═══════════════════════════════════════════════════ --}}
@extends('layouts.sima')

@section('page_title',   'Jadwal Saya')
@section('page_section', 'Mahasiswa')
@section('page_subtitle',\Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y'))

@section('main_content')

{{-- Tab filter --}}
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap">
    @foreach(['Semua','BIPA','Perkuliahan','KLN'] as $tab)
    <a href="{{ route('mahasiswa.jadwal', ['type' => strtolower($tab)]) }}"
       class="sima-btn {{ (request('type', 'semua') === strtolower($tab)) ? '' : 'sima-btn--outline' }}"
       style="font-size:12.5px;padding:7px 16px">{{ $tab }}</a>
    @endforeach
</div>

<div class="row g-3">
    {{-- Jadwal Hari Ini --}}
    <div class="col-md-5 sima-fade">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Hari Ini</h5>
                    <div class="sima-card__subtitle">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM') }}</div>
                </div>
                <span class="sima-badge sima-badge--green">
                    <i class="fas fa-circle" style="font-size:5px;animation:pulse 1.5s infinite"></i> Live
                </span>
            </div>
            <div class="sima-card__body">
                @forelse($todaySchedules ?? [] as $sch)
                <div class="sima-sch">
                    <div class="sima-sch__time">{{ $sch->jam_mulai }} – {{ $sch->jam_selesai }}</div>
                    <div class="sima-sch__dot" style="border-color:{{ $sch->color ?? '#2563EB' }};background:{{ $sch->color_bg ?? '#EFF6FF' }}"></div>
                    <div class="sima-sch__info">
                        <div class="sima-sch__title">{{ $sch->mata_kuliah }}</div>
                        <div class="sima-sch__meta"><i class="fas fa-map-marker-alt"></i> {{ $sch->ruangan }} &nbsp;·&nbsp; {{ $sch->dosen }}</div>
                    </div>
                </div>
                @empty
                @foreach([
                    ['08:00','10:00','Matematika Lanjut','Gd. 4, Ruang 201','Dr. Hendro Susanto','#2563EB','#EFF6FF'],
                    ['13:00','15:00','Pemrograman Web','Lab Komputer 3','Budi Santoso, M.T.','#0D9488','#F0FDFA'],
                ] as [$s,$e,$mk,$r,$d,$c,$cb])
                <div class="sima-sch">
                    <div class="sima-sch__time">{{ $s }} – {{ $e }}</div>
                    <div class="sima-sch__dot" style="border-color:{{ $c }};background:{{ $cb }}"></div>
                    <div class="sima-sch__info">
                        <div class="sima-sch__title">{{ $mk }}</div>
                        <div class="sima-sch__meta"><i class="fas fa-map-marker-alt"></i> {{ $r }} &nbsp;·&nbsp; {{ $d }}</div>
                    </div>
                </div>
                @endforeach
                @endforelse
            </div>
        </div>
    </div>

    {{-- Tabel jadwal mingguan --}}
    <div class="col-md-7 sima-fade sima-fade--1">
        <div class="sima-card">
            <div class="sima-card__header">
                <h5 class="sima-card__title">Jadwal Mingguan</h5>
            </div>
            <div class="sima-card__body" style="padding:0">
                <table class="sima-table">
                    <thead>
                        <tr>
                            <th>Hari</th><th>Waktu</th><th>Mata Kuliah</th><th>Ruangan</th><th>Tipe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($weeklySchedules ?? [] as $sch)
                        <tr>
                            <td style="font-weight:600">{{ $sch->hari }}</td>
                            <td style="font-family:var(--f-mono);font-size:12px;color:var(--c-text-3)">{{ $sch->jam_mulai }}–{{ $sch->jam_selesai }}</td>
                            <td>
                                <div style="font-weight:500">{{ $sch->mata_kuliah }}</div>
                                <div style="font-size:11.5px;color:var(--c-text-3)">{{ $sch->dosen }}</div>
                            </td>
                            <td style="font-size:12.5px">{{ $sch->ruangan }}</td>
                            <td><span class="sima-badge sima-badge--blue">{{ $sch->tipe }}</span></td>
                        </tr>
                        @empty
                        @foreach([
                            ['Senin','08:00–10:00','Matematika Lanjut','Dr. Hendro','Gd.4 R.201','Kuliah'],
                            ['Senin','13:00–15:00','Pemrograman Web','Budi Santoso','Lab Komp 3','Kuliah'],
                            ['Selasa','09:00–11:00','Basis Data','Dr. Rini','Gd.3 R.105','Kuliah'],
                            ['Rabu','10:00–12:00','BIPA B1','Ibu Sari','Gd.1 R.202','BIPA'],
                            ['Kamis','08:00–10:00','Jaringan Komputer','Pak Andi','Lab Net','Kuliah'],
                            ['Jumat','13:00–15:00','Struktur Data','Dr. Fauzi','Gd.4 R.301','Kuliah'],
                        ] as [$h,$w,$mk,$d,$r,$t])
                        <tr>
                            <td style="font-weight:600">{{ $h }}</td>
                            <td style="font-family:var(--f-mono);font-size:12px;color:var(--c-text-3)">{{ $w }}</td>
                            <td><div style="font-weight:500">{{ $mk }}</div><div style="font-size:11.5px;color:var(--c-text-3)">{{ $d }}</div></td>
                            <td style="font-size:12.5px">{{ $r }}</td>
                            <td><span class="sima-badge {{ $t === 'BIPA' ? 'sima-badge--amber' : 'sima-badge--blue' }}">{{ $t }}</span></td>
                        </tr>
                        @endforeach
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
