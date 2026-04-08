@extends('layouts.sima')

@section('page_title',    'Mata Kuliah')
@section('page_section',  'ADMIN JURUSAN')
@section('page_subtitle', 'Pengelolaan mata kuliah di jurusan Anda')

@section('main_content')

<div class="sima-card">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Daftar Mata Kuliah</h5>
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
            <input id="searchInput" type="text" placeholder="Cari nama / kode..."
                   class="sima-input">
            <button id="openAddMkModal" class="sima-btn sima-btn--blue">
                <i class="fas fa-plus"></i> Tambah Matakuliah
            </button>
        </div>
    </div>
    <div style="overflow-x:auto">
        <table class="sima-table">
            <thead>
                <tr>
                    <th onclick="sortBy('id')" style="cursor:pointer">ID <i class="fa-solid fa-sort"></i></th>
                    <th onclick="sortBy('kode')" style="cursor:pointer">KODE <i class="fa-solid fa-sort"></i></th>
                    <th onclick="sortBy('nama')" style="cursor:pointer">NAMA <i class="fa-solid fa-sort"></i></th>
                    <th>SKS</th>
                    <th>SEMESTER</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody id="mkTable"></tbody>
        </table>
    </div>
</div>

{{-- ── MODAL TAMBAH ─────────────────────────────────── --}}
<div id="mkModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);
     backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#0f172a;width:500px;max-height:90vh;overflow:auto;
                padding:30px;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,.5);">
        <h2 style="color:white;font-size:20px;margin-bottom:20px;">Tambah Mata Kuliah</h2>
        <form id="mkForm">
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">Kode Matakuliah</label>
                <input type="text" name="kode"
                       style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">Nama Matakuliah</label>
                <input type="text" name="nama"
                       style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:15px;">
                <div>
                    <label style="color:#94a3b8;">SKS</label>
                    <input type="number" name="sks" min="1" max="6"
                           style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
                </div>
                <div>
                    <label style="color:#94a3b8;">Semester</label>
                    <select name="semester"
                            style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}">Semester {{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div style="display:flex;justify-content:space-between;margin-top:20px;">
                <button type="button" onclick="closeModal()" class="sima-btn sima-btn--gold">Cancel</button>
                <button type="submit" class="sima-btn sima-btn--blue">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL EDIT ───────────────────────────────────── --}}
<div id="mkEditModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);
     backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#0f172a;width:500px;max-height:90vh;overflow:auto;
                padding:30px;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,.5);">
        <h2 style="color:white;font-size:20px;margin-bottom:20px;">Edit Mata Kuliah</h2>
        <form id="mkEditForm">
            <input type="hidden" id="editMkId">
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">Kode Matakuliah</label>
                <input type="text" name="kode" id="editMkKode"
                       style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">Nama Matakuliah</label>
                <input type="text" name="nama" id="editMkNama"
                       style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:15px;">
                <div>
                    <label style="color:#94a3b8;">SKS</label>
                    <input type="number" name="sks" id="editMkSks" min="1" max="6"
                           style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
                </div>
                <div>
                    <label style="color:#94a3b8;">Semester</label>
                    <select name="semester" id="editMkSemester"
                            style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}">Semester {{ $i }}</option>
                        @endfor
                    </select>
                </div>
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
    const tbody       = document.getElementById('mkTable');
    const searchInput = document.getElementById('searchInput');
    const sortInput   = document.getElementById('sortInput');
    const modal       = document.getElementById('mkModal');
    const editModal   = document.getElementById('mkEditModal');
    const form        = document.getElementById('mkForm');
    const editForm    = document.getElementById('mkEditForm');

    function renderEmpty(msg = 'Tidak ada mata kuliah') {
        tbody.innerHTML = `<tr><td colspan="6" style="padding:24px;text-align:center;color:#94a3b8;">${msg}</td></tr>`;
    }

    function renderMk(list) {
        tbody.innerHTML = '';
        if (!Array.isArray(list) || !list.length) { renderEmpty(); return; }
        list.forEach(mk => {
            tbody.innerHTML += `
                <tr>
                    <td>${mk.id ?? '-'}</td>
                    <td><span style="font-family:var(--f-mono);font-size:12px;background:var(--c-blue-lt);
                                     color:var(--c-blue);padding:3px 8px;border-radius:6px">${mk.kode ?? '-'}</span></td>
                    <td style="font-weight:500">${mk.nama ?? '-'}</td>
                    <td style="text-align:center">${mk.sks ?? '-'}</td>
                    <td style="text-align:center">${mk.semester ?? '-'}</td>
                    <td>
                        <button onclick="editMk(${mk.id})" class="sima-btn sima-btn--blue sima-btn--sm">
                            <i class="fas fa-pen"></i> Edit
                        </button>
                        <button onclick="deleteMk(${mk.id})" class="sima-btn sima-btn--danger sima-btn--sm">
                            <i class="fas fa-trash"></i>
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

    function loadMk(search = '', sort = '') {
        renderEmpty('Memuat data...');
        let url = "{{ route('jurusan.matakuliah.data') }}";
        const p = new URLSearchParams();
        if (search) p.append('nama', search);
        if (sort)   p.append('sort',  sort);
        if (p.toString()) url += '?' + p.toString();

        fetch(url)
            .then(r => r.json())
            .then(res => { if (res.success) renderMk(extract(res)); else renderEmpty(res.message); })
            .catch(e => renderEmpty('Gagal memuat: ' + e.message));
    }

    let timer;
    searchInput.addEventListener('keyup', function () {
        clearTimeout(timer);
        timer = setTimeout(() => loadMk(this.value, sortInput.value), 400);
    });
    sortInput.addEventListener('change', function () { loadMk(searchInput.value, this.value); });
    window.sortBy = field => {
        sortInput.value = sortInput.value === field + '_asc' ? field + '_desc' : field + '_asc';
        loadMk(searchInput.value, sortInput.value);
    };

    document.getElementById('openAddMkModal').addEventListener('click', () => { modal.style.display = 'flex'; });
    window.closeModal = () => { modal.style.display = 'none'; editModal.style.display = 'none'; };

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(form).entries());
        fetch("{{ route('jurusan.matakuliah.store') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(res => { if (res.success) { modal.style.display = 'none'; form.reset(); loadMk(); } else alert(res.message); });
    });

    window.editMk = function (id) {
        fetch(`/jurusan/matakuliah/${id}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(res => {
            const mk = res.data ?? res;
            document.getElementById('editMkId').value       = mk.id;
            document.getElementById('editMkKode').value     = mk.kode ?? '';
            document.getElementById('editMkNama').value     = mk.nama ?? '';
            document.getElementById('editMkSks').value      = mk.sks ?? '';
            document.getElementById('editMkSemester').value = mk.semester ?? '';
            editModal.style.display = 'flex';
        });
    };

    editForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const id   = document.getElementById('editMkId').value;
        const data = Object.fromEntries(new FormData(editForm).entries());
        fetch(`/jurusan/matakuliah/${id}`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(res => { if (res.success) { editModal.style.display = 'none'; editForm.reset(); loadMk(); } else alert(res.message); });
    });

    window.deleteMk = function (id) {
        if (!confirm('Hapus mata kuliah ini?')) return;
        fetch(`/jurusan/matakuliah/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => { if (res.success) loadMk(); else alert(res.message); });
    };

    loadMk();
});
</script>
@endsection