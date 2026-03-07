document.addEventListener('DOMContentLoaded', function () {

    const tbody = document.getElementById('usersTable');
    const searchInput = document.getElementById('searchInput');
    const modal = document.getElementById('userModal');
    const editModal = document.getElementById('userEdit');
    const openBtn = document.getElementById('openAddUserModal');
    const form = document.getElementById('userForm');
    const editForm = document.getElementById('userEditForm');

    /* =========================
       USERS TABLE
    ==========================*/

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

        tbody.innerHTML = '';

        if (!Array.isArray(users) || users.length === 0) {
            renderEmpty();
            return;
        }

        users.forEach(user => {

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
                        <button onclick="editUser(${user.id})" style="padding:10px 18px; background:#6366f1; color:white; border-radius:12px; border:none; cursor:pointer;">
                         <i class="fa-solid fa-pen"></i>    Edit
                        </button>
                        <button onclick="deleteUser(${user.id})" style="padding:10px 18px; background:#991b1b; color:white; border-radius:12px; border:none; cursor:pointer;">
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
        // Jika response punya property data.data (pagination)
        if (response?.data?.data && Array.isArray(response.data.data)) {
            return response.data.data;
        }
        return [];
    }

    function loadUsers(search = '') {

        renderEmpty('Loading...');

        fetch(`{{ route('kln.users.data') }}?email=${encodeURIComponent(search)}`)
            .then(res => res.json())
            .then(response => {
                renderUsers(extractUsers(response));
            })
            .catch(() => renderEmpty('Failed to load data'));
    }

    loadUsers();

    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            loadUsers(this.value);
        });
    }

    /* =========================
       MODAL CONTROL
    ==========================*/

    openBtn.addEventListener('click', () => {
        modal.style.display = 'flex';
    });

    window.closeModal = function() {
        modal.style.display = 'none';
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

    openBtn.addEventListener('click', () => {
        modal.style.display = 'flex';
    });

    window.closeModal = function() {
        modal.style.display = 'none';
    }

    window.handleRoleChange = function() {
        const role = document.getElementById('roleSelect').value;

        document.getElementById('mahasiswaSection').style.display =
            role === 'mahasiswa' ? 'block' : 'none';

        document.getElementById('dosenSection').style.display =
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
        // Fetch user data
        fetch(`/kln/users/${userId}`, {
            method:"GET",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            }
        })
        .then(res => res.json())
        .then(user => {
            // Populate form
            document.getElementById('editUserId').value = user.id;
            document.getElementById('editRoleSelect').value = user.role;
            document.getElementById('editEmail').value = user.email;
            document.getElementById('editStatus').value = user.status;
            document.getElementById('editJurusanId').value = user.jurusan_id || '';
            
            // Handle role-specific fields
            if (user.role === 'mahasiswa' && user.mahasiswa) {
                document.getElementById('editMahasiswaNpm').value = user.mahasiswa.npm || '';
                document.getElementById('editMahasiswaNama').value = user.mahasiswa.nama || '';
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
        const formData = new FormData(editForm);
        const data = buildUserData(formData, true);
        
        if (!validateForm(data, true)) {
            return;
        }

        fetch(`/kln/users/${userId}`, {
            method: "PUT",
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
            return res.json();
        })
        .then(response => {
            if (response.success) {
                editModal.style.display = 'none';
                editForm.reset();
                loadUsers();
                alert('User berhasil diupdate!');
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
                    loadUsers();
                    alert('User berhasil dihapus!');
                } else {
                    alert(response.message || 'Gagal menghapus user');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Server error: " + error.message);
            });
        }
    }

});