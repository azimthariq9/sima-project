<x-app-layout>
@section('page_title',    'User Index')
@section('page_section',  'KERJA SAMA LUAR NEGERI')
@section('page_subtitle', 'Pengelolaan Akun Admin, Mahasiswa, dan Dosen')
    {{-- <x-slot name="header">
        <h2 class="text-2xl font-bold text-white">
            Users KLN
        </h2>
    </x-slot> --}}
@section('main_content')
    

    <div class="sima-card">
        <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Users List</h5>
                    {{-- <div class="sima-card__subtitle">kumpulan user yang ada dalam sistem</div> --}}
                </div>
                <div>
                    <select name="sort" id="sortInput" class="sima-input" style="min-width: 150px;">
                        <option value="">Sort By</option>
                        <optgroup label="Email">
                            <option value="email_asc">Email (A-Z)</option>
                            <option value="email_desc">Email (Z-A)</option>
                        </optgroup>
                        <optgroup label="Role">
                            <option value="role_asc">Role (A-Z)</option>
                            <option value="role_desc">Role (Z-A)</option>
                        </optgroup>
                        <optgroup label="Status">
                            <option value="status_asc">Status (Active first)</option>
                            <option value="status_desc">Status (Inactive first)</option>
                        </optgroup>
                        <optgroup label="ID">
                            <option value="id_asc">ID (Ascending)</option>
                            <option value="id_desc">ID (Descending)</option>
                        </optgroup>
                    </select>
                </div>
                <div style="display:flex; gap:12px;">
                    <input id="searchInput"
                        type="text"
                        placeholder="Search email..."
                        class="sima-input"
                        >

                    <button id="openAddUserModal"
                        class="sima-btn sima-btn--blue sima-btn--full">
                        + Add User
                    </button>
                </div>
        </div>
        <div class="overflow-x-auto">
            <!-- Table -->
                <table class="sima-table">
                    <thead >
                        <tr>
                            <th style="padding:16px 24px; text-align:left; cursor: pointer;" onclick="sortBy('id')">
                                ID <i class="fa-solid fa-sort"></i>
                            </th>
                            <th style="padding:16px 24px; text-align:left; cursor: pointer;" onclick="sortBy('role')">
                                ROLE <i class="fa-solid fa-sort"></i>
                            </th>
                            <th style="padding:16px 24px; text-align:left; cursor: pointer;" onclick="sortBy('email')">
                                EMAIL <i class="fa-solid fa-sort"></i>
                            </th>
                            <th style="padding:16px 24px; text-align:left; cursor: pointer;">
                                JURUSAN <i class="fa-solid fa-sort"></i>
                            </th>
                            <th style="padding:16px 24px; text-align:left; cursor: pointer;" onclick="sortBy('status')">
                                STATUS <i class="fa-solid fa-sort"></i>
                            </th>
                            <th style="padding:16px 24px; text-align:left;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="usersTable">
                    </tbody>

                </table>
        </div>
        <div id="paginationContainer" style="padding:14px 20px;border-top:1px solid var(--c-border);display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;"></div>

    </div>

    
<div id="userModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; width:600px; max-height:90vh; overflow:auto; border-radius:18px; box-shadow:0 20px 60px rgba(0,0,0,.2);">

        <div style="padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
            <div style="font-size:15px;font-weight:700;color:#1e293b">Tambah User Baru</div>
            <button type="button" onclick="closeModal()" style="width:32px;height:32px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;cursor:pointer;color:#64748b">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <div style="padding:20px 24px">
        <form id="userForm">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px">
                <div>
                    <label class="sima-label">Role <span style="color:var(--c-red)">*</span></label>
                    <select name="role" id="roleSelect" onchange="handleRoleChange()" class="sima-input">
                        <option value="">Pilih Role</option>
                        <option value="bipa">BIPA</option>
                        <option value="kln">KLN</option>
                        <option value="jurusan">Jurusan</option>
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="dosen">Dosen</option>
                    </select>
                </div>
                <div>
                    <label class="sima-label">Status</label>
                    <select name="status" class="sima-input">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:14px">
                <label class="sima-label">Email <span style="color:var(--c-red)">*</span></label>
                <input type="email" name="email" class="sima-input" placeholder="email@instansi.ac.id">
            </div>

            <div style="margin-bottom:14px">
                <label class="sima-label">Password
                    <span style="font-weight:400;color:var(--c-text-3);font-size:11.5px">(opsional — kosongkan untuk akun login via OTP)</span>
                </label>
                <input type="password" name="password" class="sima-input" placeholder="Min. 6 karakter, atau kosongkan">
            </div>

            <div style="margin-bottom:14px">
                <label class="sima-label">Jurusan</label>
                <select name="jurusan_id" id="jurusan_id" onchange="handleRoleChange()" class="sima-input">
                    <option value="">Pilih Jurusan</option>
                    @foreach($jurusan as $j)
                        <option value="{{ $j->id }}">{{ $j->namaJurusan }}</option>
                    @endforeach
                </select>
            </div>

            <!-- MAHASISWA SECTION -->
            <div id="mahasiswaSection" style="display:none;background:#f8fafc;border-radius:12px;padding:14px;margin-bottom:14px;border:1px solid #e2e8f0">
                <div style="font-size:12.5px;font-weight:700;color:#1e293b;margin-bottom:12px;text-transform:uppercase;letter-spacing:.05em">
                    <i class="fas fa-user-graduate" style="margin-right:4px"></i> Data Mahasiswa
                </div>
                <div style="margin-bottom:12px">
                    <label class="sima-label">NPM
                        <span style="font-weight:400;color:var(--c-text-3);font-size:11.5px">(kosongkan untuk auto-generate)</span>
                    </label>
                    <div style="display:flex;gap:8px">
                        <input type="text" name="mahasiswa[npm]" id="npmInput" class="sima-input" placeholder="Auto-generate jika kosong">
                        <button type="button" id="btnGenerateNpm" onclick="generateNpm()"
                            style="white-space:nowrap;padding:8px 14px;border:1px solid var(--c-border);border-radius:8px;background:#f1f5f9;color:#475569;font-size:12.5px;cursor:pointer;font-weight:600">
                            <i class="fas fa-dice"></i> Generate
                        </button>
                    </div>
                </div>
                <div>
                    <label class="sima-label">Nama Mahasiswa <span style="color:var(--c-red)">*</span></label>
                    <input type="text" name="mahasiswa[nama]" class="sima-input" placeholder="Nama lengkap">
                </div>
                <div style="margin-top:12px">
                    <label class="sima-label">Tipe Mahasiswa</label>
                    <select name="mahasiswa[tipeMahasiswa]" class="sima-input">
                        <option value="">— Pilih Tipe —</option>
                        <option value="Beasiswa TIAS">Beasiswa TIAS</option>
                        <option value="Beasiswa KNB">Beasiswa KNB</option>
                        <option value="Beasiswa Gunadarma">Beasiswa Gunadarma</option>
                        <option value="Internasional Mandiri">Internasional Mandiri</option>
                        <option value="Short Course (3 Bulan)">Short Course (3 Bulan)</option>
                    </select>
                </div>
            </div>

            <!-- DOSEN SECTION -->
            <div id="dosenSection" style="display:none;background:#f8fafc;border-radius:12px;padding:14px;margin-bottom:14px;border:1px solid #e2e8f0">
                <div style="font-size:12.5px;font-weight:700;color:#1e293b;margin-bottom:12px;text-transform:uppercase;letter-spacing:.05em">
                    <i class="fas fa-chalkboard-teacher" style="margin-right:4px"></i> Data Dosen
                </div>
                <div style="margin-bottom:12px">
                    <label class="sima-label">Nama Dosen <span style="color:var(--c-red)">*</span></label>
                    <input type="text" name="dosen[nama]" class="sima-input" placeholder="Nama lengkap">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                    <div>
                        <label class="sima-label">NIDN</label>
                        <input type="text" name="dosen[nidn]" class="sima-input" placeholder="Nomor Induk">
                    </div>
                    <div>
                        <label class="sima-label">Kode Dosen</label>
                        <input type="text" name="dosen[kodeDos]" class="sima-input" placeholder="Kode">
                    </div>
                </div>
            </div>

            <div id="createErr" style="display:none;font-size:12.5px;color:#dc2626;margin-bottom:12px;padding:10px;background:#fef2f2;border-radius:8px"></div>

            <div style="display:flex;gap:8px;margin-top:4px">
                <button type="submit" class="sima-btn"><i class="fas fa-plus"></i> Simpan</button>
                <button type="button" onclick="closeModal()" class="sima-btn sima-btn--outline">Batal</button>
            </div>
        </form>
        </div>
    </div>
</div>
<div id="userEdit" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; width:600px; max-height:90vh; overflow:auto; border-radius:18px; box-shadow:0 20px 60px rgba(0,0,0,.2);">

        <div style="padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
            <div style="font-size:15px;font-weight:700;color:#1e293b">Edit User</div>
            <button type="button" onclick="closeModal()" style="width:32px;height:32px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;cursor:pointer;color:#64748b">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <div style="padding:20px 24px">
        <form id="userEditForm">
            <input type="hidden" id="editUserId" name="id">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px">
                <div>
                    <label class="sima-label">Role</label>
                    <select name="role" id="editRoleSelect" onchange="handleEditRoleChange()" class="sima-input">
                        <option value="">Pilih Role</option>
                        <option value="bipa">BIPA</option>
                        <option value="kln">KLN</option>
                        <option value="jurusan">Jurusan</option>
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="dosen">Dosen</option>
                    </select>
                </div>
                <div>
                    <label class="sima-label">Status</label>
                    <select name="status" id="editStatus" class="sima-input">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:14px">
                <label class="sima-label">Email</label>
                <input type="email" name="email" id="editEmail" class="sima-input">
            </div>

            <div style="margin-bottom:14px">
                <label class="sima-label">Password
                    <span style="font-weight:400;color:var(--c-text-3);font-size:11.5px">(kosongkan jika tidak diubah)</span>
                </label>
                <input type="password" name="password" id="editPassword" class="sima-input" placeholder="Min. 6 karakter">
            </div>

            <div style="margin-bottom:14px">
                <label class="sima-label">Jurusan</label>
                <select name="jurusan_id" id="editJurusanId" onchange="handleEditRoleChange()" class="sima-input">
                    <option value="">Pilih Jurusan</option>
                    @foreach($jurusan as $j)
                        <option value="{{ $j->id }}">{{ $j->namaJurusan }}</option>
                    @endforeach
                </select>
            </div>

            <!-- MAHASISWA SECTION EDIT -->
            <div id="editMahasiswaSection" style="display:none;background:#f8fafc;border-radius:12px;padding:14px;margin-bottom:14px;border:1px solid #e2e8f0">
                <div style="font-size:12.5px;font-weight:700;color:#1e293b;margin-bottom:12px;text-transform:uppercase;letter-spacing:.05em">
                    <i class="fas fa-user-graduate" style="margin-right:4px"></i> Data Mahasiswa
                </div>
                <div style="margin-bottom:12px">
                    <label class="sima-label">NPM</label>
                    <input type="text" name="mahasiswa[npm]" id="editMahasiswaNpm" class="sima-input">
                </div>
                <div style="margin-bottom:12px">
                    <label class="sima-label">Nama Mahasiswa</label>
                    <input type="text" name="mahasiswa[nama]" id="editMahasiswaNama" class="sima-input">
                </div>
                <div>
                    <label class="sima-label">Tipe Mahasiswa</label>
                    <select name="mahasiswa[tipeMahasiswa]" id="editMahasiswaTipe" class="sima-input">
                        <option value="">— Pilih Tipe —</option>
                        <option value="Beasiswa TIAS">Beasiswa TIAS</option>
                        <option value="Beasiswa KNB">Beasiswa KNB</option>
                        <option value="Beasiswa Gunadarma">Beasiswa Gunadarma</option>
                        <option value="Internasional Mandiri">Internasional Mandiri</option>
                        <option value="Short Course (3 Bulan)">Short Course (3 Bulan)</option>
                    </select>
                </div>
            </div>

            <!-- DOSEN SECTION EDIT -->
            <div id="editDosenSection" style="display:none;background:#f8fafc;border-radius:12px;padding:14px;margin-bottom:14px;border:1px solid #e2e8f0">
                <div style="font-size:12.5px;font-weight:700;color:#1e293b;margin-bottom:12px;text-transform:uppercase;letter-spacing:.05em">
                    <i class="fas fa-chalkboard-teacher" style="margin-right:4px"></i> Data Dosen
                </div>
                <div style="margin-bottom:12px">
                    <label class="sima-label">Nama Dosen</label>
                    <input type="text" name="dosen[nama]" id="editDosenNama" class="sima-input">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                    <div>
                        <label class="sima-label">NIDN</label>
                        <input type="text" name="dosen[nidn]" id="editDosenNidn" class="sima-input">
                    </div>
                    <div>
                        <label class="sima-label">Kode Dosen</label>
                        <input type="text" name="dosen[kodeDos]" id="editDosenKode" class="sima-input">
                    </div>
                </div>
            </div>

            <div id="editErr" style="display:none;font-size:12.5px;color:#dc2626;margin-bottom:12px;padding:10px;background:#fef2f2;border-radius:8px"></div>

            <div style="display:flex;gap:8px;margin-top:4px">
                <button type="submit" class="sima-btn"><i class="fas fa-save"></i> Update</button>
                <button type="button" onclick="closeModal()" class="sima-btn sima-btn--outline">Batal</button>
            </div>
        </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/@flasher/flasher@1.0/dist/flasher.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM loaded'); // Debug
    // console.log('window.flasher exists:', !!window.flasher); // Seharusnya true
    const tbody = document.getElementById('usersTable');
    const searchInput = document.getElementById('searchInput');
    const sortInput = document.getElementById('sortInput');
    const modal = document.getElementById('userModal');
    const editModal = document.getElementById('userEdit');
    const openBtn = document.getElementById('openAddUserModal');
    const form = document.getElementById('userForm');
    const editForm = document.getElementById('userEditForm');
    console.log('tbody exists:', !!tbody);
    console.log('searchInput exists:', !!searchInput);

    // if (typeof flasher !== 'undefined') {
    //     flasher.success('Flasher siap dari CDN!');
    // }
    /* =========================
       USERS TABLE
    ==========================*/
    let currentPage = 1;

    const jurusanMap = {
        @foreach($jurusan as $j)
            {{ $j->id }}: "{{ $j->namaJurusan }}",
        @endforeach
    };

    function renderEmpty(message = 'No users found') {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" style="padding:24px;text-align:center;color:#94a3b8;">
                    ${message}
                </td>
            </tr>
        `;
    }

    function renderUsers(users) {
        console.log('Rendering users:', users); // Debug

        tbody.innerHTML = '';

        if (!Array.isArray(users) || users.length === 0) {
            renderEmpty();
            return;
        }


        users.forEach(user => {
            const namaJurusan = user.jurusan_id
            ? (jurusanMap[user.jurusan_id] ?? '-')
            : '-';

            tbody.innerHTML += `
                <tr style="border-bottom:1px solid #334155;">
                    <td style="padding:18px 24px;">${user.id ?? '-'}</td>

                    <td style="padding:18px 24px;">
                        <span style="">
                            ${user.role ?? '-'}
                        </span>
                    </td>

                    <td style="padding:18px 24px;">
                        ${user.email ?? '-'}
                    </td>
                     <td style="padding:18px 24px;">
                        ${namaJurusan ?? '-'}
                    </td>
                    <td style="padding:18px 24px;">
                        <span style="
                            background:${user.status === 'active' ? 'rgba(34,197,94,0.15)' : 'rgba(234,179,8,0.15)'};
                            color:${user.status === 'active' ? '#22c55e' : '#eab308'};
                            padding:6px 12px;
                            border-radius:999px;
                            font-size:12px;
                        ">
                            ${user.status ?? '-'}
                        </span>
                    </td>
                    <td>
                        <button onclick="editUser(${user.id})" class="sima-btn sima-btn--blue">
                         <i class="fa-solid fa-pen"></i>    Edit
                        </button>
                        <button onclick="deleteUser(${user.id})" class="sima-btn sima-btn--danger">
                            <i class="fa-solid fa-trash"></i> Delete
                        </button>                        
                    </td>
                </tr>
            `;
        });
    }

    function extractUsers(response) {
        // Jika response adalah array, gunakan langsung
        if (Array.isArray(response)) {
            return response;
        }
        // Jika response punya property data yang berupa array
        if (response?.data && Array.isArray(response.data)) {
            return response.data;
        }
        // Jika response punya property users
        if (response?.users && Array.isArray(response.users)) {
            return response.users;
        }
        // Jika response adalah object dengan data di dalamnya
        if (response && typeof response === 'object') {
            // Coba cari array di dalam response
            for (let key in response) {
                if (Array.isArray(response[key])) {
                    return response[key];
                }
            }
        }
        return [];
    }

    function loadUsers(search = '', sort = '', page = 1) {
        currentPage = page;
        renderEmpty('Loading...');

        let url = "{{ route('kln.users.data') }}";
        let params = new URLSearchParams();

        if (search) params.append('email', search);
        if (sort)   params.append('sort', sort);
        if (page > 1) params.append('page', page);

        if (params.toString()) url += '?' + params.toString();

        fetch(url)
            .then(res => res.json())
            .then(response => {
                if (response.flash) showFlasherNotification(response.flash);

                if (response.success) {
                    renderUsers(extractUsers(response.data));
                    renderPagination(response.pagination);
                } else {
                    renderEmpty(response.message || 'Failed to load data');
                }
            })
            .catch(error => {
                console.error('Error loading users:', error);
                renderEmpty('Failed to load data: ' + error.message);
            });
    }

    function renderPagination(pagination) {
        const container = document.getElementById('paginationContainer');
        if (!pagination || pagination.last_page <= 1) {
            container.innerHTML = '';
            return;
        }

        const { current_page, last_page, total, per_page } = pagination;
        const from = (current_page - 1) * per_page + 1;
        const to   = Math.min(current_page * per_page, total);

        const baseBtn = 'padding:5px 11px;border-radius:7px;border:1px solid var(--c-border);font-size:13px;font-weight:500;cursor:pointer;';

        function makeBtn(label, page, isActive, isDisabled) {
            const btn = document.createElement('button');
            btn.innerHTML = label;
            btn.style.cssText = baseBtn +
                (isActive  ? 'background:var(--c-accent);color:#fff;cursor:default;' : 'background:transparent;color:var(--c-text-1);') +
                (isDisabled ? 'opacity:.4;cursor:default;' : '');
            if (!isActive && !isDisabled) {
                btn.addEventListener('click', () => goToPage(page));
            }
            return btn;
        }

        container.innerHTML = '';

        const info = document.createElement('div');
        info.style.cssText = 'font-size:12px;color:var(--c-text-3);';
        info.textContent = `Menampilkan ${from}–${to} dari ${total} users`;
        container.appendChild(info);

        const nav = document.createElement('div');
        nav.style.cssText = 'display:flex;gap:4px;align-items:center;';

        nav.appendChild(makeBtn('<i class="fas fa-angle-double-left"></i>', 1,           false, current_page === 1));
        nav.appendChild(makeBtn('<i class="fas fa-angle-left"></i>',        current_page - 1, false, current_page === 1));

        for (let p = Math.max(1, current_page - 2); p <= Math.min(last_page, current_page + 2); p++) {
            nav.appendChild(makeBtn(p, p, p === current_page, false));
        }

        nav.appendChild(makeBtn('<i class="fas fa-angle-right"></i>',        current_page + 1, false, current_page === last_page));
        nav.appendChild(makeBtn('<i class="fas fa-angle-double-right"></i>', last_page,        false, current_page === last_page));

        container.appendChild(nav);
    }

    function goToPage(page) {
        const search = searchInput ? searchInput.value : '';
        const sort   = sortInput   ? sortInput.value   : '';
        loadUsers(search, sort, page);
    }

    // Fungsi untuk menampilkan notifikasi menggunakan PHPFlasher dari JavaScript
    function showFlasherNotification(flash) {
        // PHPFlasher biasanya menyediakan JavaScript API
        if (flasher) {
            // Cek apakah PHPFlasher punya JavaScript counterpart
            switch(flash.type) {
                case 'success':
                    flasher.success(flash.message, {
                        theme: flash.theme,
                        timeout: flash.timeout
                    });
                    break;
                case 'error':
                    flasher.error(flash.message, {
                        theme: flash.theme,
                        timeout: flash.timeout
                    });
                    break;
                case 'warning':
                    flasher.warning(flash.message, {
                        theme: flash.theme,
                        timeout: flash.timeout
                    });
                    break;
                default:
                    flasher.info(flash.message, {
                        theme: flash.theme,
                        timeout: flash.timeout
                    });
            }
        } else {
            // Fallback: buat notifikasi manual
            alert('error: ' + flash.message);
        }
    }

    /* =========================
       Search And Sorting Table
    ==========================*/
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            clearTimeout(searchTimeout);
            const searchValue = this.value;
            const sortValue = sortInput ? sortInput.value : '';
            searchTimeout = setTimeout(() => {
                loadUsers(searchValue, sortValue, 1); // reset ke page 1
            }, 500);
        });
    }

    if (sortInput) {
        sortInput.addEventListener('change', function() {
            const searchValue = searchInput ? searchInput.value : '';
            loadUsers(searchValue, this.value, 1); // reset ke page 1
        });
    }

    window.sortBy = function(field) {
        const currentSort = sortInput.value;
        let newSort = currentSort === field + '_asc' ? field + '_desc' : field + '_asc';
        sortInput.value = newSort;
        const searchValue = searchInput ? searchInput.value : '';
        loadUsers(searchValue, newSort, 1);
    }

    /* =========================
       MODAL CONTROL
    ==========================*/

    openBtn.addEventListener('click', () => {
        modal.style.display = 'flex';
    });

    window.closeModal = function() {
        if (modal) modal.style.display = 'none';
        if (editModal) editModal.style.display = 'none';
    }

    window.handleRoleChange = function() {
        const role = document.getElementById('roleSelect').value;

        document.getElementById('mahasiswaSection').style.display =
            role === 'mahasiswa' ? 'block' : 'none';

        document.getElementById('dosenSection').style.display =
            role === 'dosen' ? 'block' : 'none';
    }

    /* =========================
       EDIT FORM CONTROL
    ==========================*/
    window.handleEditRoleChange = function() {
        const role = document.getElementById('editRoleSelect').value;

        if(!role){
            return alert('role tidak berhasil di load')
        }
        
        document.getElementById('editMahasiswaSection').style.display =
            role === 'mahasiswa' ? 'block' : 'none';
  
            
        document.getElementById('editDosenSection').style.display =
            role === 'dosen' ? 'block' : 'none';
  

        
    }
    /* ====================================
       VALIDATION INPUT FORM
    =======================================*/

    function validateForm(data, isEdit = false) {
        const role = data.role;

        if (!role) { alert('Role harus dipilih'); return false; }
        if (!data.email || !data.email.includes('@')) { alert('Email tidak valid'); return false; }

        // Password: opsional di create (akun OTP), opsional di edit (berarti tidak diubah)
        if (data.password && data.password.length < 6) {
            alert('Password minimal 6 karakter');
            return false;
        }

        if (role === 'mahasiswa') {
            // NPM boleh kosong — server akan auto-generate
            if (!data.mahasiswa?.nama) { alert('Nama mahasiswa harus diisi'); return false; }
            if (!data.jurusan_id) { alert('Jurusan harus dipilih'); return false; }
        }

        if (role === 'dosen') {
            if (!data.dosen?.nama) { alert('Nama dosen harus diisi'); return false; }
            if (!data.jurusan_id) { alert('Jurusan harus dipilih'); return false; }
        }

        return true;
    }

    /* =========================
       GENERATE NPM
    ==========================*/
    window.generateNpm = function() {
        const jurusanId = document.getElementById('jurusan_id').value;
        if (!jurusanId) { alert('Pilih jurusan terlebih dahulu'); return; }

        const btn = document.getElementById('btnGenerateNpm');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch(`{{ route('kln.users.generate-npm') }}?jurusan_id=${jurusanId}`)
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    document.getElementById('npmInput').value = res.npm;
                } else {
                    alert(res.message || 'Gagal generate NPM');
                }
            })
            .catch(err => alert('Error: ' + err.message))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-dice"></i> Generate';
            });
    }

    /* ====================================
       USER DATA BUILDER, SEPARATING ROLE
    =======================================*/

    function buildUserData(formData) {
        const role = formData.get('role');
        const data = {
            role: role,
            email: formData.get('email'),
            password: formData.get('password'),
            status: formData.get('status'),
            jurusan_id: formData.get('jurusan_id')
        };
        
        // Tambah jurusan_id hanya untuk mahasiswa/dosen
        if (role === 'mahasiswa' || role === 'dosen') {
            data.jurusan_id = formData.get('jurusan_id');
        }
        
        // Data spesifik role
        if (role === 'mahasiswa') {
            data.mahasiswa = {
                npm:            formData.get('mahasiswa[npm]'),
                nama:           formData.get('mahasiswa[nama]'),
                tipeMahasiswa:  formData.get('mahasiswa[tipeMahasiswa]'),
            };
        }
        
        if (role === 'dosen') {
            data.dosen = {
                nama: formData.get('dosen[nama]'),
                nidn: formData.get('dosen[nidn]'),
                kodeDos: formData.get('dosen[kodeDos]')
            };
        }
        
        return data;
    }

    /* =========================
       SUBMIT USER
    ==========================*/
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const errDiv = document.getElementById('createErr');
        errDiv.style.display = 'none';

        const formData = new FormData(form);
        const data = buildUserData(formData);
        if (!validateForm(data)) return;

        const btn = form.querySelector('[type=submit]'); btn.disabled = true;

        fetch("{{ route('kln.users.store') }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Content-Type": "application/json", "Accept": "application/json" },
            body: JSON.stringify(data)
        })
        .then(async res => {
            const text = await res.text();
            try { return JSON.parse(text); } catch { throw new Error(`HTTP ${res.status}`); }
        })
        .then(response => {
            if (response.success) {
                modal.style.display = 'none';
                if (response.flash) showFlasherNotification(response.flash);
                form.reset();
                loadUsers();
            } else {
                let msg = response.message || 'Validasi gagal';
                if (response.errors) {
                    msg += ': ' + Object.values(response.errors).flat().join(', ');
                }
                errDiv.textContent = msg;
                errDiv.style.display = 'block';
            }
        })
        .catch(error => {
            errDiv.textContent = 'Server error: ' + error.message;
            errDiv.style.display = 'block';
        })
        .finally(() => { btn.disabled = false; });
    });

    /* =========================
       EDIT USER
    ==========================*/
    
    window.editUser = function(userId) {
        // const url = userShowRoute + userId;
        console.log('Editing user with ID:', userId);
        const url = `/kln/users/${userId}`;
        console.log('Fetching from URL:', url);
        // Fetch user data
        fetch(url, {
            method:"GET",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            }
        })
        .then(res => {
                console.log('Response status:', res.status);
                if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        })
        .then(response => {
            console.log('User data received:', response); // LIHAT INI DI CONSOLE
            const user = Array.isArray(response) ? response[0] : response;
        
            if (!user) {
                throw new Error('User data is empty');
            }
            // Populate form
            document.getElementById('editUserId').value = user.id;
            document.getElementById('editRoleSelect').value = user.role;
            document.getElementById('editEmail').value = user.email;
            document.getElementById('editPassword').value = ''; // Kosongkan password
            document.getElementById('editStatus').value = user.status;
            document.getElementById('editJurusanId').value = user.jurusan_id || '';
            
            // Handle role-specific fields
            if (user.role === 'mahasiswa' && user.mahasiswa) {
                document.getElementById('editMahasiswaNpm').value   = user.mahasiswa.npm  || '';
                document.getElementById('editMahasiswaNama').value  = user.mahasiswa.nama || '';
                document.getElementById('editMahasiswaTipe').value  = user.mahasiswa.tipeMahasiswa || '';
                document.getElementById('editMahasiswaSection').style.display = 'block';
            } else {
                document.getElementById('editMahasiswaSection').style.display = 'none';
            }
            
            if (user.role === 'dosen' && user.dosen) {
                document.getElementById('editDosenNama').value = user.dosen.nama || '';
                document.getElementById('editDosenNidn').value = user.dosen.nidn || '';
                document.getElementById('editDosenKode').value = user.dosen.kodeDos || '';
                document.getElementById('editDosenSection').style.display = 'block';
            } else {
                document.getElementById('editDosenSection').style.display = 'none';
            }
            
            // Show modal
            editModal.style.display = 'flex';
        })
        .catch(error => {
            console.error('Error fetching user:', error);
            alert('Gagal mengambil data user');
        });
    }

    // Handle Edit Form Submit
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const errDiv = document.getElementById('editErr');
        errDiv.style.display = 'none';

        const userId = document.getElementById('editUserId').value;
        const formData = new FormData(editForm);
        const data = buildUserData(formData, true);
        const url = `/kln/users/${userId}`;

        if (!validateForm(data, true)) return;

        const btn = editForm.querySelector('[type=submit]'); btn.disabled = true;

        fetch(url, {
            method: "PATCH",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Content-Type": "application/json", "Accept": "application/json" },
            body: JSON.stringify(data)
        })
        .then(async res => {
            const text = await res.text();
            try { return JSON.parse(text); } catch { throw new Error(`HTTP ${res.status}`); }
        })
        .then(response => {
            if (response.success) {
                editModal.style.display = 'none';
                if (response.flash) showFlasherNotification(response.flash);
                editForm.reset();
                loadUsers();
            } else {
                let msg = response.message || 'Update gagal';
                if (response.errors) {
                    msg += ': ' + Object.values(response.errors).flat().join(', ');
                }
                errDiv.textContent = msg;
                errDiv.style.display = 'block';
            }
        })
        .catch(error => {
            errDiv.textContent = 'Server error: ' + error.message;
            errDiv.style.display = 'block';
        })
        .finally(() => { btn.disabled = false; });
    });

    /* =========================
       DELETE USER
    ==========================*/

    window.deleteUser = function(userId) {
        if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
            fetch(`/kln/users/${userId}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                }
            })
            .then(async res => {
                if (!res.ok) {
                    const text = await res.text();
                    try {
                        return JSON.parse(text);
                    } catch {
                        throw new Error(`HTTP ${res.status}`);
                    }
                }
                return res.json();
            })
            .then(response => {
                if (response.success) {
                    // Tampilkan flash message dari response
                    if (response.flash) {
                        showFlasherNotification(response.flash);
                    }
                    if (response.success) {
                    renderUsers(extractUsers(response.data));
                    }// Debounce 500ms
                    loadUsers();

                } else {
                    if (response.flash) {
                        showFlasherNotification(response.flash);
                    }else{
                        alert('Delete Failed')
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Server error: " + error.message);
            });
        }
    }

    loadUsers();
});
</script>
@endsection


</x-app-layout>