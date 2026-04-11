@extends('layouts.sima')

@section('page_title',    'Mahasiswa')
@section('page_section',  'ADMIN JURUSAN')
@section('page_subtitle', 'Data mahasiswa di jurusan Anda beserta informasi kelas')

@section('main_content')

<div class="sima-card">
    <div class="sima-card__header">
        <div><h5 class="sima-card__title">Daftar Mahasiswa</h5></div>
        <div style="display:flex;gap:12px;">
            <input id="searchInput" type="text" placeholder="Cari nama / NPM / email..."
                   class="sima-input" style="min-width:220px;">
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
                    <th>KELAS</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody id="mahasiswaTable"></tbody>
        </table>
    </div>
    {{-- Pagination --}}
    <div id="paginationBar" style="display:flex;align-items:center;justify-content:space-between;
         padding:14px 20px;border-top:1px solid var(--c-border-soft)">
        <span id="paginationInfo" style="font-size:12px;color:var(--c-text-3)"></span>
        <div id="paginationButtons" style="display:flex;gap:6px;"></div>
    </div>
</div>

{{-- ── MODAL DETAIL MAHASISWA ───────────────────────── --}}
<div id="detailModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);
     backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#0f172a;width:600px;max-height:90vh;overflow:auto;
                padding:30px;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,.5);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h2 style="color:white;font-size:20px;margin:0;">Detail Mahasiswa</h2>
            <button onclick="closeModal()"
                    style="background:none;border:none;color:#94a3b8;font-size:20px;cursor:pointer;">
                <i class="fas fa-times"></i>
            </button>
        </div>

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
                    <div style="font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">Status</div>
                    <div id="detailStatus"></div>
                </div>
            </div>
        </div>

        <div style="font-size:13px;color:#94a3b8;font-weight:600;margin-bottom:10px;">
            <i class="fas fa-door-open"></i> Kelas Terdaftar
        </div>
        <div id="detailKelas">
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
    const detailModal = document.getElementById('detailModal');
    let currentPage   = 1;

    /* ── RENDER ──────────────────────────────────────── */
    function renderEmpty(msg = 'Tidak ada data mahasiswa') {
        tbody.innerHTML = `<tr><td colspan="7" style="padding:24px;text-align:center;color:#94a3b8;">${msg}</td></tr>`;
    }

    function renderMahasiswa(list) {
        tbody.innerHTML = '';
        if (!Array.isArray(list) || !list.length) { renderEmpty(); return; }

        list.forEach(user => {
            // user adalah model User dengan relasi mahasiswa di dalamnya
            const mhs         = user.mahasiswa ?? {};
            const statusColor = user.status === 'active' ? '#22c55e' : '#eab308';
            const statusBg    = user.status === 'active' ? 'rgba(34,197,94,0.15)' : 'rgba(234,179,8,0.15)';
            // Kelas dari relasi mahasiswa.kelas (array)
            const kelasList   = mhs.kelas ?? [];
            const kelasHtml   = kelasList.length
                ? kelasList.map(k =>
                    `<span style="background:var(--c-teal-lt);color:var(--c-teal);
                                  padding:2px 7px;border-radius:5px;font-size:11px;
                                  font-family:var(--f-mono);margin-right:4px">${k.kodeKelas}</span>`
                  ).join('')
                : '<span style="color:var(--c-text-3);font-size:12px">—</span>';

            tbody.innerHTML += `
                <tr>
                    <td>${user.id ?? '-'}</td>
                    <td style="font-family:var(--f-mono);font-size:12px">${mhs.npm ?? '-'}</td>
                    <td style="font-weight:500">
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="sima-avatar"
                                 style="width:28px;height:28px;font-size:10px;border-radius:8px;
                                        background:linear-gradient(135deg,var(--c-accent),var(--c-accent-2))">
                                ${(mhs.nama ?? user.email ?? 'X').charAt(0).toUpperCase()}
                            </div>
                            ${mhs.nama ?? '-'}
                        </div>
                    </td>
                    <td style="font-size:12px;color:var(--c-text-2)">${user.email ?? '-'}</td>
                    <td>
                        <span style="background:${statusBg};color:${statusColor};
                                     padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600">
                            ${user.status ?? '-'}
                        </span>
                    </td>
                    <td>${kelasHtml}</td>
                    <td>
                        <button onclick="showDetail(${user.id})" class="sima-btn sima-btn--blue sima-btn--sm">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                    </td>
                </tr>`;
        });
    }

    /* ── PAGINATION ──────────────────────────────────── */
    function renderPagination(pagination) {
        const info    = document.getElementById('paginationInfo');
        const buttons = document.getElementById('paginationButtons');

        const from = ((pagination.current_page - 1) * pagination.per_page) + 1;
        const to   = Math.min(pagination.current_page * pagination.per_page, pagination.total);
        info.textContent = `Menampilkan ${from}–${to} dari ${pagination.total} mahasiswa`;

        buttons.innerHTML = '';

        // Prev
        const prev = document.createElement('button');
        prev.className = 'sima-btn sima-btn--outline sima-btn--sm';
        prev.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prev.disabled  = pagination.current_page === 1;
        prev.onclick   = () => { currentPage = pagination.current_page - 1; loadMahasiswa(); };
        buttons.appendChild(prev);

        // Page numbers (max 5 ditampilkan)
        const start = Math.max(1, pagination.current_page - 2);
        const end   = Math.min(pagination.last_page, pagination.current_page + 2);
        for (let i = start; i <= end; i++) {
            const btn     = document.createElement('button');
            btn.className = `sima-btn sima-btn--sm ${i === pagination.current_page ? 'sima-btn--blue' : 'sima-btn--outline'}`;
            btn.textContent = i;
            btn.onclick   = (page => () => { currentPage = page; loadMahasiswa(); })(i);
            buttons.appendChild(btn);
        }

        // Next
        const next = document.createElement('button');
        next.className = 'sima-btn sima-btn--outline sima-btn--sm';
        next.innerHTML = '<i class="fas fa-chevron-right"></i>';
        next.disabled  = pagination.current_page === pagination.last_page;
        next.onclick   = () => { currentPage = pagination.current_page + 1; loadMahasiswa(); };
        buttons.appendChild(next);
    }

    /* ── LOAD ────────────────────────────────────────── */
    function loadMahasiswa(search = '') {
        renderEmpty('Memuat data...');
        let url = "{{ route('jurusan.mahasiswa.data') }}";
        const p = new URLSearchParams();
        if (search)      p.append('search',   search);
        if (currentPage) p.append('page',     currentPage);
        if (p.toString()) url += '?' + p.toString();

        fetch(url)
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    renderMahasiswa(res.data);
                    if (res.pagination) renderPagination(res.pagination);
                } else {
                    renderEmpty(res.message);
                }
            })
            .catch(e => renderEmpty('Gagal memuat: ' + e.message));
    }

    /* ── SEARCH ──────────────────────────────────────── */
    let timer;
    searchInput.addEventListener('keyup', function () {
        clearTimeout(timer);
        const val = this.value;
        timer = setTimeout(() => { currentPage = 1; loadMahasiswa(val); }, 400);
    });

    /* ── DETAIL MODAL ────────────────────────────────── */
    window.showDetail = function (userId) {
        document.getElementById('detailNama').textContent  = '';
        document.getElementById('detailNpm').textContent   = '';
        document.getElementById('detailEmail').textContent = '';
        document.getElementById('detailStatus').innerHTML  = '';
        document.getElementById('detailKelas').innerHTML   =
            '<div style="color:#64748b;text-align:center;padding:16px;">Memuat...</div>';
        detailModal.style.display = 'flex';

        fetch(`/jurusan/mahasiswa/${userId}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(res => {
            const user        = res.data ?? res;
            const mhs         = user.mahasiswa ?? {};
            const statusColor = user.status === 'active' ? '#22c55e' : '#eab308';
            const statusBg    = user.status === 'active' ? 'rgba(34,197,94,0.15)' : 'rgba(234,179,8,0.15)';

            document.getElementById('detailNama').textContent  = mhs.nama ?? '-';
            document.getElementById('detailNpm').textContent   = mhs.npm  ?? '-';
            document.getElementById('detailEmail').textContent = user.email ?? '-';
            document.getElementById('detailStatus').innerHTML  = `
                <span style="background:${statusBg};color:${statusColor};
                             padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600">
                    ${user.status ?? '-'}
                </span>`;

            // Kelas dari mahasiswa.kelas (relasi belongsToMany)
            const kelasList = mhs.kelas ?? [];
            if (!kelasList.length) {
                document.getElementById('detailKelas').innerHTML =
                    '<div style="color:#64748b;padding:12px;text-align:center;">Belum terdaftar di kelas manapun</div>';
                return;
            }

            let html = '<div style="display:flex;flex-direction:column;gap:8px;">';
            kelasList.forEach(k => {
                // Jadwal di kelas ini (dari relasi kelas.jadwal)
                const jadwalList = k.jadwal ?? [];
                const jadwalInfo = jadwalList.map(j =>
                    `<span style="font-size:11px;color:#64748b">${j.hari} ${j.jam} — ${j.matakuliah?.namaMk ?? ''}</span>`
                ).join('<br>');

                html += `
                    <div style="background:#1e293b;border-radius:10px;padding:12px 14px;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:${jadwalInfo ? '8px' : '0'}">
                            <span style="background:rgba(13,148,136,.15);color:#0d9488;
                                         padding:3px 8px;border-radius:6px;font-size:11px;
                                         font-family:var(--f-mono)">${k.kodeKelas ?? '-'}</span>
                        </div>
                        ${jadwalInfo ? `<div style="padding-left:4px">${jadwalInfo}</div>` : ''}
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