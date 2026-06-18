@extends('layouts.sima')

@section('page_title',   'Jadwal Perkuliahan')
@section('page_section', 'Mahasiswa')
@section('page_subtitle', now()->translatedFormat('l\, d F Y'))

@push('styles')
<style>
/* ══════════════════════════════════════════════════
   JADWAL PAGE — Custom Styles
══════════════════════════════════════════════════ */

/* Tab Filter */
.jdw-tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 24px;
}
.jdw-tab {
    padding: 7px 20px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    border: 1.5px solid transparent;
    transition: all .2s ease;
    color: var(--c-text-2, #64748b);
    background: rgba(0,0,0,0.04);
}
.jdw-tab:hover {
    background: rgba(59,130,246,0.08);
    color: #2563eb;
    border-color: rgba(59,130,246,0.25);
    text-decoration: none;
}
.jdw-tab--active {
    background: #2563eb;
    color: #fff !important;
    border-color: #2563eb;
    box-shadow: 0 4px 12px rgba(37,99,235,0.3);
}

/* Timeline hari ini */
.jdw-timeline {
    position: relative;
    padding-left: 16px;
}
.jdw-timeline::before {
    content: '';
    position: absolute;
    left: 0;
    top: 8px;
    bottom: 8px;
    width: 2px;
    background: linear-gradient(180deg, #2563eb 0%, rgba(37,99,235,0.1) 100%);
    border-radius: 2px;
}
.jdw-item {
    position: relative;
    display: flex;
    gap: 14px;
    padding: 14px 0;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}
.jdw-item:last-child { border-bottom: none; }
.jdw-item::before {
    content: '';
    position: absolute;
    left: -20px;
    top: 20px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid #2563eb;
    background: #fff;
    z-index: 1;
}
.jdw-item--now::before {
    background: #2563eb;
    box-shadow: 0 0 0 4px rgba(37,99,235,0.2);
    animation: pulse-dot 1.5s infinite;
}
@keyframes pulse-dot {
    0%, 100% { box-shadow: 0 0 0 4px rgba(37,99,235,0.2); }
    50%       { box-shadow: 0 0 0 8px rgba(37,99,235,0.08); }
}
.jdw-item__time {
    min-width: 90px;
    font-size: 12px;
    font-weight: 600;
    color: var(--c-text-3, #94a3b8);
    padding-top: 2px;
    font-variant-numeric: tabular-nums;
    letter-spacing: 0.3px;
}
.jdw-item__body { flex: 1; }
.jdw-item__title {
    font-size: 14px;
    font-weight: 700;
    color: var(--c-text-1, #1e293b);
    margin-bottom: 3px;
}
.jdw-item__meta {
    font-size: 12px;
    color: var(--c-text-3, #94a3b8);
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
}
.jdw-item__meta span { display: flex; align-items: center; gap: 4px; }

/* Dot warna tipe */
.jdw-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 5px;
}

/* Chip status */
.jdw-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    flex-shrink: 0;
}
.jdw-chip--now   { background: rgba(34,197,94,0.12);  color: #16a34a; }
.jdw-chip--past  { background: rgba(148,163,184,0.12); color: #64748b; }
.jdw-chip--soon  { background: rgba(245,158,11,0.12);  color: #d97706; }
.jdw-chip--bipa  { background: rgba(168,85,247,0.12);  color: #7c3aed; }
.jdw-chip--kln   { background: rgba(20,184,166,0.12);  color: #0d9488; }

/* Tabel mingguan */
.jdw-week-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 13px;
}
.jdw-week-table thead th {
    padding: 10px 14px;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--c-text-3, #94a3b8);
    border-bottom: 1px solid rgba(0,0,0,0.07);
    background: rgba(0,0,0,0.02);
}
.jdw-week-table tbody tr {
    transition: background .15s;
}
.jdw-week-table tbody tr:hover {
    background: rgba(37,99,235,0.03);
}
.jdw-week-table td {
    padding: 12px 14px;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    vertical-align: middle;
}
.jdw-week-table tbody tr:last-child td { border-bottom: none; }
.jdw-week-table .hari-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
}
.hari-today { background: rgba(37,99,235,0.1); color: #2563eb; }
.hari-other { background: rgba(0,0,0,0.05);    color: var(--c-text-2, #64748b); }
.jdw-week-table .mono {
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

{{-- ══════════════════════════════════════
     TAB FILTER
══════════════════════════════════════ --}}
<div class="jdw-tabs fade-up">
    @foreach(['semua' => 'Semua', 'perkuliahan' => 'Perkuliahan', 'bipa' => 'BIPA', 'kln' => 'KLN'] as $key => $label)
        <a href="{{ route('mahasiswa.jadwal', ['tipe' => $key]) }}"
           class="jdw-tab {{ $activeTipe === $key ? 'jdw-tab--active' : '' }}">
            {{ $label }}
        </a>
    @endforeach
</div>


{{-- ══════════════════════════════════════
     LAYOUT UTAMA
══════════════════════════════════════ --}}
<div class="row g-3">

    {{-- ─────────────────────────────────────
         KOLOM KIRI: Jadwal Hari Ini
    ───────────────────────────────────── --}}
    <div class="col-lg-5 fade-up fade-up--1">
        <div class="sima-card h-100">

            <div class="sima-card__header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="sima-card__title mb-0">Hari Ini</h5>
                    <div style="font-size:12px;color:var(--c-text-3);margin-top:2px">
                        {{ now()->translatedFormat('l\, d F') }}
                    </div>
                </div>
                <span style="
                    display:inline-flex;align-items:center;gap:5px;
                    padding:4px 10px;border-radius:999px;
                    background:rgba(34,197,94,0.1);
                    color:#16a34a;font-size:11.5px;font-weight:700;
                ">
                    <span style="width:6px;height:6px;border-radius:50%;background:#16a34a;animation:pulse-dot 1.5s infinite"></span>
                    Live
                </span>
            </div>

            <div class="sima-card__body">

                @if($todaySchedules->isEmpty())

                    <div class="jdw-empty">
                        <div class="jdw-empty__icon">🎉</div>
                        <div style="font-size:14px;font-weight:600;color:var(--c-text-1);margin-bottom:4px">
                            Tidak ada kelas hari ini
                        </div>
                        <div class="jdw-empty__text">Nikmati waktu luangmu!</div>
                    </div>

                @else

                    <div class="jdw-timeline">

                        @php
                            $now = now();
                        @endphp

                        @foreach($todaySchedules as $sch)

                            @php
                                $jamMulai   = $sch->jam_mulai   ?? '-';
                                $jamSelesai = $sch->jam_selesai ?? '-';
                                $tipeKelas  = strtolower($sch->tipe_kelas ?? 'perkuliahan');

                                // Tentukan status waktu
                                try {
                                    $mulaiTime   = \Carbon\Carbon::today()->setTimeFromTimeString($jamMulai);
                                    $selesaiTime = \Carbon\Carbon::today()->setTimeFromTimeString($jamSelesai);
                                    $isNow  = $now->between($mulaiTime, $selesaiTime);
                                    $isPast = $now->gt($selesaiTime);
                                } catch(\Exception $e) {
                                    $isNow  = false;
                                    $isPast = false;
                                }

                                // Warna per tipe
                                $dotColor = match($tipeKelas) {
                                    'bipa'         => '#7c3aed',
                                    'kln'          => '#0d9488',
                                    default        => '#2563eb',
                                };
                            @endphp

                            <div class="jdw-item {{ $isNow ? 'jdw-item--now' : '' }}">

                                {{-- JAM --}}
                                <div class="jdw-item__time">
                                    {{ $jamMulai }}<br>
                                    <span style="font-weight:400;font-size:11px">{{ $jamSelesai }}</span>
                                </div>

                                {{-- DOT WARNA --}}
                                <div class="jdw-dot" style="background:{{ $dotColor }}"></div>

                                {{-- KONTEN --}}
                                <div class="jdw-item__body">

                                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
                                        <div class="jdw-item__title">{{ $sch->mata_kuliah ?? '-' }}</div>

                                        @if($isNow)
                                            <span class="jdw-chip jdw-chip--now">● Sedang berlangsung</span>
                                        @elseif($isPast)
                                            <span class="jdw-chip jdw-chip--past">Selesai</span>
                                        @else
                                            <span class="jdw-chip jdw-chip--soon">Akan datang</span>
                                        @endif
                                    </div>

                                    <div class="jdw-item__meta">
                                        <span>
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                            {{ $sch->ruangan ?? '-' }}
                                        </span>
                                        <span>
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                            {{ $sch->dosen ?? 'Dosen' }}
                                        </span>
                                        @if($tipeKelas !== 'perkuliahan')
                                            <span class="jdw-chip jdw-chip--{{ $tipeKelas }}" style="padding:1px 8px;font-size:10.5px">
                                                {{ strtoupper($tipeKelas) }}
                                            </span>
                                        @endif
                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @endif

            </div>

        </div>
    </div>


    {{-- ─────────────────────────────────────
         KOLOM KANAN: Jadwal Mingguan
    ───────────────────────────────────── --}}
    <div class="col-lg-7 fade-up fade-up--2">
        <div class="sima-card">

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

                <div style="overflow-x:auto">
                    <table class="jdw-week-table">
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
                                    $isHariIni  = $sch->hari === $hariIni;
                                    $tipeKelas  = $sch->tipe_kelas ?? 'Perkuliahan';
                                    $tipeLower  = strtolower($tipeKelas);
                                    $chipClass  = match($tipeLower) {
                                        'bipa'  => 'jdw-chip--bipa',
                                        'kln'   => 'jdw-chip--kln',
                                        default => '',
                                    };
                                    $chipStyle = $tipeLower === 'perkuliahan'
                                        ? 'background:rgba(37,99,235,0.1);color:#2563eb;padding:2px 10px;border-radius:999px;font-size:11px;font-weight:600'
                                        : '';
                                @endphp

                                <tr>

                                    <td>
                                        <span class="hari-badge {{ $isHariIni ? 'hari-today' : 'hari-other' }}">
                                            {{ $sch->hari ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="mono">
                                        {{ $sch->jam_mulai ?? '-' }}–{{ $sch->jam_selesai ?? '-' }}
                                    </td>

                                    <td>
                                        <div style="font-weight:600;font-size:13px;color:var(--c-text-1)">
                                            {{ $sch->mata_kuliah ?? '-' }}
                                        </div>
                                        <div style="font-size:11.5px;color:var(--c-text-3);margin-top:1px">
                                            {{ $sch->dosen ?? '-' }}
                                        </div>
                                    </td>

                                    <td style="font-size:12.5px;color:var(--c-text-2)">
                                        {{ $sch->ruangan ?? '-' }}
                                    </td>

                                    <td>
                                        @if($chipStyle)
                                            <span style="{{ $chipStyle }}">{{ $tipeKelas }}</span>
                                        @else
                                            <span class="jdw-chip {{ $chipClass }}" style="padding:2px 10px;font-size:11px">
                                                {{ strtoupper($tipeKelas) }}
                                            </span>
                                        @endif
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
