@extends('layouts.sima')

@section('page_title',    'Lecturers')
@section('page_section',  'DEPARTMENT ADMIN')
@section('page_subtitle', 'Manage lecturer data in your department')

@section('main_content')

<div class="sima-card sima-fade">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Lecturer List</h5>
            <div class="sima-card__subtitle">Total {{ $dosens->count() }} lecturers</div>
        </div>
        <a href="{{ route('jurusan.dosen.create') }}" class="sima-btn sima-btn--sm">
            <i class="fas fa-plus"></i> Add Lecturer
        </a>
    </div>

    <div style="overflow-x:auto">
        <table class="sima-table" data-datatable>
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Lecturer</th>
                        <th>NIDN</th>
                        <th>Code</th>
                        <th>Email</th>
                        <th style="width:110px">Status</th>
                        <th style="width:100px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dosens as $dos)
                    @php
                        $stMap = [
                            'active'   => ['cls'=>'sima-badge--green', 'label'=>'Active'],
                            'inactive' => ['cls'=>'sima-badge--grey',  'label'=>'Inactive'],
                            'pending'  => ['cls'=>'sima-badge--blue',  'label'=>'Pending'],
                        ];
                        $st = $stMap[$dos->user->status ?? ''] ?? ['cls'=>'sima-badge--grey', 'label'=>($dos->user->status ?? '—')];
                    @endphp
                    <tr>
                        <td style="color:var(--c-text-3);font-size:12px">
                            {{ $loop->index + 1 }}
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:13.5px">{{ $dos->nama }}</div>
                        </td>
                        <td style="font-family:var(--f-mono);font-size:12px">{{ $dos->nidn ?? '—' }}</td>
                        <td style="font-family:var(--f-mono);font-size:12px">{{ $dos->kodeDos ?? '—' }}</td>
                        <td style="font-size:12.5px;color:var(--c-text-2)">{{ $dos->user->email ?? '—' }}</td>
                        <td>
                            <span class="sima-badge {{ $st['cls'] }}" style="font-size:11px">{{ $st['label'] }}</span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px">
                                <button type="button" class="sima-btn sima-btn--sm sima-btn--outline btn-edit-dosen"
                                        data-id="{{ $dos->id }}"
                                        style="font-size:11.5px;padding:4px 10px" title="Edit">
                                    <i class="fas fa-pencil"></i>
                                </button>
                                <button type="button" class="sima-btn sima-btn--sm btn-del-dosen"
                                        data-id="{{ $dos->id }}" data-nama="{{ $dos->nama }}"
                                        style="font-size:11.5px;padding:4px 10px;background:rgba(220,38,38,.08);color:#dc2626;border:1px solid rgba(220,38,38,.2)"
                                        title="Delete">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
</div>





{{-- ══════════════════════════════════════
     MODAL EDIT DOSEN (SIMA Light)
══════════════════════════════════════ --}}
<div id="modalEdit" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:18px;width:100%;max-width:480px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2);overflow:hidden">
        <div style="padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
            <div style="font-size:15px;font-weight:700;color:#1e293b">Edit Lecturer</div>
            <button type="button" onclick="closeModals()"
                    style="width:32px;height:32px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;cursor:pointer;color:#64748b">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div style="padding:20px 24px">
            <form id="formEdit">
                <input type="hidden" id="editId">
                <div style="margin-bottom:14px">
                    <label class="sima-label">Lecturer Name <span style="color:var(--c-red)">*</span></label>
                    <input type="text" name="nama" id="editNama" class="sima-input" required>
                </div>
                <div style="margin-bottom:14px">
                    <label class="sima-label">NIDN</label>
                    <input type="text" name="nidn" id="editNidn" class="sima-input">
                </div>
                <div style="margin-bottom:18px">
                    <label class="sima-label">Lecturer Code</label>
                    <input type="text" name="kodeDos" id="editKodeDos" class="sima-input">
                </div>
                <div id="editErr" style="display:none;font-size:12.5px;color:#dc2626;margin-bottom:12px;padding:10px;background:#fef2f2;border-radius:8px;border:1px solid #fecaca"></div>
                <div style="display:flex;gap:8px">
                    <button type="submit" class="sima-btn"><i class="fas fa-save"></i> Save Changes</button>
                    <button type="button" onclick="closeModals()" class="sima-btn sima-btn--outline">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('page_js')
<script>
const CSRF = '{{ csrf_token() }}';

document.querySelectorAll('#modalEdit').forEach(el => {
    el.addEventListener('click', function(e) { if (e.target === this) this.style.display = 'none'; });
});

function closeModals() {
    document.getElementById('modalEdit').style.display = 'none';
}

/* ─── Buka modal edit ─── */
document.querySelectorAll('.btn-edit-dosen').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const errDiv = document.getElementById('editErr');
        errDiv.style.display = 'none';

        fetch(`/jurusan/dosen/${id}`, { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(res => {
                const d = res.data ?? res;
                document.getElementById('editId').value        = d.id;
                document.getElementById('editNama').value      = d.nama ?? '';
                document.getElementById('editNidn').value      = d.nidn ?? '';
                document.getElementById('editKodeDos').value   = d.kodeDos ?? '';
                document.getElementById('modalEdit').style.display = 'flex';
            })
            .catch(err => alert('Failed to load data: ' + err.message));
    });
});

/* ─── Simpan edit ─── */
document.getElementById('formEdit').addEventListener('submit', function(e) {
    e.preventDefault();
    const errDiv = document.getElementById('editErr');
    errDiv.style.display = 'none';
    const id  = document.getElementById('editId').value;
    const btn = this.querySelector('[type=submit]');
    btn.disabled = true;

    const data = Object.fromEntries(new FormData(this));

    fetch(`/jurusan/dosen/${id}`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) { window.location.reload(); }
        else { errDiv.textContent = res.message || 'Failed to update'; errDiv.style.display = 'block'; }
    })
    .catch(err => { errDiv.textContent = 'Error: ' + err.message; errDiv.style.display = 'block'; })
    .finally(() => { btn.disabled = false; });
});

/* ─── Hapus dosen ─── */
document.querySelectorAll('.btn-del-dosen').forEach(btn => {
    btn.addEventListener('click', function() {
        const id   = this.dataset.id;
        const nama = this.dataset.nama;
        if (!confirm(`Delete lecturer "${nama}"?`)) return;

        fetch(`/jurusan/dosen/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        })
        .then(r => r.json())
        .then(res => { if (res.success) window.location.reload(); else alert(res.message); })
        .catch(err => alert('Error: ' + err.message));
    });
});
</script>
@endsection
