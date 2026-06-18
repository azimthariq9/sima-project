@extends('layouts.sima')

@section('page_title',    'Request Dokumen')
@section('page_section',  'KERJA SAMA LUAR NEGERI')
@section('page_subtitle', 'Pengelolaan permintaan dokumen dari mahasiswa')

@section('main_content')

{{-- ── STAT CARDS ──────────────────────────────────── --}}

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
        <form method="GET" action="{{ route('kln.dokumen.page') }}"
              style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <select name="status" class="sima-input" style="min-width:140px;" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari mahasiswa / tipe..."
                   class="sima-input" style="min-width:200px;">
            <button type="submit" class="sima-btn sima-btn--outline"><i class="fas fa-search"></i></button>
            @if(request('status') || request('search'))
            <a href="{{ route('kln.dokumen.page') }}" class="sima-btn sima-btn--outline"><i class="fas fa-times"></i></a>
            @endif
        </form>
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
    @if($requests->hasPages())
    <div style="padding:14px 20px;border-top:1px solid var(--c-border);">
        {{ $requests->links('vendor.pagination.sima') }}
    </div>
    @endif

</div>

{{-- ── MODAL DETAIL ─────────────────────────────────── --}}
<div id="detailModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:var(--c-surface);border-radius:var(--radius-lg);width:100%;max-width:520px;box-shadow:var(--shadow-lg);overflow:hidden;margin:16px;max-height:90vh;overflow-y:auto;">

        {{-- Header modal --}}
        <div style="padding:20px 24px 16px;border-bottom:1px solid var(--c-border);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:var(--c-surface);z-index:1;">
            <div>
                <div style="font-size:15px;font-weight:700;color:var(--c-text-1)">Detail Request Dokumen</div>
                <div id="modalSubtitle" style="font-size:12px;color:var(--c-text-3);margin-top:2px">Tinjau permintaan dokumen mahasiswa</div>
            </div>
            <button onclick="closeModal()" style="width:32px;height:32px;border:1px solid var(--c-border);border-radius:8px;background:none;cursor:pointer;color:var(--c-text-3);font-size:16px">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Body modal --}}
        <div style="padding:20px 24px;">

            {{-- Info grid (diisi via JS) --}}
            <div id="modalContent" style="display:grid;gap:10px;margin-bottom:20px;"></div>

            {{-- Alasan penolakan (hanya tampil jika rejected) --}}
            <div id="rejectedInfo" style="display:none;background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.25);border-radius:10px;padding:14px 16px;margin-bottom:16px;">
                <div style="font-size:11px;font-weight:600;color:var(--c-red);text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">
                    <i class="fas fa-ban me-1"></i>Alasan Penolakan
                </div>
                <div id="rejectedReason" style="font-size:13px;color:var(--c-text-2);line-height:1.6;"></div>
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

            {{-- Upload form --}}
            <form id="uploadForm" enctype="multipart/form-data" style="display:none;">
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
                <div style="display:flex;gap:8px;">
                    <button type="submit" id="uploadBtn" class="sima-btn sima-btn--full" style="justify-content:center">
                        <i class="fas fa-upload"></i> Upload & Approve
                    </button>
                    <button type="button" id="btnShowReject" onclick="showRejectPanel()"
                            class="sima-btn sima-btn--danger" style="white-space:nowrap;flex-shrink:0;">
                        <i class="fas fa-ban me-1"></i> Tolak
                    </button>
                </div>
            </form>

            {{-- Reject form (muncul setelah klik Tolak) --}}
            <div id="rejectPanel" style="display:none;">
                <div style="background:rgba(239,68,68,.05);border:1px solid rgba(239,68,68,.2);border-radius:10px;padding:16px;margin-bottom:12px;">
                    <label style="font-size:12px;font-weight:600;color:var(--c-red);display:block;margin-bottom:8px;">
                        <i class="fas fa-ban me-1"></i>Alasan Penolakan <span style="color:var(--c-red)">*</span>
                    </label>
                    <textarea id="rejectReason" rows="3" class="sima-input"
                              placeholder="Jelaskan alasan penolakan request ini..."
                              style="resize:vertical;margin-bottom:0;"></textarea>
                    <div id="rejectError" style="display:none;font-size:12px;color:var(--c-red);margin-top:6px;"></div>
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="button" onclick="submitReject()"
                            id="btnConfirmReject"
                            class="sima-btn sima-btn--danger sima-btn--full" style="justify-content:center;">
                        <i class="fas fa-ban me-1"></i> Konfirmasi Tolak
                    </button>
                    <button type="button" onclick="cancelReject()" class="sima-btn sima-btn--outline" style="white-space:nowrap;flex-shrink:0;">
                        Batal
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('page_js')
<script>
let currentId = null;

/* ── DETAIL MODAL ────────────────────────── */
function showDetail(id) {
    currentId = id;
    fetch('/kln/dokumen/' + id)
        .then(res => res.json())
        .then(data => {
            const statusColor = { approved: 'var(--c-green)', rejected: 'var(--c-red)', pending: 'var(--c-amber)' };
            const badgeColor  = statusColor[data.status] ?? 'var(--c-text-3)';

            document.getElementById('modalContent').innerHTML = `
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div style="background:var(--c-bg);border-radius:10px;padding:12px;">
                        <div style="font-size:11px;color:var(--c-text-3);font-weight:600;text-transform:uppercase;letter-spacing:.05em">Mahasiswa</div>
                        <div style="font-size:14px;font-weight:600;color:var(--c-text-1);margin-top:4px">${escHtml(data.mahasiswa ?? '-')}</div>
                        <div style="font-size:11px;color:var(--c-text-3);margin-top:2px">${escHtml(data.npm ?? '')}</div>
                    </div>
                    <div style="background:var(--c-bg);border-radius:10px;padding:12px;">
                        <div style="font-size:11px;color:var(--c-text-3);font-weight:600;text-transform:uppercase;letter-spacing:.05em">Tipe Dokumen</div>
                        <div style="font-size:14px;font-weight:600;color:var(--c-text-1);margin-top:4px">${escHtml((data.tipe ?? '-').replace(/_/g,' '))}</div>
                    </div>
                    <div style="background:var(--c-bg);border-radius:10px;padding:12px;">
                        <div style="font-size:11px;color:var(--c-text-3);font-weight:600;text-transform:uppercase;letter-spacing:.05em">Status</div>
                        <div style="font-size:14px;font-weight:700;color:${badgeColor};margin-top:4px;text-transform:capitalize;">${escHtml(data.status ?? '-')}</div>
                    </div>
                    <div style="background:var(--c-bg);border-radius:10px;padding:12px;">
                        <div style="font-size:11px;color:var(--c-text-3);font-weight:600;text-transform:uppercase;letter-spacing:.05em">Pesan Mahasiswa</div>
                        <div style="font-size:13px;color:var(--c-text-2);margin-top:4px">${escHtml(data.message ?? '-')}</div>
                    </div>
                </div>
            `;

            // Alasan penolakan (rejected only)
            const rejectedInfo   = document.getElementById('rejectedInfo');
            const rejectedReason = document.getElementById('rejectedReason');
            if (data.status === 'rejected' && data.keterangan) {
                rejectedReason.textContent = data.keterangan;
                rejectedInfo.style.display = 'block';
            } else {
                rejectedInfo.style.display = 'none';
            }

            const uploadForm  = document.getElementById('uploadForm');
            const fileInfo    = document.getElementById('fileInfo');
            const rejectPanel = document.getElementById('rejectPanel');
            rejectPanel.style.display = 'none';
            document.getElementById('rejectReason').value = '';
            document.getElementById('rejectError').style.display = 'none';

            if (data.status === 'rejected') {
                // Sudah ditolak — sembunyikan semua action form
                fileInfo.style.display   = 'none';
                uploadForm.style.display = 'none';
                document.getElementById('modalSubtitle').textContent = 'Request ini telah ditolak.';
            } else if (data.file) {
                // Ada file / approved — tampilkan info file
                const fileName   = data.file.path.split('/').pop();
                const fileSizeKb = data.file.fileSize ? (data.file.fileSize / 1024).toFixed(1) + ' KB' : '';
                document.getElementById('fileName').textContent  = fileName;
                document.getElementById('fileSize').textContent  = fileSizeKb;
                document.getElementById('fileDownload').href     = '/kln/dokumen/' + data.id + '/file';
                fileInfo.style.display   = 'block';
                uploadForm.style.display = 'none';
                document.getElementById('modalSubtitle').textContent = 'Dokumen sudah diupload.';
            } else {
                // Pending — tampilkan form upload + tombol tolak
                fileInfo.style.display   = 'none';
                uploadForm.style.display = 'block';
                document.getElementById('modalSubtitle').textContent = 'Upload PDF untuk menyetujui, atau tolak request.';
            }

            document.getElementById('detailModal').style.display = 'flex';
        })
        .catch(() => alert('Gagal memuat detail request'));
}

function closeModal() {
    document.getElementById('detailModal').style.display = 'none';
    document.getElementById('uploadForm').reset();
    document.getElementById('rejectPanel').style.display = 'none';
    document.getElementById('rejectReason').value = '';
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

/* ── REJECT FLOW ─────────────────────────── */
function showRejectPanel() {
    document.getElementById('uploadForm').style.display  = 'none';
    document.getElementById('rejectPanel').style.display = 'block';
    document.getElementById('rejectReason').focus();
}

function cancelReject() {
    document.getElementById('rejectPanel').style.display = 'none';
    document.getElementById('uploadForm').style.display  = 'block';
    document.getElementById('rejectReason').value = '';
    document.getElementById('rejectError').style.display = 'none';
}

function submitReject() {
    const reason = document.getElementById('rejectReason').value.trim();
    const errEl  = document.getElementById('rejectError');

    if (!reason) {
        errEl.textContent     = 'Alasan penolakan wajib diisi.';
        errEl.style.display   = 'block';
        document.getElementById('rejectReason').focus();
        return;
    }
    errEl.style.display = 'none';

    const btn = document.getElementById('btnConfirmReject');
    btn.disabled   = true;
    btn.innerHTML  = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';

    fetch('/kln/dokumen/' + currentId + '/reject', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ keterangan: reason }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Update badge di tabel
            const badge = document.getElementById('badge-' + currentId);
            if (badge) { badge.className = 'sima-badge sima-badge--red'; badge.textContent = 'Rejected'; }
            const row = document.getElementById('row-' + currentId);
            if (row) row.dataset.status = 'rejected';
            closeModal();
        } else {
            errEl.textContent   = data.message ?? 'Gagal menolak request.';
            errEl.style.display = 'block';
        }
    })
    .catch(() => {
        errEl.textContent   = 'Terjadi kesalahan. Coba lagi.';
        errEl.style.display = 'block';
    })
    .finally(() => {
        btn.disabled  = false;
        btn.innerHTML = '<i class="fas fa-ban me-1"></i> Konfirmasi Tolak';
    });
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
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

