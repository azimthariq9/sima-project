@extends('layouts.sima')

@section('page_title',    'Jadwal KLN')
@section('page_section',  'KLN')
@section('page_subtitle', 'Kelola jadwal sesi BIPA, konsultasi, dan perkuliahan')

@section('main_content')

<div class="row g-3 mb-4">
    {{-- Stat ringkasan jadwal --}}
    @foreach([
        ['label'=>'Jadwal Hari Ini', 'val'=>$todayCount??4,   'ico'=>'fa-calendar-day',  'cl'=>'blue'],
        ['label'=>'Minggu Ini',      'val'=>$weekCount??24,   'ico'=>'fa-calendar-week', 'cl'=>'purple'],
        ['label'=>'Sesi BIPA',       'val'=>$bipaCount??8,    'ico'=>'fa-language',      'cl'=>'teal'],
        ['label'=>'Konsultasi KLN',  'val'=>$konsulCount??6,  'ico'=>'fa-comments',      'cl'=>'amber'],
    ] as $s)
    <div class="col-6 col-md-3 sima-fade sima-fade--{{ $loop->index+1 }}">
        <div class="sima-stat sima-stat--{{ $s['cl'] }}">
            <div class="sima-stat__icon sima-stat__icon--{{ $s['cl'] }}"><i class="fas {{ $s['ico'] }}"></i></div>
            <div class="sima-stat__label">{{ $s['label'] }}</div>
            <span class="sima-stat__value" data-count="{{ $s['val'] }}">{{ $s['val'] }}</span>
            <div class="sima-stat__delta sima-stat__delta--flat"><i class="fas fa-calendar-alt"></i> Sesi terjadwal</div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-3">

    {{-- Jadwal hari ini --}}
    <div class="col-md-4 sima-fade">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Jadwal Hari Ini</h5>
                    <div class="sima-card__subtitle">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</div>
                </div>
            </div>
            <div class="sima-card__body">
                @foreach([
                    ['time'=>'08:00–10:00','title'=>'Kelas BIPA A1',           'room'=>'Gedung 1, R.201', 'type'=>'BIPA',       'color'=>'#D97706','bg'=>'#FFFBEB'],
                    ['time'=>'10:00–11:00','title'=>'Konsultasi KITAS — Batch 1','room'=>'Kantor KLN',     'type'=>'Konsultasi', 'color'=>'#2563EB','bg'=>'#EFF6FF'],
                    ['time'=>'13:00–15:00','title'=>'Kelas BIPA B1',           'room'=>'Gedung 1, R.202', 'type'=>'BIPA',       'color'=>'#D97706','bg'=>'#FFFBEB'],
                    ['time'=>'15:00–16:00','title'=>'Orientasi Mahasiswa Baru', 'room'=>'Aula Utama',      'type'=>'Orientasi',  'color'=>'#7C3AED','bg'=>'#F5F3FF'],
                ] as $sch)
                <div class="sima-sch">
                    <div class="sima-sch__time">{{ $sch['time'] }}</div>
                    <div class="sima-sch__dot" style="border-color:{{ $sch['color'] }};background:{{ $sch['bg'] }}"></div>
                    <div class="sima-sch__info">
                        <div class="sima-sch__title">{{ $sch['title'] }}</div>
                        <div class="sima-sch__meta">
                            <i class="fas fa-map-marker-alt"></i> {{ $sch['room'] }}
                        </div>
                    </div>
                    <span class="sima-badge {{ $sch['type']==='BIPA'?'sima-badge--amber':($sch['type']==='Konsultasi'?'sima-badge--blue':'sima-badge--purple') }}"
                          style="flex-shrink:0">{{ $sch['type'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Tabel jadwal mingguan --}}
    <div class="col-md-8 sima-fade sima-fade--1">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Jadwal Mingguan</h5>
                    <div class="sima-card__subtitle">Semua sesi KLN minggu ini</div>
                </div>
                <button class="sima-btn sima-btn--gold sima-btn--sm">
                    <i class="fas fa-plus"></i> Tambah Sesi
                </button>
            </div>
            <div style="overflow-x:auto">
                <table class="sima-table">
                    <thead>
                        <tr><th>Hari</th><th>Waktu</th><th>Sesi</th><th>Lokasi</th><th>Kapasitas</th><th>Tipe</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach([
                            ['Senin',    '08:00–10:00','Kelas BIPA A1',              'Gd.1 R.201','20','BIPA'],
                            ['Senin',    '10:00–11:00','Konsultasi KITAS Batch 1',   'Kantor KLN','10','Konsultasi'],
                            ['Selasa',   '09:00–11:00','Kelas BIPA B2',              'Gd.1 R.202','20','BIPA'],
                            ['Rabu',     '13:00–15:00','Kelas BIPA A2',              'Gd.1 R.201','20','BIPA'],
                            ['Kamis',    '10:00–11:00','Orientasi Mahasiswa Baru',   'Aula Utama','120','Orientasi'],
                            ['Jumat',    '09:00–10:00','Konsultasi Dokumen Batch 2', 'Kantor KLN','8','Konsultasi'],
                            ['Sabtu',    '10:00–12:00','Workshop Budaya Indonesia',  'Aula Kecil','40','Workshop'],
                        ] as $r)
                        <tr>
                            <td style="font-weight:600">{{ $r[0] }}</td>
                            <td style="font-family:var(--f-mono);font-size:11.5px;color:var(--c-text-3)">{{ $r[1] }}</td>
                            <td style="font-weight:500">{{ $r[2] }}</td>
                            <td style="font-size:12.5px;color:var(--c-text-3)">{{ $r[3] }}</td>
                            <td style="font-family:var(--f-mono);text-align:center;font-size:12px">{{ $r[4] }}</td>
                            <td>
                                <span class="sima-badge {{ $r[5]==='BIPA'?'sima-badge--amber':($r[5]==='Konsultasi'?'sima-badge--blue':($r[5]==='Orientasi'?'sima-badge--purple':'sima-badge--teal')) }}">
                                    {{ $r[5] }}
                                </span>
                            </td>
                            <td>
                                <div style="display:flex;gap:5px">
                                    <button class="sima-btn sima-btn--outline sima-btn--sm"><i class="fas fa-pen"></i></button>
                                    <button class="sima-btn sima-btn--danger sima-btn--sm"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
