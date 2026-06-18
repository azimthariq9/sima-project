@extends('layouts.sima')

@section('page_title',    'Notifikasi KLN')
@section('page_section',  'NOTIFIKASI')
@section('page_subtitle', 'Alert board — monitoring kondisi yang perlu perhatian')

@section('main_content')

{{-- ── STAT CARDS ───────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--red">
            <div class="sima-stat__icon sima-stat__icon--red"><i class="fas fa-exclamation-circle"></i></div>
            <div class="sima-stat__label">Dokumen Kadaluwarsa</div>
            <div class="sima-stat__value">{{ $stats['expired'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--amber">
            <div class="sima-stat__icon sima-stat__icon--amber"><i class="fas fa-clock"></i></div>
            <div class="sima-stat__label">Hampir Kadaluwarsa</div>
            <div class="sima-stat__value">{{ $stats['nearExpired'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--blue">
            <div class="sima-stat__icon sima-stat__icon--blue"><i class="fas fa-user-slash"></i></div>
            <div class="sima-stat__label">Akun Nonaktif</div>
            <div class="sima-stat__value">{{ $stats['inactive'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--amber">
            <div class="sima-stat__icon sima-stat__icon--amber"><i class="fas fa-file-alt"></i></div>
            <div class="sima-stat__label">Request Pending</div>
            <div class="sima-stat__value">{{ $stats['pending'] }}</div>
        </div>
    </div>
</div>

{{-- ── SECTION 1: DOKUMEN KADALUWARSA ─────────────────── --}}
<div class="sima-card mb-4">
    <div class="sima-card__header" style="border-left: 4px solid var(--c-red);">
        <div style="display:flex;align-items:center;gap:8px;">
            <i class="fas fa-exclamation-circle" style="color:var(--c-red);"></i>
            <h5 class="sima-card__title" style="margin:0;">Dokumen Sudah Kadaluwarsa</h5>
            <span class="sima-badge sima-badge--red">{{ $stats['expired'] }}</span>
        </div>
    </div>

    @if($expiredDokumen->isEmpty())
    <div class="text-center text-muted py-4" style="font-size:13px;">
        <i class="fas fa-check-circle fa-2x d-block mb-2" style="color:var(--c-green);opacity:.6;"></i>
        Tidak ada dokumen yang sudah kadaluwarsa.
    </div>
    @else
    <div class="table-responsive">
        <table class="sima-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mahasiswa</th>
                    <th>Jurusan</th>
                    <th>Tipe Dokumen</th>
                    <th style="text-align:center;">Tgl Kadaluwarsa</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($expiredDokumen as $i => $doc)
                <tr>
                    <td>{{ $expiredDokumen->firstItem() + $loop->index }}</td>
                    <td>
                        <div class="fw-600">{{ $doc->nama }}</div>
                        <code style="font-size:11px;color:var(--c-text-3);">{{ $doc->npm }}</code>
                    </td>
                    <td style="font-size:13px;color:var(--c-text-3);">{{ $doc->namaJurusan ?? '—' }}</td>
                    <td>
                        <span class="sima-badge sima-badge--red">{{ $doc->tipeDkmn }}</span>
                    </td>
                    <td style="text-align:center;font-size:13px;color:var(--c-red);font-weight:600;">
                        {{ \Carbon\Carbon::parse($doc->tglKdlwrs)->isoFormat('D MMM YYYY') }}
                    </td>
                    <td>
                        <a href="{{ route('kln.students.mahasiswa', $doc->mahasiswa_id) }}"
                           class="sima-btn sima-btn--outline sima-btn--sm">
                            <i class="fas fa-eye me-1"></i> Lihat
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($expiredDokumen->hasPages())
    <div style="padding:14px 20px;border-top:1px solid var(--c-border);">
        {{ $expiredDokumen->links('vendor.pagination.sima') }}
    </div>
    @endif
    @endif
</div>

{{-- ── SECTION 2: HAMPIR KADALUWARSA ──────────────────── --}}
<div class="sima-card mb-4">
    <div class="sima-card__header" style="border-left: 4px solid var(--c-amber);">
        <div style="display:flex;align-items:center;gap:8px;">
            <i class="fas fa-clock" style="color:var(--c-amber);"></i>
            <h5 class="sima-card__title" style="margin:0;">Dokumen Hampir Kadaluwarsa <small style="font-weight:400;font-size:12px;color:var(--c-text-3);">(≤ 30 hari)</small></h5>
            <span class="sima-badge sima-badge--amber">{{ $stats['nearExpired'] }}</span>
        </div>
    </div>

    @if($nearExpiredDokumen->isEmpty())
    <div class="text-center text-muted py-4" style="font-size:13px;">
        <i class="fas fa-check-circle fa-2x d-block mb-2" style="color:var(--c-green);opacity:.6;"></i>
        Tidak ada dokumen yang hampir kadaluwarsa dalam 30 hari ke depan.
    </div>
    @else
    <div class="table-responsive">
        <table class="sima-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mahasiswa</th>
                    <th>Jurusan</th>
                    <th>Tipe Dokumen</th>
                    <th style="text-align:center;">Kadaluwarsa</th>
                    <th style="text-align:center;">Sisa Hari</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($nearExpiredDokumen as $i => $doc)
                @php
                    $sisa = (int) $doc->sisa_hari;
                    $sisaBadge = $sisa <= 7 ? 'sima-badge--red' : ($sisa <= 14 ? 'sima-badge--amber' : 'sima-badge--blue');
                @endphp
                <tr>
                    <td>{{ $nearExpiredDokumen->firstItem() + $loop->index }}</td>
                    <td>
                        <div class="fw-600">{{ $doc->nama }}</div>
                        <code style="font-size:11px;color:var(--c-text-3);">{{ $doc->npm }}</code>
                    </td>
                    <td style="font-size:13px;color:var(--c-text-3);">{{ $doc->namaJurusan ?? '—' }}</td>
                    <td>
                        <span class="sima-badge sima-badge--amber">{{ $doc->tipeDkmn }}</span>
                    </td>
                    <td style="text-align:center;font-size:13px;">
                        {{ \Carbon\Carbon::parse($doc->tglKdlwrs)->isoFormat('D MMM YYYY') }}
                    </td>
                    <td style="text-align:center;">
                        <span class="sima-badge {{ $sisaBadge }}">{{ $sisa }} hari</span>
                    </td>
                    <td>
                        <a href="{{ route('kln.students.mahasiswa', $doc->mahasiswa_id) }}"
                           class="sima-btn sima-btn--outline sima-btn--sm">
                            <i class="fas fa-eye me-1"></i> Lihat
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($nearExpiredDokumen->hasPages())
    <div style="padding:14px 20px;border-top:1px solid var(--c-border);">
        {{ $nearExpiredDokumen->links('vendor.pagination.sima') }}
    </div>
    @endif
    @endif
</div>

{{-- ── SECTION 3: AKUN NONAKTIF ────────────────────────── --}}
<div class="sima-card mb-4">
    <div class="sima-card__header" style="border-left: 4px solid var(--c-blue);">
        <div style="display:flex;align-items:center;gap:8px;">
            <i class="fas fa-user-slash" style="color:var(--c-blue);"></i>
            <h5 class="sima-card__title" style="margin:0;">Akun Mahasiswa Nonaktif</h5>
            <span class="sima-badge sima-badge--blue">{{ $stats['inactive'] }}</span>
        </div>
    </div>

    @if($inactiveMahasiswa->isEmpty())
    <div class="text-center text-muted py-4" style="font-size:13px;">
        <i class="fas fa-check-circle fa-2x d-block mb-2" style="color:var(--c-green);opacity:.6;"></i>
        Semua akun mahasiswa sudah aktif.
    </div>
    @else
    <div class="table-responsive">
        <table class="sima-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mahasiswa</th>
                    <th>Email</th>
                    <th>Jurusan</th>
                    <th style="text-align:center;">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($inactiveMahasiswa as $i => $mhs)
                <tr>
                    <td>{{ $inactiveMahasiswa->firstItem() + $loop->index }}</td>
                    <td>
                        <div class="fw-600">{{ $mhs->nama }}</div>
                        <code style="font-size:11px;color:var(--c-text-3);">{{ $mhs->npm }}</code>
                    </td>
                    <td style="font-size:13px;color:var(--c-text-3);">{{ $mhs->email }}</td>
                    <td style="font-size:13px;color:var(--c-text-3);">{{ $mhs->namaJurusan ?? '—' }}</td>
                    <td style="text-align:center;">
                        <span class="sima-badge sima-badge--red">Nonaktif</span>
                    </td>
                    <td>
                        <a href="{{ route('kln.users.page') }}"
                           class="sima-btn sima-btn--outline sima-btn--sm">
                            <i class="fas fa-key me-1"></i> Kelola
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($inactiveMahasiswa->hasPages())
    <div style="padding:14px 20px;border-top:1px solid var(--c-border);">
        {{ $inactiveMahasiswa->links('vendor.pagination.sima') }}
    </div>
    @endif
    @endif
</div>

{{-- ── SECTION 4: REQUEST DOKUMEN PENDING ─────────────── --}}
<div class="sima-card mb-4">
    <div class="sima-card__header" style="border-left: 4px solid var(--c-amber);">
        <div style="display:flex;align-items:center;gap:8px;">
            <i class="fas fa-file-alt" style="color:var(--c-amber);"></i>
            <h5 class="sima-card__title" style="margin:0;">Request Dokumen Pending</h5>
            <span class="sima-badge sima-badge--amber">{{ $stats['pending'] }}</span>
        </div>
    </div>

    @if($pendingRequests->isEmpty())
    <div class="text-center text-muted py-4" style="font-size:13px;">
        <i class="fas fa-check-circle fa-2x d-block mb-2" style="color:var(--c-green);opacity:.6;"></i>
        Tidak ada request dokumen yang pending.
    </div>
    @else
    <div class="table-responsive">
        <table class="sima-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mahasiswa</th>
                    <th>Jurusan</th>
                    <th>Tipe Dokumen</th>
                    <th style="text-align:center;">Tanggal Request</th>
                    <th style="text-align:center;">Menunggu</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingRequests as $i => $req)
                @php
                    $menunggu = now()->diffInDays(\Carbon\Carbon::parse($req->created_at));
                    $menungguBadge = $menunggu >= 7 ? 'sima-badge--red' : ($menunggu >= 3 ? 'sima-badge--amber' : 'sima-badge--blue');
                @endphp
                <tr>
                    <td>{{ $pendingRequests->firstItem() + $loop->index }}</td>
                    <td>
                        <div class="fw-600">{{ $req->nama }}</div>
                        <code style="font-size:11px;color:var(--c-text-3);">{{ $req->npm }}</code>
                    </td>
                    <td style="font-size:13px;color:var(--c-text-3);">{{ $req->namaJurusan ?? '—' }}</td>
                    <td>
                        <span class="sima-badge sima-badge--amber">{{ $req->tipeDkmn }}</span>
                    </td>
                    <td style="text-align:center;font-size:13px;">
                        {{ \Carbon\Carbon::parse($req->created_at)->isoFormat('D MMM YYYY') }}
                    </td>
                    <td style="text-align:center;">
                        <span class="sima-badge {{ $menungguBadge }}">{{ $menunggu }} hari</span>
                    </td>
                    <td>
                        <a href="{{ route('kln.dokumen.page') }}"
                           class="sima-btn sima-btn--outline sima-btn--sm">
                            <i class="fas fa-external-link-alt me-1"></i> Proses
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($pendingRequests->hasPages())
    <div style="padding:14px 20px;border-top:1px solid var(--c-border);">
        {{ $pendingRequests->links('vendor.pagination.sima') }}
    </div>
    @endif
    @endif
</div>

@endsection
