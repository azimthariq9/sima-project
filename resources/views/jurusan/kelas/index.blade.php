{{-- ============================================================
   FILE 1: jurusan/kelas/index.blade.php
   Kelas hanya punya field: kodeKelas
   ============================================================ --}}
@extends('layouts.sima')

@section('page_title',    'Kelas')
@section('page_section',  'ADMIN JURUSAN')
@section('page_subtitle', 'Pengelolaan kelas dan anggota mahasiswa')

@section('main_content')

<div class="sima-card">
    <div class="sima-card__header">
        <div><h5 class="sima-card__title">Daftar Kelas</h5></div>
        <div>
            <select id="sortInput" class="sima-input" style="min-width:150px;">
                <option value="">Sort By</option>
                <option value="kodeKelas_asc">Kode (A-Z)</option>
                <option value="kodeKelas_desc">Kode (Z-A)</option>
                <option value="id_asc">ID (Ascending)</option>
                <option value="id_desc">ID (Descending)</option>
            </select>
        </div>
        <div style="display:flex;gap:12px;">
            <input id="searchInput" type="text" placeholder="Cari kode kelas..."
                   class="sima-input">
            <button id="openAddKelasModal" class="sima-btn sima-btn--blue">
                <i class="fas fa-plus"></i> Tambah Kelas
            </button>
        </div>
    </div>
    <div style="overflow-x:auto">
        <table class="sima-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>KODE KELAS</th>
                    <th>JUMLAH MAHASISWA</th>
                    <th>MAHASISWA</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody id="kelasTable"></tbody>
        </table>
    </div>
</div>

{{-- ── PANEL MAHASISWA KELAS ─────────────────────────── --}}
<div id="mahasiswaPanel" style="display:none;margin-top:20px;">
    <div class="sima-card">
        <div class="sima-card__header">
            <div>
                <h5 class="sima-card__title" id="panelKelasTitle">Mahasiswa Kelas</h5>
                <div class="sima-card__subtitle">Daftar mahasiswa terdaftar</div>
            </div>
            <div style="display:flex;gap:8px;">
                <button id="openAddMahasiswaModal" class="sima-btn sima-btn--blue sima-btn--sm">
                    <i class="fas fa-user-plus"></i> Tambah Mahasiswa
                </button>
                <button onclick="closeMahasiswaPanel()" class="sima-btn sima-btn--outline sima-btn--sm">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>
        </div>
        <div style="overflow-x:auto">
            <table class="sima-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NPM</th>
                        <th>NAMA MAHASISWA</th>
                        <th>EMAIL</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody id="mahasiswaKelasTable"></tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── MODAL TAMBAH KELAS ───────────────────────────── --}}
<div id="kelasModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);
     backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#0f172a;width:420px;padding:30px;border-radius:20px;
                box-shadow:0 20px 60px rgba(0,0,0,.5);">
        <h2 style="color:white;font-size:20px;margin-bottom:20px;">Tambah Kelas</h2>
        <form id="kelasForm">
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">Kode Kelas</label>
                <input type="text" name="kodeKelas"
                       style="width:100%;padding:10px;background:#1e293b;color:white;
                              border-radius:10px;border:1px solid #334155;"
                       placeholder="Contoh: TI-3A">
            </div>
            <div style="display:flex;justify-content:space-between;margin-top:20px;">
                <button type="button" onclick="closeModal()" class="sima-btn sima-btn--gold">Cancel</button>
                <button type="submit" class="sima-btn sima-btn--blue">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL EDIT KELAS ─────────────────────────────── --}}
<div id="kelasEditModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);
     backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#0f172a;width:420px;padding:30px;border-radius:20px;
                box-shadow:0 20px 60px rgba(0,0,0,.5);">
        <h2 style="color:white;font-size:20px;margin-bottom:20px;">Edit Kelas</h2>
        <form id="kelasEditForm">
            <input type="hidden" id="editKelasId">
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">Kode Kelas</label>
                <input type="text" name="kodeKelas" id="editKodeKelas"
                       style="width:100%;padding:10px;background:#1e293b;color:white;
                              border-radius:10px;border:1px solid #334155;">
            </div>
            <div style="display:flex;justify-content:space-between;margin-top:20px;">
                <button type="button" onclick="closeModal()" class="sima-btn sima-btn--gold">Cancel</button>
                <button type="submit" class="sima-btn sima-btn--blue">Update</button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL TAMBAH MAHASISWA KE KELAS ─────────────── --}}
<div id="addMhsModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);
     backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#0f172a;width:420px;padding:30px;border-radius:20px;
                box-shadow:0 20px 60px rgba(0,0,0,.5);">
        <h2 style="color:white;font-size:20px;margin-bottom:20px;">Tambah Mahasiswa ke Kelas</h2>
        <form id="addMhsForm">
            <input type="hidden" id="addMhsKelasId">
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">ID Mahasiswa</label>
                <input type="number" name="mahasiswa_id" id="addMhsMhsId"
                       style="width:100%;padding:10px;background:#1e293b;color:white;
                              border-radius:10px;border:1px solid #334155;"
                       placeholder="Masukkan ID mahasiswa">
            </div>
            <div style="display:flex;justify-content:space-between;margin-top:20px;">
                <button type="button" onclick="closeModal()" class="sima-btn sima-btn--gold">Cancel</button>
                <button type="submit" class="sima-btn sima-btn--blue">Tambahkan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('page_js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody       = document.getElementById('kelasTable');
    const searchInput = document.getElementById('searchInput');
    const sortInput   = document.getElementById('sortInput');
    const modal       = document.getElementById('kelasModal');
    const editModal   = document.getElementById('kelasEditModal');
    const addMhsModal = document.getElementById('addMhsModal');
    const form        = document.getElementById('kelasForm');
    const editForm    = document.getElementById('kelasEditForm');
    const addMhsForm  = document.getElementById('addMhsForm');
    let activeKelasId = null;
    let activeKelasNama = '';

    /* ── RENDER KELAS ────────────────────────────────── */
    function renderEmpty(msg = 'Tidak ada kelas') {
        tbody.innerHTML = `<tr><td colspan="5" style="padding:24px;text-align:center;color:#94a3b8;">${msg}</td></tr>`;
    }

    function renderKelas(list) {
        tbody.innerHTML = '';
        if (!Array.isArray(list) || !list.length) { renderEmpty(); return; }
        list.forEach(k => {
            const mhsCount = k.mahasiswa?.length ?? k.mahasiswa_count ?? '-';
            tbody.innerHTML += `
                <tr>
                    <td>${k.id ?? '-'}</td>
                    <td>
                        <span style="font-family:var(--f-mono);font-size:13px;font-weight:600;
                                     background:var(--c-teal-lt);color:var(--c-teal);
                                     padding:4px 10px;border-radius:6px">
                            ${k.kodeKelas ?? '-'}
                        </span>
                    </td>
                    <td>
                        <span class="sima-badge sima-badge--purple">${mhsCount} mahasiswa</span>
                    </td>
                    <td>
                        <button onclick="viewMahasiswa(${k.id}, '${k.kodeKelas}')"
                                class="sima-btn sima-btn--outline sima-btn--sm">
                            <i class="fas fa-users"></i> Lihat
                        </button>
                    </td>
                    <td>
                        <button onclick="editKelas(${k.id})" class="sima-btn sima-btn--blue sima-btn--sm">
                            <i class="fas fa-pen"></i> Edit
                        </button>
                        <button onclick="deleteKelas(${k.id})" class="sima-btn sima-btn--danger sima-btn--sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
        });
    }

    /* ── RENDER MAHASISWA KELAS ──────────────────────── */
    function renderMahasiswaKelas(data) {
        const tbl  = document.getElementById('mahasiswaKelasTable');
        const list = data?.mahasiswaKelas ?? data?.mahasiswa ?? [];
        if (!list.length) {
            tbl.innerHTML = `<tr><td colspan="5" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada mahasiswa di kelas ini</td></tr>`;
            return;
        }
        tbl.innerHTML = '';
        list.forEach(item => {
            const m = item.mahasiswa ?? item;
            tbl.innerHTML += `
                <tr>
                    <td>${m.id ?? '-'}</td>
                    <td style="font-family:var(--f-mono)">${m.npm ?? '-'}</td>
                    <td style="font-weight:500">${m.nama ?? '-'}</td>
                    <td>${m.user?.email ?? '-'}</td>
                    <td>
                        <button onclick="removeMahasiswa(${activeKelasId}, ${m.id})"
                                class="sima-btn sima-btn--danger sima-btn--sm">
                            <i class="fas fa-user-minus"></i>
                        </button>
                    </td>
                </tr>`;
        });
    }

    function extract(res) {
        if (Array.isArray(res)) return res;
        if (Array.isArray(res?.data)) return res.data;
        if (Array.isArray(res?.data?.data)) return res.data.data;
        return [];
    }

    /* ── LOAD KELAS ──────────────────────────────────── */
    function loadKelas(search = '', sort = '') {
        renderEmpty('Memuat data...');
        let url = "{{ route('jurusan.kelas.data') }}";
        const p = new URLSearchParams();
        if (search) p.append('kode', search);
        if (sort)   p.append('sort',  sort);
        if (p.toString()) url += '?' + p.toString();

        fetch(url)
            .then(r => r.json())
            .then(res => { if (res.success) renderKelas(extract(res)); else renderEmpty(res.message); })
            .catch(e => renderEmpty('Gagal memuat: ' + e.message));
    }

    /* ── VIEW MAHASISWA ──────────────────────────────── */
    window.viewMahasiswa = function (kelasId, kelasKode) {
        activeKelasId   = kelasId;
        activeKelasNama = kelasKode;
        document.getElementById('panelKelasTitle').textContent = `Mahasiswa — Kelas ${kelasKode}`;
        document.getElementById('addMhsKelasId').value = kelasId;
        document.getElementById('mahasiswaPanel').style.display = 'block';
        document.getElementById('mahasiswaKelasTable').innerHTML =
            `<tr><td colspan="5" style="padding:20px;text-align:center;color:#94a3b8;">Memuat...</td></tr>`;

        fetch(`/jurusan/kelas/${kelasId}/mahasiswa`, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(res => renderMahasiswaKelas(res.data ?? res))
            .catch(() => {
                document.getElementById('mahasiswaKelasTable').innerHTML =
                    `<tr><td colspan="5" style="padding:20px;text-align:center;color:#94a3b8;">Gagal memuat</td></tr>`;
            });
    };

    window.closeMahasiswaPanel = () => {
        document.getElementById('mahasiswaPanel').style.display = 'none';
        activeKelasId = null;
    };

    /* ── SEARCH & SORT ───────────────────────────────── */
    let timer;
    searchInput.addEventListener('keyup', function () {
        clearTimeout(timer);
        timer = setTimeout(() => loadKelas(this.value, sortInput.value), 400);
    });
    sortInput.addEventListener('change', function () { loadKelas(searchInput.value, this.value); });

    /* ── MODAL ───────────────────────────────────────── */
    document.getElementById('openAddKelasModal').addEventListener('click', () => { modal.style.display = 'flex'; });
    document.getElementById('openAddMahasiswaModal').addEventListener('click', () => { addMhsModal.style.display = 'flex'; });
    window.closeModal = () => {
        modal.style.display = 'none';
        editModal.style.display = 'none';
        addMhsModal.style.display = 'none';
    };

    /* ── STORE KELAS ─────────────────────────────────── */
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(form).entries());
        fetch("{{ route('jurusan.kelas.store') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) { modal.style.display = 'none'; form.reset(); loadKelas(); }
            else alert(res.message || 'Gagal menyimpan');
        });
    });

    /* ── EDIT KELAS ──────────────────────────────────── */
    window.editKelas = function (id) {
        fetch(`/jurusan/kelas/${id}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(res => {
            const k = res.data ?? res;
            document.getElementById('editKelasId').value    = k.id;
            document.getElementById('editKodeKelas').value  = k.kodeKelas ?? '';
            editModal.style.display = 'flex';
        });
    };

    editForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const id   = document.getElementById('editKelasId').value;
        const data = Object.fromEntries(new FormData(editForm).entries());
        fetch(`/jurusan/kelas/${id}`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(res => { if (res.success) { editModal.style.display = 'none'; editForm.reset(); loadKelas(); } else alert(res.message); });
    });

    /* ── DELETE KELAS ────────────────────────────────── */
    window.deleteKelas = function (id) {
        if (!confirm('Hapus kelas ini? Semua mahasiswa di kelas akan dilepas.')) return;
        fetch(`/jurusan/kelas/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => { if (res.success) loadKelas(); else alert(res.message); });
    };

    /* ── ADD MAHASISWA KE KELAS ──────────────────────── */
    addMhsForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const kelasId = document.getElementById('addMhsKelasId').value;
        const data    = { mahasiswa_id: parseInt(document.getElementById('addMhsMhsId').value) };
        fetch(`/jurusan/kelas/${kelasId}/mahasiswa`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                addMhsModal.style.display = 'none';
                addMhsForm.reset();
                window.viewMahasiswa(activeKelasId, activeKelasNama);
                loadKelas(); // refresh count
            } else alert(res.message);
        });
    });

    /* ── REMOVE MAHASISWA ────────────────────────────── */
    window.removeMahasiswa = function (kelasId, mahasiswaId) {
        if (!confirm('Hapus mahasiswa dari kelas ini?')) return;
        fetch(`/jurusan/kelas/${kelasId}/mahasiswa/${mahasiswaId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) { window.viewMahasiswa(kelasId, activeKelasNama); loadKelas(); }
            else alert(res.message);
        });
    };

    loadKelas();
});
</script>
@endsection