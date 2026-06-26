@extends('layouts.sima')

@section('page_title',    'Kelas')
@section('page_section',  'ADMIN JURUSAN')
@section('page_subtitle', 'Pengelolaan kelas dan anggota mahasiswa')

@section('main_content')

@php
$tahunSekarang    = (int) date('Y');
$tahunAjarDefault = date('Y').'/'.(date('Y')+1);
$tahunAjarList    = [];
for ($i = -1; $i <= 10; $i++) {
    $a = $tahunSekarang + $i;
    $tahunAjarList[] = "{$a}/".($a+1);
}
@endphp

<div class="sima-card sima-fade">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Daftar Kelas</h5>
            <div class="sima-card__subtitle">Total {{ $kelas->total() }} kelas</div>
        </div>
        <button type="button" id="btnTambah" class="sima-btn sima-btn--sm">
            <i class="fas fa-plus"></i> Tambah Kelas
        </button>
    </div>

    {{-- Filter --}}
    <div style="padding:12px 20px;border-bottom:1px solid var(--c-border-soft)">
        <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode kelas…"
                   class="sima-input" style="width:240px;font-size:13px">
            <button type="submit" class="sima-btn sima-btn--sm sima-btn--outline">
                <i class="fas fa-search"></i> Cari
            </button>
            @if($search)
                <a href="{{ route('jurusan.kelas.page') }}" class="sima-btn sima-btn--sm sima-btn--outline">
                    <i class="fas fa-xmark"></i> Reset
                </a>
            @endif
        </form>
    </div>

    @if($kelas->isEmpty())
        <div class="sima-card__body" style="text-align:center;padding:48px 20px;color:var(--c-text-3)">
            <i class="fas fa-door-open" style="font-size:36px;opacity:.3;display:block;margin-bottom:12px"></i>
            <div style="font-size:14px;font-weight:500;color:var(--c-text-2)">
                {{ $search ? 'Tidak ada kelas yang cocok' : 'Belum ada kelas' }}
            </div>
        </div>
    @else
        <div style="overflow-x:auto">
            <table class="sima-table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Kode Kelas</th>
                        <th>Tahun Ajaran</th>
                        <th style="width:130px">Mahasiswa</th>
                        <th style="width:140px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kelas as $k)
                    <tr>
                        <td style="color:var(--c-text-3);font-size:12px">
                            {{ $kelas->firstItem() + $loop->index }}
                        </td>
                        <td>
                            <span style="font-family:var(--f-mono);font-size:13px;font-weight:700;
                                         background:var(--c-teal-lt);color:var(--c-teal);
                                         padding:4px 10px;border-radius:6px">
                                {{ $k->kodeKelas }}
                            </span>
                        </td>
                        <td style="font-size:12.5px;color:var(--c-text-2)">{{ $k->tahunAjar ?? '—' }}</td>
                        <td>
                            <span class="sima-badge sima-badge--purple" style="font-size:11px">
                                {{ $k->mahasiswa_count ?? 0 }} mahasiswa
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px">
                                <button type="button" class="sima-btn sima-btn--sm sima-btn--outline btn-view-mhs"
                                        data-id="{{ $k->id }}" data-kode="{{ $k->kodeKelas }}"
                                        style="font-size:11.5px;padding:4px 10px">
                                    <i class="fas fa-users"></i>
                                </button>
                                <button type="button" class="sima-btn sima-btn--sm sima-btn--outline btn-edit-kelas"
                                        data-id="{{ $k->id }}"
                                        style="font-size:11.5px;padding:4px 10px">
                                    <i class="fas fa-pencil"></i>
                                </button>
                                <button type="button" class="sima-btn sima-btn--sm btn-del-kelas"
                                        data-id="{{ $k->id }}" data-kode="{{ $k->kodeKelas }}"
                                        style="font-size:11.5px;padding:4px 10px;background:rgba(220,38,38,.08);color:#dc2626;border:1px solid rgba(220,38,38,.2)">
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
            {{ $kelas->links('vendor.pagination.sima') }}
        </div>
    @endif
</div>

{{-- Mahasiswa Panel --}}
<div id="mahasiswaPanel" style="display:none;margin-top:20px">
    <div class="sima-card sima-fade">
        <div class="sima-card__header">
            <div>
                <h5 class="sima-card__title" id="panelTitle">Mahasiswa Kelas</h5>
                <div class="sima-card__subtitle">Daftar mahasiswa terdaftar</div>
            </div>
            <div style="display:flex;gap:8px">
                <button type="button" id="btnAddMhs" class="sima-btn sima-btn--sm">
                    <i class="fas fa-user-plus"></i> Tambah Mahasiswa
                </button>
                <button type="button" onclick="closeMhsPanel()" class="sima-btn sima-btn--sm sima-btn--outline">
                    <i class="fas fa-xmark"></i> Tutup
                </button>
            </div>
        </div>
        <div style="overflow-x:auto">
            <table class="sima-table">
                <thead>
                    <tr><th>#</th><th>NPM</th><th>Nama</th><th>Email</th><th style="width:80px"></th></tr>
                </thead>
                <tbody id="mhsPanelTbody">
                    <tr><td colspan="5" style="padding:24px;text-align:center;color:var(--c-text-3)">Pilih kelas untuk melihat mahasiswa</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


{{-- MODAL TAMBAH KELAS --}}
<div id="modalTambah" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:18px;width:100%;max-width:420px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2)">
        <div style="padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
            <div style="font-size:15px;font-weight:700;color:#1e293b">Tambah Kelas</div>
            <button type="button" onclick="closeModals()" style="width:32px;height:32px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;cursor:pointer;color:#64748b"><i class="fas fa-xmark"></i></button>
        </div>
        <div style="padding:20px 24px">
            <form id="formTambah">
                <div style="margin-bottom:14px">
                    <label class="sima-label">Kode Kelas <span style="color:var(--c-red)">*</span></label>
                    <input type="text" name="kodeKelas" class="sima-input" required placeholder="Contoh: 3KA35" style="font-family:var(--f-mono)">
                </div>
                <div style="margin-bottom:18px">
                    <label class="sima-label">Tahun Ajaran</label>
                    <select name="tahunAjar" class="sima-input">
                        @foreach($tahunAjarList as $ta)
                            <option value="{{ $ta }}" {{ $ta === $tahunAjarDefault ? 'selected' : '' }}>{{ $ta }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="tambahErr" style="display:none;font-size:12.5px;color:#dc2626;margin-bottom:12px;padding:10px;background:#fef2f2;border-radius:8px"></div>
                <div style="display:flex;gap:8px">
                    <button type="submit" class="sima-btn"><i class="fas fa-plus"></i> Simpan</button>
                    <button type="button" onclick="closeModals()" class="sima-btn sima-btn--outline">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT KELAS --}}
<div id="modalEdit" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:18px;width:100%;max-width:420px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2)">
        <div style="padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
            <div style="font-size:15px;font-weight:700;color:#1e293b">Edit Kelas</div>
            <button type="button" onclick="closeModals()" style="width:32px;height:32px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;cursor:pointer;color:#64748b"><i class="fas fa-xmark"></i></button>
        </div>
        <div style="padding:20px 24px">
            <form id="formEdit">
                <input type="hidden" id="editId">
                <div style="margin-bottom:14px">
                    <label class="sima-label">Kode Kelas <span style="color:var(--c-red)">*</span></label>
                    <input type="text" name="kodeKelas" id="editKodeKelas" class="sima-input" required style="font-family:var(--f-mono)">
                </div>
                <div style="margin-bottom:18px">
                    <label class="sima-label">Tahun Ajaran</label>
                    <select name="tahunAjar" id="editTahunAjar" class="sima-input">
                        @foreach($tahunAjarList as $ta)
                            <option value="{{ $ta }}" {{ $ta === $tahunAjarDefault ? 'selected' : '' }}>{{ $ta }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="editErr" style="display:none;font-size:12.5px;color:#dc2626;margin-bottom:12px;padding:10px;background:#fef2f2;border-radius:8px"></div>
                <div style="display:flex;gap:8px">
                    <button type="submit" class="sima-btn"><i class="fas fa-save"></i> Simpan</button>
                    <button type="button" onclick="closeModals()" class="sima-btn sima-btn--outline">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH MAHASISWA --}}
<div id="modalAddMhs" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:18px;width:100%;max-width:380px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2)">
        <div style="padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
            <div style="font-size:15px;font-weight:700;color:#1e293b">Tambah Mahasiswa ke Kelas</div>
            <button type="button" onclick="closeModals()" style="width:32px;height:32px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;cursor:pointer;color:#64748b"><i class="fas fa-xmark"></i></button>
        </div>
        <div style="padding:20px 24px">
            <form id="formAddMhs">
                <input type="hidden" id="addMhsKelasId">
                <div style="margin-bottom:18px">
                    <label class="sima-label">ID Mahasiswa <span style="color:var(--c-red)">*</span></label>
                    <input type="number" name="mahasiswa_id" id="addMhsId" class="sima-input" required placeholder="ID mahasiswa">
                </div>
                <div id="addMhsErr" style="display:none;font-size:12.5px;color:#dc2626;margin-bottom:12px;padding:10px;background:#fef2f2;border-radius:8px"></div>
                <div style="display:flex;gap:8px">
                    <button type="submit" class="sima-btn"><i class="fas fa-user-plus"></i> Tambahkan</button>
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
let activeKelasId   = null;
let activeKelasKode = '';

['modalTambah','modalEdit','modalAddMhs'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) { if (e.target === this) closeModals(); });
});
function closeModals() {
    document.getElementById('modalTambah').style.display  = 'none';
    document.getElementById('modalEdit').style.display    = 'none';
    document.getElementById('modalAddMhs').style.display  = 'none';
}
function closeMhsPanel() {
    document.getElementById('mahasiswaPanel').style.display = 'none';
    activeKelasId = null;
}

/* ─── Tambah kelas ─── */
document.getElementById('btnTambah').addEventListener('click', function() {
    document.getElementById('formTambah').reset();
    document.getElementById('tambahErr').style.display = 'none';
    document.getElementById('modalTambah').style.display = 'flex';
});
document.getElementById('formTambah').addEventListener('submit', function(e) {
    e.preventDefault();
    const errDiv = document.getElementById('tambahErr');
    errDiv.style.display = 'none';
    const btn = this.querySelector('[type=submit]'); btn.disabled = true;
    fetch('{{ route("jurusan.kelas.store") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(Object.fromEntries(new FormData(this))),
    })
    .then(r => r.json())
    .then(res => { if (res.success) window.location.reload(); else { errDiv.textContent = res.message || 'Gagal'; errDiv.style.display = 'block'; } })
    .catch(err => { errDiv.textContent = err.message; errDiv.style.display = 'block'; })
    .finally(() => { btn.disabled = false; });
});

/* ─── Edit kelas ─── */
document.querySelectorAll('.btn-edit-kelas').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        document.getElementById('editErr').style.display = 'none';
        fetch(`/jurusan/kelas/${id}`, { headers: { Accept: 'application/json' } })
        .then(r => r.json())
        .then(res => {
            const k = res.data ?? res;
            document.getElementById('editId').value          = k.id;
            document.getElementById('editKodeKelas').value   = k.kodeKelas ?? '';
            document.getElementById('editTahunAjar').value   = k.tahunAjar ?? '';
            document.getElementById('modalEdit').style.display = 'flex';
        });
    });
});
document.getElementById('formEdit').addEventListener('submit', function(e) {
    e.preventDefault();
    const errDiv = document.getElementById('editErr');
    errDiv.style.display = 'none';
    const id = document.getElementById('editId').value;
    const btn = this.querySelector('[type=submit]'); btn.disabled = true;
    fetch(`/jurusan/kelas/${id}`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(Object.fromEntries(new FormData(this))),
    })
    .then(r => r.json())
    .then(res => { if (res.success) window.location.reload(); else { errDiv.textContent = res.message || 'Gagal'; errDiv.style.display = 'block'; } })
    .catch(err => { errDiv.textContent = err.message; errDiv.style.display = 'block'; })
    .finally(() => { btn.disabled = false; });
});

/* ─── Hapus kelas ─── */
document.querySelectorAll('.btn-del-kelas').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id; const kode = this.dataset.kode;
        if (!confirm(`Hapus kelas "${kode}"?`)) return;
        fetch(`/jurusan/kelas/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        })
        .then(r => r.json())
        .then(res => { if (res.success) window.location.reload(); else alert(res.message); });
    });
});

/* ─── Lihat mahasiswa kelas ─── */
function loadMhsKelas() {
    document.getElementById('mhsPanelTbody').innerHTML =
        '<tr><td colspan="5" style="padding:20px;text-align:center;color:var(--c-text-3)">Memuat…</td></tr>';
    fetch(`/jurusan/kelas/${activeKelasId}/mahasiswa`, { headers: { Accept: 'application/json' } })
    .then(r => r.json())
    .then(res => {
        const list = res.data?.mahasiswa ?? res.mahasiswa ?? [];
        const tbody = document.getElementById('mhsPanelTbody');
        if (!list.length) {
            tbody.innerHTML = '<tr><td colspan="5" style="padding:20px;text-align:center;color:var(--c-text-3)">Belum ada mahasiswa</td></tr>';
            return;
        }
        tbody.innerHTML = '';
        list.forEach((m, i) => {
            tbody.innerHTML += `<tr>
                <td style="color:var(--c-text-3);font-size:12px">${i+1}</td>
                <td style="font-family:var(--f-mono);font-size:12px">${m.npm ?? '—'}</td>
                <td style="font-weight:600">${m.nama ?? '—'}</td>
                <td style="font-size:12.5px;color:var(--c-text-2)">${m.user?.email ?? '—'}</td>
                <td>
                    <button type="button" class="sima-btn sima-btn--sm"
                            onclick="removeMhs(${activeKelasId},${m.id})"
                            style="font-size:11px;padding:4px 10px;background:rgba(220,38,38,.08);color:#dc2626;border:1px solid rgba(220,38,38,.2)">
                        <i class="fas fa-user-minus"></i>
                    </button>
                </td>
            </tr>`;
        });
    })
    .catch(() => {
        document.getElementById('mhsPanelTbody').innerHTML =
            '<tr><td colspan="5" style="padding:20px;text-align:center;color:var(--c-text-3)">Gagal memuat</td></tr>';
    });
}

document.querySelectorAll('.btn-view-mhs').forEach(btn => {
    btn.addEventListener('click', function() {
        activeKelasId   = this.dataset.id;
        activeKelasKode = this.dataset.kode;
        document.getElementById('panelTitle').textContent = `Mahasiswa — Kelas ${activeKelasKode}`;
        document.getElementById('addMhsKelasId').value    = activeKelasId;
        document.getElementById('mahasiswaPanel').style.display = 'block';
        loadMhsKelas();
    });
});

/* ─── Tambah mahasiswa ke kelas ─── */
document.getElementById('btnAddMhs').addEventListener('click', function() {
    if (!activeKelasId) { alert('Pilih kelas terlebih dahulu'); return; }
    document.getElementById('formAddMhs').reset();
    document.getElementById('addMhsErr').style.display = 'none';
    document.getElementById('modalAddMhs').style.display = 'flex';
});
document.getElementById('formAddMhs').addEventListener('submit', function(e) {
    e.preventDefault();
    const errDiv = document.getElementById('addMhsErr');
    errDiv.style.display = 'none';
    const btn = this.querySelector('[type=submit]'); btn.disabled = true;
    fetch(`/jurusan/kelas/${activeKelasId}/mahasiswa`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ mahasiswa_id: parseInt(document.getElementById('addMhsId').value) }),
    })
    .then(r => r.json())
    .then(res => { if (res.success) { closeModals(); loadMhsKelas(); } else { errDiv.textContent = res.message || 'Gagal'; errDiv.style.display = 'block'; } })
    .catch(err => { errDiv.textContent = err.message; errDiv.style.display = 'block'; })
    .finally(() => { btn.disabled = false; });
});

/* ─── Hapus mahasiswa dari kelas ─── */
function removeMhs(kelasId, mhsId) {
    if (!confirm('Hapus mahasiswa dari kelas ini?')) return;
    fetch(`/jurusan/kelas/${kelasId}/mahasiswa/${mhsId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(res => { if (res.success) loadMhsKelas(); else alert(res.message); });
}
</script>
@endsection
