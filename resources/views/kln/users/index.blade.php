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
                    <a href="{{ route('kln.users.create') }}" class="sima-btn sima-btn--blue">
                        + Add User
                    </a>
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

    


<script src="https://cdn.jsdelivr.net/npm/@flasher/flasher@1.0/dist/flasher.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('usersTable');
    console.log('tbody exists:', !!tbody);

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
                <td colspan="6" style="padding:24px;text-align:center;color:#94a3b8;">
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
        loadUsers(search, '', page);
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
       SEARCH AND SORTING
    ==========================*/
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                loadUsers(this.value, 1);
            }, 500);
        });
    }

    window.sortBy = function(field) {
        const currentSort = window._currentSort || '';
        let newSort = currentSort === field + '_asc' ? field + '_desc' : field + '_asc';
        window._currentSort = newSort;
        loadUsers(searchInput ? searchInput.value : '', newSort, 1);
    }

    window.editUser = function(userId) {
        window.location.href = '/kln/users/' + userId + '/edit';
    }

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

    // Toast notifications on redirect
    const params = new URLSearchParams(window.location.search);
    if (params.has('created') || params.has('updated')) {
        const msg = params.has('created') ? 'User created successfully.' : 'User updated successfully.';
        const toast = document.createElement('div');
        toast.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;padding:14px 20px;background:rgba(5,150,105,.95);color:#fff;border-radius:10px;font-size:13px;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,.2);';
        toast.innerHTML = '<i class="fas fa-check-circle me-2"></i> ' + msg;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3500);
        history.replaceState(null, '', window.location.pathname);
    }
});
</script>
@endsection


</x-app-layout>