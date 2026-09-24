@extends('layouts.sima')

@section('page_title',    'Document Types')
@section('page_section',  'KERJA SAMA LUAR NEGERI')
@section('page_subtitle', 'Manage document type definitions')

@section('main_content')

<a href="{{ route('kln.users.page') }}" class="sima-btn sima-btn--outline sima-btn--sm" style="margin-bottom:16px">
    <i class="fas fa-arrow-left me-1"></i> Back
</a>

<div class="sima-card">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Document Types</h5>
            <div class="sima-card__subtitle">Manage document type codes and categories</div>
        </div>
        <div>
            <button onclick="openAddModal()" class="sima-btn sima-btn--blue">
                <i class="fas fa-plus"></i> Add Type
            </button>
        </div>
    </div>

    <div style="overflow-x:auto;padding:8px 0">
        <table class="sima-table" data-datatable>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Default Issuer</th>
                    <th>Status</th>
                    <th style="text-align:center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($types as $i => $type)
                <tr id="row-{{ $type->id }}">
                    <td>{{ $types->firstItem() + $i }}</td>
                    <td style="font-family:var(--f-mono);font-size:12px;font-weight:600;color:var(--c-accent)">{{ $type->kode ?? '-' }}</td>
                    <td style="font-weight:600;color:var(--c-text-1)">{{ $type->nama ?? '-' }}</td>
                    <td>
                        <span class="sima-badge {{ ($type->kategori ?? '') === 'external' ? 'sima-badge--blue' : 'sima-badge--amber' }}">
                            {{ ucfirst($type->kategori ?? '-') }}
                        </span>
                    </td>
                    <td style="color:var(--c-text-2)">{{ $type->penerbit_default ?? '—' }}</td>
                    <td>
                        <span class="sima-badge {{ $type->is_active ? 'sima-badge--green' : 'sima-badge--red' }}">
                            {{ $type->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td style="text-align:center;white-space:nowrap">
                        <button onclick="openEditModal({{ $type->id }}, '{{ addslashes($type->kode ?? '') }}', '{{ addslashes($type->nama ?? '') }}', '{{ addslashes($type->kategori ?? '') }}', '{{ addslashes($type->penerbit_default ?? '') }}')"
                                class="sima-btn sima-btn--blue sima-btn--sm">
                            <i class="fas fa-pen"></i> Edit
                        </button>
                        <button onclick="toggleActive({{ $type->id }}, {{ $type->is_active ? 'true' : 'false' }})"
                                class="sima-btn sima-btn--sm {{ $type->is_active ? 'sima-btn--danger' : 'sima-btn--gold' }}"
                                style="margin-left:6px">
                            <i class="fas fa-{{ $type->is_active ? 'ban' : 'check' }}"></i>
                            {{ $type->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--c-text-3)">
                        <i class="fas fa-inbox" style="font-size:32px;margin-bottom:10px;display:block"></i>
                        No document types found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding:14px 20px;border-top:1px solid var(--c-border-soft);display:flex;align-items:center;justify-content:space-between;">
        <div style="font-size:12px;color:var(--c-text-3)">
            Showing {{ $types->firstItem() ?? 0 }}–{{ $types->lastItem() ?? 0 }} of {{ $types->total() ?? 0 }} types
        </div>
        <div>
            {{ $types->links('vendor.pagination.sima') }}
        </div>
    </div>
</div>

{{-- ── MODAL ADD / EDIT ────────────────────────────── --}}
<div id="typeModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:var(--c-surface);border-radius:var(--radius-lg);width:100%;max-width:480px;box-shadow:var(--shadow-lg);overflow:hidden;margin:16px;max-height:90vh;overflow-y:auto;">

        <div style="padding:20px 24px 16px;border-bottom:1px solid var(--c-border);display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div id="modalTitle" style="font-size:15px;font-weight:700;color:var(--c-text-1)">Add Document Type</div>
                <div style="font-size:12px;color:var(--c-text-3);margin-top:2px">Fill in the document type details</div>
            </div>
            <button onclick="closeModal()" style="width:32px;height:32px;border:1px solid var(--c-border);border-radius:8px;background:none;cursor:pointer;color:var(--c-text-3);font-size:16px">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="typeForm" style="padding:20px 24px;" onsubmit="submitForm(event)">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div style="margin-bottom:16px;">
                <label class="sima-label" for="typeKode">Code <span style="color:var(--c-red)">*</span></label>
                <input type="text" id="typeKode" name="kode" class="sima-input" placeholder="e.g. KTP, Paspor" required>
            </div>

            <div style="margin-bottom:16px;">
                <label class="sima-label" for="typeNama">Name <span style="color:var(--c-red)">*</span></label>
                <input type="text" id="typeNama" name="nama" class="sima-input" placeholder="Enter document type name" required>
            </div>

            <div style="margin-bottom:16px;">
                <label class="sima-label" for="typeKategori">Category <span style="color:var(--c-red)">*</span></label>
                <select id="typeKategori" name="kategori" class="sima-input" required>
                    <option value="">Select category</option>
                    <option value="external">External</option>
                    <option value="internal">Internal</option>
                </select>
            </div>

            <div style="margin-bottom:16px;">
                <label class="sima-label" for="typePenerbit">Default Issuer</label>
                <input type="text" id="typePenerbit" name="penerbit_default" class="sima-input" placeholder="e.g. Disdukcapil, Imigrasi">
            </div>

            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" onclick="closeModal()" class="sima-btn sima-btn--outline">
                    Cancel
                </button>
                <button type="submit" id="saveBtn" class="sima-btn sima-btn--blue">
                    <i class="fas fa-save"></i> Save
                </button>
            </div>
        </form>

    </div>
</div>

@endsection

@push('page_js')
<script>
let editId = null;

function openAddModal() {
    editId = null;
    document.getElementById('modalTitle').textContent = 'Add Document Type';
    document.getElementById('typeKode').value = '';
    document.getElementById('typeNama').value = '';
    document.getElementById('typeKategori').value = '';
    document.getElementById('typePenerbit').value = '';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('typeForm').action = '{{ route("kln.types.dokumen.store") }}';
    document.getElementById('typeModal').style.display = 'flex';
}

function openEditModal(id, kode, nama, kategori, penerbit) {
    editId = id;
    document.getElementById('modalTitle').textContent = 'Edit Document Type';
    document.getElementById('typeKode').value = kode;
    document.getElementById('typeNama').value = nama;
    document.getElementById('typeKategori').value = kategori;
    document.getElementById('typePenerbit').value = penerbit;
    document.getElementById('formMethod').value = 'PATCH';
    document.getElementById('typeForm').action = '{{ route("kln.types.dokumen.update", ":id") }}'.replace(':id', id);
    document.getElementById('typeModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('typeModal').style.display = 'none';
    document.getElementById('typeForm').reset();
}

function submitForm(e) {
    e.preventDefault();
    const form = document.getElementById('typeForm');
    const btn  = document.getElementById('saveBtn');

    const formData = new FormData(form);
    const url      = form.action;

    btn.disabled  = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData,
    })
    .then(async res => {
        if (!res.ok) {
            const text = await res.text();
            try { return JSON.parse(text); } catch { throw new Error('HTTP ' + res.status); }
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Failed to save type');
        }
    })
    .catch(err => {
        console.error(err);
        alert('An error occurred: ' + err.message);
    })
    .finally(() => {
        btn.disabled  = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Save';
    });
}

function toggleActive(id, isActive) {
    const action = isActive ? 'deactivate' : 'activate';
    if (!confirm('Are you sure you want to ' + action + ' this type?')) return;

    fetch('{{ route("kln.types.dokumen.toggle", ":id") }}'.replace(':id', id), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    })
    .then(async res => {
        if (!res.ok) {
            const text = await res.text();
            try { return JSON.parse(text); } catch { throw new Error('HTTP ' + res.status); }
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Failed to toggle status');
        }
    })
    .catch(err => {
        console.error(err);
        alert('An error occurred: ' + err.message);
    });
}

document.getElementById('typeModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endpush
