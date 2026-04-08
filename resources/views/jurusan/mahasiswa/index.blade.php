@extends('layouts.sima')

@section('page_title',    'Mahasiswa')
@section('page_section',  'ADMIN JURUSAN')
@section('page_subtitle', 'Data mahasiswa di jurusan Anda beserta informasi kelas')

@section('main_content')

<div class="sima-card">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Daftar Mahasiswa</h5>
        </div>
        <div>
            <select id="sortInput" class="sima-input" style="min-width:150px;">
                <option value="">Sort By</option>
                <option value="nama_asc">Nama (A-Z)</option>
                <option value="nama_desc">Nama (Z-A)</option>
                <option value="npm_asc">NPM (Asc)</option>
                <option value="npm_desc">NPM (Desc)</option>
                <option value="id_asc">ID (Ascending)</option>
                <option value="id_desc">ID (Descending)</option>
            </select>
        </div>
        <div style="display:flex;gap:12px;">
            <input id="searchInput" type="text" placeholder="Cari nama / NPM..."
                   class="sima-input">
        </div>
    </div>
    <div style="overflow-x:auto">
        <table class="sima-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NPM</th>
                    <th>NAMA</th>
                    <th>EMAIL</th>
                    <th>STATUS AKUN</th>
                    <th>KELAS TERDAFTAR</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody id="mahasiswaTable"></tbody>
        </table>
    </div>
</div>

{{-- ── MODAL DETAIL MAHASISWA ───────────────────────── --}}
<div id="detailModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);
     backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#0f172a;width:600px;max-height:90vh;overflow:auto;
                padding:30px;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,.5);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h2 style="color:white;font-size:20px;margin:0;">Detail Mahasiswa</h2>
            <button onclick="closeModal()" style="background:none;border:none;color:#94a3b8;font-size:20px;cursor:pointer;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Info mahasiswa --}}
        <div style="background:#1e293b;border-radius:12px;padding:16px;margin-bottom:16px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <div style="font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">Nama</div>
                    <div id="detailNama" style="color:white;font-weight:500"></div>
                </div>
                <div>
                    <div style="font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">NPM</div>
                    <div id="detailNpm" style="color:white;font-family:var(--f-mono)"></div>
                </div>
                <div>
                    <div style="font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">Email</div>
                    <div id="detailEmail" style="color:white"></div>
                </div>
                <div>
                    <div style="font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">Status Akun</div>
                    <div id="detailStatus"></div>
                </div>
            </div>
        </div>

        {{-- Daftar kelas --}}
        <div style="font-size:13px;color:#94a3b8;font-weight:600;margin-bottom:10px;">
            <i class="fas fa-door-open"></i> Kelas Terdaftar
        </div>
        <div id="detailKelas" style="color:white;font-size:13px;">
            <div style="color:#64748b;text-align:center;padding:16px;">Memuat...</div>
        </div>

        <div style="margin-top:20px;text-align:right;">
            <button onclick="closeModal()" class="sima-btn sima-btn--gold">Tutup</button>
        </div>
    </div>
</div>

@endsection

@section('page_js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody       = document.getElementById('mahasiswaTable');
    const searchInput = document.getElementById('searchInput');
    const sortInput   = document.getElementById('sortInput');
    const detailModal = document.getElementById('detailModal');

    /* ── RENDER ──────────────────────────────────────── */
    function renderEmpty(msg = 'Tidak ada data mahasiswa') {
        tbody.innerHTML = `<tr><td colspan="7" style="padding:24px;text-align:center;color:#94a3b8;">${msg}</td></tr>`;
    }

    function renderMahasiswa(list) {
        tbody.innerHTML = '';
        if (!Array.isArray(list) || !list.length) { renderEmpty(); return; }

        list.forEach(m => {
            const statusColor = m.user?.status === 'active' ? '#22c55e' : '#eab308';
            const statusBg    = m.user?.status === 'active' ? 'rgba(34,197,94,0.15)' : 'rgba(234,179,8,0.15)';
            // Hitung jumlah kelas dari relasi mahasiswaKelas
            const kelasCount  = m.mahasiswa_kelas?.length ?? m.kelas?.length ?? 0;

            tbody.innerHTML += `
                <tr>
                    <td>${m.id ?? '-'}</td>
                    <td style="font-family:var(--f-mono);font-size:12px">${m.npm ?? m.mahasiswa?.npm ?? '-'}</td>
                    <td style="font-weight:500">${m.nama ?? m.mahasiswa?.nama ?? '-'}</td>
                    <td style="font-size:12px;color:var(--c-text-2)">${m.user?.email ?? '-'}</td>
                    <td>
                        <span style="background:${statusBg};color:${statusColor};
                                     padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600">
                            ${m.user?.status ?? '-'}
                        </span>
                    </td>
                    <td>
                        <span style="background:var(--c-purple-lt);color:var(--c-purple);
                                     padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600">
                            ${kelasCount} kelas
                        </span>
                    </td>
                    <td>
                        <button onclick="showDetail(${m.id})" class="sima-btn sima-btn--blue sima-btn--sm">
                            <i class="fas fa-eye"></i> Detail
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

    /* ── LOAD ────────────────────────────────────────── */
    function loadMahasiswa(search = '', sort = '') {
        renderEmpty('Memuat data...');
        let url = "{{ route('jurusan.mahasiswa.data') }}";
        const p = new URLSearchParams();
        if (search) p.append('search', search);
        if (sort)   p.append('sort',   sort);
        if (p.toString()) url += '?' + p.toString();

        fetch(url)
            .then(r => r.json())
            .then(res => { if (res.success) renderMahasiswa(extract(res)); else renderEmpty(res.message); })
            .catch(e => renderEmpty('Gagal memuat: ' + e.message));
    }

    /* ── SEARCH & SORT ───────────────────────────────── */
    let timer;
    searchInput.addEventListener('keyup', function () {
        clearTimeout(timer);
        timer = setTimeout(() => loadMahasiswa(this.value, sortInput.value), 400);
    });
    sortInput.addEventListener('change', function () { loadMahasiswa(searchInput.value, this.value); });

    /* ── DETAIL MODAL ────────────────────────────────── */
    window.showDetail = function (id) {
        // Reset
        document.getElementById('detailNama').textContent  = '';
        document.getElementById('detailNpm').textContent   = '';
        document.getElementById('detailEmail').textContent = '';
        document.getElementById('detailStatus').innerHTML  = '';
        document.getElementById('detailKelas').innerHTML   = '<div style="color:#64748b;text-align:center;padding:16px;">Memuat...</div>';
        detailModal.style.display = 'flex';

        fetch(`/jurusan/mahasiswa/${id}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(res => {
            const m = res.data ?? res;
            const statusColor = m.user?.status === 'active' ? '#22c55e' : '#eab308';
            const statusBg    = m.user?.status === 'active' ? 'rgba(34,197,94,0.15)' : 'rgba(234,179,8,0.15)';

            document.getElementById('detailNama').textContent  = m.nama ?? '-';
            document.getElementById('detailNpm').textContent   = m.npm ?? '-';
            document.getElementById('detailEmail').textContent = m.user?.email ?? '-';
            document.getElementById('detailStatus').innerHTML  = `
                <span style="background:${statusBg};color:${statusColor};
                             padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600">
                    ${m.user?.status ?? '-'}
                </span>`;

            // Render daftar kelas
            const kelasList = m.mahasiswa_kelas ?? m.kelas ?? [];
            if (!kelasList.length) {
                document.getElementById('detailKelas').innerHTML =
                    '<div style="color:#64748b;padding:12px;text-align:center;">Belum terdaftar di kelas manapun</div>';
                return;
            }
            let html = '<div style="display:flex;flex-direction:column;gap:8px;">';
            kelasList.forEach(mk => {
                const k = mk.kelas ?? mk;
                html += `
                    <div style="background:#1e293b;border-radius:10px;padding:12px 14px;
                                display:flex;align-items:center;gap:12px;">
                        <span style="background:rgba(13,148,136,.15);color:#0d9488;
                                     padding:3px 8px;border-radius:6px;font-size:11px;
                                     font-family:var(--f-mono)">${k.kode ?? '-'}</span>
                        <div style="flex:1">
                            <div style="color:white;font-size:13px;font-weight:500">${k.nama ?? '-'}</div>
                            <div style="color:#64748b;font-size:11px">${k.matakuliah?.nama ?? ''}</div>
                        </div>
                        <div style="color:#64748b;font-size:11px">${k.dosen?.nama ?? ''}</div>
                    </div>`;
            });
            html += '</div>';
            document.getElementById('detailKelas').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('detailKelas').innerHTML =
                '<div style="color:#ef4444;text-align:center;padding:16px;">Gagal memuat detail</div>';
        });
    };

    window.closeModal = () => { detailModal.style.display = 'none'; };

    loadMahasiswa();
});
</script>
@endsection