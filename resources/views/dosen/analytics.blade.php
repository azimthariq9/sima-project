@extends('layouts.sima')

@section('page_title', 'Analytics & Absensi')
@section('page_section', 'Dosen')
@section('page_subtitle', 'Kelola absensi dan pantau performa akademik')

@section('main_content')

{{-- ═══════════════════════════════════════════════
     STAT CARDS
═══════════════════════════════════════════════ --}}
<div class="row g-3">

    <div class="col-md-4">
        <div class="sima-stat sima-stat--blue">
            <div class="sima-stat__label">Total Kelas</div>
            <div class="sima-stat__value">{{ $total_kelas }}</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="sima-stat sima-stat--green">
            <div class="sima-stat__label">Total Mahasiswa</div>
            <div class="sima-stat__value">{{ $total_mahasiswa }}</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="sima-stat sima-stat--amber">
            <div class="sima-stat__label">Rata-rata Kehadiran</div>
            <div class="sima-stat__value">{{ $rata_kehadiran }}%</div>
        </div>
    </div>

</div>


{{-- ═══════════════════════════════════════════════
     START ATTENDANCE — FORM GENERATE KODE
═══════════════════════════════════════════════ --}}
<div class="sima-card mt-4">

    <div class="sima-card__header d-flex justify-content-between align-items-center">
        <h5 class="sima-card__title mb-0">
            <span style="margin-right:8px">🎯</span> Start Absensi
        </h5>
        <span class="badge bg-secondary" style="font-size:12px">
            {{ now()->translatedFormat('l, d M Y') }}
        </span>
    </div>

    <div class="sima-card__body">
        <form method="POST" action="{{ route('dosen.absensi.start') }}">
            @csrf

            <div class="row g-3 align-items-end">

                {{-- PILIH MATA KULIAH --}}
                <div class="col-md-5">
                    <label class="form-label fw-semibold" style="font-size:13px">
                        Mata Kuliah
                    </label>
<select name="course_id" class="form-select" required>

<option value="" disabled selected>-- Pilih Mata Kuliah --</option>

<option value="1">IT011234 — Konsep Data Mining</option>
<option value="2">AK011332 — Sistem Keamanan Teknologi Informasi</option>
<option value="3">AK011331 — Disain dan Manajemen Jaringan Komputer</option>
<option value="4">AK011305 — Interaksi Manusia dan Komputer</option>
<option value="5">AK011302 — Analisis & Perencanaan Sistem Info</option>
<option value="6">AK011229 — Metode Penelitian</option>
<option value="7">IT011308 — Graf dan Analisis Algoritma</option>
<option value="8">IT011240 — Pengantar Sain Data</option>
<option value="9">IT011243 — Business Intelligence</option>

</select>                </div>

                {{-- PILIH PERTEMUAN --}}
                <div class="col-md-3">
                    <label class="form-label fw-semibold" style="font-size:13px">
                        Pertemuan Ke-
                    </label>
                    <select name="meeting_number" class="form-select" required>
                        @for($i = 1; $i <= 16; $i++)
                            <option value="{{ $i }}">Pertemuan {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                {{-- TOMBOL --}}
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100" style="height:42px;font-weight:600">
                        ▶ Generate Kode Absensi
                    </button>
                </div>

            </div>

        </form>

        {{-- INFO: tidak ada jadwal hari ini --}}
        @if($jadwal_hari_ini->isEmpty())
            <div class="mt-3 p-3 rounded" style="background:rgba(234,179,8,0.1);border:1px solid rgba(234,179,8,0.3);font-size:13px;color:#92400e">
                ⚠️ Tidak ada jadwal mengajar hari ini — kamu tetap bisa start absensi secara manual di atas.
            </div>
        @endif

    </div>

</div>


{{-- ═══════════════════════════════════════════════
     KODE ABSENSI AKTIF + QR CODE
═══════════════════════════════════════════════ --}}
@if(isset($active_attendance) && $active_attendance)

<div class="row g-3 mt-0">

    {{-- KODE ABSENSI --}}
    <div class="col-md-6">
        <div class="sima-card h-100">

            <div class="sima-card__header d-flex justify-content-between align-items-center">
                <h5 class="sima-card__title mb-0">
                    <span style="margin-right:6px">🔑</span> Kode Absensi Aktif
                </h5>
                {{-- CLOSE ABSENSI --}}
                <form method="POST" action="{{ route('dosen.absensi.close') }}">
                    @csrf
                    <input type="hidden" name="session_id" value="{{ $active_attendance->id }}">
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        Tutup Absensi
                    </button>
                </form>
            </div>

            <div class="sima-card__body text-center py-4">

                {{-- KODE BESAR --}}
                <div style="
                    font-size:52px;
                    font-weight:800;
                    letter-spacing:12px;
                    color:#3b82f6;
                    font-family:monospace;
                    background:rgba(59,130,246,0.08);
                    border:2px dashed rgba(59,130,246,0.3);
                    border-radius:12px;
                    padding:16px 24px;
                    display:inline-block;
                    margin-bottom:12px;
                ">
                    {{ $active_attendance->attendance_code }}
                </div>

                <div style="font-size:13px;color:var(--c-text-2)">
                    Berlaku sampai
                    <strong style="color:#ef4444">
                        {{ \Carbon\Carbon::parse($active_attendance->end_time)->format('H:i') }}
                    </strong>
                </div>

                <div style="font-size:12px;color:var(--c-text-3);margin-top:4px">
                    Mahasiswa hadir: <strong>{{ $students_present->count() }}</strong> orang
                </div>

            </div>

        </div>
    </div>

    {{-- QR CODE --}}
    <div class="col-md-6">
        <div class="sima-card h-100">

            <div class="sima-card__header">
                <h5 class="sima-card__title mb-0">
                    <span style="margin-right:6px">📱</span> QR Code Absensi
                </h5>
            </div>

            <div class="sima-card__body text-center py-3">

                {{--
                    Menggunakan API QR publik — tidak perlu install package composer.
                    Mahasiswa scan QR ini → mendapat kode → input di halaman absensi.
                --}}
                <img
                    src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($active_attendance->attendance_code) }}&bgcolor=ffffff&color=1d4ed8&margin=10"
                    alt="QR Code {{ $active_attendance->attendance_code }}"
                    style="border-radius:12px;border:3px solid rgba(59,130,246,0.2)"
                    width="180"
                    height="180"
                />

                <div style="font-size:12px;color:var(--c-text-3);margin-top:10px">
                    Tampilkan ke mahasiswa untuk scan
                </div>

            </div>

        </div>
    </div>

</div>


{{-- ═══════════════════════════════════════════════
     DAFTAR MAHASISWA HADIR (LIVE)
═══════════════════════════════════════════════ --}}
<div class="sima-card mt-3">

    <div class="sima-card__header d-flex justify-content-between align-items-center">
        <h5 class="sima-card__title mb-0">
            <span style="margin-right:6px">✅</span> Mahasiswa Hadir
        </h5>
        <span class="badge bg-success">
            {{ $students_present->count() }} hadir
        </span>
    </div>

    <div class="sima-card__body" style="padding:0">

        @if($students_present->isEmpty())

            <div class="text-center py-4" style="color:var(--c-text-2);font-size:13px">
                Belum ada mahasiswa yang absen
            </div>

        @else

            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:13px">
                    <thead style="background:rgba(0,0,0,0.03)">
                        <tr>
                            <th style="padding:10px 16px">#</th>
                            <th style="padding:10px 16px">Nama Mahasiswa</th>
                            <th style="padding:10px 16px">Waktu Check-in</th>
                            <th style="padding:10px 16px">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students_present as $i => $s)
                            <tr>
                                <td style="padding:10px 16px;color:var(--c-text-3)">{{ $i + 1 }}</td>
                                <td style="padding:10px 16px;font-weight:500">{{ $s->nama }}</td>
                                <td style="padding:10px 16px;color:var(--c-text-2)">
                                    {{ \Carbon\Carbon::parse($s->checkin_time)->format('H:i:s') }}
                                </td>
                                <td style="padding:10px 16px">
                                    @if(($s->status ?? 'present') === 'late')
                                        <span class="badge bg-warning text-dark">Terlambat</span>
                                    @else
                                        <span class="badge bg-success">Hadir</span>
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

@endif {{-- end if active_attendance --}}


{{-- ═══════════════════════════════════════════════
     JADWAL HARI INI (INFO SAJA)
═══════════════════════════════════════════════ --}}
@if($jadwal_hari_ini->isNotEmpty())
<div class="sima-card mt-3">

    <div class="sima-card__header">
        <h5 class="sima-card__title mb-0">
            <span style="margin-right:6px">📅</span> Jadwal Mengajar Hari Ini
        </h5>
    </div>

    <div class="sima-card__body" style="padding:0">
        @foreach($jadwal_hari_ini as $jadwal)
            <div class="d-flex align-items-center border-bottom px-4 py-3 gap-3">
                <div style="
                    width:40px;height:40px;border-radius:10px;
                    background:rgba(59,130,246,0.1);
                    display:flex;align-items:center;justify-content:center;
                    font-size:18px;flex-shrink:0
                ">📖</div>
                <div style="flex:1">
                    <div style="font-weight:600;font-size:14px">
                        {{ $jadwal->matakuliah ?? '-' }}
                    </div>
                    <div style="font-size:12px;color:var(--c-text-3)">
                        {{ $jadwal->ruangan ?? '-' }}
                        &nbsp;•&nbsp;
                        {{-- Fallback untuk berbagai kemungkinan nama kolom jam --}}
                        {{ $jadwal->jamMulai ?? $jadwal->jam_mulai ?? $jadwal->jam ?? '?' }}
                        –
                        {{ $jadwal->jamSelesai ?? $jadwal->jam_selesai ?? $jadwal->jam_akhir ?? '?' }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endif


{{-- ═══════════════════════════════════════════════
     PROGRESS PERTEMUAN
═══════════════════════════════════════════════ --}}
<div class="sima-card mt-3">

    <div class="sima-card__header">
        <h5 class="sima-card__title mb-0">
            <span style="margin-right:6px">📊</span> Progress Pertemuan
        </h5>
    </div>

    <div class="sima-card__body">

        @php
            $pct = min(round(($meeting_progress / 16) * 100), 100);
        @endphp

        <div class="d-flex justify-content-between mb-2" style="font-size:13px">
            <span style="color:var(--c-text-2)">
                {{ $meeting_progress }} dari 16 pertemuan
            </span>
            <span style="font-weight:700;color:#3b82f6">{{ $pct }}%</span>
        </div>

        <div class="progress" style="height:14px;border-radius:8px;background:rgba(0,0,0,0.08)">
            <div
                class="progress-bar bg-primary"
                style="width:{{ $pct }}%;border-radius:8px;transition:width .6s ease"
            ></div>
        </div>

        <div class="row g-2 mt-3">
            @for($i = 1; $i <= 16; $i++)
                @php
                    $done = $chart_data->where('meeting_number', $i)->first();
                @endphp
                <div class="col" style="min-width:0">
                    <div style="
                        text-align:center;
                        padding:6px 2px;
                        border-radius:8px;
                        font-size:11px;
                        font-weight:600;
                        background:{{ $done ? 'rgba(34,197,94,0.15)' : 'rgba(0,0,0,0.05)' }};
                        color:{{ $done ? '#16a34a' : 'var(--c-text-3)' }};
                        border:1px solid {{ $done ? 'rgba(34,197,94,0.3)' : 'transparent' }};
                    ">
                        {{ $i }}
                    </div>
                </div>
            @endfor
        </div>

    </div>

</div>


{{-- ═══════════════════════════════════════════════
     CHART KEHADIRAN
═══════════════════════════════════════════════ --}}
<div class="sima-card mt-3">

    <div class="sima-card__header">
        <h5 class="sima-card__title mb-0">
            <span style="margin-right:6px">📈</span> Chart Kehadiran per Pertemuan
        </h5>
    </div>

    <div class="sima-card__body">
        <canvas id="attendanceChart" height="100"></canvas>
    </div>

</div>


{{-- ═══════════════════════════════════════════════
     HISTORY ABSENSI
═══════════════════════════════════════════════ --}}
<div class="sima-card mt-3">

    <div class="sima-card__header">
        <h5 class="sima-card__title mb-0">
            <span style="margin-right:6px">🕐</span> Riwayat Absensi Terakhir
        </h5>
    </div>

    <div class="sima-card__body" style="padding:0">

        @if($attendance_history->isEmpty())
            <div class="text-center py-4" style="color:var(--c-text-2);font-size:13px">
                Belum ada riwayat absensi
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-borderless mb-0" style="font-size:13px">
                    <thead style="background:rgba(0,0,0,0.03)">
                        <tr style="color:var(--c-text-2)">
                            <th style="padding:10px 16px">Mata Kuliah</th>
                            <th style="padding:10px 16px">Pertemuan</th>
                            <th style="padding:10px 16px">Kode</th>
                            <th style="padding:10px 16px">Hadir</th>
                            <th style="padding:10px 16px">Tanggal</th>
                            <th style="padding:10px 16px">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendance_history as $item)
                            <tr>
                                <td style="padding:10px 16px;font-weight:500">
                                    {{ $item->matakuliah ?? '-' }}
                                </td>
                                <td style="padding:10px 16px;color:var(--c-text-2)">
                                    #{{ $item->meeting_number ?? '-' }}
                                </td>
                                <td style="padding:10px 16px">
                                    <code style="font-size:13px;color:#3b82f6">
                                        {{ $item->attendance_code ?? '-' }}
                                    </code>
                                </td>
                                <td style="padding:10px 16px">
                                    <span class="badge bg-success">
                                        {{ $item->total_hadir ?? 0 }}
                                    </span>
                                </td>
                                <td style="padding:10px 16px;color:var(--c-text-3);font-size:12px">
                                    {{ $item->date ? \Carbon\Carbon::parse($item->date)->format('d M Y') : '-' }}
                                </td>
                                <td style="padding:10px 16px">
                                    @if(($item->status ?? '') === 'active')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Selesai</span>
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


{{-- ═══════════════════════════════════════════════
     INSIGHT
═══════════════════════════════════════════════ --}}
<div class="sima-card mt-3 mb-4">

    <div class="sima-card__header">
        <h5 class="sima-card__title mb-0">
            <span style="margin-right:6px">💡</span> Insight
        </h5>
    </div>

    <div class="sima-card__body">

        <p style="font-size:13px;color:var(--c-text-2);margin:0">
            Rata-rata kehadiran mahasiswa berada di
            <strong style="color:{{ $rata_kehadiran >= 75 ? '#16a34a' : '#ef4444' }}">
                {{ $rata_kehadiran }}%
            </strong>.

            @if($rata_kehadiran >= 85)
                Kehadiran mahasiswa sangat baik — pertahankan metode pembelajaran yang ada.
            @elseif($rata_kehadiran >= 75)
                Kehadiran cukup baik. Pertimbangkan variasi metode mengajar untuk meningkatkan partisipasi lebih lanjut.
            @else
                Kehadiran di bawah standar 75%. Disarankan melakukan evaluasi metode pembelajaran dan berkoordinasi dengan KLN.
            @endif
        </p>

    </div>

</div>


{{-- ═══════════════════════════════════════════════
     CHART JS SCRIPT
═══════════════════════════════════════════════ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const ctx = document.getElementById('attendanceChart');
    if (!ctx) return;

    // Data dari controller
    const chartData = @json($chart_data);

    const labels  = chartData.map(d => 'Ptm ' + d.meeting_number);
    const hadir   = chartData.map(d => d.hadir);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels.length > 0 ? labels : ['Belum ada data'],
            datasets: [{
                label: 'Mahasiswa Hadir',
                data: hadir.length > 0 ? hadir : [0],
                backgroundColor: 'rgba(59,130,246,0.2)',
                borderColor:     'rgba(59,130,246,0.8)',
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.parsed.y + ' mahasiswa hadir'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { stepSize: 5 }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
})();
</script>

@endsection
