<x-app-layout>
    @section('page_title', 'Pengumuman KLN')
    @section('page_subtitle', 'Page pengaturan pengumuman')

    @section('main_content')
    <div class="sima-card ">
        <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Pengumuman List</h5>
                    <div class="sima-card__subtitle">kumpulan pengumuman yang diupload oleh admin KLN</div>
                </div>
                <div style="display:flex; gap:12px;">
                    <input id="searchInput"
                        type="text"
                        placeholder="Search email..."
                        class="sima-input">

                    <button id="openAddAnnouncementModal" class="sima-btn sima-btn--blue sima-btn--full"
                        >
                        + Add Announcement
                    </button>
                </div>
        </div>
        <div class="overflow-x-auto">
            <!-- Table -->
                <table class="sima-table" >
                    
                    <thead>
                        <tr>
                            <th style="padding:16px 24px; text-align:left;">ID</th>
                            <th style="padding:16px 24px; text-align:left;">SUBJECT</th>
                            <th style="padding:16px 24px; text-align:left;">MESSAGE</th>
                            <th style="padding:16px 24px; text-align:left;">STATUS</th>
                            <th style="padding:16px 24px; text-align:left;">CREATED AT</th>
                            <th style="padding:16px 24px; text-align:left;"></th>
                        </tr>
                    </thead>

                    <tbody id="announcementsTable">
                    </tbody>
                </table>
        </div>

    </div>
    <div id="announceModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.7); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:#0f172a; width:600px; max-height:90vh; overflow:auto; padding:30px; border-radius:20px; box-shadow:0 20px 60px rgba(0,0,0,.5);">

            <h2 style="color:white; font-size:20px; margin-bottom:20px;">Create New Announcement</h2>

            <form id="announceForm">
                <div style="margin-bottom:15px;">
                    <label style="color:#94a3b8;">Subject</label>
                    <input type="text" name="subject" style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="color:#94a3b8;">Message</label>
                    <input type="text" name="message"
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
                    <label style="color:#94a3b8;">Send To</label>
                    <select name="jurusan_id" id="jurusan_id" onchange="handleRoleChange()" 
                        style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;">
                        {{-- <option value="">Select </option>
                        @foreach ( $jurusan as $j)
                            <option value="{{ $j->id }}">{{ $j->namaJurusan }}</option>
                        @endforeach --}}
                    </select>
                    {{-- <input type="number" name="jurusan_id"
                        style="width:100%; padding:10px; background:#1e293b; color:white; border-radius:10px;"> --}}
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
    <script src="https://cdn.jsdelivr.net/npm/@flasher/flasher@1.0/dist/flasher.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        console.log('DOM loaded'); // Debug

        const tbody = document.getElementById('announcementsTable');
        const searchInput = document.getElementById('searchInput');
        const modal = document.getElementById('announceModal');
        const editModal = document.getElementById('announceEdit');
        const openBtn = document.getElementById('openAddAnnouncementModal');
        const form = document.getElementById('announceForm');
        const editForm = document.getElementById('announceEditForm');
        
        console.log('tbody exists:', !!tbody);
        console.log('searchInput exists:', !!searchInput);

        /* =========================
        ANNOUNCEMENT TABLE
        ==========================*/

        function renderEmpty(message = 'No announcements found') {
            if (!tbody) return;
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" style="padding:24px;text-align:center;color:#94a3b8;">
                        ${message}
                    </td>
                </tr>
            `;
        }

        function renderAnnouncements(announcements) {
            console.log('Rendering announcements:', announcements);

            if (!tbody) return;
            tbody.innerHTML = '';

            if (!Array.isArray(announcements) || announcements.length === 0) {
                renderEmpty();
                return;
            }

            announcements.forEach(announcement => {
                // Format tanggal
                const createdAt = announcement.created_at 
                    ? new Date(announcement.created_at).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })
                    : '-';

                tbody.innerHTML += `
                    <tr style="border-bottom:1px solid #334155;">
                        <td style="padding:18px 24px;">${announcement.id ?? '-'}</td>

                        <td style="padding:18px 24px;">
                            <div style="font-weight:500; margin-bottom:4px;">${announcement.subject ?? '-'}</div>
                            <div style="font-size:12px; color:#94a3b8;">${announcement.excerpt ?? ''}</div>
                        </td>

                        <td style="padding:18px 24px;">
                            ${announcement.message ?? '-'}
                        </td>

                        <td style="padding:18px 24px;">
                            <span style="
                                background:${announcement.status === 'published' ? 'rgba(34,197,94,0.15)' : 'rgba(234,179,8,0.15)'};
                                color:${announcement.status === 'published' ? '#22c55e' : '#eab308'};
                                padding:6px 12px;
                                border-radius:999px;
                                font-size:12px;
                            ">
                                ${announcement.status ?? 'draft'}
                            </span>
                        </td>

                        <td style="padding:18px 24px; font-size:13px; color:#94a3b8;">
                            ${createdAt}
                        </td>

                        <td>
                            <button onclick="editAnnouncement(${announcement.id})" class="sima-btn sima-btn--blue">
                                <i class="fa-solid fa-pen"></i> Edit
                            </button>
                            <button onclick="deleteAnnouncement(${announcement.id})" class="sima-btn sima-btn--danger">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>                        
                        </td>
                    </tr>
                `;
            });
        }

        function extractAnnouncements(response) {
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
            // Jika response punya property announcements
            if (response?.announcements && Array.isArray(response.announcements)) {
                return response.announcements;
            }
            return [];
        }

        function loadAnnouncements(search = '') {
            renderEmpty('Loading...');

            // Gunakan URL yang benar sesuai route announcement.data
            const url = search 
                ? `{{ route('kln.announcement.data') }}?search=${encodeURIComponent(search)}`
                : "{{ route('kln.announcement.data') }}";
            
            fetch(url)
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                    return res.json();
                })
                .then(response => {
                    console.log('Response from server:', response);
                    
                    // Tampilkan flash message dari response jika ada
                    if (response.flash) {
                        showFlasherNotification(response.flash);
                    }
                    
                    renderAnnouncements(extractAnnouncements(response));
                })
                .catch(error => {
                    console.error('Error loading announcements:', error);
                    renderEmpty('Failed to load data: ' + error.message);
                    
                    showFlasherNotification({
                        type: 'error',
                        message: 'Failed to load data: ' + error.message,
                        theme: 'amazon',
                        timeout: 5000
                    });
                });
        }

        // Fungsi untuk menampilkan notifikasi menggunakan PHPFlasher
        function showFlasherNotification(flash) {
            if (typeof flasher !== 'undefined') {
                switch(flash.type) {
                    case 'success':
                        flasher.success(flash.message, {
                            theme: flash.theme || 'amazon',
                            timeout: flash.timeout || 5000
                        });
                        break;
                    case 'error':
                        flasher.error(flash.message, {
                            theme: flash.theme || 'amazon',
                            timeout: flash.timeout || 5000
                        });
                        break;
                    case 'warning':
                        flasher.warning(flash.message, {
                            theme: flash.theme || 'amazon',
                            timeout: flash.timeout || 5000
                        });
                        break;
                    default:
                        flasher.info(flash.message, {
                            theme: flash.theme || 'amazon',
                            timeout: flash.timeout || 5000
                        });
                }
            } else {
                // Fallback jika flasher tidak tersedia
                console.log('Notification:', flash.type, flash.message);
                alert(flash.type + ': ' + flash.message);
            }
        }

        /* =========================
        SEARCH ANNOUNCEMENT
        ==========================*/
        let searchTimeout;
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadAnnouncements(this.value);
                }, 500); // Debounce 500ms
            });
        }

        /* =========================
        MODAL CONTROL
        ==========================*/

        if (openBtn) {
            openBtn.addEventListener('click', () => {
                if (modal) modal.style.display = 'flex';
            });
        }

        window.closeModal = function() {
            if (modal) modal.style.display = 'none';
            if (editModal) editModal.style.display = 'none';
        }

        /* =========================
        FORM VALIDATION
        ==========================*/

        function validateAnnouncementForm(data) {
            if (!data.title || data.title.trim() === '') {
                showFlasherNotification({
                    type: 'error',
                    message: 'Title harus diisi',
                    theme: 'amazon',
                    timeout: 5000
                });
                return false;
            }
            
            if (!data.content || data.content.trim() === '') {
                showFlasherNotification({
                    type: 'error',
                    message: 'Content harus diisi',
                    theme: 'amazon',
                    timeout: 5000
                });
                return false;
            }
            
            return true;
        }

        /* =========================
        BUILD ANNOUNCEMENT DATA
        ==========================*/

        function buildAnnouncementData(formData) {
            return {
                title: formData.get('title'),
                content: formData.get('content'),
                excerpt: formData.get('excerpt') || null,
                status: formData.get('status') || 'draft',
                published_at: formData.get('published_at') || null
            };
        }

        /* =========================
        SUBMIT ANNOUNCEMENT
        ==========================*/
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(form);
                const data = buildAnnouncementData(formData);
                
                if (!validateAnnouncementForm(data)) {
                    return;
                }
                
                console.log('Data to send:', data);

                fetch("{{ route('kln.announcement.store') }}", {
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
                        if (modal) modal.style.display = 'none';
                        form.reset();
                        
                        if (response.flash) {
                            showFlasherNotification(response.flash);
                        }
                        
                        loadAnnouncements();
                    } else {
                        let errorMsg = response.message || "Validation failed";
                        if (response.errors) {
                            errorMsg += "\n\n" + Object.entries(response.errors)
                                .map(([field, errors]) => `${field}: ${errors.join(', ')}`)
                                .join('\n');
                        }
                        
                        showFlasherNotification({
                            type: 'error',
                            message: errorMsg,
                            theme: 'amazon',
                            timeout: 5000
                        });
                        
                        console.log(response.errors);
                    }
                })
                .catch(error => {
                    console.error('Fetch Error:', error);
                    
                    showFlasherNotification({
                        type: 'error',
                        message: "Server error: " + error.message,
                        theme: 'amazon',
                        timeout: 5000
                    });
                });
            });
        }

        /* =========================
        EDIT ANNOUNCEMENT
        ==========================*/
        
        window.editAnnouncement = function(announcementId) {
            console.log('Editing announcement with ID:', announcementId);
            const url = `/kln/announcement/${announcementId}`; // Sesuai route announcement.show
            
            fetch(url, {
                method: "GET",
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
                console.log('Announcement data received:', response);
                
                // Sesuaikan dengan struktur response dari controller
                const announcement = response.data || response;
            
                if (!announcement) {
                    throw new Error('Announcement data is empty');
                }
                
                // Populate form
                document.getElementById('editAnnouncementId').value = announcement.id;
                document.getElementById('editTitle').value = announcement.title || '';
                document.getElementById('editContent').value = announcement.content || '';
                document.getElementById('editExcerpt').value = announcement.excerpt || '';
                document.getElementById('editStatus').value = announcement.status || 'draft';
                
                if (document.getElementById('editPublishedAt')) {
                    document.getElementById('editPublishedAt').value = announcement.published_at || '';
                }
                
                // Show modal
                if (editModal) editModal.style.display = 'flex';
            })
            .catch(error => {
                console.error('Error fetching announcement:', error);
                
                showFlasherNotification({
                    type: 'error',
                    message: 'Gagal mengambil data announcement',
                    theme: 'amazon',
                    timeout: 5000
                });
            });
        }

        // Handle Edit Form Submit
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const announcementId = document.getElementById('editAnnouncementId').value;
                console.log('Announcement Id: ', announcementId);
                
                const formData = new FormData(editForm);
                const data = buildAnnouncementData(formData);
                const url = `/kln/announcement/update/${announcementId}`; // Sesuai route announcement.update
                
                if (!validateAnnouncementForm(data)) {
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
                    return res.json();
                })
                .then(response => {
                    if (response.success) {
                        if (editModal) editModal.style.display = 'none';
                        editForm.reset();
                        
                        if (response.flash) {
                            showFlasherNotification(response.flash);
                        }
                        
                        loadAnnouncements();
                    } else {
                        let errorMsg = response.message || "Update failed";
                        if (response.errors) {
                            errorMsg += "\n\n" + Object.entries(response.errors)
                                .map(([field, errors]) => `${field}: ${errors.join(', ')}`)
                                .join('\n');
                        }
                        
                        showFlasherNotification({
                            type: 'error',
                            message: errorMsg,
                            theme: 'amazon',
                            timeout: 5000
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    showFlasherNotification({
                        type: 'error',
                        message: "Server error: " + error.message,
                        theme: 'amazon',
                        timeout: 5000
                    });
                });
            });
        }

        /* =========================
        DELETE ANNOUNCEMENT
        ==========================*/

        window.deleteAnnouncement = function(announcementId) {
            if (confirm('Apakah Anda yakin ingin menghapus announcement ini?')) {
                fetch(`/kln/announcement/${announcementId}`, {
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
                        if (response.flash) {
                            showFlasherNotification(response.flash);
                        }
                        loadAnnouncements();
                    } else {
                        if (response.flash) {
                            showFlasherNotification(response.flash);
                        } else {
                            showFlasherNotification({
                                type: 'error',
                                message: response.message || 'Delete Failed',
                                theme: 'amazon',
                                timeout: 5000
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    showFlasherNotification({
                        type: 'error',
                        message: "Server error: " + error.message,
                        theme: 'amazon',
                        timeout: 5000
                    });
                });
            }
        }

        // Initial load
        loadAnnouncements();
    });
    </script>
    @endsection


</x-app-layout>