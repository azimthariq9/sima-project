{{-- ============================================================
   FILE: jurusan/jadwal/index.blade.php
   Field: kelas_id, matakuliah_id, dosen_id, hari, jam, ruangan, totalSesi
   ============================================================ --}}
@extends('layouts.sima')

@section('page_title',    'Jadwal')
@section('page_section',  'ADMIN JURUSAN')
@section('page_subtitle', 'Pengelolaan jadwal perkuliahan di jurusan Anda')

@section('main_content')

<div class="sima-card">
    <div class="sima-card__header">
        <div><h5 class="sima-card__title">Daftar Jadwal</h5></div>
        <div>
            <select id="filterHari" class="sima-input" style="min-width:140px;">
                <option value="">Semua Hari</option>
                <option value="Senin">Senin</option>
                <option value="Selasa">Selasa</option>
                <option value="Rabu">Rabu</option>
                <option value="Kamis">Kamis</option>
                <option value="Jumat">Jumat</option>
                <option value="Sabtu">Sabtu</option>
            </select>
        </div>
        <div>
            <select id="sortInput" class="sima-input" style="min-width:140px;">
                <option value="">Sort By</option>
                <option value="id_asc">ID (Ascending)</option>
                <option value="id_desc">ID (Descending)</option>
            </select>
        </div>
        <div>
            <button id="openAddJadwalModal" class="sima-btn sima-btn--blue">
                <i class="fas fa-plus"></i> Tambah Jadwal
            </button>
        </div>
    </div>
    <div style="overflow-x:auto">
        <table class="sima-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>HARI</th>
                    <th>JAM</th>
                    <th>KELAS</th>
                    <th>MATA KULIAH</th>
                    <th>DOSEN</th>
                    <th>RUANGAN</th>
                    <th>TOTAL SESI</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody id="jadwalTable"></tbody>
        </table>
    </div>
</div>

{{-- ── MODAL TAMBAH JADWAL ──────────────────────────── --}}
<div id="jadwalModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);
     backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#0f172a;width:540px;max-height:90vh;overflow:auto;
                padding:30px;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,.5);">
        <h2 style="color:white;font-size:20px;margin-bottom:20px;">Tambah Jadwal</h2>
        <form id="jadwalForm">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:15px;">
                <div>
                    <label style="color:#94a3b8;">Hari</label>
                    <select name="hari"
                            style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                    </select>
                </div>
                <div>
                    <label style="color:#94a3b8;">Jam</label>
                    <input type="text" name="jam"
                           style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;"
                           placeholder="Contoh: 08:00-10:00">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:15px;">
                <div>
                    <label style="color:#94a3b8;">Ruangan</label>
                    <input type="text" name="ruangan"
                           style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;"
                           placeholder="Contoh: Gd.4 R.201">
                </div>
                <div>
                    <label style="color:#94a3b8;">Total Sesi</label>
                    <input type="number" name="totalSesi" min="1" value="16"
                           style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
                </div>
            </div>
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">ID Kelas</label>
                <input type="number" name="kelas_id"
                       style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">ID Mata Kuliah</label>
                <input type="number" name="matakuliah_id"
                       style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">ID Dosen</label>
                <input type="number" name="dosen_id"
                       style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
            </div>
            <div style="display:flex;justify-content:space-between;margin-top:20px;">
                <button type="button" onclick="closeModal()" class="sima-btn sima-btn--gold">Cancel</button>
                <button type="submit" class="sima-btn sima-btn--blue">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL EDIT JADWAL ────────────────────────────── --}}
<div id="jadwalEditModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);
     backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#0f172a;width:540px;max-height:90vh;overflow:auto;
                padding:30px;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,.5);">
        <h2 style="color:white;font-size:20px;margin-bottom:20px;">Edit Jadwal</h2>
        <form id="jadwalEditForm">
            <input type="hidden" id="editJadwalId">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:15px;">
                <div>
                    <label style="color:#94a3b8;">Hari</label>
                    <select name="hari" id="editJadwalHari"
                            style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                    </select>
                </div>
                <div>
                    <label style="color:#94a3b8;">Jam</label>
                    <input type="text" name="jam" id="editJadwalJam"
                           style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:15px;">
                <div>
                    <label style="color:#94a3b8;">Ruangan</label>
                    <input type="text" name="ruangan" id="editJadwalRuangan"
                           style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
                </div>
                <div>
                    <label style="color:#94a3b8;">Total Sesi</label>
                    <input type="number" name="totalSesi" id="editJadwalTotalSesi" min="1"
                           style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
                </div>
            </div>
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">ID Kelas</label>
                <input type="number" name="kelas_id" id="editJadwalKelasId"
                       style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">ID Mata Kuliah</label>
                <input type="number" name="matakuliah_id" id="editJadwalMkId"
                       style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">ID Dosen</label>
                <input type="number" name="dosen_id" id="editJadwalDosenId"
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
    const tbody      = document.getElementById('jadwalTable');
    const filterHari = document.getElementById('filterHari');
    const sortInput  = document.getElementById('sortInput');
    const modal      = document.getElementById('jadwalModal');
    const editModal  = document.getElementById('jadwalEditModal');
    const form       = document.getElementById('jadwalForm');
    const editForm   = document.getElementById('jadwalEditForm');

    const hariColor = {
        'Senin':'#2563EB','Selasa':'#0D9488','Rabu':'#7C3AED',
        'Kamis':'#D97706','Jumat':'#DC2626','Sabtu':'#059669'
    };

    function renderEmpty(msg = 'Tidak ada jadwal') {
        tbody.innerHTML = `<tr><td colspan="9" style="padding:24px;text-align:center;color:#94a3b8;">${msg}</td></tr>`;
    }

    function renderJadwal(list) {
        tbody.innerHTML = '';
        if (!Array.isArray(list) || !list.length) { renderEmpty(); return; }
        list.forEach(j => {
            const color = hariColor[j.hari] ?? '#888';
            tbody.innerHTML += `
                <tr>
                    <td>${j.id ?? '-'}</td>
                    <td>
                        <span style="background:${color}18;color:${color};border:1px solid ${color}30;
                                     padding:3px 10px;border-radius:6px;font-size:12px;font-weight:600">
                            ${j.hari ?? '-'}
                        </span>
                    </td>
                    <td style="font-family:var(--f-mono);font-size:12px;font-weight:600">${j.jam ?? '-'}</td>
                    <td>
                        <span style="font-family:var(--f-mono);background:var(--c-teal-lt);color:var(--c-teal);
                                     padding:3px 8px;border-radius:6px;font-size:12px">
                            ${j.kelas?.kodeKelas ?? '-'}
                        </span>
                    </td>
                    <td style="font-size:12px">${j.matakuliah?.namaMk ?? '-'}</td>
                    <td style="font-size:12px;color:var(--c-text-2)">${j.dosen?.nama ?? '-'}</td>
                    <td style="font-size:12px;color:var(--c-text-3)">${j.ruangan ?? '-'}</td>
                    <td style="text-align:center;font-family:var(--f-mono)">${j.totalSesi ?? '-'}</td>
                    <td>
                        <button onclick="editJadwal(${j.id})" class="sima-btn sima-btn--blue sima-btn--sm">
                            <i class="fas fa-pen"></i> Edit
                        </button>
                        <button onclick="deleteJadwal(${j.id})" class="sima-btn sima-btn--danger sima-btn--sm">
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

    function loadJadwal(hari = '', sort = '') {
        renderEmpty('Memuat data...');
        let url = "{{ route('jurusan.jadwal.data') }}";
        const p = new URLSearchParams();
        if (hari) p.append('hari', hari);
        if (sort) p.append('sort', sort);
        if (p.toString()) url += '?' + p.toString();

        fetch(url)
            .then(r => r.json())
            .then(res => { if (res.success) renderJadwal(extract(res)); else renderEmpty(res.message); })
            .catch(e => renderEmpty('Gagal memuat: ' + e.message));
    }

    filterHari.addEventListener('change', function () { loadJadwal(this.value, sortInput.value); });
    sortInput.addEventListener('change',  function () { loadJadwal(filterHari.value, this.value); });

    document.getElementById('openAddJadwalModal').addEventListener('click', () => { modal.style.display = 'flex'; });
    window.closeModal = () => { modal.style.display = 'none'; editModal.style.display = 'none'; };

    /* STORE */
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(form).entries());
        // Konversi numerik
        ['kelas_id','matakuliah_id','dosen_id','totalSesi'].forEach(k => {
            if (data[k]) data[k] = parseInt(data[k]);
        });
        fetch("{{ route('jurusan.jadwal.store') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) { modal.style.display = 'none'; form.reset(); loadJadwal(); }
            else {
                const err = res.errors ? Object.values(res.errors).flat().join('\n') : (res.message || 'Gagal');
                alert(err);
            }
        });
    });

    /* EDIT */
    window.editJadwal = function (id) {
        fetch(`/jurusan/jadwal/${id}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(res => {
            const j = res.data ?? res;
            document.getElementById('editJadwalId').value         = j.id;
            document.getElementById('editJadwalHari').value       = j.hari ?? '';
            document.getElementById('editJadwalJam').value        = j.jam ?? '';
            document.getElementById('editJadwalRuangan').value    = j.ruangan ?? '';
            document.getElementById('editJadwalTotalSesi').value  = j.totalSesi ?? '';
            document.getElementById('editJadwalKelasId').value    = j.kelas_id ?? '';
            document.getElementById('editJadwalMkId').value       = j.matakuliah_id ?? '';
            document.getElementById('editJadwalDosenId').value    = j.dosen_id ?? '';
            editModal.style.display = 'flex';
        });
    };

    editForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const id   = document.getElementById('editJadwalId').value;
        const data = Object.fromEntries(new FormData(editForm).entries());
        ['kelas_id','matakuliah_id','dosen_id','totalSesi'].forEach(k => {
            if (data[k]) data[k] = parseInt(data[k]);
        });
        fetch(`/jurusan/jadwal/${id}`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(res => { if (res.success) { editModal.style.display = 'none'; editForm.reset(); loadJadwal(); } else alert(res.message); });
    });

    /* DELETE */
    window.deleteJadwal = function (id) {
        if (!confirm('Hapus jadwal ini?')) return;
        fetch(`/jurusan/jadwal/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => { if (res.success) loadJadwal(); else alert(res.message); });
    };

    loadJadwal();
});
</script>
@endsection 