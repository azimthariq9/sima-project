@extends('layouts.sima')

@section('page_title', 'Dashboard Mahasiswa')
@section('page_subtitle', 'Selamat datang kembali, {{ auth()->user()->name ?? "Mahasiswa" }} 👋')

@section('main_content')

{{-- ═══════════════════════════════════════════ --}}
{{-- ALERT STRIP                                  --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="sima-alert sima-alert--amber sima-fade sima-fade--1" style="margin-bottom:20px">
    <i class="fas fa-exclamation-triangle" style="font-size:16px;flex-shrink:0"></i>
    <div>
        <strong>Dokumen KITAS akan expired dalam 12 hari.</strong>
        Segera perpanjang ke KLN untuk menghindari masalah izin tinggal.
        <a href="#" style="margin-left:8px;text-decoration:underline">Lihat detail →</a>
    </div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- STAT ROW                                     --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row" style="gap:0">

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--2">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--blue"></div>
            <div class="sima-stat__icon sima-stat__icon--blue">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="sima-stat__label">Dokumen Pending</div>
            <div class="sima-stat__value" data-count="3">3</div>
            <div class="sima-stat__delta sima-stat__delta--up">
                <i class="fas fa-arrow-up"></i> +1 dari minggu lalu
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--3">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--red"></div>
            <div class="sima-stat__icon sima-stat__icon--red">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="sima-stat__label">Dokumen Expired</div>
            <div class="sima-stat__value" data-count="1">1</div>
            <div class="sima-stat__delta sima-stat__delta--down">
                <i class="fas fa-exclamation"></i> Perlu perhatian segera
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--4">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--emerald"></div>
            <div class="sima-stat__icon sima-stat__icon--emerald">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="sima-stat__label">Jadwal Hari Ini</div>
            <div class="sima-stat__value" data-count="2">2</div>
            <div class="sima-stat__delta sima-stat__delta--flat">
                <i class="fas fa-clock"></i> 08:00 &amp; 13:00 WIB
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--5">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--amber"></div>
            <div class="sima-stat__icon sima-stat__icon--amber">
                <i class="fas fa-bell"></i>
            </div>
            <div class="sima-stat__label">Notifikasi Baru</div>
            <div class="sima-stat__value" data-count="4">4</div>
            <div class="sima-stat__delta sima-stat__delta--flat">
                <i class="fas fa-circle" style="font-size:7px"></i> Belum dibaca
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- MAIN ROW: Schedule + Timeline               --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row">

    {{-- ── Jadwal Hari Ini ────────────────── --}}
    <div class="col-md-5 mb-4 sima-fade sima-fade--6">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Jadwal Hari Ini</h5>
                    <div class="sima-card__subtitle">Kamis, {{ date('d F Y') }}</div>
                </div>
                <a href="#" class="sima-card__action">Lihat Semua</a>
            </div>
            <div class="sima-card__body">

                <div class="sima-schedule">
                    <div class="sima-schedule__time">08:00–10:00</div>
                    <div class="sima-schedule__dot" style="border-color:#2563EB;background:#EFF6FF"></div>
                    <div class="sima-schedule__info">
                        <div class="sima-schedule__title">Matematika Lanjut</div>
                        <div class="sima-schedule__meta">
                            <i class="fas fa-map-marker-alt"></i> Gd. 4, Ruang 201
                            &nbsp;·&nbsp; Dr. Hendro S.
                        </div>
                    </div>
                </div>

                <div class="sima-schedule">
                    <div class="sima-schedule__time">13:00–15:00</div>
                    <div class="sima-schedule__dot" style="border-color:#0D9488;background:#F0FDFA"></div>
                    <div class="sima-schedule__info">
                        <div class="sima-schedule__title">Pemrograman Web</div>
                        <div class="sima-schedule__meta">
                            <i class="fas fa-map-marker-alt"></i> Lab Komputer 3
                            &nbsp;·&nbsp; Budi Santoso, M.T.
                        </div>
                    </div>
                </div>

                <div class="sima-divider" style="margin:8px 0"></div>

                {{-- Attendance Rate --}}
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                    <span style="font-size:12px;font-weight:500;color:var(--sima-text-secondary)">Kehadiran Semester Ini</span>
                    <span style="font-family:var(--font-mono);font-size:13px;font-weight:600;color:var(--sima-emerald)">87%</span>
                </div>
                <div class="sima-progress">
                    <div class="sima-progress__bar" style="width:87%;background:linear-gradient(90deg,var(--sima-emerald),#34D399)"></div>
                </div>
                <div style="display:flex;justify-content:space-between;margin-top:6px">
                    <span style="font-size:11px;color:var(--sima-text-muted)">Min. kehadiran: 80%</span>
                    <span style="font-size:11px;color:var(--sima-text-muted)">26/30 pertemuan</span>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Activity Timeline ───────────────── --}}
    <div class="col-md-4 mb-4 sima-fade sima-fade--7">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Aktivitas Terbaru</h5>
                    <div class="sima-card__subtitle">Riwayat 7 hari terakhir</div>
                </div>
            </div>
            <div class="sima-card__body">
                <ul class="sima-timeline">

                    <li class="sima-timeline__item">
                        <div class="sima-timeline__dot" style="background:var(--sima-blue-soft);color:var(--sima-blue)">
                            <i class="fas fa-file-upload" style="font-size:13px"></i>
                        </div>
                        <div class="sima-timeline__content">
                            <div class="sima-timeline__title">Request Dokumen dikirim</div>
                            <div class="sima-timeline__time">Surat Keterangan Aktif · Hari ini, 09:14</div>
                        </div>
                    </li>

                    <li class="sima-timeline__item">
                        <div class="sima-timeline__dot" style="background:var(--sima-emerald-soft);color:var(--sima-emerald)">
                            <i class="fas fa-check-circle" style="font-size:13px"></i>
                        </div>
                        <div class="sima-timeline__content">
                            <div class="sima-timeline__title">Dokumen Disetujui</div>
                            <div class="sima-timeline__time">Surat Izin Tinggal · Kemarin, 14:30</div>
                        </div>
                    </li>

                    <li class="sima-timeline__item">
                        <div class="sima-timeline__dot" style="background:var(--sima-purple-soft);color:var(--sima-purple)">
                            <i class="fas fa-user-edit" style="font-size:13px"></i>
                        </div>
                        <div class="sima-timeline__content">
                            <div class="sima-timeline__title">Profil diperbarui</div>
                            <div class="sima-timeline__time">Foto & Nomor HP · 3 hari lalu</div>
                        </div>
                    </li>

                    <li class="sima-timeline__item">
                        <div class="sima-timeline__dot" style="background:var(--sima-amber-soft);color:var(--sima-amber)">
                            <i class="fas fa-bullhorn" style="font-size:13px"></i>
                        </div>
                        <div class="sima-timeline__content">
                            <div class="sima-timeline__title">Announcement baru diterima</div>
                            <div class="sima-timeline__time">Orientasi Kampus · 5 hari lalu</div>
                        </div>
                    </li>

                    <li class="sima-timeline__item">
                        <div class="sima-timeline__dot" style="background:var(--sima-teal-soft);color:var(--sima-teal)">
                            <i class="fas fa-calendar-plus" style="font-size:13px"></i>
                        </div>
                        <div class="sima-timeline__content">
                            <div class="sima-timeline__title">Jadwal baru ditambahkan</div>
                            <div class="sima-timeline__time">Semester Genap 2025/2026 · 1 minggu lalu</div>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
    </div>

    {{-- ── Quick Actions ───────────────────── --}}
    <div class="col-md-3 mb-4 sima-fade sima-fade--8">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <h5 class="sima-card__title">Aksi Cepat</h5>
            </div>
            <div class="sima-card__body">
                <div class="row g-2">

                    <div class="col-6">
                        <a href="#" class="sima-quick">
                            <div class="sima-quick__icon" style="background:var(--sima-blue-soft);color:var(--sima-blue)">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="sima-quick__label">Request Dokumen</div>
                        </a>
                    </div>

                    <div class="col-6">
                        <a href="#" class="sima-quick">
                            <div class="sima-quick__icon" style="background:var(--sima-emerald-soft);color:var(--sima-emerald)">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="sima-quick__label">Lihat Jadwal</div>
                        </a>
                    </div>

                    <div class="col-6">
                        <a href="#" class="sima-quick">
                            <div class="sima-quick__icon" style="background:var(--sima-purple-soft);color:var(--sima-purple)">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="sima-quick__label">Edit Profil</div>
                        </a>
                    </div>

                    <div class="col-6">
                        <a href="#" class="sima-quick">
                            <div class="sima-quick__icon" style="background:var(--sima-amber-soft);color:var(--sima-amber)">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="sima-quick__label">Notifikasi</div>
                        </a>
                    </div>

                </div>

                {{-- Profile Summary --}}
                <div style="margin-top:20px;padding:16px;background:var(--sima-surface-2);border-radius:12px;border:1px solid var(--sima-border-soft)">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
                        <div class="sima-avatar" style="width:44px;height:44px;background:linear-gradient(135deg,#2563EB,#7C3AED);color:white;font-size:16px;border-radius:12px">
                            {{ strtoupper(substr(auth()->user()->name ?? 'M', 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:600;color:var(--sima-text-primary)">{{ auth()->user()->name ?? 'Mahasiswa' }}</div>
                            <div style="font-size:11px;color:var(--sima-text-muted)">{{ auth()->user()->nim ?? '50421000' }}</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:8px">
                        <div style="flex:1;text-align:center;padding:8px;background:white;border-radius:8px;border:1px solid var(--sima-border-soft)">
                            <div style="font-family:var(--font-display);font-size:18px;font-weight:600;color:var(--sima-text-primary)">6</div>
                            <div style="font-size:10px;color:var(--sima-text-muted);text-transform:uppercase;letter-spacing:0.06em">Semester</div>
                        </div>
                        <div style="flex:1;text-align:center;padding:8px;background:white;border-radius:8px;border:1px solid var(--sima-border-soft)">
                            <div style="font-family:var(--font-display);font-size:18px;font-weight:600;color:var(--sima-text-primary)">3.72</div>
                            <div style="font-size:10px;color:var(--sima-text-muted);text-transform:uppercase;letter-spacing:0.06em">IPK</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- BOTTOM ROW: Announcements + Doc Status      --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row">

    {{-- ── Announcements ──────────────────── --}}
    <div class="col-md-7 mb-4 sima-fade sima-fade--6">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Pengumuman Terbaru</h5>
                    <div class="sima-card__subtitle">Dari KLN &amp; Jurusan</div>
                </div>
                <a href="#" class="sima-card__action">Semua Pengumuman</a>
            </div>
            <div class="sima-card__body">

                <div class="sima-announce">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px">
                        <div class="sima-announce__title">📋 Orientasi Mahasiswa Baru Semester Genap</div>
                        <span class="sima-badge sima-badge--blue" style="flex-shrink:0;margin-left:10px">KLN</span>
                    </div>
                    <div class="sima-announce__body">Orientasi akan dilaksanakan pada Senin, 24 Februari 2026 pukul 08.00 WIB di Aula Utama. Seluruh mahasiswa asing wajib hadir dengan membawa dokumen identitas.</div>
                    <div class="sima-announce__meta">
                        <i class="fas fa-clock"></i> 2 jam lalu
                        <span class="sima-badge sima-badge--amber">Penting</span>
                    </div>
                </div>

                <div class="sima-announce">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px">
                        <div class="sima-announce__title">🎓 Jadwal UTS Semester Genap 2025/2026</div>
                        <span class="sima-badge sima-badge--purple" style="flex-shrink:0;margin-left:10px">Jurusan</span>
                    </div>
                    <div class="sima-announce__body">Ujian Tengah Semester akan dilaksanakan 10–21 Maret 2026. Pastikan kartu ujian sudah diambil di bagian akademik.</div>
                    <div class="sima-announce__meta">
                        <i class="fas fa-clock"></i> 1 hari lalu
                        <span class="sima-badge sima-badge--grey">Informasi</span>
                    </div>
                </div>

                <div class="sima-announce">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px">
                        <div class="sima-announce__title">🌐 Kelas BIPA Tingkat Lanjut Dibuka</div>
                        <span class="sima-badge sima-badge--teal" style="flex-shrink:0;margin-left:10px">BIPA</span>
                    </div>
                    <div class="sima-announce__body">Pendaftaran kelas BIPA B2 dibuka mulai 20 Februari 2026. Kuota terbatas 20 mahasiswa.</div>
                    <div class="sima-announce__meta">
                        <i class="fas fa-clock"></i> 3 hari lalu
                        <span class="sima-badge sima-badge--grey">Informasi</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Dokumen Status ───────────────────── --}}
    <div class="col-md-5 mb-4 sima-fade sima-fade--7">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Status Dokumen</h5>
                    <div class="sima-card__subtitle">Kelengkapan dokumen kamu</div>
                </div>
            </div>
            <div class="sima-card__body">

                @php
                $docs = [
                    ['name' => 'Paspor', 'status' => 'valid', 'exp' => '12 Jan 2028'],
                    ['name' => 'KITAS', 'status' => 'expiring', 'exp' => '03 Mar 2026'],
                    ['name' => 'Surat Aktif', 'status' => 'pending', 'exp' => 'Menunggu'],
                    ['name' => 'Asuransi Kesehatan', 'status' => 'valid', 'exp' => '30 Jun 2026'],
                    ['name' => 'SKCK', 'status' => 'expired', 'exp' => '15 Jan 2026'],
                ];
                $statusMap = [
                    'valid'    => ['label' => 'Valid',    'badge' => 'emerald', 'icon' => 'fa-check-circle'],
                    'expiring' => ['label' => 'Expiring', 'badge' => 'amber',   'icon' => 'fa-exclamation-circle'],
                    'pending'  => ['label' => 'Pending',  'badge' => 'blue',    'icon' => 'fa-clock'],
                    'expired'  => ['label' => 'Expired',  'badge' => 'red',     'icon' => 'fa-times-circle'],
                ];
                @endphp

                <table class="sima-table">
                    <thead>
                        <tr>
                            <th>Dokumen</th>
                            <th>Berlaku</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($docs as $doc)
                        @php $s = $statusMap[$doc['status']]; @endphp
                        <tr>
                            <td style="font-weight:500">{{ $doc['name'] }}</td>
                            <td style="font-family:var(--font-mono);font-size:12px;color:var(--sima-text-muted)">{{ $doc['exp'] }}</td>
                            <td>
                                <span class="sima-badge sima-badge--{{ $s['badge'] }}">
                                    <i class="fas {{ $s['icon'] }}"></i>
                                    {{ $s['label'] }}
                                </span>
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
