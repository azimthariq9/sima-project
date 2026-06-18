@extends('layouts.sima')

@section('page_title',    'Pengumuman KLN')
@section('page_section',  'PENGUMUMAN')
@section('page_subtitle', 'Kelola pengumuman yang dibuat oleh KLN')

@section('main_content')

{{-- ── STAT CARDS ───────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--blue">
            <div class="sima-stat__icon sima-stat__icon--blue"><i class="fas fa-bullhorn"></i></div>
            <div class="sima-stat__label">Total</div>
            <div class="sima-stat__value">{{ $totalAll }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--green">
            <div class="sima-stat__icon sima-stat__icon--green"><i class="fas fa-check-circle"></i></div>
            <div class="sima-stat__label">Aktif</div>
            <div class="sima-stat__value">{{ $totalActive }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--amber">
            <div class="sima-stat__icon sima-stat__icon--amber"><i class="fas fa-edit"></i></div>
            <div class="sima-stat__label">Draft</div>
            <div class="sima-stat__value">{{ $totalDraft }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--red">
            <div class="sima-stat__icon sima-stat__icon--red"><i class="fas fa-exclamation-circle"></i></div>
            <div class="sima-stat__label">Penting</div>
            <div class="sima-stat__value">{{ $totalPenting }}</div>
        </div>
    </div>
</div>

{{-- ── FLASH MESSAGE ────────────────────────────────── --}}
@if(session('success'))
<div style="background:rgba(34,197,94,.12);border:1px solid var(--c-green);border-radius:10px;
            padding:12px 16px;margin-bottom:16px;color:var(--c-green);font-size:13px;font-weight:600;
            display:flex;align-items:center;gap:8px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

{{-- ── TABLE CARD ───────────────────────────────────── --}}
<div class="sima-card">
    <div class="sima-card__header">
        <h5 class="sima-card__title">Daftar Pengumuman</h5>
        <div style="display:flex;gap:8px;align-items:center;">
            <input type="text" id="searchInput" class="sima-input" style="width:200px;"
                   placeholder="Cari judul..." oninput="filterAnn()">
            <button class="sima-btn sima-btn--outline" onclick="resetFilter()">
                <i class="fas fa-redo"></i>
            </button>
            <a href="{{ route('kln.announcement.create') }}" class="sima-btn sima-btn--accent">
                <i class="fas fa-plus me-1"></i> Buat Pengumuman
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="sima-table" id="annTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Judul &amp; Ringkasan</th>
                    <th>Lampiran</th>
                    <th>Status</th>
                    <th>Penting</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($announcements as $i => $ann)
                @php
                    $statusMap = [
                        'active'   => ['label' => 'Aktif',    'cls' => 'sima-badge--green'],
                        'inactive' => ['label' => 'Nonaktif', 'cls' => 'sima-badge--red'],
                        'draft'    => ['label' => 'Draft',    'cls' => 'sima-badge--amber'],
                    ];
                    $s = $statusMap[$ann->status] ?? ['label' => $ann->status, 'cls' => 'sima-badge--amber'];
                @endphp
                <tr id="row-{{ $ann->id }}" data-judul="{{ strtolower($ann->subject) }}">
                    <td>{{ $i + 1 }}</td>
                    <td style="max-width:300px;">
                        <div class="fw-600" style="margin-bottom:3px;">{{ $ann->subject }}</div>
                        <div style="font-size:12px;color:var(--c-text-3);line-height:1.5;">
                            {{ Str::limit(strip_tags($ann->message), 100) }}
                        </div>
                    </td>
                    <td>
                        @if($ann->file_count > 0)
                            <span class="sima-badge sima-badge--blue">
                                <i class="fas fa-paperclip me-1"></i>{{ $ann->file_count }}
                            </span>
                        @else
                            <span class="text-muted" style="font-size:13px;">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="sima-badge {{ $s['cls'] }}">{{ $s['label'] }}</span>
                    </td>
                    <td>
                        @if($ann->is_penting)
                            <span class="sima-badge sima-badge--red">
                                <i class="fas fa-exclamation me-1"></i>Penting
                            </span>
                        @else
                            <span class="text-muted" style="font-size:13px;">—</span>
                        @endif
                    </td>
                    <td style="font-size:13px;color:var(--c-text-3);">
                        {{ \Carbon\Carbon::parse($ann->created_at)->isoFormat('D MMM YYYY') }}
                    </td>
                    <td>
                        <a href="{{ route('kln.announcement.edit', $ann->id) }}"
                           class="sima-btn sima-btn--outline sima-btn--sm me-1">
                            <i class="fas fa-pen me-1"></i> Edit
                        </a>
                        <button onclick="deleteAnn({{ $ann->id }})"
                                class="sima-btn sima-btn--sm"
                                style="background:var(--c-red);color:#fff;border:none;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="fas fa-bullhorn fa-2x d-block mb-2" style="opacity:.3;"></i>
                        Belum ada pengumuman.
                        <a href="{{ route('kln.announcement.create') }}" style="color:var(--c-accent);">Buat sekarang</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($announcements->hasPages())
    <div style="padding:14px 20px;border-top:1px solid var(--c-border);">
        {{ $announcements->links() }}
    </div>
    @endif
</div>

@endsection

@push('page_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
function filterAnn() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#annTable tbody tr[data-judul]').forEach(row => {
        row.style.display = !q || row.dataset.judul.includes(q) ? '' : 'none';
    });
}
function resetFilter() {
    document.getElementById('searchInput').value = '';
    filterAnn();
}

function deleteAnn(id) {
    if (!confirm('Hapus pengumuman ini? Semua lampiran juga akan dihapus.')) return;
    fetch(`/kln/announcement/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('row-' + id)?.remove();
        }
    })
    .catch(() => alert('Gagal menghapus. Coba lagi.'));
}
</script>
@endpush
