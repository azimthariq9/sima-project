@extends('layouts.sima')

@section('page_title',    'Dosen')
@section('page_section',  'ADMIN JURUSAN')
@section('page_subtitle', 'Pengelolaan data dosen di jurusan Anda')

@section('main_content')

<div class="sima-card">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Daftar Dosen</h5>
        </div>
        <div>
            <select id="sortInput" class="sima-input" style="min-width:150px;">
                <option value="">Sort By</option>
                <optgroup label="Nama">
                    <option value="nama_asc">Nama (A-Z)</option>
                    <option value="nama_desc">Nama (Z-A)</option>
                </optgroup>
                <optgroup label="ID">
                    <option value="id_asc">ID (Ascending)</option>
                    <option value="id_desc">ID (Descending)</option>
                </optgroup>
            </select>
        </div>
        <div style="display:flex;gap:12px;">
            <input id="searchInput" type="text" placeholder="Cari nama / NIDN..."
                   class="sima-input">
            <button id="openAddDosenModal" class="sima-btn sima-btn--blue">
                <i class="fas fa-plus"></i> Tambah Dosen
            </button>
        </div>
    </div>
    <div style="overflow-x:auto">
        <table class="sima-table">
            <thead>
                <tr>
                    <th onclick="sortBy('id')" style="cursor:pointer">ID <i class="fa-solid fa-sort"></i></th>
                    <th>NAMA</th>
                    <th>NIDN</th>
                    <th>KODE DOSEN</th>
                    <th>EMAIL</th>
                    <th>STATUS AKUN</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody id="dosenTable"></tbody>
        </table>
    </div>
</div>

{{-- ── MODAL TAMBAH DOSEN ──────────────────────────────── --}}
<div id="dosenModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);
     backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#0f172a;width:520px;max-height:90vh;overflow:auto;
                padding:30px;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,.5);">
        <h2 style="color:white;font-size:20px;margin-bottom:20px;">Tambah Data Dosen</h2>
        <form id="dosenForm">
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">Nama Dosen</label>
                <input type="text" name="nama"
                       style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">NIDN</label>
                <input type="text" name="nidn"
                       style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">Kode Dosen</label>
                <input type="text" name="kodeDos"
                       style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">User (Akun)</label>
                <select name="user_id"
                        style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
                    <option value="">Pilih akun user dosen</option>
                </select>
            </div>
            <div style="display:flex;justify-content:space-between;margin-top:20px;">
                <button type="button" onclick="closeModal()" class="sima-btn sima-btn--gold">Cancel</button>
                <button type="submit" class="sima-btn sima-btn--blue">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL EDIT DOSEN ──────────────────────────────── --}}
<div id="dosenEditModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);
     backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#0f172a;width:520px;max-height:90vh;overflow:auto;
                padding:30px;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,.5);">
        <h2 style="color:white;font-size:20px;margin-bottom:20px;">Edit Data Dosen</h2>
        <form id="dosenEditForm">
            <input type="hidden" id="editDosenId">
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">Nama Dosen</label>
                <input type="text" name="nama" id="editNama"
                       style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">NIDN</label>
                <input type="text" name="nidn" id="editNidn"
                       style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">Kode Dosen</label>
                <input type="text" name="kodeDos" id="editKodeDos"
                       style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
            </div>
            <div style="display:flex;justify-content:space-between;margin-top:20px;">
                <button type="button" onclick="closeModal()" class="sima-btn sima-btn--gold">Cancel</button>
                <button type="submit" class="sima-btn sima-btn--blue">Update</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('page_js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody       = document.getElementById('dosenTable');
    const searchInput = document.getElementById('searchInput');
    const sortInput   = document.getElementById('sortInput');
    const modal       = document.getElementById('dosenModal');
    const editModal   = document.getElementById('dosenEditModal');
    const form        = document.getElementById('dosenForm');
    const editForm    = document.getElementById('dosenEditForm');

    /* ── RENDER ──────────────────────────────────────── */
    function renderEmpty(msg = 'Tidak ada data dosen') {
        tbody.innerHTML = `<tr><td colspan="7" style="padding:24px;text-align:center;color:#94a3b8;">${msg}</td></tr>`;
    }

    function renderDosen(list) {
        tbody.innerHTML = '';
        if (!Array.isArray(list) || !list.length) { renderEmpty(); return; }

        list.forEach(d => {
            const statusColor = d.user?.status === 'active' ? '#22c55e' : '#eab308';
            const statusBg    = d.user?.status === 'active' ? 'rgba(34,197,94,0.15)' : 'rgba(234,179,8,0.15)';
            tbody.innerHTML += `
                <tr>
                    <td>${d.id ?? '-'}</td>
                    <td style="font-weight:500">${d.nama ?? '-'}</td>
                    <td style="font-family:var(--f-mono);font-size:12px">${d.nidn ?? '-'}</td>
                    <td style="font-family:var(--f-mono);font-size:12px">${d.kodeDos ?? '-'}</td>
                    <td>${d.user?.email ?? '-'}</td>
                    <td>
                        <span style="background:${statusBg};color:${statusColor};
                                     padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600">
                            ${d.user?.status ?? '-'}
                        </span>
                    </td>
                    <td>
                        <button onclick="editDosen(${d.id})" class="sima-btn sima-btn--blue sima-btn--sm">
                            <i class="fas fa-pen"></i> Edit
                        </button>
                        <button onclick="deleteDosen(${d.id})" class="sima-btn sima-btn--danger sima-btn--sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
        });
    }

    function extract(res) {
        if (Array.isArray(res)) return res;
        if (Array.isArray(res?.data)) return res.data;
        if (Array.isArray(res?.data?.data)) return res.data.data; // paginated
        return [];
    }

    /* ── LOAD ────────────────────────────────────────── */
    function loadDosen(search = '', sort = '') {
        renderEmpty('Memuat data...');
        let url = "{{ route('jurusan.dosen.data') }}";
        const p = new URLSearchParams();
        if (search) p.append('nama', search);
        if (sort)   p.append('sort',  sort);
        if (p.toString()) url += '?' + p.toString();

        fetch(url)
            .then(r => r.json())
            .then(res => { if (res.success) renderDosen(extract(res)); else renderEmpty(res.message); })
            .catch(e => renderEmpty('Gagal memuat: ' + e.message));
    }

    /* ── SEARCH & SORT ───────────────────────────────── */
    let timer;
    searchInput.addEventListener('keyup', function () {
        clearTimeout(timer);
        timer = setTimeout(() => loadDosen(this.value, sortInput.value), 400);
    });
    sortInput.addEventListener('change', function () {
        loadDosen(searchInput.value, this.value);
    });
    window.sortBy = field => {
        const cur = sortInput.value;
        sortInput.value = cur === field + '_asc' ? field + '_desc' : field + '_asc';
        loadDosen(searchInput.value, sortInput.value);
    };

    /* ── MODAL ───────────────────────────────────────── */
    document.getElementById('openAddDosenModal').addEventListener('click', () => {
        modal.style.display = 'flex';
    });
    window.closeModal = () => {
        modal.style.display = 'none';
        editModal.style.display = 'none';
    };

    /* ── STORE ───────────────────────────────────────── */
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const fd = new FormData(form);
        const data = Object.fromEntries(fd.entries());

        fetch("{{ route('jurusan.dosen.store') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) { modal.style.display = 'none'; form.reset(); loadDosen(); }
            else alert(res.message || 'Gagal menyimpan');
        })
        .catch(e => alert('Error: ' + e.message));
    });

    /* ── EDIT ────────────────────────────────────────── */
    window.editDosen = function (id) {
        fetch(`/jurusan/dosen/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => {
            const d = res.data ?? res;
            document.getElementById('editDosenId').value  = d.id;
            document.getElementById('editNama').value     = d.nama ?? '';
            document.getElementById('editNidn').value     = d.nidn ?? '';
            document.getElementById('editKodeDos').value  = d.kodeDos ?? '';
            editModal.style.display = 'flex';
        })
        .catch(e => alert('Gagal mengambil data: ' + e.message));
    };

    editForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const id   = document.getElementById('editDosenId').value;
        const fd   = new FormData(editForm);
        const data = Object.fromEntries(fd.entries());

        fetch(`/jurusan/dosen/${id}`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) { editModal.style.display = 'none'; editForm.reset(); loadDosen(); }
            else alert(res.message || 'Gagal update');
        })
        .catch(e => alert('Error: ' + e.message));
    });

    /* ── DELETE ──────────────────────────────────────── */
    window.deleteDosen = function (id) {
        if (!confirm('Hapus data dosen ini?')) return;
        fetch(`/jurusan/dosen/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => { if (res.success) loadDosen(); else alert(res.message); })
        .catch(e => alert('Error: ' + e.message));
    };

    loadDosen();
});
</script>
@endsection