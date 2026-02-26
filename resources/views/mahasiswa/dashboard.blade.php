@extends('layouts.sima')

@section('page_title',    'Dashboard')
@section('page_section',  'Mahasiswa')
@section('page_subtitle', 'Selamat datang kembali, ' . (auth()->user()->name ?? 'Mahasiswa') . ' 👋')

@section('main_content')

{{-- ═══ ALERT KRITIS ═══ --}}
<div class="sima-alert sima-alert--amber sima-fade" style="margin-bottom:22px">
    <i class="fas fa-exclamation-triangle sima-alert__icon"></i>
    <div class="sima-alert__text">
        <strong>Dokumen KITAS akan expired dalam 12 hari.</strong>
        Segera hubungi KLN untuk proses perpanjangan sebelum tanggal 3 Maret 2026.
    </div>
    <a href="{{ route('mahasiswa.request.create') }}" class="sima-alert__action" style="white-space:nowrap">
        Request Sekarang →
    </a>
</div>

{{-- ═══ STAT CARDS ═══ --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3 sima-fade sima-fade--1">
        <a href="{{ route('mahasiswa.request.index') }}" class="sima-stat sima-stat--blue d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--blue"><i class="fas fa-file-alt"></i></div>
            <div class="sima-stat__label">Dokumen Pending</div>
            <span class="sima-stat__value" data-count="{{ $pendingDocs ?? 3 }}">{{ $pendingDocs ?? 3 }}</span>
            <div class="sima-stat__delta sima-stat__delta--flat"><i class="fas fa-clock"></i> Menunggu validasi KLN</div>
        </a>
    </div>
    <div class="col-6 col-md-3 sima-fade sima-fade--2">
        <a href="{{ route('mahasiswa.profil') }}" class="sima-stat sima-stat--red d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--red"><i class="fas fa-exclamation-circle"></i></div>
            <div class="sima-stat__label">Dokumen Expired</div>
            <span class="sima-stat__value" data-count="{{ $expiredDocs ?? 1 }}">{{ $expiredDocs ?? 1 }}</span>
            <div class="sima-stat__delta sima-stat__delta--down"><i class="fas fa-arrow-up"></i> Tindakan segera diperlukan</div>
        </a>
    </div>
    <div class="col-6 col-md-3 sima-fade sima-fade--3">
        <a href="{{ route('mahasiswa.jadwal') }}" class="sima-stat sima-stat--green d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--green"><i class="fas fa-calendar-check"></i></div>
            <div class="sima-stat__label">Jadwal Hari Ini</div>
            <span class="sima-stat__value" data-count="{{ $todaySchedule ?? 2 }}">{{ $todaySchedule ?? 2 }}</span>
            <div class="sima-stat__delta sima-stat__delta--flat"><i class="fas fa-clock"></i> 08:00 &amp; 13:00 WIB</div>
        </a>
    </div>
    <div class="col-6 col-md-3 sima-fade sima-fade--4">
        <a href="{{ route('mahasiswa.notifikasi') }}" class="sima-stat sima-stat--amber d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--amber"><i class="fas fa-bell"></i></div>
            <div class="sima-stat__label">Notifikasi Baru</div>
            <span class="sima-stat__value" data-count="{{ $unreadNotif ?? 4 }}">{{ $unreadNotif ?? 4 }}</span>
            <div class="sima-stat__delta sima-stat__delta--flat"><i class="fas fa-circle" style="font-size:6px"></i> Belum dibaca</div>
        </a>
    </div>
</div>

{{-- ═══ ROW 2 — Jadwal + Timeline + Quick Actions ═══ --}}
<div class="row g-3 mb-4">

    {{-- JADWAL --}}
    <div class="col-md-5 sima-fade sima-fade--5">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Jadwal Hari Ini</h5>
                    <div class="sima-card__subtitle">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</div>
                </div>
                <a href="{{ route('mahasiswa.jadwal') }}" class="sima-card__action">
                    <i class="fas fa-calendar-alt"></i> Jadwal Lengkap
                </a>
            </div>
            <div class="sima-card__body">
                @forelse($todaySchedules ?? [] as $sch)
                <div class="sima-sch" onclick="window.location='{{ route('mahasiswa.jadwal') }}'">
                    <div class="sima-sch__time">{{ $sch->jam_mulai }} – {{ $sch->jam_selesai }}</div>
                    <div class="sima-sch__dot" style="border-color:{{ $sch->color ?? '#2563EB' }};background:{{ $sch->color_bg ?? '#EFF6FF' }}"></div>
                    <div class="sima-sch__info">
                        <div class="sima-sch__title">{{ $sch->mata_kuliah }}</div>
                        <div class="sima-sch__meta"><i class="fas fa-map-marker-alt"></i> {{ $sch->ruangan }} &nbsp;·&nbsp; {{ $sch->dosen }}</div>
                    </div>
                    @if($sch->is_live ?? false)
                        <span class="sima-badge sima-badge--green" style="flex-shrink:0"><i class="fas fa-circle" style="font-size:5px;animation:pulse 1.5s infinite"></i> Live</span>
                    @endif
                </div>
                @empty
                <div class="sima-sch">
                    <div class="sima-sch__time">08:00 – 10:00</div>
                    <div class="sima-sch__dot" style="border-color:#2563EB;background:#EFF6FF"></div>
                    <div class="sima-sch__info">
                        <div class="sima-sch__title">Matematika Lanjut</div>
                        <div class="sima-sch__meta"><i class="fas fa-map-marker-alt"></i> Gd. 4, Ruang 201 &nbsp;·&nbsp; Dr. Hendro Susanto</div>
                    </div>
                </div>
                <div class="sima-sch">
                    <div class="sima-sch__time">13:00 – 15:00</div>
                    <div class="sima-sch__dot" style="border-color:#0D9488;background:#F0FDFA"></div>
                    <div class="sima-sch__info">
                        <div class="sima-sch__title">Pemrograman Web</div>
                        <div class="sima-sch__meta"><i class="fas fa-map-marker-alt"></i> Lab Komputer 3 &nbsp;·&nbsp; Budi Santoso, M.T.</div>
                    </div>
                    <span class="sima-badge sima-badge--blue" style="flex-shrink:0"><i class="fas fa-circle" style="font-size:5px"></i> Aktif</span>
                </div>
                @endforelse

                <div style="height:1px;background:var(--c-border-soft);margin:16px 0"></div>

                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                    <span style="font-size:12.5px;font-weight:500;color:var(--c-text-2)">Kehadiran Semester Ini</span>
                    <span style="font-family:var(--f-mono);font-size:14px;font-weight:700;color:var(--c-green)">{{ $attendanceRate ?? 87 }}%</span>
                </div>
                <div class="sima-prog">
                    <div class="sima-prog__bar" data-w="{{ $attendanceRate ?? 87 }}" style="background:linear-gradient(90deg,#059669,#34D399)"></div>
                </div>
                <div style="display:flex;justify-content:space-between;margin-top:7px">
                    <span style="font-size:11px;color:var(--c-text-3)"><i class="fas fa-info-circle"></i> Minimal 80%</span>
                    <span style="font-size:11px;color:var(--c-text-3)">{{ $attendanceMet ?? 26 }} / {{ $attendanceTotal ?? 30 }} pertemuan</span>
                </div>
            </div>
        </div>
    </div>

    {{-- AKTIVITAS --}}
    <div class="col-md-4 sima-fade sima-fade--6">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Aktivitas Terbaru</h5>
                    <div class="sima-card__subtitle">Riwayat 7 hari terakhir</div>
                </div>
            </div>
            <div class="sima-card__body">
                <ul class="sima-timeline">
                    @forelse($recentActivities ?? [] as $act)
                    <li class="sima-tl-item">
                        <div class="sima-tl-dot" style="background:{{ $act->bg }};color:{{ $act->color }}"><i class="fas {{ $act->icon }}"></i></div>
                        <div class="sima-tl-content">
                            <div class="sima-tl-title">{{ $act->title }}</div>
                            <div class="sima-tl-time">{{ $act->subtitle }} · {{ $act->time }}</div>
                        </div>
                    </li>
                    @empty
                    <li class="sima-tl-item">
                        <div class="sima-tl-dot" style="background:#EFF6FF;color:#2563EB"><i class="fas fa-file-upload"></i></div>
                        <div class="sima-tl-content">
                            <div class="sima-tl-title">Request Dokumen dikirim</div>
                            <div class="sima-tl-time">Surat Keterangan Aktif · Hari ini, 09:14</div>
                        </div>
                    </li>
                    <li class="sima-tl-item">
                        <div class="sima-tl-dot" style="background:#ECFDF5;color:#059669"><i class="fas fa-check-circle"></i></div>
                        <div class="sima-tl-content">
                            <div class="sima-tl-title">Dokumen Disetujui KLN</div>
                            <div class="sima-tl-time">Surat Izin Tinggal · Kemarin, 14:30</div>
                        </div>
                    </li>
                    <li class="sima-tl-item">
                        <div class="sima-tl-dot" style="background:#F5F3FF;color:#7C3AED"><i class="fas fa-user-edit"></i></div>
                        <div class="sima-tl-content">
                            <div class="sima-tl-title">Profil diperbarui</div>
                            <div class="sima-tl-time">Foto & Nomor HP · 3 hari lalu</div>
                        </div>
                    </li>
                    <li class="sima-tl-item">
                        <div class="sima-tl-dot" style="background:#FFFBEB;color:#D97706"><i class="fas fa-bullhorn"></i></div>
                        <div class="sima-tl-content">
                            <div class="sima-tl-title">Pengumuman baru diterima</div>
                            <div class="sima-tl-time">Orientasi Kampus · 5 hari lalu</div>
                        </div>
                    </li>
                    <li class="sima-tl-item">
                        <div class="sima-tl-dot" style="background:#F0FDFA;color:#0D9488"><i class="fas fa-calendar-plus"></i></div>
                        <div class="sima-tl-content">
                            <div class="sima-tl-title">Jadwal baru ditambahkan</div>
                            <div class="sima-tl-time">Semester Genap 2025/2026 · 1 minggu lalu</div>
                        </div>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- QUICK ACTIONS + PROFILE --}}
    <div class="col-md-3 sima-fade sima-fade--7">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <h5 class="sima-card__title">Aksi Cepat</h5>
            </div>
            <div class="sima-card__body">
                <div class="row g-2 mb-4">
                    <div class="col-6">
                        <a href="{{ route('mahasiswa.request.create') }}" class="sima-quick">
                            <div class="sima-quick__icon" style="background:#EFF6FF;color:#2563EB"><i class="fas fa-file-circle-plus"></i></div>
                            <div class="sima-quick__label">Request Dokumen</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('mahasiswa.jadwal') }}" class="sima-quick">
                            <div class="sima-quick__icon" style="background:#ECFDF5;color:#059669"><i class="fas fa-calendar-days"></i></div>
                            <div class="sima-quick__label">Lihat Jadwal</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('mahasiswa.profil') }}" class="sima-quick">
                            <div class="sima-quick__icon" style="background:#F5F3FF;color:#7C3AED"><i class="fas fa-id-card"></i></div>
                            <div class="sima-quick__label">Profil & Dokumen</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('mahasiswa.notifikasi') }}" class="sima-quick">
                            <div class="sima-quick__icon" style="background:#FFFBEB;color:#D97706"><i class="fas fa-bell"></i></div>
                            <div class="sima-quick__label">Notifikasi</div>
                        </a>
                    </div>
                </div>

                {{-- Profile mini --}}
                <div style="padding:16px;background:var(--c-surface-2);border-radius:13px;border:1px solid var(--c-border-soft)">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px">
                        <div class="sima-avatar" style="width:46px;height:46px;border-radius:12px;font-size:17px;background:linear-gradient(135deg,#2563EB,#7C3AED)">
                            {{ strtoupper(substr(auth()->user()->name ?? 'M', 0, 2)) }}
                        </div>
                        <div style="min-width:0">
                            <div style="font-size:13.5px;font-weight:600;color:var(--c-text-1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                {{ auth()->user()->name ?? 'Ahmad Rahimov' }}
                            </div>
                            <div style="font-size:11px;color:var(--c-text-3)">
                                {{ auth()->user()->nim ?? '50421001' }} &nbsp;·&nbsp; 🇺🇿
                            </div>
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                        <div style="padding:10px;background:var(--c-surface);border-radius:10px;border:1px solid var(--c-border-soft);text-align:center">
                            <div style="font-family:var(--f-display);font-size:22px;font-weight:700;color:var(--c-text-1)">{{ $semester ?? 6 }}</div>
                            <div style="font-size:9px;text-transform:uppercase;letter-spacing:.08em;color:var(--c-text-3);margin-top:2px">Semester</div>
                        </div>
                        <div style="padding:10px;background:var(--c-surface);border-radius:10px;border:1px solid var(--c-border-soft);text-align:center">
                            <div style="font-family:var(--f-display);font-size:22px;font-weight:700;color:var(--c-text-1)">{{ $ipk ?? '3.72' }}</div>
                            <div style="font-size:9px;text-transform:uppercase;letter-spacing:.08em;color:var(--c-text-3);margin-top:2px">IPK</div>
                        </div>
                    </div>
                    <a href="{{ route('mahasiswa.profil') }}"
                       class="sima-btn sima-btn--outline sima-btn--sm sima-btn--full d-flex justify-content-center"
                       style="margin-top:12px">
                        <i class="fas fa-user-circle"></i> Lihat Profil Lengkap
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ═══ ROW 3 — Pengumuman + Status Dokumen ═══ --}}
<div class="row g-3 mb-4">

    <div class="col-md-7 sima-fade sima-fade--5">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Pengumuman Terbaru</h5>
                    <div class="sima-card__subtitle">Dari KLN, Jurusan &amp; BIPA</div>
                </div>
                <a href="{{ route('mahasiswa.announcement') }}" class="sima-card__action">Semua Pengumuman →</a>
            </div>
            <div class="sima-card__body">
                @forelse($announcements ?? [] as $ann)
                <div class="sima-announce" onclick="window.location='{{ route('mahasiswa.announcement.show', $ann->id) }}'">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:5px">
                        <div class="sima-announce__title">{{ $ann->icon ?? '📋' }} {{ $ann->judul }}</div>
                        <span class="sima-badge sima-badge--{{ $ann->badge_color ?? 'blue' }}" style="flex-shrink:0;margin-left:10px">{{ $ann->sumber }}</span>
                    </div>
                    <div class="sima-announce__body">{{ Str::limit($ann->isi, 110) }}</div>
                    <div class="sima-announce__meta">
                        <i class="fas fa-clock"></i> {{ $ann->created_at->diffForHumans() }}
                        @if($ann->is_penting)
                            <span class="sima-badge sima-badge--amber">Penting</span>
                        @else
                            <span class="sima-badge sima-badge--grey">Informasi</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="sima-announce">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:5px">
                        <div class="sima-announce__title">📋 Orientasi Mahasiswa Baru Semester Genap</div>
                        <span class="sima-badge sima-badge--teal" style="flex-shrink:0;margin-left:10px">KLN</span>
                    </div>
                    <div class="sima-announce__body">Orientasi dilaksanakan Senin, 24 Februari 2026 pukul 08.00 WIB di Aula Utama Gedung Rektorat. Seluruh mahasiswa asing wajib hadir membawa dokumen identitas asli dan KTM.</div>
                    <div class="sima-announce__meta">
                        <i class="fas fa-clock"></i> 2 jam lalu
                        <span class="sima-badge sima-badge--amber">Penting</span>
                    </div>
                </div>
                <div class="sima-announce">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:5px">
                        <div class="sima-announce__title">🎓 Jadwal UTS Semester Genap 2025/2026</div>
                        <span class="sima-badge sima-badge--purple" style="flex-shrink:0;margin-left:10px">Jurusan</span>
                    </div>
                    <div class="sima-announce__body">Ujian Tengah Semester dilaksanakan 10–21 Maret 2026. Kartu ujian diambil mulai 3 Maret di bagian akademik.</div>
                    <div class="sima-announce__meta">
                        <i class="fas fa-clock"></i> 1 hari lalu
                        <span class="sima-badge sima-badge--grey">Informasi</span>
                    </div>
                </div>
                <div class="sima-announce">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:5px">
                        <div class="sima-announce__title">🌐 Kelas BIPA B2 Dibuka — Kuota Terbatas</div>
                        <span class="sima-badge sima-badge--amber" style="flex-shrink:0;margin-left:10px">BIPA</span>
                    </div>
                    <div class="sima-announce__body">Pendaftaran kelas BIPA B2 dibuka mulai 20 Februari 2026 melalui portal SIMA. Kuota terbatas 20 mahasiswa.</div>
                    <div class="sima-announce__meta">
                        <i class="fas fa-clock"></i> 3 hari lalu
                        <span class="sima-badge sima-badge--grey">Informasi</span>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-5 sima-fade sima-fade--6">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Status Dokumen</h5>
                    <div class="sima-card__subtitle">Kelengkapan dokumen resmi kamu</div>
                </div>
                <a href="{{ route('mahasiswa.profil') }}" class="sima-card__action"><i class="fas fa-folder-open"></i> Kelola</a>
            </div>
            <div class="sima-card__body" style="padding:0">
                @php
                $docs = $documents ?? [
                    ['name' => 'Paspor',      'status' => 'valid',    'exp' => '12 Jan 2028', 'icon' => 'fa-passport'],
                    ['name' => 'KITAS',       'status' => 'expiring', 'exp' => '03 Mar 2026', 'icon' => 'fa-id-card'],
                    ['name' => 'Surat Aktif', 'status' => 'pending',  'exp' => 'Menunggu',    'icon' => 'fa-file-signature'],
                    ['name' => 'Asuransi',    'status' => 'valid',    'exp' => '30 Jun 2026', 'icon' => 'fa-heart-pulse'],
                    ['name' => 'SKCK',        'status' => 'expired',  'exp' => '15 Jan 2026', 'icon' => 'fa-shield-halved'],
                ];
                $sm = [
                    'valid'    => ['label' => 'Valid',    'cls' => 'green',  'ico' => 'fa-circle-check'],
                    'expiring' => ['label' => 'Expiring', 'cls' => 'amber',  'ico' => 'fa-circle-exclamation'],
                    'pending'  => ['label' => 'Pending',  'cls' => 'blue',   'ico' => 'fa-clock'],
                    'expired'  => ['label' => 'Expired',  'cls' => 'red',    'ico' => 'fa-circle-xmark'],
                ];
                @endphp
                <table class="sima-table">
                    <thead>
                        <tr><th>Dokumen</th><th>Berlaku s/d</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($docs as $doc)
                        @php $s = is_array($doc) ? $doc : $doc->toArray(); $status = $s['status']; $info = $sm[$status] ?? $sm['pending']; @endphp
                        <tr onclick="window.location='{{ route('mahasiswa.profil') }}'" style="cursor:pointer">
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <div style="width:28px;height:28px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:12px;
                                         background:{{ $status === 'valid' ? '#ECFDF5' : ($status === 'expired' ? '#FEF2F2' : ($status === 'expiring' ? '#FFFBEB' : '#EFF6FF')) }};
                                         color:{{ $status === 'valid' ? '#059669' : ($status === 'expired' ? '#DC2626' : ($status === 'expiring' ? '#D97706' : '#2563EB')) }}">
                                        <i class="fas {{ $s['icon'] ?? 'fa-file' }}"></i>
                                    </div>
                                    <span style="font-weight:500">{{ $s['name'] }}</span>
                                </div>
                            </td>
                            <td style="font-family:var(--f-mono);font-size:11.5px;color:var(--c-text-3)">{{ $s['exp'] }}</td>
                            <td><span class="sima-badge sima-badge--{{ $info['cls'] }}"><i class="fas {{ $info['ico'] }}"></i> {{ $info['label'] }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="padding:14px 20px;border-top:1px solid var(--c-border-soft);display:flex;align-items:center;justify-content:space-between">
                    <span style="font-size:12px;color:var(--c-text-3)"><i class="fas fa-info-circle"></i> Klik baris untuk detail</span>
                    <a href="{{ route('mahasiswa.request.create') }}" class="sima-btn sima-btn--gold sima-btn--sm"><i class="fas fa-plus"></i> Upload Baru</a>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ═══ ROW 4 — Kehadiran per Matkul + Notifikasi ═══ --}}
<div class="row g-3">
    <div class="col-md-8 sima-fade sima-fade--5">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Kehadiran per Mata Kuliah</h5>
                    <div class="sima-card__subtitle">Semester Genap 2025/2026</div>
                </div>
                <a href="{{ route('mahasiswa.analytics') }}" class="sima-card__action"><i class="fas fa-chart-bar"></i> Analitik Lengkap</a>
            </div>
            <div class="sima-card__body">
                @php
                $matkulList = $matkulAttendance ?? [
                    ['name' => 'Matematika Lanjut',  'pct' => 87, 'color' => '#059669', 'sks' => 3],
                    ['name' => 'Basis Data',          'pct' => 90, 'color' => '#059669', 'sks' => 3],
                    ['name' => 'Struktur Data',       'pct' => 83, 'color' => '#D97706', 'sks' => 3],
                    ['name' => 'Jaringan Komputer',   'pct' => 92, 'color' => '#059669', 'sks' => 2],
                    ['name' => 'Pemrograman Web',     'pct' => 80, 'color' => '#D97706', 'sks' => 3],
                    ['name' => 'Algoritma Lanjut',    'pct' => 85, 'color' => '#059669', 'sks' => 3],
                ];
                @endphp
                <div class="row g-3">
                    @foreach($matkulList as $mk)
                    @php $m = is_array($mk) ? $mk : $mk->toArray(); @endphp
                    <div class="col-md-6">
                        <div style="padding:14px;background:var(--c-surface-2);border-radius:11px;border:1px solid var(--c-border-soft)">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:9px">
                                <div>
                                    <div style="font-size:12.5px;font-weight:600;color:var(--c-text-1)">{{ $m['name'] }}</div>
                                    <div style="font-size:10.5px;color:var(--c-text-3)">{{ $m['sks'] }} SKS</div>
                                </div>
                                <div style="font-family:var(--f-mono);font-size:17px;font-weight:700;color:{{ $m['color'] }}">{{ $m['pct'] }}%</div>
                            </div>
                            <div class="sima-prog">
                                <div class="sima-prog__bar" data-w="{{ $m['pct'] }}" style="background:{{ $m['color'] }}"></div>
                            </div>
                            @if($m['pct'] < 80)
                            <div style="font-size:10.5px;color:var(--c-red);margin-top:5px"><i class="fas fa-triangle-exclamation"></i> Di bawah batas minimum</div>
                            @elseif($m['pct'] < 85)
                            <div style="font-size:10.5px;color:var(--c-amber);margin-top:5px"><i class="fas fa-exclamation-circle"></i> Perlu ditingkatkan</div>
                            @else
                            <div style="font-size:10.5px;color:var(--c-green);margin-top:5px"><i class="fas fa-circle-check"></i> Kehadiran baik</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 sima-fade sima-fade--6">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Notifikasi</h5>
                    <div class="sima-card__subtitle">{{ $unreadNotif ?? 4 }} belum dibaca</div>
                </div>
                <a href="{{ route('mahasiswa.notifikasi') }}" class="sima-card__action">Semua</a>
            </div>
            @php
            $notifs = $latestNotifs ?? [
                ['ic' => 'fa-circle-check',       'cl' => '#059669', 'bg' => '#ECFDF5', 'text' => 'Dokumen <strong>KITAS</strong> telah diverifikasi oleh KLN.',       'time' => 'Hari ini, 09:14',  'unread' => true],
                ['ic' => 'fa-triangle-exclamation','cl' => '#D97706', 'bg' => '#FFFBEB', 'text' => '<strong>KITAS</strong> akan expired dalam <strong>12 hari</strong>.','time' => 'Hari ini, 07:00',  'unread' => true],
                ['ic' => 'fa-bullhorn',            'cl' => '#2563EB', 'bg' => '#EFF6FF', 'text' => 'Pengumuman baru dari <strong>KLN</strong>: Orientasi Smt Genap.',    'time' => 'Kemarin, 15:30',   'unread' => true],
                ['ic' => 'fa-calendar-check',      'cl' => '#7C3AED', 'bg' => '#F5F3FF', 'text' => 'Jadwal <strong>UTS Semester Genap</strong> telah dirilis.',          'time' => 'Kemarin, 10:00',   'unread' => true],
                ['ic' => 'fa-file-alt',            'cl' => '#0D9488', 'bg' => '#F0FDFA', 'text' => 'Request dokumen <strong>REQ-003</strong> sudah selesai diproses.',   'time' => '2 hari lalu',      'unread' => false],
            ];
            @endphp
            <div>
                @foreach($notifs as $n)
                @php $nr = is_array($n) ? $n : $n->toArray(); @endphp
                <a href="{{ route('mahasiswa.notifikasi') }}"
                   style="display:flex;align-items:flex-start;gap:12px;padding:13px 20px;border-bottom:1px solid var(--c-border-soft);background:{{ ($nr['unread'] ?? false) ? 'rgba(108,143,255,.03)' : 'transparent' }};text-decoration:none;transition:background .13s"
                   onmouseover="this.style.background='var(--c-surface-2)'"
                   onmouseout="this.style.background='{{ ($nr['unread'] ?? false) ? 'rgba(108,143,255,.03)' : 'transparent' }}'">
                    <div style="width:32px;height:32px;border-radius:9px;background:{{ $nr['bg'] }};color:{{ $nr['cl'] }};display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0">
                        <i class="fas {{ $nr['ic'] }}"></i>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:12.5px;color:var(--c-text-1);line-height:1.5;margin-bottom:3px;{{ ($nr['unread'] ?? false) ? 'font-weight:500' : '' }}">{!! $nr['text'] !!}</div>
                        <div style="font-family:var(--f-mono);font-size:10px;color:var(--c-text-3)">{{ $nr['time'] }}</div>
                    </div>
                    @if($nr['unread'] ?? false)
                    <div style="width:7px;height:7px;border-radius:50%;background:var(--c-accent);flex-shrink:0;margin-top:5px"></div>
                    @endif
                </a>
                @endforeach
            </div>
            <div style="padding:12px 20px">
                <a href="{{ route('mahasiswa.notifikasi') }}" class="sima-btn sima-btn--outline sima-btn--sm sima-btn--full d-flex justify-content-center">
                    <i class="fas fa-bell"></i> Lihat Semua Notifikasi
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page_js')
<script>
(function () {
    const now = new Date(), h = now.getHours(), m = now.getMinutes(), cur = h * 60 + m;
    document.querySelectorAll('.sima-sch').forEach(function (el) {
        const timeEl = el.querySelector('.sima-sch__time');
        if (!timeEl) return;
        const parts = timeEl.textContent.split('–');
        if (parts.length < 2) return;
        function toMin(s) { const [hh, mm] = s.trim().split(':').map(Number); return hh * 60 + mm; }
        const start = toMin(parts[0]), end = toMin(parts[1]);
        if (cur >= start && cur <= end) {
            const existBadge = el.querySelector('.sima-badge');
            if (!existBadge) {
                const dot = el.querySelector('.sima-sch__dot');
                if (dot) dot.style.background = '#059669';
                const badge = document.createElement('span');
                badge.className = 'sima-badge sima-badge--green';
                badge.style.flexShrink = '0';
                badge.innerHTML = '<i class="fas fa-circle" style="font-size:5px"></i> Sedang Berlangsung';
                el.appendChild(badge);
            }
        }
    });
})();
</script>
@endsection
