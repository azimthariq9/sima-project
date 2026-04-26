@extends('layouts.sima')

@section('page_title',    'Jadwal')
@section('page_section',  'ADMIN JURUSAN')
@section('page_subtitle', 'Pengelolaan jadwal perkuliahan di jurusan Anda')

@section('main_content')

@php
// Generate tahun ajaran: dari tahun lalu sampai 10 tahun ke depan
$tahunSekarang = (int) date('Y');
$tahunAjarList = [];
for ($i = -1; $i <= 10; $i++) {
    $awal = $tahunSekarang + $i;
    $akhir = $awal + 1;
    $tahunAjarList[] = "{$awal}/{$akhir}";
}
$tahunAjarDefault = date('Y') . '/' . (date('Y') + 1);
@endphp

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
                    <th>TAHUN AJAR</th>
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
    <div id="paginationBar"
         style="display:flex;align-items:center;justify-content:space-between;
                padding:14px 20px;border-top:1px solid var(--c-border-soft)">
        <span id="paginationInfo" style="font-size:12px;color:var(--c-text-3)"></span>
        <div id="paginationButtons" style="display:flex;gap:6px;"></div>
    </div>
</div>

{{-- ── MODAL TAMBAH ─────────────────────────────────── --}}
<div id="jadwalModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);
     backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#0f172a;width:560px;max-height:90vh;overflow:auto;
                padding:30px;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,.5);">
        <h2 style="color:white;font-size:20px;margin-bottom:6px;">Tambah Jadwal</h2>
        <p style="color:#64748b;font-size:12px;margin-bottom:20px;">
            Isi kode — sistem akan mencari ID secara otomatis
        </p>
        <form id="jadwalForm">

            {{-- Baris 1: Hari + Jam --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:15px;">
                <div>
                    <label style="color:#94a3b8;font-size:12px;display:block;margin-bottom:6px">Hari</label>
                    <select name="hari" style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                    </select>
                </div>
                <div>
                    <label style="color:#94a3b8;font-size:12px;display:block;margin-bottom:6px">Jam</label>
                    <input type="text" name="jam" placeholder="Contoh: 08:00-10:00"
                           style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
                </div>
            </div>

            {{-- Baris 2: Ruangan + Total Sesi --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:15px;">
                <div>
                    <label style="color:#94a3b8;font-size:12px;display:block;margin-bottom:6px">Ruangan</label>
                    <input type="text" name="ruangan" placeholder="Contoh: Gd.4 R.201"
                           style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
                </div>
                <div>
                    <label style="color:#94a3b8;font-size:12px;display:block;margin-bottom:6px">Total Sesi</label>
                    <input type="number" name="totalSesi" value="16" min="1"
                           style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
                </div>
            </div>

            {{-- Divider --}}
            <div style="border-top:1px solid #334155;margin:18px 0;"></div>
            <p style="color:#64748b;font-size:11px;margin-bottom:14px;">
                <i class="fas fa-info-circle"></i>
                Isi kode di bawah — ID akan dicari otomatis oleh sistem
            </p>

            {{-- Kelas: kodeKelas + tahunAjar --}}
            <div style="background:#1e293b;border-radius:12px;padding:14px;margin-bottom:14px;border:1px solid #334155;">
                <label style="color:#94a3b8;font-size:11px;font-weight:700;text-transform:uppercase;
                              letter-spacing:.05em;display:block;margin-bottom:10px">
                    <i class="fas fa-door-open"></i> Kelas
                </label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label style="color:#64748b;font-size:11px;display:block;margin-bottom:4px">Kode Kelas</label>
                        <input type="text" name="kodeKelas" placeholder="Contoh: 3KA35"
                               style="width:100%;padding:9px;background:#0f172a;color:white;border-radius:8px;border:1px solid #334155;font-family:var(--f-mono)">
                    </div>
                    <div>
                        <label style="color:#64748b;font-size:11px;display:block;margin-bottom:4px">Tahun Ajaran</label>
                        <select name="tahunAjar"
                                style="width:100%;padding:9px;background:#0f172a;color:white;border-radius:8px;border:1px solid #334155;">
                            @foreach($tahunAjarList as $ta)
                                <option value="{{ $ta }}" {{ $ta === $tahunAjarDefault ? 'selected' : '' }}>
                                    {{ $ta }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="kelasPreview" style="margin-top:8px;font-size:11px;color:#64748b;min-height:16px;"></div>
            </div>

            {{-- Matakuliah: kodeMk --}}
            <div style="background:#1e293b;border-radius:12px;padding:14px;margin-bottom:14px;border:1px solid #334155;">
                <label style="color:#94a3b8;font-size:11px;font-weight:700;text-transform:uppercase;
                              letter-spacing:.05em;display:block;margin-bottom:10px">
                    <i class="fas fa-book"></i> Mata Kuliah
                </label>
                <label style="color:#64748b;font-size:11px;display:block;margin-bottom:4px">Kode MK</label>
                <input type="text" name="kodeMk" placeholder="Contoh: IT012236"
                       style="width:100%;padding:9px;background:#0f172a;color:white;border-radius:8px;border:1px solid #334155;font-family:var(--f-mono)">
                <div id="mkPreview" style="margin-top:8px;font-size:11px;color:#64748b;min-height:16px;"></div>
            </div>

            {{-- Dosen: kodeDos atau nidn --}}
            <div style="background:#1e293b;border-radius:12px;padding:14px;margin-bottom:20px;border:1px solid #334155;">
                <label style="color:#94a3b8;font-size:11px;font-weight:700;text-transform:uppercase;
                              letter-spacing:.05em;display:block;margin-bottom:10px">
                    <i class="fas fa-chalkboard-teacher"></i> Dosen
                </label>
                <label style="color:#64748b;font-size:11px;display:block;margin-bottom:4px">
                    Kode Dosen / NIDN
                </label>
                <input type="text" name="kodeDos" placeholder="Contoh: DOS001 atau 0123456789"
                       style="width:100%;padding:9px;background:#0f172a;color:white;border-radius:8px;border:1px solid #334155;font-family:var(--f-mono)">
                <div id="dosenPreview" style="margin-top:8px;font-size:11px;color:#64748b;min-height:16px;"></div>
            </div>

            <div style="display:flex;justify-content:space-between;">
                <button type="button" onclick="closeModal()" class="sima-btn sima-btn--gold">Cancel</button>
                <button type="submit" class="sima-btn sima-btn--blue">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL EDIT ───────────────────────────────────── --}}
<div id="jadwalEditModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);
     backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#0f172a;width:560px;max-height:90vh;overflow:auto;
                padding:30px;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,.5);">
        <h2 style="color:white;font-size:20px;margin-bottom:6px;">Edit Jadwal</h2>
        <p style="color:#64748b;font-size:12px;margin-bottom:20px;">
            Kosongkan field yang tidak ingin diubah
        </p>
        <form id="jadwalEditForm">
            <input type="hidden" id="editJadwalId">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:15px;">
                <div>
                    <label style="color:#94a3b8;font-size:12px;display:block;margin-bottom:6px">Hari</label>
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
                    <label style="color:#94a3b8;font-size:12px;display:block;margin-bottom:6px">Jam</label>
                    <input type="text" name="jam" id="editJadwalJam"
                           style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:15px;">
                <div>
                    <label style="color:#94a3b8;font-size:12px;display:block;margin-bottom:6px">Ruangan</label>
                    <input type="text" name="ruangan" id="editJadwalRuangan"
                           style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
                </div>
                <div>
                    <label style="color:#94a3b8;font-size:12px;display:block;margin-bottom:6px">Total Sesi</label>
                    <input type="number" name="totalSesi" id="editJadwalTotalSesi" min="1"
                           style="width:100%;padding:10px;background:#1e293b;color:white;border-radius:10px;border:1px solid #334155;">
                </div>
            </div>

            <div style="border-top:1px solid #334155;margin:18px 0;"></div>
            <p style="color:#64748b;font-size:11px;margin-bottom:14px;">
                <i class="fas fa-info-circle"></i>
                Kosongkan jika tidak ingin mengubah kelas / MK / dosen
            </p>

            <div style="background:#1e293b;border-radius:12px;padding:14px;margin-bottom:14px;border:1px solid #334155;">
                <label style="color:#94a3b8;font-size:11px;font-weight:700;text-transform:uppercase;
                              letter-spacing:.05em;display:block;margin-bottom:10px">
                    <i class="fas fa-door-open"></i> Kelas (opsional)
                </label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label style="color:#64748b;font-size:11px;display:block;margin-bottom:4px">Kode Kelas</label>
                        <input type="text" name="kodeKelas" id="editKodeKelas"
                               style="width:100%;padding:9px;background:#0f172a;color:white;border-radius:8px;border:1px solid #334155;font-family:var(--f-mono)">
                    </div>
                    <div>
                        <label style="color:#64748b;font-size:11px;display:block;margin-bottom:4px">Tahun Ajaran</label>
                        <select name="tahunAjar" id="editTahunAjar"
                                style="width:100%;padding:9px;background:#0f172a;color:white;border-radius:8px;border:1px solid #334155;">
                            @foreach($tahunAjarList as $ta)
                                <option value="{{ $ta }}" {{ $ta === $tahunAjarDefault ? 'selected' : '' }}>
                                    {{ $ta }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div style="background:#1e293b;border-radius:12px;padding:14px;margin-bottom:14px;border:1px solid #334155;">
                <label style="color:#94a3b8;font-size:11px;font-weight:700;text-transform:uppercase;
                              letter-spacing:.05em;display:block;margin-bottom:10px">
                    <i class="fas fa-book"></i> Mata Kuliah (opsional)
                </label>
                <input type="text" name="kodeMk" id="editKodeMk" placeholder="Kosongkan jika tidak diubah"
                       style="width:100%;padding:9px;background:#0f172a;color:white;border-radius:8px;border:1px solid #334155;font-family:var(--f-mono)">
            </div>

            <div style="background:#1e293b;border-radius:12px;padding:14px;margin-bottom:20px;border:1px solid #334155;">
                <label style="color:#94a3b8;font-size:11px;font-weight:700;text-transform:uppercase;
                              letter-spacing:.05em;display:block;margin-bottom:10px">
                    <i class="fas fa-chalkboard-teacher"></i> Dosen (opsional)
                </label>
                <input type="text" name="kodeDos" id="editKodeDos" placeholder="Kosongkan jika tidak diubah"
                       style="width:100%;padding:9px;background:#0f172a;color:white;border-radius:8px;border:1px solid #334155;font-family:var(--f-mono)">
            </div>

            <div style="display:flex;justify-content:space-between;">
                <button type="button" onclick="closeModal()" class="sima-btn sima-btn--gold">Cancel</button>
                <button type="submit" class="sima-btn sima-btn--blue">
                    <i class="fas fa-save"></i> Update
                </button>
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
    const modal      = document.getElementById('jadwalModal');
    const editModal  = document.getElementById('jadwalEditModal');
    const form       = document.getElementById('jadwalForm');
    const editForm   = document.getElementById('jadwalEditForm');
    const CSRF       = '{{ csrf_token() }}';
    let currentPage  = 1;

    const hariColor = {
        'Senin':'#2563EB','Selasa':'#0D9488','Rabu':'#7C3AED',
        'Kamis':'#D97706','Jumat':'#DC2626','Sabtu':'#059669'
    };

    /* ── RENDER TABLE ────────────────────────────────── */
    function renderEmpty(msg = 'Tidak ada jadwal') {
        tbody.innerHTML = `<tr><td colspan="10" style="padding:24px;text-align:center;color:#94a3b8;">${msg}</td></tr>`;
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
                    <td style="font-size:12px;color:var(--c-text-3)">${j.kelas?.tahunAjar ?? '-'}</td>
                    <td style="font-size:12px">${j.matakuliah?.namaMk ?? '-'}</td>
                    <td style="font-size:12px;color:var(--c-text-2)">${j.dosen?.nama ?? '-'}</td>
                    <td style="font-size:12px;color:var(--c-text-3)">${j.ruangan ?? '-'}</td>
                    <td style="text-align:center;font-family:var(--f-mono)">${j.totalSesi ?? '-'}</td>
                    <td>
                        <button onclick="editJadwal(${j.id})" class="sima-btn sima-btn--blue sima-btn--sm">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button onclick="deleteJadwal(${j.id})" class="sima-btn sima-btn--danger sima-btn--sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
        });
    }

    /* ── PAGINATION ──────────────────────────────────── */
    function renderPagination(p) {
        const info    = document.getElementById('paginationInfo');
        const buttons = document.getElementById('paginationButtons');
        const from    = ((p.current_page - 1) * p.per_page) + 1;
        const to      = Math.min(p.current_page * p.per_page, p.total);
        info.textContent = `Menampilkan ${from}–${to} dari ${p.total} jadwal`;
        buttons.innerHTML = '';

        const prev = document.createElement('button');
        prev.className = 'sima-btn sima-btn--outline sima-btn--sm';
        prev.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prev.disabled  = p.current_page === 1;
        prev.onclick   = () => { currentPage = p.current_page - 1; loadJadwal(); };
        buttons.appendChild(prev);

        const start = Math.max(1, p.current_page - 2);
        const end   = Math.min(p.last_page, p.current_page + 2);
        for (let i = start; i <= end; i++) {
            const btn = document.createElement('button');
            btn.className = `sima-btn sima-btn--sm ${i === p.current_page ? 'sima-btn--blue' : 'sima-btn--outline'}`;
            btn.textContent = i;
            btn.onclick = (pg => () => { currentPage = pg; loadJadwal(); })(i);
            buttons.appendChild(btn);
        }

        const next = document.createElement('button');
        next.className = 'sima-btn sima-btn--outline sima-btn--sm';
        next.innerHTML = '<i class="fas fa-chevron-right"></i>';
        next.disabled  = p.current_page === p.last_page;
        next.onclick   = () => { currentPage = p.current_page + 1; loadJadwal(); };
        buttons.appendChild(next);
    }

    /* ── LOAD ────────────────────────────────────────── */
    function loadJadwal(hari = '') {
        renderEmpty('Memuat data...');
        let url = "{{ route('jurusan.jadwal.data') }}";
        const p = new URLSearchParams();
        if (hari)        p.append('hari', hari);
        if (currentPage) p.append('page', currentPage);
        if (p.toString()) url += '?' + p.toString();

        fetch(url)
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    renderJadwal(res.data);
                    if (res.pagination) renderPagination(res.pagination);
                } else renderEmpty(res.message);
            })
            .catch(e => renderEmpty('Gagal memuat: ' + e.message));
    }

    filterHari.addEventListener('change', function () {
        currentPage = 1;
        loadJadwal(this.value);
    });

    /* ── MODAL ───────────────────────────────────────── */
    document.getElementById('openAddJadwalModal').addEventListener('click', () => {
        modal.style.display = 'flex';
    });
    window.closeModal = () => {
        modal.style.display = 'none';
        editModal.style.display = 'none';
    };

    /* ── STORE ───────────────────────────────────────── */
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(form).entries());
        data.totalSesi = parseInt(data.totalSesi);

        const btn = form.querySelector('[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        fetch("{{ route('jurusan.jadwal.store') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                modal.style.display = 'none';
                form.reset();
                clearPreviews();
                currentPage = 1;
                loadJadwal();
            } else {
                alert(res.message || 'Gagal menyimpan');
            }
        })
        .catch(e => alert('Error: ' + e.message))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
        });
    });

    /* ── EDIT ────────────────────────────────────────── */
    window.editJadwal = function (id) {
        fetch(`/jurusan/jadwal/${id}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(res => {
            const j = res.data ?? res;
            document.getElementById('editJadwalId').value      = j.id;
            document.getElementById('editJadwalHari').value    = j.hari ?? '';
            document.getElementById('editJadwalJam').value     = j.jam ?? '';
            document.getElementById('editJadwalRuangan').value = j.ruangan ?? '';
            document.getElementById('editJadwalTotalSesi').value = j.totalSesi ?? '';
            // Isi info saat ini sebagai placeholder (bukan value — biar tahu mana yg mau diubah)
            document.getElementById('editKodeKelas').placeholder = j.kelas?.kodeKelas ?? '';
            document.getElementById('editKodeMk').placeholder    = j.matakuliah?.kodeMk ?? '';
            document.getElementById('editKodeDos').placeholder   = j.dosen?.kodeDos ?? j.dosen?.nidn ?? '';
            // Kosongkan value agar tidak dikirim jika tidak diubah
            document.getElementById('editKodeKelas').value = '';
            document.getElementById('editKodeMk').value    = '';
            document.getElementById('editKodeDos').value   = '';
            editModal.style.display = 'flex';
        });
    };

    editForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const id   = document.getElementById('editJadwalId').value;
        const data = {};

        // Hanya kirim field yang diisi
        const fields = ['hari', 'jam', 'ruangan', 'totalSesi', 'kodeKelas', 'tahunAjar', 'kodeMk', 'kodeDos'];
        fields.forEach(f => {
            const el = editForm.querySelector(`[name="${f}"]`);
            if (el && el.value.trim() !== '') data[f] = el.value.trim();
        });
        if (data.totalSesi) data.totalSesi = parseInt(data.totalSesi);

        const btn = editForm.querySelector('[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        fetch(`/jurusan/jadwal/${id}`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                editModal.style.display = 'none';
                editForm.reset();
                loadJadwal(filterHari.value);
            } else {
                alert(res.message || 'Gagal update');
            }
        })
        .catch(e => alert('Error: ' + e.message))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Update';
        });
    });

    /* ── DELETE ──────────────────────────────────────── */
    window.deleteJadwal = function (id) {
        if (!confirm('Hapus jadwal ini?')) return;
        fetch(`/jurusan/jadwal/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => { if (res.success) loadJadwal(filterHari.value); else alert(res.message); });
    };

    /* ── PREVIEW KODE (debounce lookup) ─────────────── */
    // Saat admin ketik kodeKelas, tampilkan preview nama kelas jika ditemukan
    function clearPreviews() {
        ['kelasPreview','mkPreview','dosenPreview'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = '';
        });
    }

    function previewKelas() {
        const kode     = form.querySelector('[name="kodeKelas"]').value.trim();
        const tahunAjar = form.querySelector('[name="tahunAjar"]').value.trim();
        const preview  = document.getElementById('kelasPreview');
        if (!kode || !tahunAjar) { preview.textContent = ''; return; }

        fetch(`/jurusan/kelas/preview?kodeKelas=${encodeURIComponent(kode)}&tahunAjar=${encodeURIComponent(tahunAjar)}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => {
            preview.style.color = res.found ? '#22c55e' : '#ef4444';
            preview.textContent = res.found
                ? `✓ Ditemukan: Kelas ${res.kodeKelas} (${res.tahunAjar})`
                : `✗ Kelas '${kode}' tahun '${tahunAjar}' tidak ditemukan`;
        })
        .catch(() => {});
    }

    function previewMk() {
        const kode    = form.querySelector('[name="kodeMk"]').value.trim();
        const preview = document.getElementById('mkPreview');
        if (!kode) { preview.textContent = ''; return; }

        fetch(`/jurusan/matakuliah/preview?kodeMk=${encodeURIComponent(kode)}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => {
            preview.style.color = res.found ? '#22c55e' : '#ef4444';
            preview.textContent = res.found
                ? `✓ Ditemukan: ${res.namaMk} (${res.kodeMk})`
                : `✗ Kode MK '${kode}' tidak ditemukan`;
        })
        .catch(() => {});
    }

    function previewDosen() {
        const kode    = form.querySelector('[name="kodeDos"]').value.trim();
        const preview = document.getElementById('dosenPreview');
        if (!kode) { preview.textContent = ''; return; }

        fetch(`/jurusan/dosen/preview?kodeDos=${encodeURIComponent(kode)}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => {
            preview.style.color = res.found ? '#22c55e' : '#ef4444';
            preview.textContent = res.found
                ? `✓ Ditemukan: ${res.nama} (${res.kodeDos ?? res.nidn})`
                : `✗ Kode/NIDN '${kode}' tidak ditemukan`;
        })
        .catch(() => {});
    }

    // Debounce helper
    function debounce(fn, ms) {
        let t;
        return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
    }

    const debouncedKelas  = debounce(previewKelas, 500);
    const debouncedMk     = debounce(previewMk, 500);
    const debouncedDosen  = debounce(previewDosen, 500);

    form.querySelector('[name="kodeKelas"]').addEventListener('input', debouncedKelas);
    form.querySelector('[name="tahunAjar"]').addEventListener('change', debouncedKelas);
    form.querySelector('[name="kodeMk"]').addEventListener('input', debouncedMk);
    form.querySelector('[name="kodeDos"]').addEventListener('input', debouncedDosen);

    loadJadwal();
});
</script>
@endsection