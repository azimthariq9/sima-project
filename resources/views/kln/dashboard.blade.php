@extends('layouts.sima')

@section('page_title', 'Dashboard KLN')
@section('page_section', 'KLN')
@section('page_subtitle', 'Monitoring mahasiswa — Kantor Layanan Internasional')

@section('main_content')

@php
$colorPalette = ['#2563EB','#0D9488','#7C3AED','#D97706','#DC2626','#059669','#94A3B8'];
@endphp

{{-- ═══ ROW 1: STAT CARDS ═══════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3 sima-fade sima-fade--1">
        <div class="sima-stat sima-stat--blue">
            <div class="sima-stat__icon sima-stat__icon--blue"><i class="fas fa-users"></i></div>
            <div class="sima-stat__label">Total Mahasiswa</div>
            <div class="sima-stat__value">{{ $totalMahasiswa }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3 sima-fade sima-fade--2">
        <div class="sima-stat sima-stat--amber">
            <div class="sima-stat__icon sima-stat__icon--amber"><i class="fas fa-file-clock"></i></div>
            <div class="sima-stat__label">Request Pending</div>
            <div class="sima-stat__value">{{ $dokumenPending }}</div>
            <div class="sima-stat__delta {{ $dokumenPending > 0 ? 'sima-stat__delta--down' : 'sima-stat__delta--flat' }}">
                @if($dokumenPending > 0)
                    <i class="fas fa-exclamation"></i> Perlu ditangani
                @else
                    <i class="fas fa-check"></i> Semua selesai
                @endif
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 sima-fade sima-fade--3">
        <div class="sima-stat sima-stat--red">
            <div class="sima-stat__icon sima-stat__icon--red"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="sima-stat__label">Dokumen Expired</div>
            <div class="sima-stat__value">{{ $dokumenExpired }}</div>
            <div class="sima-stat__delta {{ $dokumenExpired > 0 ? 'sima-stat__delta--down' : 'sima-stat__delta--flat' }}">
                @if($dokumenExpired > 0)
                    <i class="fas fa-exclamation"></i> Butuh tindakan
                @else
                    <i class="fas fa-check"></i> Semua valid
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ═══ ROW 2: STAT SECONDARY ════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3 sima-fade sima-fade--3">
        <div class="sima-stat sima-stat--purple">
            <div class="sima-stat__icon sima-stat__icon--purple"><i class="fas fa-globe"></i></div>
            <div class="sima-stat__label">Negara Asal</div>
            <div class="sima-stat__value">{{ $negaraDistinct }}</div>
            <div class="sima-stat__delta sima-stat__delta--flat">Negara berbeda</div>
        </div>
    </div>
    <div class="col-6 col-md-3 sima-fade sima-fade--4">
        <div class="sima-stat sima-stat--green">
            <div class="sima-stat__icon sima-stat__icon--green"><i class="fas fa-check-double"></i></div>
            <div class="sima-stat__label">Divalidasi Hari Ini</div>
            <div class="sima-stat__value">{{ $divalidasiHariIni }}</div>
            <div class="sima-stat__delta sima-stat__delta--up">
                <i class="fas fa-arrow-up"></i> Dokumen disetujui
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 sima-fade sima-fade--5">
        <div class="sima-stat sima-stat--blue">
            <div class="sima-stat__icon sima-stat__icon--blue"><i class="fas fa-calendar-alt"></i></div>
            <div class="sima-stat__label">Total Jadwal</div>
            <div class="sima-stat__value">{{ $jadwalAktif }}</div>
            <div class="sima-stat__delta sima-stat__delta--flat">Sesi terdaftar</div>
        </div>
    </div>
    <div class="col-6 col-md-3 sima-fade sima-fade--6">
        <div class="sima-stat sima-stat--amber">
            <div class="sima-stat__icon sima-stat__icon--amber"><i class="fas fa-bell"></i></div>
            <div class="sima-stat__label">Alert Board</div>
            <div class="sima-stat__value" style="font-size:14px;margin-top:4px;">
                <a href="{{ route('kln.notifikasi') }}"
                   style="color:var(--c-accent);font-weight:600;text-decoration:none;font-size:13px;">
                    <i class="fas fa-external-link-alt me-1"></i>Buka Monitor
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ═══ ROW 3: TABEL + SEBARAN NEGARA ════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- ── Dokumen Kritis ────────────────────── --}}
    <div class="col-lg-8 sima-fade sima-fade--5">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Dokumen Kritis</h5>
                    <div class="sima-card__subtitle">Expired &amp; akan expired dalam 30 hari</div>
                </div>
                <a href="{{ route('kln.notifikasi') }}" class="sima-btn sima-btn--outline sima-btn--sm">
                    <i class="fas fa-arrow-right me-1"></i> Semua
                </a>
            </div>
            <div class="table-responsive">
                <table class="sima-table">
                    <thead>
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Negara</th>
                            <th>Dokumen</th>
                            <th>Kadaluarsa</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dokumenKritis as $d)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:32px;height:32px;border-radius:8px;background:var(--c-accent);
                                                color:#fff;display:flex;align-items:center;justify-content:center;
                                                font-size:12px;font-weight:700;flex-shrink:0;">
                                        {{ strtoupper(substr($d->nama, 0, 1)) }}
                                    </div>
                                    <span style="font-size:13px;font-weight:500;">{{ $d->nama }}</span>
                                </div>
                            </td>
                            <td style="font-size:13px;">{{ $d->warNeg ?? '-' }}</td>
                            <td style="font-size:13px;font-weight:500;">{{ $d->tipeDkmn }}</td>
                            <td style="font-family:var(--f-mono);font-size:12px;color:var(--c-text-3);">
                                {{ \Carbon\Carbon::parse($d->tglKdlwrs)->isoFormat('D MMM YYYY') }}
                            </td>
                            <td>
                                @if($d->doc_status === 'expired')
                                    <span class="sima-badge sima-badge--red"><i class="fas fa-times-circle me-1"></i>Expired</span>
                                @else
                                    <span class="sima-badge sima-badge--amber"><i class="fas fa-exclamation-circle me-1"></i>Expiring</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-check-circle" style="color:var(--c-green);font-size:20px;display:block;margin-bottom:6px;"></i>
                                Tidak ada dokumen kritis saat ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Sebaran Negara ────────────────────── --}}
    <div class="col-lg-4 sima-fade sima-fade--6">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Sebaran Negara</h5>
                    <div class="sima-card__subtitle">Top {{ $sebaranNegara->count() }} negara asal</div>
                </div>
            </div>
            <div style="padding:16px 20px;">
                @php $totalNegara = $sebaranNegara->sum('jumlah'); @endphp
                @forelse($sebaranNegara as $i => $neg)
                @php $pct = $totalNegara > 0 ? round($neg->jumlah / $totalNegara * 100) : 0; @endphp
                <div style="margin-bottom:12px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                        <span style="font-size:13px;font-weight:500;">{{ $neg->warNeg }}</span>
                        <span style="font-family:var(--f-mono);font-size:12px;color:var(--c-text-3);">
                            {{ $neg->jumlah }} <span style="font-size:10px;">({{ $pct }}%)</span>
                        </span>
                    </div>
                    <div style="height:5px;background:var(--c-border);border-radius:999px;overflow:hidden;">
                        <div style="height:100%;border-radius:999px;width:{{ $pct }}%;background:{{ $colorPalette[$i % count($colorPalette)] }};"></div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4" style="font-size:13px;">
                    Belum ada data kewarganegaraan.
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- ═══ ROW 4: ANTRIAN VALIDASI + AKSI CEPAT ════════════════════════════ --}}
<div class="row g-3">

    {{-- ── Antrian Validasi ─────────────────── --}}
    <div class="col-md-7 sima-fade sima-fade--7">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Antrian Validasi</h5>
                    <div class="sima-card__subtitle">
                        {{ $antrianValidasi->count() }} request dokumen menunggu
                    </div>
                </div>
                <a href="{{ route('kln.dokumen.page') }}" class="sima-btn sima-btn--outline sima-btn--sm">
                    <i class="fas fa-arrow-right me-1"></i> Semua
                </a>
            </div>
            @if($antrianValidasi->isEmpty())
            <div class="text-center text-muted py-5" style="font-size:13px;">
                <i class="fas fa-inbox fa-2x d-block mb-2" style="opacity:.3;"></i>
                Tidak ada request pending.
            </div>
            @else
            @foreach($antrianValidasi as $i => $q)
            <div style="display:flex;align-items:center;gap:14px;padding:13px 20px;border-bottom:1px solid var(--c-border);">
                <div style="font-family:var(--f-mono);font-size:18px;font-weight:700;color:var(--c-border);min-width:28px;">
                    {{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}
                </div>
                <div style="flex:1;">
                    <div style="font-size:13px;font-weight:600;">{{ $q->nama }}</div>
                    <div style="font-size:12px;color:var(--c-text-3);">{{ $q->tipeDkmn }}</div>
                </div>
                <div style="font-family:var(--f-mono);font-size:11px;color:var(--c-text-3);">
                    {{ \Carbon\Carbon::parse($q->created_at)->diffForHumans() }}
                </div>
                <a href="{{ route('kln.dokumen.page') }}"
                   style="padding:5px 12px;background:rgba(108,143,255,.12);color:var(--c-accent);
                          border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;white-space:nowrap;">
                    Review
                </a>
            </div>
            @endforeach
            @endif
        </div>
    </div>

    {{-- ── Aksi Cepat ───────────────────────── --}}
    <div class="col-md-5 sima-fade sima-fade--8">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <h5 class="sima-card__title">Aksi Cepat</h5>
            </div>
            <div style="padding:16px 20px;display:flex;flex-direction:column;gap:10px;">
                <a href="{{ route('kln.broadcast') }}"
                   style="display:flex;align-items:center;gap:12px;padding:13px 16px;
                          border:1px solid var(--c-border);border-radius:10px;text-decoration:none;
                          color:var(--c-text-1);transition:border-color .13s;font-size:13px;"
                   onmouseover="this.style.borderColor='var(--c-accent)'"
                   onmouseout="this.style.borderColor='var(--c-border)'">
                    <div style="width:36px;height:36px;border-radius:9px;background:rgba(108,143,255,.12);
                                color:var(--c-accent);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div>
                        <div style="font-weight:600;">Kirim Broadcast</div>
                        <div style="font-size:12px;color:var(--c-text-3);">Notifikasi ke semua / mahasiswa tertentu</div>
                    </div>
                </a>
                <a href="{{ route('kln.announcement.create') }}"
                   style="display:flex;align-items:center;gap:12px;padding:13px 16px;
                          border:1px solid var(--c-border);border-radius:10px;text-decoration:none;
                          color:var(--c-text-1);transition:border-color .13s;font-size:13px;"
                   onmouseover="this.style.borderColor='var(--c-accent)'"
                   onmouseout="this.style.borderColor='var(--c-border)'">
                    <div style="width:36px;height:36px;border-radius:9px;background:rgba(5,150,105,.1);
                                color:var(--c-green);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div>
                        <div style="font-weight:600;">Buat Pengumuman</div>
                        <div style="font-size:12px;color:var(--c-text-3);">Publish pengumuman ke mahasiswa</div>
                    </div>
                </a>
                <a href="{{ route('kln.notifikasi') }}"
                   style="display:flex;align-items:center;gap:12px;padding:13px 16px;
                          border:1px solid var(--c-border);border-radius:10px;text-decoration:none;
                          color:var(--c-text-1);transition:border-color .13s;font-size:13px;"
                   onmouseover="this.style.borderColor='var(--c-accent)'"
                   onmouseout="this.style.borderColor='var(--c-border)'">
                    <div style="width:36px;height:36px;border-radius:9px;background:rgba(220,38,38,.1);
                                color:var(--c-red);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <div style="font-weight:600;">Alert Board</div>
                        <div style="font-size:12px;color:var(--c-text-3);">Monitor dokumen & akun bermasalah</div>
                    </div>
                </a>
                <a href="{{ route('kln.users.page') }}"
                   style="display:flex;align-items:center;gap:12px;padding:13px 16px;
                          border:1px solid var(--c-border);border-radius:10px;text-decoration:none;
                          color:var(--c-text-1);transition:border-color .13s;font-size:13px;"
                   onmouseover="this.style.borderColor='var(--c-accent)'"
                   onmouseout="this.style.borderColor='var(--c-border)'">
                    <div style="width:36px;height:36px;border-radius:9px;background:rgba(124,58,237,.1);
                                color:var(--c-purple);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div>
                        <div style="font-weight:600;">Kelola Users</div>
                        <div style="font-size:12px;color:var(--c-text-3);">Aktifkan / nonaktifkan akun</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

</div>

@endsection
