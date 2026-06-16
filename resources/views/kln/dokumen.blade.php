@extends('layouts.sima')

@section('page_title',    'Request Dokumen')
@section('page_section',  'KERJA SAMA LUAR NEGERI')
@section('page_subtitle', 'Pengelolaan permintaan dokumen dari mahasiswa')

@section('main_content')

{{-- ── STAT CARDS ──────────────────────────────────── --}}
@php
    $total    = $requests->count();
    $pending  = $requests->where('status.value', 'pending')->count()  ?: $requests->filter(fn($r) => ($r->status?->value ?? $r->status) === 'pending')->count();
    $approved = $requests->filter(fn($r) => ($r->status?->value ?? $r->status) === 'approved')->count();
    $rejected = $requests->filter(fn($r) => ($r->status?->value ?? $r->status) === 'rejected')->count();
@endphp

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--blue">
            <div class="sima-stat__icon sima-stat__icon--blue"><i class="fas fa-file-alt"></i></div>
            <div class="sima-stat__label">Total Request</div>
            <div class="sima-stat__value">{{ $total }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--amber">
            <div class="sima-stat__icon sima-stat__icon--amber"><i class="fas fa-clock"></i></div>
            <div class="sima-stat__label">Pending</div>
            <div class="sima-stat__value">{{ $pending }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--teal">
            <div class="sima-stat__icon sima-stat__icon--teal"><i class="fas fa-check-circle"></i></div>
            <div class="sima-stat__label">Approved</div>
            <div class="sima-stat__value">{{ $approved }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--purple">
            <div class="sima-stat__icon sima-stat__icon--purple"><i class="fas fa-times-circle"></i></div>
            <div class="sima-stat__label">Rejected</div>
            <div class="sima-stat__value">{{ $rejected }}</div>
        </div>
    </div>
</div>

{{-- ── TABEL ───────────────────────────────────────── --}}
<div class="sima-card">

    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Daftar Request Dokumen</h5>
            <div class="sima-card__subtitle">Semua permintaan dokumen dari mahasiswa</div>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <select id="filterStatus" class="sima-input" style="min-width:140px;" onchange="filterTable()">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
            <input id="searchInput" type="text" placeholder="Cari mahasiswa / tipe..."
                   class="sima-input" style="min-width:220px;" oninput="filterTable()">
        </div>
    </div>

    <div style="overflow-x:auto;padding:8px 0">
        <table class="sima-table" id="mainTable">
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>Tipe Dokumen</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse($requests as $req)
                @php
                    $statusVal = $req->status?->value ?? (is_string($req->status) ? $req->status : 'pending');
                    $tipeVal   = $req->tipeDkmn?->value ?? (is_string($req->tipeDkmn) ? $req->tipeDkmn : '-');
                    $badgeClass = match($statusVal) {
                        'approved' => 'sima-badge--green',
                        'rejected' => 'sima-badge--red',
                        default    => 'sima-badge--amber',
                    };
                @endphp
                <tr id="row-{{ $req->id }}"
                    data-mahasiswa="{{ strtolower($req->mahasiswa?->nama ?? '') }}"
                    data-tipe="{{ strtolower($tipeVal) }}"
                    data-status="{{ $statusVal }}">
                    <td>
                        <div style="font-weight:600;color:var(--c-text-1)">{{ $req->mahasiswa?->nama ?? '-' }}</div>
                        <div style="font-size:12px;color:var(--c-text-3)">{{ $req->mahasiswa?->npm ?? '' }}</div>
                    </td>
                    <td>{{ str_replace('_', ' ', $tipeVal) }}</td>
                    <td style="max-width:200px;color:var(--c-text-2);font-size:13px">
                        {{ $req->message ?? '-' }}
                    </td>
                    <td>
                        <span id="badge-{{ $req->id }}" class="sima-badge {{ $badgeClass }}">
                            {{ ucfirst($statusVal) }}
                        </span>
                    </td>
                    <td style="font-size:12px;color:var(--c-text-3);white-space:nowrap">
                        {{ $req->created_at ? \Carbon\Carbon::parse($req->created_at)->format('d M Y') : '-' }}
                    </td>
                    <td style="text-align:center;white-space:nowrap">
                        <button onclick="showDetail({{ $req->id }})"
                                class="sima-btn sima-btn--blue sima-btn--sm">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                        <button onclick="deleteReq({{ $req->id }})"
                                class="sima-btn sima-btn--danger sima-btn--sm"
                                style="margin-left:6px">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:var(--c-text-3)">
                        <i class="fas fa-inbox" style="font-size:32px;margin-bottom:10px;display:block"></i>
                        Belum ada request dokumen
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- ── MODAL DETAIL ─────────────────────────────────── --}}
<div id="detailModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:var(--c-surface);border-radius:var(--radius-lg);width:100%;max-width:520px;box-shadow:var(--shadow-lg);overflow:hidden;margin:16px">

        {{-- Header modal --}}
        <div style="padding:20px 24px 16px;border-bottom:1px solid var(--c-border-soft);display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-size:15px;font-weight:700;color:var(--c-text-1)">Detail Request Dokumen</div>
                <div style="font-size:12px;color:var(--c-text-3);margin-top:2px">Upload PDF untuk menyetujui permintaan</div>
            </div>
            <button onclick="closeModal()" style="width:32px;height:32px;border:1px solid var(--c-border);border-radius:8px;background:none;cursor:pointer;color:var(--c-text-3);font-size:16px">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Body modal --}}
        <div style="padding:20px 24px;">
            <div id="modalContent" style="display:grid;gap:10px;margin-bottom:20px;">
                {{-- diisi via JS --}}
            </div>

            {{-- File sudah ada (approved) --}}
            <div id="fileInfo" style="display:none;background:var(--c-bg);border-radius:12px;padding:14px 16px;margin-bottom:16px;">
                <div style="font-size:11px;font-weight:600;color:var(--c-text-3);text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px">
                    Dokumen Terupload
                </div>
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:36px;height:36px;background:rgba(239,68,68,.1);border-radius:8px;display:grid;place-items:center;flex-shrink:0">
                        <i class="fas fa-file-pdf" style="color:#ef4444;font-size:16px"></i>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div id="fileName" style="font-size:13px;font-weight:600;color:var(--c-text-1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"></div>
                        <div id="fileSize" style="font-size:11px;color:var(--c-text-3);margin-top:2px"></div>
                    </div>
                    <a id="fileDownload" href="#" class="sima-btn sima-btn--sm sima-btn--outline" style="flex-shrink:0">
                        <i class="fas fa-download"></i> Unduh
                    </a>
                    <button type="button" onclick="gantiFile()" class="sima-btn sima-btn--sm sima-btn--danger" style="flex-shrink:0">
                        <i class="fas fa-rotate"></i> Ganti
                    </button>
                </div>
            </div>

            {{-- Upload form (hanya tampil kalau belum approved / sedang ganti file) --}}
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                    <label id="uploadLabel" style="font-size:12px;font-weight:600;color:var(--c-text-2)">
                        Upload Dokumen (PDF)
                    </label>
                    <button type="button" id="batalGanti" onclick="batalGanti()"
                            style="display:none;font-size:12px;color:var(--c-text-3);background:none;border:none;cursor:pointer;">
                        <i class="fas fa-arrow-left"></i> Batal
                    </button>
                </div>
                <input type="file" name="file" accept="application/pdf,image/*" class="sima-input" style="margin-bottom:12px">
                <button type="submit" id="uploadBtn" class="sima-btn sima-btn--full" style="justify-content:center">
                    <i class="fas fa-upload"></i> Upload & Approve
                </button>
            </form>
        </div>

    </div>
</div>

@endsection

@push('page_js')
<script>
let currentId = null;

/* ── FILTER / SEARCH ─────────────────────── */
function filterTable() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('filterStatus').value;
    document.querySelectorAll('#tableBody tr[id^="row-"]').forEach(row => {
        const mhs    = row.dataset.mahasiswa ?? '';
        const tipe   = row.dataset.tipe ?? '';
        const rowSts = row.dataset.status ?? '';
        const matchSearch = !search || mhs.includes(search) || tipe.includes(search);
        const matchStatus = !status || rowSts === status;
        row.style.display = (matchSearch && matchStatus) ? '' : 'none';
    });
}

/* ── DETAIL MODAL ────────────────────────── */
function showDetail(id) {
    currentId = id;
    fetch('/kln/dokumen/' + id)
        .then(res => res.json())
        .then(data => {
            const badgeColor = data.status === 'approved'
                ? 'var(--c-green)' : (data.status === 'rejected'
                ? 'var(--c-red)' : 'var(--c-amber)');

            document.getElementById('modalContent').innerHTML = `
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div style="background:var(--c-bg);border-radius:10px;padding:12px;">
                        <div style="font-size:11px;color:var(--c-text-3);font-weight:600;text-transform:uppercase;letter-spacing:.05em">Mahasiswa</div>
                        <div style="font-size:14px;font-weight:600;color:var(--c-text-1);margin-top:4px">${data.mahasiswa ?? '-'}</div>
                        <div style="font-size:11px;color:var(--c-text-3);margin-top:2px">${data.npm ?? ''}</div>
                    </div>
                    <div style="background:var(--c-bg);border-radius:10px;padding:12px;">
                        <div style="font-size:11px;color:var(--c-text-3);font-weight:600;text-transform:uppercase;letter-spacing:.05em">Tipe</div>
                        <div style="font-size:14px;font-weight:600;color:var(--c-text-1);margin-top:4px">${(data.tipe ?? '-').replace(/_/g,' ')}</div>
                    </div>
                    <div style="background:var(--c-bg);border-radius:10px;padding:12px;">
                        <div style="font-size:11px;color:var(--c-text-3);font-weight:600;text-transform:uppercase;letter-spacing:.05em">Status</div>
                        <div style="font-size:14px;font-weight:700;color:${badgeColor};margin-top:4px">${data.status ?? '-'}</div>
                    </div>
                    <div style="background:var(--c-bg);border-radius:10px;padding:12px;">
                        <div style="font-size:11px;color:var(--c-text-3);font-weight:600;text-transform:uppercase;letter-spacing:.05em">Keterangan</div>
                        <div style="font-size:13px;color:var(--c-text-2);margin-top:4px">${data.message ?? '-'}</div>
                    </div>
                </div>
            `;

            const uploadForm = document.getElementById('uploadForm');
            const fileInfo   = document.getElementById('fileInfo');

            if (data.file) {
                // Ada file — tampilkan info file, sembunyikan form upload
                const fileName   = data.file.path.split('/').pop();
                const fileSizeKb = data.file.fileSize
                    ? (data.file.fileSize / 1024).toFixed(1) + ' KB'
                    : '';
                document.getElementById('fileName').textContent = fileName;
                document.getElementById('fileSize').textContent = fileSizeKb;
                // Pakai route aman (lewat auth middleware), bukan direct storage URL
                document.getElementById('fileDownload').href    = '/kln/dokumen/' + data.id + '/file';
                fileInfo.style.display   = 'block';
                uploadForm.style.display = 'none';
            } else {
                // Belum ada file — tampilkan form upload
                fileInfo.style.display   = 'none';
                uploadForm.style.display = 'block';
            }

            document.getElementById('detailModal').style.display = 'flex';
        })
        .catch(() => alert('Gagal memuat detail request'));
}

function closeModal() {
    document.getElementById('detailModal').style.display = 'none';
    document.getElementById('uploadForm').reset();
    resetUploadForm();
}

function gantiFile() {
    document.getElementById('fileInfo').style.display   = 'none';
    document.getElementById('uploadForm').style.display = 'block';
    document.getElementById('uploadLabel').textContent  = 'Ganti File (PDF)';
    document.getElementById('uploadBtn').innerHTML      = '<i class="fas fa-rotate"></i> Ganti & Simpan';
    document.getElementById('batalGanti').style.display = 'inline';
}

function batalGanti() {
    document.getElementById('fileInfo').style.display   = 'block';
    document.getElementById('uploadForm').style.display = 'none';
    resetUploadForm();
}

function resetUploadForm() {
    document.getElementById('uploadForm').reset();
    document.getElementById('uploadLabel').textContent  = 'Upload Dokumen (PDF)';
    document.getElementById('uploadBtn').innerHTML      = '<i class="fas fa-upload"></i> Upload & Approve';
    document.getElementById('batalGanti').style.display = 'none';
}

/* ── UPLOAD ──────────────────────────────── */
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const btn = document.getElementById('uploadBtn');
    const originalLabel = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengupload...';

    fetch('/kln/dokumen/' + currentId + '/upload', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Update badge di tabel
            const badge = document.getElementById('badge-' + currentId);
            if (badge) { badge.className = 'sima-badge sima-badge--green'; badge.textContent = 'Approved'; }
            const row = document.getElementById('row-' + currentId);
            if (row) row.dataset.status = 'approved';

            // Tampilkan info file baru di modal (tanpa tutup)
            if (data.file) {
                const fileName = data.file.path.split('/').pop();
                const fileSizeKb = data.file.fileSize ? (data.file.fileSize / 1024).toFixed(1) + ' KB' : '';
                document.getElementById('fileName').textContent = fileName;
                document.getElementById('fileSize').textContent = fileSizeKb;
                document.getElementById('fileDownload').href    = '/kln/dokumen/' + currentId + '/file';
                document.getElementById('fileInfo').style.display   = 'block';
                document.getElementById('uploadForm').style.display = 'none';
                resetUploadForm();
            } else {
                closeModal();
            }
        } else {
            alert(data.message ?? 'Upload gagal');
        }
    })
    .catch(() => alert('Terjadi kesalahan saat upload'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalLabel;
    });
});

/* ── DELETE ──────────────────────────────── */
function deleteReq(id) {
    if (!confirm('Yakin ingin menghapus request ini?')) return;
    fetch('/kln/dokumen/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('row-' + id)?.remove();
        } else {
            alert(data.message ?? 'Gagal menghapus');
        }
    })
    .catch(() => alert('Terjadi kesalahan'));
}

/* tutup modal saat klik backdrop */
document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endpush

