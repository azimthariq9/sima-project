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

    </div>

    
<div id="userModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.7); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center;">
    
    <div style="background:#0f172a; width:600px; max-height:90vh; overflow:auto; padding:30px; border-radius:20px; box-shadow:0 20px 60px rgba(0,0,0,.5);">

        <h2 style="color:white; font-size:20px; margin-bottom:20px;">Create New User</h2>

        <form id="userForm">
            {{-- @csrf --}}
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">Role</label>
                <select name="role" id="roleSelect" onchange="handleRoleChange()" 
                    style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
                    <option value="">Select Role</option>
                    <option value="bipa">BIPA</option>
                    <option value="kln">KLN</option>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                </select>
            </div>

            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">Email</label>
                <input type="email" name="email"
                    style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
            </div>

            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">Password</label>
                <input type="password" name="password"
                    style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
            </div>

            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">Status</label>
                <select name="status"
                    style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">Jurusan</label>
                <select name="jurusan_id" id="jurusan_id" onchange="handleRoleChange()" 
                    style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
                    <option value="">Select Jurusan</option>
                    @foreach ( $jurusan as $j)
                        <option value="{{ $j->id }}">{{ $j->namaJurusan }}</option>
                    @endforeach
                </select>
                {{-- <input type="number" name="jurusan_id"
                    style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;"> --}}
            </div>

            <!-- MAHASISWA SECTION -->
            <div id="mahasiswaSection" style="display:none;">

                <div style="margin-bottom:15px;">
                    <label style="color:#94a3b8;">NPM</label>
                    <input type="text" name="mahasiswa[npm]"
                        style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="color:#94a3b8;">Nama Mahasiswa</label>
                    <input type="text" name="mahasiswa[nama]"
                        style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
                </div>

            </div>

            <!-- DOSEN SECTION -->
            <div id="dosenSection" style="display:none;">

                <div style="margin-bottom:15px;">
                    <label style="color:#94a3b8;">Nama Dosen</label>
                    <input type="text" name="dosen[nama]"
                        style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="color:#94a3b8;">NIDN</label>
                    <input type="text" name="dosen[nidn]"
                        style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="color:#94a3b8;">Kode Dosen</label>
                    <input type="text" name="dosen[kodeDos]"
                        style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
                </div>

            </div>

            <div style="display:flex; justify-content:space-between; margin-top:20px;">
                <button type="button" onclick="closeModal()" 
                    class="sima-btn sima-btn--gold">
                    Cancel
                </button>

                <button
                    class="sima-btn sima-btn--blue">
                    Save
                </button>
            </div>

        </form>
    </div>
</div>
<div id="userEdit" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.7); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#0f172a; width:600px; max-height:90vh; overflow:auto; padding:30px; border-radius:20px; box-shadow:0 20px 60px rgba(0,0,0,.5);">

        <h2 style="color:white; font-size:20px; margin-bottom:20px;">Edit User</h2>
        <form id="userEditForm">
            <input type="hidden" id="editUserId" name="id">
            
            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">Role</label>
                <select name="role" id="editRoleSelect" onchange="handleEditRoleChange()" 
                    style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
                    <option value="">Select Role</option>
                    <option value="bipa">BIPA</option>
                    <option value="kln">KLN</option>
                    <option value="jurusan">Jurusan</option>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                </select>
            </div>

            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">Email</label>
                <input type="email" name="email" id="editEmail"
                    style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
            </div>

            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">Password (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" id="editPassword"
                    style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
            </div>

            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">Status</label>
                <select name="status" id="editStatus"
                    style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div style="margin-bottom:15px;">
                <label style="color:#94a3b8;">Jurusan</label>
                <select name="jurusan_id" id="editJurusanId" onchange="handleEditRoleChange()"
                    style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
                    <option value="">Select Jurusan</option>
                    @foreach ( $jurusan as $j)
                        <option value="{{ $j->id }}">{{ $j->namaJurusan }}</option>
                    @endforeach
                </select>
            </div>

            <!-- MAHASISWA SECTION EDIT -->
            <div id="editMahasiswaSection" style="display:none;">
                <div style="margin-bottom:15px;">
                    <label style="color:#94a3b8;">NPM</label>
                    <input type="text" name="mahasiswa[npm]" id="editMahasiswaNpm"
                        style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
                </div>
                <div style="margin-bottom:15px;">
                    <label style="color:#94a3b8;">Nama Mahasiswa</label>
                    <input type="text" name="mahasiswa[nama]" id="editMahasiswaNama"
                        style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
                </div>
            </div>

            <!-- DOSEN SECTION EDIT -->
            <div id="editDosenSection" style="display:none;">
                <div style="margin-bottom:15px;">
                    <label style="color:#94a3b8;">Nama Dosen</label>
                    <input type="text" name="dosen[nama]" id="editDosenNama"
                        style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
                </div>
                <div style="margin-bottom:15px;">
                    <label style="color:#94a3b8;">NIDN</label>
                    <input type="text" name="dosen[nidn]" id="editDosenNidn"
                        style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
                </div>
                <div style="margin-bottom:15px;">
                    <label style="color:#94a3b8;">Kode Dosen</label>
                    <input type="text" name="dosen[kodeDos]" id="editDosenKode"
                        style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
                </div>
            </div>

            <div style="display:flex; justify-content:space-between; margin-top:20px;">
                <button type="button" onclick="closeModal()" 
                class="sima-btn sima-btn--gold">
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="sima-btn sima-btn--blue">
                    Update
                </button>
            </div>
        </form>
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

    function loadUsers(search = '', sort = '') {
        renderEmpty('Loading...');
        
        let url = "{{ route('kln.users.data') }}";
        let params = new URLSearchParams();
        
        if (search) {
            params.append('email', search);
        }
        
        if (sort) {
            params.append('sort', sort);
        }
        
        if (params.toString()) {
            url += '?' + params.toString();
        }

        fetch(url)
            .then(res => res.json())
            .then(response => {
                console.log('Response from server:', response);
                
                // Tampilkan flash message dari response
                if (response.flash) {
                    showFlasherNotification(response.flash);
                }
                
                if (response.success) {
                    renderUsers(extractUsers(response.data));
                } else {
                    renderEmpty(response.message || 'Failed to load data');
                }
            })
            .catch(error => {
                console.error('Error loading users:', error);
                renderEmpty('Failed to load data: ' + error.message);
                
                // Tampilkan error flash
                showFlasherNotification({
                    type: 'error',
                    message: 'Failed to load data: ' + error.message,
                    theme: 'amazon',
                    timeout: 5000
                });
            });
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
                loadUsers(searchValue, sortValue);
            }, 500); // Debounce 500ms
        });
    }

    // Event listener untuk sort
    if (sortInput) {
        sortInput.addEventListener('change', function() {
            const searchValue = searchInput ? searchInput.value : '';
            const sortValue = this.value;
            loadUsers(searchValue, sortValue);
        });
    }

    window.sortBy = function(field) {
        const currentSort = sortInput.value;
        let newSort = '';
        
        if (currentSort === field + '_asc') {
            newSort = field + '_desc';
        } else {
            newSort = field + '_asc';
        }
        
        sortInput.value = newSort;
        const searchValue = searchInput ? searchInput.value : '';
        loadUsers(searchValue, newSort);
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

    function validateForm(data) {
        const role = data.role;
        
        // Validasi umum
        if (!role) {
            alert('Role harus dipilih');
            return false;
        }
        
        if (!data.email || !data.email.includes('@')) {
            alert('Email tidak valid');
            return false;
        }
        
        if (!data.password || data.password.length < 6) {
            alert('Password minimal 6 karakter');
            return false;
        }
        
        // Validasi spesifik role
        if (role === 'mahasiswa') {
            if (!data.mahasiswa?.npm) {
                alert('NPM harus diisi');
                return false;
            }
            if (!data.mahasiswa?.nama) {
                alert('Nama mahasiswa harus diisi');
                return false;
            }
            if (!data.jurusan_id) {
                alert('Jurusan harus dipilih');
                return false;
            }
        }
        
        if (role === 'dosen') {
            if (!data.dosen?.nama) {
                alert('Nama dosen harus diisi');
                return false;
            }
            if (!data.dosen?.nidn) {
                alert('NIDN harus diisi');
                return false;
            }
            if (!data.jurusan_id) {
                alert('Jurusan harus dipilih');
                return false;
            }
        }
        
        return true;
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
                npm: formData.get('mahasiswa[npm]'),
                nama: formData.get('mahasiswa[nama]')
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

        const formData = new FormData(form);
        const data = buildUserData(formData);
        
        if (!validateForm(data)){
            return;
        }
        console.log('Filtered data to send:', data); // DEBUG

        fetch("{{ route('kln.users.store') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify(data)
        })
        .then(async res => {
            if (!res.ok) {
                const text = await res.text();
                console.log('Error response:', text);
                try {
                    return JSON.parse(text);
                } catch {
                    throw new Error(`HTTP ${res.status}: ${text.substring(0, 200)}`);
                }
            }
            return res.json();
        })
        .then(response => {
            if (response.success) {
                modal.style.display = 'none';
                // Tampilkan flash message dari response
                if (response.flash) {
                    showFlasherNotification(response.flash);
                }
                
                if (response.success) {
                    renderUsers(extractUsers(response.data));
                }// Debounce 500ms
                form.reset();
                loadUsers();
            } else {
                // Tampilkan error dengan lebih baik
                let errorMsg = response.message || "Validation failed";
                if (response.errors) {
                    errorMsg += "\n\n" + Object.entries(response.errors)
                        .map(([field, errors]) => `${field}: ${errors.join(', ')}`)
                        .join('\n');
                }
                alert(errorMsg);
                console.log(response.errors);
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            alert("Server error: " + error.message);
        });
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
                document.getElementById('editMahasiswaNpm').value = user.mahasiswa.npm || '';
                document.getElementById('editMahasiswaNama').value = user.mahasiswa.nama|| '';
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
        
        const userId = document.getElementById('editUserId').value;
        console.log('user Id: ', userId);
        const formData = new FormData(editForm);
        const data = buildUserData(formData, true);
        const url = `/kln/users/${userId}`;
        
        if (!validateForm(data, true)) {
            return;
        }

        fetch(url, {
            method: "PATCH",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify(data)
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
            console.log('response', res);
            return res.json();
        })
        .then(response => {
            if (response.success) {
                editModal.style.display = 'none';
                // Tampilkan flash message dari response
                if (response.flash) {
                    showFlasherNotification(response.flash);
                }
                if (response.success) {
                    renderUsers(extractUsers(response.data));
                }// Debounce 500ms

                editForm.reset();
                loadUsers();
            } else {
                let errorMsg = response.message || "Update failed";
                if (response.errors) {
                    errorMsg += "\n\n" + Object.entries(response.errors)
                        .map(([field, errors]) => `${field}: ${errors.join(', ')}`)
                        .join('\n');
                }
                alert(errorMsg);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Server error: " + error.message);
        });
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