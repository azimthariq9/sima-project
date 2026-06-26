@extends('layouts.sima')

@section('page_title',    'Dosen')
@section('page_section',  'ADMIN JURUSAN')
@section('page_subtitle', 'Pengelolaan data dosen di jurusan Anda')

@section('main_content')

<div class="sima-card sima-fade">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Daftar Dosen</h5>
            <div class="sima-card__subtitle">Total {{ $dosens->total() }} dosen</div>
        </div>
        <button type="button" id="btnTambah" class="sima-btn sima-btn--sm">
            <i class="fas fa-plus"></i> Tambah Dosen
        </button>
    </div>

    {{-- Filter bar --}}
    <div style="padding:12px 20px;border-bottom:1px solid var(--c-border-soft)">
        <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / NIDN / kode dosen…"
                   class="sima-input" style="width:280px;font-size:13px">
            <button type="submit" class="sima-btn sima-btn--sm sima-btn--outline">
                <i class="fas fa-search"></i> Cari
            </button>
            @if($search)
                <a href="{{ route('jurusan.dosen.page') }}" class="sima-btn sima-btn--sm sima-btn--outline">
                    <i class="fas fa-xmark"></i> Reset
                </a>
            @endif
        </form>
    </div>

    @if($dosens->isEmpty())
        <div class="sima-card__body" style="text-align:center;padding:48px 20px;color:var(--c-text-3)">
            <i class="fas fa-chalkboard-user" style="font-size:36px;opacity:.3;display:block;margin-bottom:12px"></i>
            <div style="font-size:14px;font-weight:500;color:var(--c-text-2)">
                {{ $search ? 'Tidak ada dosen yang cocok' : 'Belum ada dosen terdaftar' }}
            </div>
        </div>
    @else
        <div style="overflow-x:auto">
            <table class="sima-table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Dosen</th>
                        <th>NIDN</th>
                        <th>Kode</th>
                        <th>Email</th>
                        <th style="width:110px">Status</th>
                        <th style="width:100px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dosens as $dos)
                    @php
                        $stMap = [
                            'active'   => ['cls'=>'sima-badge--green', 'label'=>'Aktif'],
                            'inactive' => ['cls'=>'sima-badge--grey',  'label'=>'Nonaktif'],
                            'pending'  => ['cls'=>'sima-badge--blue',  'label'=>'Pending'],
                        ];
                        $st = $stMap[$dos->user->status ?? ''] ?? ['cls'=>'sima-badge--grey', 'label'=>($dos->user->status ?? '—')];
                    @endphp
                    <tr>
                        <td style="color:var(--c-text-3);font-size:12px">
                            {{ $dosens->firstItem() + $loop->index }}
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
                                        title="Hapus">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="padding:14px 20px">
            {{ $dosens->links('vendor.pagination.sima') }}
        </div>
    @endif
</div>


{{-- ══════════════════════════════════════
     MODAL TAMBAH DOSEN (SIMA Light)
══════════════════════════════════════ --}}
<div id="modalTambah" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:18px;width:100%;max-width:480px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2);overflow:hidden">
        <div style="padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
            <div style="font-size:15px;font-weight:700;color:#1e293b">Tambah Dosen</div>
            <button type="button" onclick="closeModals()"
                    style="width:32px;height:32px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;cursor:pointer;color:#64748b">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div style="padding:20px 24px">
            <form id="formTambah">
                <div style="margin-bottom:14px">
                    <label class="sima-label">Nama Dosen <span style="color:var(--c-red)">*</span></label>
                    <input type="text" name="nama" class="sima-input" required placeholder="Nama lengkap dosen">
                </div>
                <div style="margin-bottom:14px">
                    <label class="sima-label">NIDN</label>
                    <input type="text" name="nidn" class="sima-input" placeholder="Nomor Induk Dosen Nasional">
                </div>
                <div style="margin-bottom:14px">
                    <label class="sima-label">Kode Dosen</label>
                    <input type="text" name="kodeDos" class="sima-input" placeholder="Kode singkat dosen">
                </div>
                <div style="margin-bottom:18px">
                    <label class="sima-label">Akun User <span style="color:var(--c-red)">*</span></label>
                    <select name="user_id" id="selectUser" class="sima-input" required>
                        <option value="">— Memuat daftar akun… —</option>
                    </select>
                    <div style="font-size:11px;color:var(--c-text-3);margin-top:4px">Pilih akun user dengan role Dosen</div>
                </div>
                <div id="tambahErr" style="display:none;font-size:12.5px;color:#dc2626;margin-bottom:12px;padding:10px;background:#fef2f2;border-radius:8px;border:1px solid #fecaca"></div>
                <div style="display:flex;gap:8px">
                    <button type="submit" class="sima-btn"><i class="fas fa-plus"></i> Simpan</button>
                    <button type="button" onclick="closeModals()" class="sima-btn sima-btn--outline">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════
     MODAL EDIT DOSEN (SIMA Light)
══════════════════════════════════════ --}}
<div id="modalEdit" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:18px;width:100%;max-width:480px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2);overflow:hidden">
        <div style="padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
            <div style="font-size:15px;font-weight:700;color:#1e293b">Edit Dosen</div>
            <button type="button" onclick="closeModals()"
                    style="width:32px;height:32px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;cursor:pointer;color:#64748b">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div style="padding:20px 24px">
            <form id="formEdit">
                <input type="hidden" id="editId">
                <div style="margin-bottom:14px">
                    <label class="sima-label">Nama Dosen <span style="color:var(--c-red)">*</span></label>
                    <input type="text" name="nama" id="editNama" class="sima-input" required>
                </div>
                <div style="margin-bottom:14px">
                    <label class="sima-label">NIDN</label>
                    <input type="text" name="nidn" id="editNidn" class="sima-input">
                </div>
                <div style="margin-bottom:18px">
                    <label class="sima-label">Kode Dosen</label>
                    <input type="text" name="kodeDos" id="editKodeDos" class="sima-input">
                </div>
                <div id="editErr" style="display:none;font-size:12.5px;color:#dc2626;margin-bottom:12px;padding:10px;background:#fef2f2;border-radius:8px;border:1px solid #fecaca"></div>
                <div style="display:flex;gap:8px">
                    <button type="submit" class="sima-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
                    <button type="button" onclick="closeModals()" class="sima-btn sima-btn--outline">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('page_js')
<script>
const CSRF = '{{ csrf_token() }}';

/* ─── close modal on backdrop click ─── */
['modalTambah','modalEdit'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) closeModals();
    });
});

function closeModals() {
    document.getElementById('modalTambah').style.display = 'none';
    document.getElementById('modalEdit').style.display   = 'none';
}

/* ─── Buka modal tambah → load user list ─── */
document.getElementById('btnTambah').addEventListener('click', function() {
    const sel = document.getElementById('selectUser');
    sel.innerHTML = '<option value="">— Memuat… —</option>';

    fetch('{{ route("jurusan.users.data") }}?role=dosen', { headers: { Accept: 'application/json' } })
        .then(r => r.json())
        .then(res => {
            sel.innerHTML = '<option value="">— Pilih akun —</option>';
            (res.data || []).forEach(u => {
                sel.innerHTML += `<option value="${u.id}">${u.email} (${u.name ?? u.email})</option>`;
            });
        })
        .catch(() => { sel.innerHTML = '<option value="">Gagal memuat</option>'; });

    document.getElementById('tambahErr').style.display = 'none';
    document.getElementById('formTambah').reset();
    document.getElementById('modalTambah').style.display = 'flex';
});

/* ─── Tambah dosen ─── */
document.getElementById('formTambah').addEventListener('submit', function(e) {
    e.preventDefault();
    const errDiv = document.getElementById('tambahErr');
    errDiv.style.display = 'none';
    const btn = this.querySelector('[type=submit]');
    btn.disabled = true;

    const data = Object.fromEntries(new FormData(this));

    fetch('{{ route("jurusan.dosen.store") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) { window.location.reload(); }
        else {
            errDiv.textContent = res.message || 'Gagal menyimpan';
            errDiv.style.display = 'block';
        }
    })
    .catch(err => { errDiv.textContent = 'Error: ' + err.message; errDiv.style.display = 'block'; })
    .finally(() => { btn.disabled = false; });
});

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
            .catch(err => alert('Gagal memuat data: ' + err.message));
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
        else { errDiv.textContent = res.message || 'Gagal update'; errDiv.style.display = 'block'; }
    })
    .catch(err => { errDiv.textContent = 'Error: ' + err.message; errDiv.style.display = 'block'; })
    .finally(() => { btn.disabled = false; });
});

/* ─── Hapus dosen ─── */
document.querySelectorAll('.btn-del-dosen').forEach(btn => {
    btn.addEventListener('click', function() {
        const id   = this.dataset.id;
        const nama = this.dataset.nama;
        if (!confirm(`Hapus dosen "${nama}"?`)) return;

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
