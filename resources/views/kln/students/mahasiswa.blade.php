@extends('layouts.sima')

@section('page_title',    $mahasiswa->nama)
@section('page_section',  'STUDENTS & LECTURERS')
@section('page_subtitle', 'Student detail data')

@section('main_content')

@php
    $expiredCount  = $dokumen->filter(fn($d) => $d->tglKdlwrs && \Carbon\Carbon::parse($d->tglKdlwrs)->startOfDay()->lt(today()))->count();
    $warningCount  = $dokumen->filter(fn($d) => $d->tglKdlwrs && \Carbon\Carbon::parse($d->tglKdlwrs)->startOfDay()->gte(today()) && \Carbon\Carbon::parse($d->tglKdlwrs)->lte(today()->addDays(30)))->count();
    $validCount    = $dokumen->filter(fn($d) => $d->tglKdlwrs && \Carbon\Carbon::parse($d->tglKdlwrs)->gt(today()->addDays(30)))->count();
    $pendingCount  = $dokumen->filter(fn($d) => ($d->status ?? '') === 'pending')->count();
    $approvedCount = $dokumen->filter(fn($d) => ($d->status ?? '') === 'approved')->count();
@endphp

{{-- ── BACK BUTTON ─────────────────────────────────── --}}
<div class="mb-3">
    <a href="{{ route('kln.students.page') }}" class="sima-btn sima-btn--outline sima-btn--sm">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

{{-- ── PROFILE HEADER ──────────────────────────────── --}}
<div class="sima-card mb-4" style="padding:24px;">
    <div class="d-flex align-items-center gap-4 flex-wrap">
        <div style="width:80px;height:80px;border-radius:50%;overflow:hidden;border:3px solid var(--c-border);flex-shrink:0;background:var(--c-accent);display:flex;align-items:center;justify-content:center;">
            @if($mahasiswa->fotoProfil)
                <img src="{{ route('kln.students.foto', $mahasiswa->id) }}" style="width:100%;height:100%;object-fit:cover;" alt="{{ $mahasiswa->nama }}">
            @else
                <span style="font-size:32px;font-weight:700;color:#fff;">{{ strtoupper(substr($mahasiswa->nama, 0, 1)) }}</span>
            @endif
        </div>
        <div style="flex:1;">
            <h4 class="mb-1" style="font-family:var(--f-display);font-weight:700;">{{ $mahasiswa->nama }}</h4>
            <div class="d-flex gap-2 flex-wrap" style="margin-top:4px;">
                <span class="sima-badge sima-badge--blue">Mahasiswa</span>
                <span class="sima-badge {{ $mahasiswa->status === 'active' ? 'sima-badge--green' : 'sima-badge--red' }}">
                    {{ $mahasiswa->status === 'active' ? 'Active' : 'Inactive' }}
                </span>
                @if($mahasiswa->namaJurusan)
                    <span class="sima-badge sima-badge--amber">{{ $mahasiswa->namaJurusan }}</span>
                @endif
                @if($mahasiswa->tipeMahasiswa)
                    <span class="sima-badge sima-badge--purple">{{ $mahasiswa->tipeMahasiswa }}</span>
                @endif
                @if(isset($mahasiswa->isOnline))
                    <span class="sima-badge {{ $mahasiswa->isOnline ? 'sima-badge--green' : 'sima-badge--teal' }}">
                        {{ $mahasiswa->isOnline ? 'Online' : 'Offline' }}
                    </span>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('kln.users.mahasiswa.edit', $mahasiswa->user_id) }}" class="sima-btn sima-btn--blue sima-btn--sm">
                <i class="fas fa-pen me-1"></i> Edit
            </a>
        </div>
    </div>
</div>

{{-- ── DOCUMENT STATUS SUMMARY ────────────────────── --}}
@if($dokumen->isNotEmpty())
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--red" style="padding:14px 16px;">
            <div class="sima-stat__icon sima-stat__icon--red" style="width:36px;height:36px;font-size:14px;"><i class="fas fa-exclamation-circle"></i></div>
            <div class="sima-stat__label" style="font-size:11px;">Expired</div>
            <div class="sima-stat__value" style="font-size:20px;">{{ $expiredCount }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--amber" style="padding:14px 16px;">
            <div class="sima-stat__icon sima-stat__icon--amber" style="width:36px;height:36px;font-size:14px;"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="sima-stat__label" style="font-size:11px;">Warning</div>
            <div class="sima-stat__value" style="font-size:20px;">{{ $warningCount }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--green" style="padding:14px 16px;">
            <div class="sima-stat__icon sima-stat__icon--green" style="width:36px;height:36px;font-size:14px;"><i class="fas fa-check-circle"></i></div>
            <div class="sima-stat__label" style="font-size:11px;">Valid</div>
            <div class="sima-stat__value" style="font-size:20px;">{{ $validCount }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--blue" style="padding:14px 16px;">
            <div class="sima-stat__icon sima-stat__icon--blue" style="width:36px;height:36px;font-size:14px;"><i class="fas fa-clock"></i></div>
            <div class="sima-stat__label" style="font-size:11px;">Pending</div>
            <div class="sima-stat__value" style="font-size:20px;">{{ $pendingCount }}</div>
        </div>
    </div>
</div>
@endif

{{-- ── PERSONAL INFO ───────────────────────────────── --}}
<div class="sima-card mb-4" style="padding:24px;">
    <h6 class="fw-700 mb-3" style="font-family:var(--f-display);letter-spacing:.5px;">
        <i class="fas fa-id-card me-2" style="color:var(--c-accent);"></i>Personal Information
    </h6>
    <div class="row g-3">
        <div class="col-12 col-md-6">
            <div class="d-flex flex-column gap-3">
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">NPM</div>
                    <div class="fw-600" style="font-family:var(--f-mono);">{{ $mahasiswa->npm ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Email</div>
                    <div class="fw-600">{{ $mahasiswa->email ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">WhatsApp Number</div>
                    <div class="fw-600">{{ $mahasiswa->noWa ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Emergency Number</div>
                    <div class="fw-600">{{ $mahasiswa->noDarurat ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Date of Birth</div>
                    <div class="fw-600">
                        {{ $mahasiswa->tglLahir ? \Carbon\Carbon::parse($mahasiswa->tglLahir)->translatedFormat('d F Y') : '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Active Period</div>
                    <div class="fw-600">
                        @if($mahasiswa->masaAktif)
                            @php $mAktif = \Carbon\Carbon::parse($mahasiswa->masaAktif); @endphp
                            @if($mAktif->startOfDay()->lt(today()))
                                <span style="color:var(--c-red)">{{ $mAktif->translatedFormat('d F Y') }} (Expired)</span>
                            @elseif($mAktif->lte(today()->addDays(30)))
                                <span style="color:#d97706">{{ $mAktif->translatedFormat('d F Y') }} (Expiring soon)</span>
                            @else
                                {{ $mAktif->translatedFormat('d F Y') }}
                            @endif
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="d-flex flex-column gap-3">
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Nationality</div>
                    <div class="fw-600">{{ $mahasiswa->warNeg ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Department</div>
                    <div class="fw-600">{{ $mahasiswa->namaJurusan ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Student Type</div>
                    <div class="fw-600">{{ $mahasiswa->tipeMahasiswa ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Enrollment Year</div>
                    <div class="fw-600">{{ $mahasiswa->tahunMasuk ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Home Address</div>
                    <div class="fw-600">{{ $mahasiswa->alamatAsal ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Indonesia Address</div>
                    <div class="fw-600">{{ $mahasiswa->alamatIndo ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── DOKUMEN ─────────────────────────────────────── --}}
<div class="sima-card">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Important Documents</h5>
            <div class="sima-card__subtitle">{{ $dokumen->count() }} documents</div>
        </div>
    </div>

    @if($dokumen->isEmpty())
        <div class="text-center text-muted py-4" style="padding:0 24px 24px;">
            <i class="fas fa-folder-open fa-2x mb-2 d-block" style="opacity:.3;"></i>
            No documents uploaded.
        </div>
    @else
    <div class="table-responsive">
        <table class="sima-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Document Type</th>
                    <th>Document Name</th>
                    <th>Issuer</th>
                    <th>Doc. Number</th>
                    <th>Issued</th>
                    <th>Expiry</th>
                    <th>Condition</th>
                    <th>Status</th>
                    <th>File</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dokumen as $i => $dok)
                @php
                    $tipe      = str_replace('_', ' ', $dok->tipeDkmn ?? '-');
                    $expiry    = $dok->tglKdlwrs ? \Carbon\Carbon::parse($dok->tglKdlwrs) : null;
                    $isExpired = $expiry && $expiry->startOfDay()->lt(today());
                    $isWarning = $expiry && !$isExpired && $expiry->lte(today()->addDays(30));
                    if ($isExpired) {
                        $kondisiBadge = 'sima-badge--red';
                        $kondisiLabel = 'Expired';
                        $kondisiIcon  = 'fa-exclamation-circle';
                    } elseif ($isWarning) {
                        $kondisiBadge = 'sima-badge--amber';
                        $kondisiLabel = 'Warning';
                        $kondisiIcon  = 'fa-exclamation-triangle';
                    } elseif ($expiry) {
                        $kondisiBadge = 'sima-badge--green';
                        $kondisiLabel = 'Valid';
                        $kondisiIcon  = 'fa-check-circle';
                    } else {
                        $kondisiBadge = '';
                        $kondisiLabel = '-';
                        $kondisiIcon  = '';
                    }
                    $previewUrl   = $dok->file_path ? route('kln.students.dokumen.preview',  [$mahasiswa->id, $dok->id]) : null;
                    $downloadUrl  = $dok->file_path ? route('kln.students.dokumen.download', [$mahasiswa->id, $dok->id]) : null;
                    $statusUrl    = route('kln.students.dokumen.status', [$mahasiswa->id, $dok->id]);
                    $isImage      = $dok->file_path && str_contains($dok->mimeType ?? '', 'image');
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><span class="sima-badge sima-badge--amber" style="font-size:11px;">{{ $tipe }}</span></td>
                    <td>{{ $dok->namaDkmn ?? '-' }}</td>
                    <td>{{ $dok->penerbit ?? '-' }}</td>
                    <td><code>{{ $dok->noDkmn ?? '-' }}</code></td>
                    <td>{{ $dok->tglTerbit ? \Carbon\Carbon::parse($dok->tglTerbit)->format('d/m/Y') : '-' }}</td>
                    <td>
                        @if($dok->tglKdlwrs)
                            <span class="{{ $isExpired ? 'text-danger fw-600' : ($isWarning ? 'text-warning fw-600' : '') }}">
                                {{ \Carbon\Carbon::parse($dok->tglKdlwrs)->format('d/m/Y') }}
                            </span>
                        @else -
                        @endif
                    </td>
                    <td>
                        @if($kondisiLabel !== '-')
                            <span class="sima-badge {{ $kondisiBadge }}">
                                <i class="fas {{ $kondisiIcon }} me-1"></i>{{ $kondisiLabel }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @php $curStatus = $dok->status ?? 'pending'; @endphp
                        <select class="sima-input status-select"
                                data-url="{{ $statusUrl }}"
                                data-id="{{ $dok->id }}"
                                style="min-width:110px;font-size:12px;padding:4px 8px;">
                            <option value="pending"  {{ $curStatus === 'pending'  ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $curStatus === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $curStatus === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </td>
                    <td>
                        @if($dok->file_path)
                            <div style="display:flex;gap:6px;">
                                <button class="sima-btn sima-btn--outline sima-btn--sm"
                                        onclick="previewFile('{{ $previewUrl }}', '{{ $isImage ? 'image' : 'pdf' }}')"
                                        title="View file">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ $downloadUrl }}" class="sima-btn sima-btn--outline sima-btn--sm" title="Download file">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        @else
                            <span class="text-muted" style="font-size:12px;">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- ── PREVIEW MODAL ───────────────────────────────── --}}
<div id="previewModal" style="display:none;position:fixed;inset:0;z-index:1060;background:rgba(0,0,0,.55);
     align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;width:90%;max-width:900px;
                height:85vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.3);">
        <div style="display:flex;align-items:center;justify-content:space-between;
                    padding:14px 20px;border-bottom:1px solid var(--c-border);">
            <span style="font-weight:600;font-size:14px;">Document Preview</span>
            <button onclick="closePreview()" style="border:none;background:none;font-size:20px;cursor:pointer;color:var(--c-text-3);">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="previewBody" style="flex:1;overflow:auto;display:flex;align-items:center;justify-content:center;background:#f4f5f9;">
            {{-- filled by JS --}}
        </div>
    </div>
</div>

@endsection

@push('page_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
// ── STATUS UPDATE ──────────────────────────────────
document.querySelectorAll('.status-select').forEach(sel => {
    sel.addEventListener('change', function () {
        const url    = this.dataset.url;
        const status = this.value;
        const orig   = this.querySelector('option[selected]');

        fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ status }),
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                alert('Failed to change status.');
                this.value = orig ? orig.value : 'pending';
            }
        })
        .catch(() => {
            alert('An error occurred.');
        });
    });
});

// ── FILE PREVIEW ───────────────────────────────────
function previewFile(url, type) {
    const body = document.getElementById('previewBody');
    if (type === 'image') {
        body.innerHTML = `<img src="${url}" style="max-width:100%;max-height:100%;object-fit:contain;padding:16px;">`;
    } else {
        body.innerHTML = `<iframe src="${url}" style="width:100%;height:100%;border:none;"></iframe>`;
    }
    const modal = document.getElementById('previewModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePreview() {
    document.getElementById('previewModal').style.display = 'none';
    document.getElementById('previewBody').innerHTML = '';
    document.body.style.overflow = '';
}

document.getElementById('previewModal').addEventListener('click', function (e) {
    if (e.target === this) closePreview();
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closePreview();
});
</script>
@endpush
