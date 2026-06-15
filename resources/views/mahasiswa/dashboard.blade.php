@extends('layouts.sima')

@section('page_title',    'Dashboard')
@section('page_section',  'Mahasiswa')
@section('page_subtitle', 'Selamat datang kembali, ' . (optional($mahasiswa)->nama ?? auth()->user()->name ?? 'Mahasiswa') . ' 👋')

@section('main_content')

{{-- ═══════════════════════════════════════════════
   ROW 0 — Alert KITAS (hanya jika ada dokumen expiring)
   ═══════════════════════════════════════════════ --}}
@if(isset($kitasExpiringSoon) && $kitasExpiringSoon)
<div class="sima-alert sima-alert--amber sima-fade" style="margin-bottom:20px">
    <i class="fas fa-triangle-exclamation sima-alert__icon"></i>
    <div class="sima-alert__text">
        <strong>Dokumen KITAS akan expired dalam {{ $kitasDaysLeft }} hari.</strong>
        Segera hubungi KLN untuk proses perpanjangan.
    </div>
    <a href="{{ route('mahasiswa.request.create') }}" class="sima-alert__action">Request →</a>
</div>
@endif

{{-- ═══════════════════════════════════════════════
   ROW 1 — Stat Cards (4 kolom)
   ═══════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- Jadwal Hari Ini --}}
    <div class="col-6 col-md-3 sima-fade sima-fade--1">
        <a href="{{ route('mahasiswa.jadwal') }}" class="sima-stat sima-stat--blue d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--blue">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="sima-stat__label">Jadwal Hari Ini</div>
            <span class="sima-stat__value">{{ $jadwalHariIni->count() }}</span>
            <div class="sima-stat__delta sima-stat__delta--flat">
                @if($jadwalHariIni->count() > 0)
                    <i class="fas fa-clock"></i>
                    Mulai {{ optional($jadwalHariIni->first())->jam_mulai ?? '--' }} WIB
                @else
                    <i class="fas fa-moon"></i> Tidak ada jadwal
                @endif
            </div>
        </a>
    </div>

    {{-- Total Hadir --}}
    <div class="col-6 col-md-3 sima-fade sima-fade--2">
        <a href="{{ route('mahasiswa.analytics') }}" class="sima-stat sima-stat--green d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--green">
                <i class="fas fa-circle-check"></i>
            </div>
            <div class="sima-stat__label">Total Hadir</div>
            <span class="sima-stat__value">{{ $totalHadir }}</span>
            <div class="sima-stat__delta sima-stat__delta--up">
                <i class="fas fa-arrow-up"></i>
                {{ $totalHadir + $totalTelat + $totalAlpha > 0
                    ? round($totalHadir / ($totalHadir + $totalTelat + $totalAlpha) * 100) . '% kehadiran'
                    : 'Belum ada data' }}
            </div>
        </a>
    </div>

    {{-- Terlambat --}}
    <div class="col-6 col-md-3 sima-fade sima-fade--3">
        <a href="{{ route('mahasiswa.analytics') }}" class="sima-stat sima-stat--amber d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--amber">
                <i class="fas fa-clock-rotate-left"></i>
            </div>
            <div class="sima-stat__label">Terlambat</div>
            <span class="sima-stat__value">{{ $totalTelat }}</span>
            <div class="sima-stat__delta sima-stat__delta--flat">
                <i class="fas fa-circle" style="font-size:6px"></i>
                pertemuan tercatat
            </div>
        </a>
    </div>

    {{-- Notifikasi --}}
    <div class="col-6 col-md-3 sima-fade sima-fade--4">
        <a href="{{ route('mahasiswa.notifikasi') }}" class="sima-stat sima-stat--purple d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--purple">
                <i class="fas fa-bell"></i>
            </div>
            <div class="sima-stat__label">Notifikasi Baru</div>
            <span class="sima-stat__value">{{ $unreadNotifCount }}</span>
            <div class="sima-stat__delta {{ $unreadNotifCount > 0 ? 'sima-stat__delta--down' : 'sima-stat__delta--flat' }}">
                @if($unreadNotifCount > 0)
                    <i class="fas fa-circle" style="font-size:6px;animation:pulse 1.5s infinite"></i> Belum dibaca
                @else
                    <i class="fas fa-circle-check"></i> Semua terbaca
                @endif
            </div>
        </a>
    </div>

</div>

{{-- ═══════════════════════════════════════════════
   ROW 2 — Jadwal + Kehadiran + Absensi Aktif
   ═══════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- Jadwal Hari Ini --}}
    <div class="col-md-5 sima-fade sima-fade--5">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Jadwal Hari Ini</h5>
                    <div class="sima-card__subtitle">
                        {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    </div>
                </div>
                <a href="{{ route('mahasiswa.jadwal') }}" class="sima-card__action">
                    Semua <i class="fas fa-arrow-right" style="font-size:10px"></i>
                </a>
            </div>
            <div class="sima-card__body">

                {{-- Alert absensi aktif --}}
                @if($active_attendance)
                <div style="padding:11px 13px;border-radius:10px;background:{{ $sudah_absen ? 'var(--c-green-lt)' : 'var(--c-blue-lt)' }};border:1px solid {{ $sudah_absen ? 'rgba(5,150,105,.15)' : 'rgba(37,99,235,.12)' }};margin-bottom:14px;display:flex;align-items:center;gap:10px">
                    <div style="width:30px;height:30px;border-radius:8px;background:{{ $sudah_absen ? 'var(--c-green)' : 'var(--c-blue)' }};color:white;display:grid;place-items:center;font-size:12px;flex-shrink:0">
                        <i class="fas {{ $sudah_absen ? 'fa-circle-check' : 'fa-qrcode' }}"></i>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:12.5px;font-weight:600;color:{{ $sudah_absen ? 'var(--c-green)' : 'var(--c-blue)' }}">
                            {{ $sudah_absen ? 'Absensi tercatat' : 'Sesi absensi aktif!' }}
                        </div>
                        <div style="font-size:11px;color:var(--c-text-3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            {{ $active_attendance->matakuliah }}
                        </div>
                    </div>
                    @if(!$sudah_absen)
                    <form method="POST" action="{{ route('mahasiswa.absensi.submit') }}">
                        @csrf
                        <div style="display:flex;gap:6px;align-items:center">
                            <input name="attendance_code" maxlength="10" placeholder="Kode"
                                style="width:70px;padding:5px 8px;border-radius:7px;border:1px solid var(--c-border);font-size:12px;font-family:var(--f-mono);text-transform:uppercase;outline:none"
                                required>
                            <button type="submit" class="sima-btn sima-btn--blue sima-btn--sm">
                                Absen
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
                @endif

                {{-- Daftar jadwal --}}
                @forelse($jadwalHariIni as $sch)
                @php
                    $colors = ['kuliah' => ['border'=>'#2563EB','bg'=>'#EFF6FF'], 'bipa' => ['border'=>'#0D9488','bg'=>'#F0FDFA'], 'kln' => ['border'=>'#7C3AED','bg'=>'#F5F3FF']];
                    $c = $colors[strtolower($sch->jenis ?? 'kuliah')] ?? $colors['kuliah'];
                    $now = \Carbon\Carbon::now();
                    $start = \Carbon\Carbon::parse($sch->jam_mulai);
                    $end   = \Carbon\Carbon::parse($sch->jam_selesai);
                    $isLive = $now->between($start, $end);
                @endphp
                <div class="sima-sch" onclick="window.location='{{ route('mahasiswa.jadwal') }}'">
                    <div class="sima-sch__time">{{ \Carbon\Carbon::parse($sch->jam_mulai)->format('H:i') }} – {{ \Carbon\Carbon::parse($sch->jam_selesai)->format('H:i') }}</div>
                    <div class="sima-sch__dot" style="border-color:{{ $c['border'] }};background:{{ $c['bg'] }}"></div>
                    <div class="sima-sch__info">
                        <div class="sima-sch__title">{{ $sch->mata_kuliah }}</div>
                        <div class="sima-sch__meta">
                            <i class="fas fa-location-dot" style="font-size:10px"></i> {{ $sch->ruangan }}
                            @if($sch->kelas) · {{ $sch->kelas }} @endif
                        </div>
                    </div>
                    @if($isLive)
                    <span class="sima-badge sima-badge--green" style="flex-shrink:0">
                        <i class="fas fa-circle" style="font-size:5px;animation:pulse 1.5s infinite"></i> Live
                    </span>
                    @else
                    <span class="sima-badge sima-badge--{{ strtolower($sch->jenis ?? 'kuliah') === 'bipa' ? 'teal' : (strtolower($sch->jenis ?? '') === 'kln' ? 'purple' : 'blue') }}" style="flex-shrink:0;font-size:9px">
                        {{ strtoupper($sch->jenis ?? 'Kuliah') }}
                    </span>
                    @endif
                </div>
                @empty
                <div style="padding:28px 0;text-align:center">
                    <div style="width:44px;height:44px;border-radius:12px;background:var(--c-bg);display:grid;place-items:center;margin:0 auto 10px;font-size:18px;color:var(--c-text-4)">
                        <i class="fas fa-coffee"></i>
                    </div>
                    <div style="font-size:13px;font-weight:500;color:var(--c-text-2)">Tidak ada jadwal hari ini</div>
                    <div style="font-size:11.5px;color:var(--c-text-3);margin-top:3px">Waktu yang baik untuk belajar mandiri</div>
                </div>
                @endforelse

                {{-- Progress kehadiran keseluruhan --}}
                @php
                    $totalAll = $totalHadir + $totalTelat + $totalAlpha;
                    $pct = $totalAll > 0 ? round(($totalHadir + $totalTelat) / $totalAll * 100) : 0;
                @endphp
                @if($totalAll > 0)
                <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--c-border-soft)">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:7px">
                        <span style="font-size:12px;color:var(--c-text-3);font-weight:500">
                            <i class="fas fa-chart-pie" style="font-size:10px"></i> Kehadiran keseluruhan
                        </span>
                        <span style="font-family:var(--f-mono);font-size:13px;font-weight:700;color:{{ $pct >= 80 ? 'var(--c-green)' : 'var(--c-red)' }}">
                            {{ $pct }}%
                        </span>
                    </div>
                    <div class="sima-prog">
                        <div class="sima-prog__bar" data-w="{{ $pct }}"
                             style="background:linear-gradient(90deg,{{ $pct >= 80 ? '#059669,#34d399' : '#dc2626,#f87171' }})">
                        </div>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-top:6px">
                        <span style="font-size:10.5px;color:var(--c-text-3)">
                            <i class="fas fa-circle-check" style="color:var(--c-green)"></i> {{ $totalHadir }} hadir
                            &nbsp;·&nbsp;
                            <i class="fas fa-clock" style="color:var(--c-amber)"></i> {{ $totalTelat }} terlambat
                            &nbsp;·&nbsp;
                            <i class="fas fa-circle-xmark" style="color:var(--c-red)"></i> {{ $totalAlpha }} alpha
                        </span>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    {{-- Riwayat Absensi --}}
    <div class="col-md-4 sima-fade sima-fade--6">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Riwayat Absensi</h5>
                    <div class="sima-card__subtitle">10 sesi terakhir</div>
                </div>
                <a href="{{ route('mahasiswa.analytics') }}" class="sima-card__action">
                    Detail <i class="fas fa-arrow-right" style="font-size:10px"></i>
                </a>
            </div>
            <div class="sima-card__body" style="padding:0">
                @forelse($history as $rec)
                @php
                    $smap = [
                        'present' => ['label'=>'Hadir',    'cls'=>'green',  'ico'=>'fa-circle-check'],
                        'late'    => ['label'=>'Terlambat','cls'=>'amber',  'ico'=>'fa-clock'],
                        'absent'  => ['label'=>'Alpha',    'cls'=>'red',    'ico'=>'fa-circle-xmark'],
                    ];
                    $s = $smap[$rec->status] ?? $smap['present'];
                @endphp
                <div style="display:flex;align-items:center;gap:12px;padding:10px 18px;border-bottom:1px solid var(--c-border-soft)">
                    <div style="width:28px;height:28px;border-radius:7px;display:grid;place-items:center;font-size:11px;
                                background:var(--c-{{ $s['cls'] }}-lt);color:var(--c-{{ $s['cls'] }});flex-shrink:0">
                        <i class="fas {{ $s['ico'] }}"></i>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:12.5px;font-weight:500;color:var(--c-text-1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            {{ $rec->namaMk }}
                        </div>
                        <div style="font-size:10.5px;color:var(--c-text-3)">
                            Pertemuan ke-{{ $rec->meeting_number }}
                            · {{ \Carbon\Carbon::parse($rec->checkin_time)->format('d M') }}
                        </div>
                    </div>
                    <span class="sima-badge sima-badge--{{ $s['cls'] }}" style="flex-shrink:0">
                        {{ $s['label'] }}
                    </span>
                </div>
                @empty
                <div style="padding:40px 0;text-align:center">
                    <div style="font-size:28px;margin-bottom:8px;opacity:.3">📋</div>
                    <div style="font-size:13px;color:var(--c-text-3)">Belum ada riwayat absensi</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Aksi Cepat + Profil Mini --}}
    <div class="col-md-3 sima-fade sima-fade--7">

        {{-- Profil Mini --}}
        <div class="sima-card" style="margin-bottom:12px">
            <div class="sima-card__body" style="padding:18px">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px">
                    <div class="sima-avatar" style="width:44px;height:44px;border-radius:11px;font-size:16px;
                         background:linear-gradient(135deg,#2563EB,#7C3AED);flex-shrink:0">
                        {{ strtoupper(substr(optional($mahasiswa)->nama ?? auth()->user()->name ?? 'M', 0, 2)) }}
                    </div>
                    <div style="min-width:0">
                        <div style="font-size:13px;font-weight:600;color:var(--c-text-1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            {{ optional($mahasiswa)->nama ?? auth()->user()->name }}
                        </div>
                        <div style="font-size:10.5px;color:var(--c-text-3)">
                            {{ optional($mahasiswa)->npm ?? '—' }}
                        </div>
                        <div style="margin-top:3px">
                            <span class="sima-badge sima-badge--blue" style="font-size:9px">
                                {{ strtoupper(optional($mahasiswa)->warga_negara ?? 'INT') }}
                            </span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('mahasiswa.profile') }}"
                   class="sima-btn sima-btn--outline sima-btn--sm sima-btn--full d-flex justify-content-center">
                    <i class="fas fa-user-pen"></i> Edit Profil
                </a>
            </div>
        </div>

        {{-- Aksi Cepat --}}
        <div class="sima-card">
            <div class="sima-card__header">
                <h5 class="sima-card__title">Aksi Cepat</h5>
            </div>
            <div class="sima-card__body" style="padding:12px">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('mahasiswa.request.create') }}" class="sima-quick">
                            <div class="sima-quick__icon" style="background:#EFF6FF;color:#2563EB">
                                <i class="fas fa-file-circle-plus"></i>
                            </div>
                            <div class="sima-quick__label">Request Dokumen</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('mahasiswa.jadwal') }}" class="sima-quick">
                            <div class="sima-quick__icon" style="background:#ECFDF5;color:#059669">
                                <i class="fas fa-calendar-days"></i>
                            </div>
                            <div class="sima-quick__label">Lihat Jadwal</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('mahasiswa.analytics') }}" class="sima-quick">
                            <div class="sima-quick__icon" style="background:#F5F3FF;color:#7C3AED">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="sima-quick__label">Analitik</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('mahasiswa.notifikasi') }}" class="sima-quick">
                            <div class="sima-quick__icon" style="background:#FFFBEB;color:#D97706">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="sima-quick__label">Notifikasi</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════════
   ROW 3 — Pengumuman Terbaru
   ═══════════════════════════════════════════════ --}}
<div class="row g-3">

    <div class="col-12 sima-fade sima-fade--5">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Pengumuman Terbaru</h5>
                    <div class="sima-card__subtitle">Dari KLN, Jurusan &amp; BIPA</div>
                </div>
                <a href="{{ route('mahasiswa.announcement') }}" class="sima-card__action">
                    Semua <i class="fas fa-arrow-right" style="font-size:10px"></i>
                </a>
            </div>
            <div class="sima-card__body">
                @forelse($announcements as $ann)
                @php
                    $sumberMap = [
                        'kln'     => ['cls'=>'teal',   'label'=>'KLN'],
                        'jurusan' => ['cls'=>'purple',  'label'=>'Jurusan'],
                        'bipa'    => ['cls'=>'amber',   'label'=>'BIPA'],
                    ];
                    $src = $sumberMap[strtolower($ann->sumber ?? 'kln')] ?? ['cls'=>'blue','label'=>$ann->sumber ?? 'SIMA'];
                @endphp
                <div class="sima-announce"
                     onclick="window.location='{{ route('mahasiswa.announcement') }}'">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px">
                        <div class="sima-announce__title">{{ $ann->judul }}</div>
                        <span class="sima-badge sima-badge--{{ $src['cls'] }}" style="flex-shrink:0">
                            {{ $src['label'] }}
                        </span>
                    </div>
                    <div class="sima-announce__body">
                        {{ \Illuminate\Support\Str::limit($ann->isi, 130) }}
                    </div>
                    <div class="sima-announce__meta">
                        <i class="fas fa-clock"></i>
                        {{ $ann->created_at instanceof \Carbon\Carbon ? $ann->created_at->diffForHumans() : \Carbon\Carbon::parse($ann->created_at)->diffForHumans() }}
                        @if(!empty($ann->is_penting))
                            <span class="sima-badge sima-badge--amber"><i class="fas fa-star" style="font-size:8px"></i> Penting</span>
                        @endif
                    </div>
                </div>
                @empty
                <div style="padding:32px 0;text-align:center">
                    <div style="font-size:28px;margin-bottom:8px;opacity:.3">📭</div>
                    <div style="font-size:13px;color:var(--c-text-3)">Belum ada pengumuman</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection

{{-- ═══ JS: highlight jadwal sedang berlangsung ═══ --}}
@section('page_js')
<script>
(function () {
    const now  = new Date();
    const cur  = now.getHours() * 60 + now.getMinutes();

    document.querySelectorAll('.sima-sch').forEach(function (el) {
        const t = el.querySelector('.sima-sch__time');
        if (!t) return;

        const parts = t.textContent.split('–');
        if (parts.length < 2) return;

        function toMin(s) {
            const [h, m] = s.trim().split(':').map(Number);
            return h * 60 + m;
        }

        const start = toMin(parts[0]);
        const end   = toMin(parts[1]);

        if (cur >= start && cur <= end) {
            const dot = el.querySelector('.sima-sch__dot');
            if (dot) dot.style.background = '#059669';
        }
    });
})();
</script>
@endsection
