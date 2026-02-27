<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-white">
            Users KLN
        </h2>
    </x-slot>

    <div class="p-6">

        <div class="bg-slate-900/80 backdrop-blur-xl rounded-2xl shadow-2xl border border-slate-700">

            <!-- Header -->
            <div style="display:flex; justify-content:space-between; align-items:center; padding:20px 24px; border-bottom:1px solid #334155;">
                <h3 style="color:white; font-size:18px; font-weight:600;">
                    List Users
                </h3>

                <div style="display:flex; gap:12px;">
                    <input id="searchInput"
                        type="text"
                        placeholder="Search email..."
                        style="padding:10px 16px; border-radius:12px; background:#1e293b; border:1px solid #334155; color:white; outline:none;">

                    <button id="openAddUserModal"
                        style="padding:10px 18px; background:#6366f1; color:white; border-radius:12px; border:none; cursor:pointer;">
                        + Add User
                    </button>
                </div>
            </div>
            <!-- Table -->
            <div class="overflow-x-auto">
                <table style="width:100%; border-collapse: collapse; font-size:14px; color:#e2e8f0;">
                    
                    <thead style="background-color:#1e293b; text-transform:uppercase; font-size:12px; letter-spacing:1px; color:#94a3b8;">
                        <tr>
                            <th style="padding:16px 24px; text-align:left;">ID</th>
                            <th style="padding:16px 24px; text-align:left;">ROLE</th>
                            <th style="padding:16px 24px; text-align:left;">EMAIL</th>
                            <th style="padding:16px 24px; text-align:left;">STATUS</th>
                        </tr>
                    </thead>

                    <tbody id="usersTable">
                        <tr>
                            <td colspan="4" style="padding:24px; text-align:center; color:#94a3b8;">
                                Loading data...
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>
        </div>

    </div>

    
<div id="userModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.7); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center;">
    
    <div style="background:#0f172a; width:600px; max-height:90vh; overflow:auto; padding:30px; border-radius:20px; box-shadow:0 20px 60px rgba(0,0,0,.5);">

        <h2 style="color:white; font-size:20px; margin-bottom:20px;">Create New User</h2>

        <form id="userForm">

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
                <label style="color:#94a3b8;">Jurusan ID</label>
                <input type="number" name="jurusan_id"
                    style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
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
                    style="background:#475569; color:white; padding:10px 20px; border-radius:10px;">
                    Cancel
                </button>

                <button type="submit" 
                    style="background:#6366f1; color:white; padding:10px 20px; border-radius:10px;">
                    Save
                </button>
            </div>

        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const tbody = document.getElementById('usersTable');
    const searchInput = document.getElementById('searchInput');
    const modal = document.getElementById('userModal');
    const openBtn = document.getElementById('openAddUserModal');
    const form = document.getElementById('userForm');

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
                        <span style="background:rgba(99,102,241,0.15);color:#818cf8;padding:6px 12px;border-radius:999px;font-size:12px;">
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
                </tr>
            `;
        });
    }

    function extractUsers(response) {
        if (response?.data?.data) return response.data.data;
        if (response?.data) return response.data;
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
       SUBMIT USER
    ==========================*/

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);

        fetch("{{ route('kln.users.store') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: formData
        })
        .then(res => res.json())
        .then(response => {

            if (response.success) {
                modal.style.display = 'none';
                form.reset();
                loadUsers();
            } else {
                alert(response.message || "Validation failed");
                console.log(response);
            }

        })
        .catch(error => {
            console.error(error);
            alert("Server error");
        });
    });

});
</script>
</x-app-layout>