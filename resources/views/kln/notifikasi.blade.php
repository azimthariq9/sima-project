@extends('layouts.sima')

@section('page_title',    'KLN Notifications')
@section('page_section',  'NOTIFIKASI')
@section('page_subtitle', 'Alert board — monitoring issues that need attention')

@section('main_content')

{{-- ── STAT CARDS ───────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--red">
            <div class="sima-stat__icon sima-stat__icon--red"><i class="fas fa-exclamation-circle"></i></div>
            <div class="sima-stat__label">Expired Documents</div>
            <div class="sima-stat__value">{{ $stats['expired'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--amber">
            <div class="sima-stat__icon sima-stat__icon--amber"><i class="fas fa-clock"></i></div>
            <div class="sima-stat__label">Expiring Soon</div>
            <div class="sima-stat__value">{{ $stats['nearExpired'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--blue">
            <div class="sima-stat__icon sima-stat__icon--blue"><i class="fas fa-user-slash"></i></div>
            <div class="sima-stat__label">Inactive Accounts</div>
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
            <h5 class="sima-card__title" style="margin:0;">Expired Documents</h5>
            <span class="sima-badge sima-badge--red">{{ $stats['expired'] }}</span>
        </div>
    </div>

    @if($expiredDokumen->isEmpty())
    <div class="text-center text-muted py-4" style="font-size:13px;">
        <i class="fas fa-check-circle fa-2x d-block mb-2" style="color:var(--c-green);opacity:.6;"></i>
        No expired documents.
    </div>
    @else
    <div class="table-responsive">
        <table class="sima-table" data-datatable>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Major</th>
                    <th>Document Type</th>
                    <th style="text-align:center;">Expiry Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($expiredDokumen as $doc)
                <tr>
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
                            <i class="fas fa-eye me-1"></i> View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- ── SECTION 2: HAMPIR KADALUWARSA ──────────────────── --}}
<div class="sima-card mb-4">
    <div class="sima-card__header" style="border-left: 4px solid var(--c-amber);">
        <div style="display:flex;align-items:center;gap:8px;">
            <i class="fas fa-clock" style="color:var(--c-amber);"></i>
            <h5 class="sima-card__title" style="margin:0;">Expiring Soon <small style="font-weight:400;font-size:12px;color:var(--c-text-3);">(≤ 30 days)</small></h5>
            <span class="sima-badge sima-badge--amber">{{ $stats['nearExpired'] }}</span>
        </div>
    </div>

    @if($nearExpiredDokumen->isEmpty())
    <div class="text-center text-muted py-4" style="font-size:13px;">
        <i class="fas fa-check-circle fa-2x d-block mb-2" style="color:var(--c-green);opacity:.6;"></i>
        No documents expiring within 30 days.
    </div>
    @else
    <div class="table-responsive">
        <table class="sima-table" data-datatable>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Major</th>
                    <th>Document Type</th>
                    <th style="text-align:center;">Expiry</th>
                    <th style="text-align:center;">Days Left</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($nearExpiredDokumen as $doc)
                @php
                    $sisa = (int) $doc->sisa_hari;
                    $sisaBadge = $sisa <= 7 ? 'sima-badge--red' : ($sisa <= 14 ? 'sima-badge--amber' : 'sima-badge--blue');
                @endphp
                <tr>
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
                        <span class="sima-badge {{ $sisaBadge }}">{{ $sisa }} days</span>
                    </td>
                    <td>
                        <a href="{{ route('kln.students.mahasiswa', $doc->mahasiswa_id) }}"
                           class="sima-btn sima-btn--outline sima-btn--sm">
                            <i class="fas fa-eye me-1"></i> View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- ── SECTION 3: AKUN NONAKTIF ────────────────────────── --}}
<div class="sima-card mb-4">
    <div class="sima-card__header" style="border-left: 4px solid var(--c-blue);">
        <div style="display:flex;align-items:center;gap:8px;">
            <i class="fas fa-user-slash" style="color:var(--c-blue);"></i>
            <h5 class="sima-card__title" style="margin:0;">Inactive Student Accounts</h5>
            <span class="sima-badge sima-badge--blue">{{ $stats['inactive'] }}</span>
        </div>
    </div>

    @if($inactiveMahasiswa->isEmpty())
    <div class="text-center text-muted py-4" style="font-size:13px;">
        <i class="fas fa-check-circle fa-2x d-block mb-2" style="color:var(--c-green);opacity:.6;"></i>
        All student accounts are active.
    </div>
    @else
    <div class="table-responsive">
        <table class="sima-table" data-datatable>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Major</th>
                    <th style="text-align:center;">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($inactiveMahasiswa as $mhs)
                <tr>
                    <td>
                        <div class="fw-600">{{ $mhs->nama }}</div>
                        <code style="font-size:11px;color:var(--c-text-3);">{{ $mhs->npm }}</code>
                    </td>
                    <td style="font-size:13px;color:var(--c-text-3);">{{ $mhs->email }}</td>
                    <td style="font-size:13px;color:var(--c-text-3);">{{ $mhs->namaJurusan ?? '—' }}</td>
                    <td style="text-align:center;">
                        <span class="sima-badge sima-badge--red">Inactive</span>
                    </td>
                    <td>
                        <a href="{{ route('kln.users.page') }}"
                           class="sima-btn sima-btn--outline sima-btn--sm">
                            <i class="fas fa-key me-1"></i> Manage
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- ── SECTION 4: REQUEST DOKUMEN PENDING ─────────────── --}}
<div class="sima-card mb-4">
    <div class="sima-card__header" style="border-left: 4px solid var(--c-amber);">
        <div style="display:flex;align-items:center;gap:8px;">
            <i class="fas fa-file-alt" style="color:var(--c-amber);"></i>
            <h5 class="sima-card__title" style="margin:0;">Pending Document Requests</h5>
            <span class="sima-badge sima-badge--amber">{{ $stats['pending'] }}</span>
        </div>
    </div>

    @if($pendingRequests->isEmpty())
    <div class="text-center text-muted py-4" style="font-size:13px;">
        <i class="fas fa-check-circle fa-2x d-block mb-2" style="color:var(--c-green);opacity:.6;"></i>
        No pending document requests.
    </div>
    @else
    <div class="table-responsive">
        <table class="sima-table" data-datatable>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Major</th>
                    <th>Document Type</th>
                    <th style="text-align:center;">Request Date</th>
                    <th style="text-align:center;">Waiting</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingRequests as $req)
                @php
                    $menunggu = now()->diffInDays(\Carbon\Carbon::parse($req->created_at));
                    $menungguBadge = $menunggu >= 7 ? 'sima-badge--red' : ($menunggu >= 3 ? 'sima-badge--amber' : 'sima-badge--blue');
                @endphp
                <tr>
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
                        <span class="sima-badge {{ $menungguBadge }}">{{ $menunggu }} days</span>
                    </td>
                    <td>
                        <a href="{{ route('kln.dokumen.page') }}"
                           class="sima-btn sima-btn--outline sima-btn--sm">
                            <i class="fas fa-external-link-alt me-1"></i> Process
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection
