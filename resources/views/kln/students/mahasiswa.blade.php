@extends('layouts.sima')

@section('page_title',    $mahasiswa->nama)
@section('page_section',  'STUDENTS & LECTURERS')
@section('page_subtitle', 'Detail data mahasiswa')

@section('main_content')

{{-- ── BACK BUTTON ─────────────────────────────────── --}}
<div class="mb-3">
    <a href="{{ route('kln.students.page') }}" class="sima-btn sima-btn--outline sima-btn--sm">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

{{-- ── PROFILE HEADER ──────────────────────────────── --}}
<div class="sima-card mb-4" style="padding: 24px;">
    <div class="d-flex align-items-center gap-4 flex-wrap">
        <div style="width:72px;height:72px;border-radius:50%;background:var(--c-accent);
                    display:flex;align-items:center;justify-content:center;
                    font-size:28px;font-weight:700;color:#fff;flex-shrink:0;">
            {{ strtoupper(substr($mahasiswa->nama, 0, 1)) }}
        </div>
        <div>
            <h4 class="mb-1" style="font-family:var(--f-display);font-weight:700;">{{ $mahasiswa->nama }}</h4>
            <div class="d-flex gap-2 flex-wrap">
                <span class="sima-badge sima-badge--blue">Mahasiswa</span>
                <span class="sima-badge {{ $mahasiswa->status === 'active' ? 'sima-badge--green' : 'sima-badge--red' }}">
                    {{ $mahasiswa->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                </span>
                @if($mahasiswa->namaJurusan)
                    <span class="sima-badge sima-badge--amber">{{ $mahasiswa->namaJurusan }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── PERSONAL INFO ───────────────────────────────── --}}
<div class="sima-card mb-4" style="padding: 24px;">
    <h6 class="fw-700 mb-3" style="font-family:var(--f-display);letter-spacing:.5px;">
        <i class="fas fa-id-card me-2" style="color:var(--c-accent);"></i>Informasi Pribadi
    </h6>
    <div class="row g-3">
        <div class="col-12 col-md-6">
            <div class="d-flex flex-column gap-3">
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">NPM</div>
                    <div class="fw-600">{{ $mahasiswa->npm ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Email</div>
                    <div class="fw-600">{{ $mahasiswa->email ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">No. WhatsApp</div>
                    <div class="fw-600">{{ $mahasiswa->noWa ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">No. Darurat</div>
                    <div class="fw-600">{{ $mahasiswa->noDarurat ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Tanggal Lahir</div>
                    <div class="fw-600">
                        {{ $mahasiswa->tglLahir ? \Carbon\Carbon::parse($mahasiswa->tglLahir)->translatedFormat('d F Y') : '-' }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="d-flex flex-column gap-3">
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Kewarganegaraan</div>
                    <div class="fw-600">{{ $mahasiswa->warNeg ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Jurusan</div>
                    <div class="fw-600">{{ $mahasiswa->namaJurusan ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Alamat Asal</div>
                    <div class="fw-600">{{ $mahasiswa->alamatAsal ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Alamat di Indonesia</div>
                    <div class="fw-600">{{ $mahasiswa->alamatIndo ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── DOKUMEN ─────────────────────────────────────── --}}
<div class="sima-card">
    <h6 class="fw-700 mb-3" style="font-family:var(--f-display);letter-spacing:.5px;padding: 24px 24px 0;">
        <i class="fas fa-folder-open me-2" style="color:var(--c-accent);"></i>Dokumen Penting
        <span class="sima-badge sima-badge--blue ms-2">{{ $dokumen->count() }}</span>
    </h6>

    @if($dokumen->isEmpty())
        <div class="text-center text-muted py-4" style="padding: 0 24px 24px;">
            <i class="fas fa-folder-open fa-2x mb-2 d-block" style="opacity:.3;"></i>
            Belum ada dokumen yang diupload.
        </div>
    @else
    <div class="table-responsive">
        <table class="sima-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tipe Dokumen</th>
                    <th>Nama Dokumen</th>
                    <th>Penerbit</th>
                    <th>No. Dokumen</th>
                    <th>Terbit</th>
                    <th>Kedaluwarsa</th>
                    <th>Kondisi</th>
                    <th>Status</th>
                    <th>File</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dokumen as $i => $dok)
                @php
                    $tipe      = str_replace('_', ' ', $dok->tipeDkmn ?? '-');
                    $expiry    = $dok->tglKdlwrs ? \Carbon\Carbon::parse($dok->tglKdlwrs) : null;
                    $isExpired = $expiry && $expiry->isPast();
                    $isWarning = $expiry && !$isExpired && $expiry->lte(now()->addDays(30));
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
                        $kondisiLabel = 'Aman';
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
                                        title="Lihat file">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ $downloadUrl }}" class="sima-btn sima-btn--outline sima-btn--sm" title="Unduh file">
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
            <span style="font-weight:600;font-size:14px;">Preview Dokumen</span>
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
                alert('Gagal mengubah status.');
                this.value = orig ? orig.value : 'pending';
            }
        })
        .catch(() => {
            alert('Terjadi kesalahan.');
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
