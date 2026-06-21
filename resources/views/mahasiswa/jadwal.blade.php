@extends('layouts.sima')

@section('page_title',   'Jadwal Perkuliahan')
@section('page_section', 'Mahasiswa')
@section('page_subtitle', now()->translatedFormat('l\, d F Y'))

@push('styles')
<style>
/* ══════════════════════════════════════════════════
   JADWAL PAGE — Custom Styles
══════════════════════════════════════════════════ */


/* Tabel mingguan */
.hari-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
}
.hari-today { background: rgba(37,99,235,0.1); color: #2563eb; }
.hari-other { background: rgba(0,0,0,0.05);    color: var(--c-text-2, #64748b); }
.mono {
    font-family: 'SF Mono', 'Fira Code', monospace;
    font-size: 11.5px;
    color: var(--c-text-3, #94a3b8);
    letter-spacing: 0.2px;
}

/* Attendance summary card */
.att-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    gap: 12px;
}
.att-row:last-child { border-bottom: none; }
.att-mk { font-size: 13px; font-weight: 600; color: var(--c-text-1, #1e293b); }
.att-bar-wrap {
    flex: 1;
    height: 6px;
    background: rgba(0,0,0,0.06);
    border-radius: 999px;
    overflow: hidden;
    min-width: 80px;
}
.att-bar-fill {
    height: 100%;
    border-radius: 999px;
    transition: width .6s ease;
}
.att-pct { font-size: 12px; font-weight: 700; min-width: 36px; text-align: right; }

/* Kosong state */
.jdw-empty {
    text-align: center;
    padding: 40px 20px;
    color: var(--c-text-3, #94a3b8);
}
.jdw-empty__icon { font-size: 36px; margin-bottom: 10px; }
.jdw-empty__text { font-size: 13px; }

/* Fade in animation */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
.fade-up { animation: fadeUp .35s ease both; }
.fade-up--1 { animation-delay: .06s; }
.fade-up--2 { animation-delay: .12s; }
.fade-up--3 { animation-delay: .18s; }
.fade-up--4 { animation-delay: .24s; }
</style>
@endpush

@section('main_content')

<div class="sima-card fade-up">

            <div class="sima-card__header">
                <h5 class="sima-card__title mb-0">Jadwal Mingguan</h5>
            </div>

            @if($weeklySchedules->isEmpty())

                <div class="sima-card__body">
                    <div class="jdw-empty">
                        <div class="jdw-empty__icon">📅</div>
                        <div class="jdw-empty__text">Belum ada jadwal terdaftar</div>
                    </div>
                </div>

            @else

                <div class="sima-card__body" style="padding:0">
                    <table class="sima-table">
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Waktu</th>
                                <th>Mata Kuliah</th>
                                <th>Ruangan</th>
                                <th>Tipe</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($weeklySchedules as $sch)
                                @php
                                    $isHariIni = $sch->hari === $hariIni;
                                    $tipeLower = strtolower($sch->tipe_kelas ?? 'perkuliahan');
                                    [$chipCls, $chipLabel] = match($tipeLower) {
                                        'bipa'  => ['sima-badge--purple', 'BIPA'],
                                        'kln'   => ['sima-badge--teal',   'KLN'],
                                        default => ['sima-badge--blue',   'Perkuliahan'],
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <span class="hari-badge {{ $isHariIni ? 'hari-today' : 'hari-other' }}">
                                            {{ $sch->hari ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="mono">
                                        {{ $sch->jam_mulai ?? '-' }} – {{ $sch->jam_selesai ?? '-' }}
                                    </td>
                                    <td>
                                        <div style="font-weight:600;font-size:13px;color:var(--c-text-1)">
                                            {{ $sch->mata_kuliah ?? '-' }}
                                        </div>
                                        <div style="font-size:11.5px;color:var(--c-text-3);margin-top:1px">
                                            {{ $sch->dosen ?? '-' }}
                                        </div>
                                    </td>
                                    <td>{{ $sch->ruangan ?? '-' }}</td>
                                    <td>
                                        <span class="sima-badge {{ $chipCls }}">{{ $chipLabel }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @endif

        </div>

</div>


{{-- ══════════════════════════════════════
     RINGKASAN KEHADIRAN PER MATA KULIAH
══════════════════════════════════════ --}}
@if($attendanceSummary->isNotEmpty())

<div class="sima-card mt-3 fade-up fade-up--3">

    <div class="sima-card__header d-flex justify-content-between align-items-center">
        <h5 class="sima-card__title mb-0">
            <span style="margin-right:6px">📊</span> Kehadiran per Mata Kuliah
        </h5>
    </div>

    <div class="sima-card__body">

        @foreach($attendanceSummary as $att)

            @php
                $totalSesi  = $totalSesiPerMk[$att->course_id]->total_sesi ?? 1;
                $hadir      = $att->hadir ?? 0;
                $pct        = $totalSesi > 0 ? round(($hadir / $totalSesi) * 100) : 0;
                $barColor   = $pct >= 75 ? '#22c55e' : ($pct >= 50 ? '#f59e0b' : '#ef4444');
            @endphp

            <div class="att-row">

                <div style="min-width:140px">
                    <div class="att-mk">{{ $att->namaMk ?? '-' }}</div>
                    <div style="font-size:11.5px;color:var(--c-text-3)">
                        {{ $hadir }} hadir
                        @if(($att->terlambat ?? 0) > 0)
                            · {{ $att->terlambat }} terlambat
                        @endif
                        · {{ $totalSesi }} sesi
                    </div>
                </div>

                <div class="att-bar-wrap">
                    <div class="att-bar-fill" style="width:{{ $pct }}%;background:{{ $barColor }}"></div>
                </div>

                <div class="att-pct" style="color:{{ $barColor }}">{{ $pct }}%</div>

            </div>

        @endforeach

    </div>

</div>

@endif


{{-- ══════════════════════════════════════
     INFO TAMBAHAN: Tidak ada data
══════════════════════════════════════ --}}
@if($weeklySchedules->isEmpty() && $todaySchedules->isEmpty())

<div class="sima-card mt-3 fade-up fade-up--4">
    <div class="sima-card__body">
        <div class="jdw-empty">
            <div class="jdw-empty__icon">📋</div>
            <div style="font-size:14px;font-weight:600;color:var(--c-text-1);margin-bottom:6px">
                Belum ada jadwal terdaftar
            </div>
            <div class="jdw-empty__text">
                Jadwal akan muncul setelah admin mendaftarkan kamu ke kelas.
                Hubungi KLN jika ada pertanyaan.
            </div>
        </div>
    </div>
</div>

@endif

@endsection
