@extends('layouts.sima')

@section('page_title',    'Create User')
@section('page_section',  'USERS')
@section('page_subtitle', 'Add a new user account to the system')

@section('main_content')

<div class="mb-3">
    <a href="{{ route('kln.users.page') }}" class="sima-btn sima-btn--outline sima-btn--sm">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="sima-card">
    <div style="padding:20px 24px;border-bottom:1px solid var(--c-border);">
        <h5 style="font-weight:700;font-family:var(--f-display);margin:0;">Create New User</h5>
        <div style="font-size:13px;color:var(--c-text-3);margin-top:2px;">
            Fill in the details below to create a new user account.
        </div>
    </div>

    <div style="padding:24px;">
        <form id="createUserForm">
            <div style="display:flex;flex-direction:column;gap:22px;">

                {{-- Role + Status --}}
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                            Role <span style="color:var(--c-red)">*</span>
                        </label>
                        <select name="role" id="roleSelect" onchange="handleRoleChange()" class="sima-input mt-1" required>
                            <option value="">Select Role</option>
                            <option value="bipa">BIPA</option>
                            <option value="kln">KLN</option>
                            <option value="jurusan">Jurusan</option>
                            <option value="mahasiswa">Mahasiswa</option>
                            <option value="dosen">Dosen</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                            Status <span style="color:var(--c-red)">*</span>
                        </label>
                        <select name="status" class="sima-input mt-1" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                        Email <span style="color:var(--c-red)">*</span>
                    </label>
                    <input type="email" name="email" class="sima-input mt-1"
                           placeholder="email@institution.ac.id" required>
                </div>

                {{-- Password --}}
                <div>
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                        Password
                        <span style="font-weight:400;color:var(--c-text-3);font-size:11px;">(optional — leave empty for OTP login)</span>
                    </label>
                    <input type="password" name="password" class="sima-input mt-1"
                           placeholder="Min. 6 characters, or leave empty">
                </div>

                {{-- Jurusan --}}
                <div>
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                        Jurusan
                    </label>
                    <select name="jurusan_id" id="jurusan_id" onchange="handleRoleChange()" class="sima-input mt-1">
                        <option value="">Select Jurusan</option>
                        @foreach($jurusan as $j)
                            <option value="{{ $j->id }}">{{ $j->namaJurusan }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- MAHASISWA SECTION --}}
                <div id="mahasiswaSection" style="display:none;background:var(--c-bg-2);border-radius:12px;padding:16px;border:1px solid var(--c-border);">
                    <div style="font-size:12px;font-weight:700;color:var(--c-text-2);margin-bottom:14px;text-transform:uppercase;letter-spacing:.05em;">
                        <i class="fas fa-user-graduate" style="margin-right:4px;"></i> Student Data
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                NPM
                                <span style="font-weight:400;color:var(--c-text-3);font-size:11px;">(leave empty to auto-generate)</span>
                            </label>
                            <div style="display:flex;gap:8px;margin-top:4px;">
                                <input type="text" name="mahasiswa[npm]" id="npmInput" class="sima-input"
                                       placeholder="Auto-generate if empty">
                                <button type="button" id="btnGenerateNpm" onclick="generateNpm()"
                                    class="sima-btn sima-btn--outline sima-btn--sm" style="white-space:nowrap;">
                                    <i class="fas fa-dice"></i> Generate
                                </button>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                Student Name <span style="color:var(--c-red)">*</span>
                            </label>
                            <input type="text" name="mahasiswa[nama]" class="sima-input mt-1" placeholder="Full name" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                Student Type
                            </label>
                            <select name="mahasiswa[tipeMahasiswa]" class="sima-input mt-1">
                                <option value="">— Select Type —</option>
                                <option value="Beasiswa TIAS">Beasiswa TIAS</option>
                                <option value="Beasiswa KNB">Beasiswa KNB</option>
                                <option value="Beasiswa Gunadarma">Beasiswa Gunadarma</option>
                                <option value="Internasional Mandiri">Internasional Mandiri</option>
                                <option value="Short Course (3 Bulan)">Short Course (3 Bulan)</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- DOSEN SECTION --}}
                <div id="dosenSection" style="display:none;background:var(--c-bg-2);border-radius:12px;padding:16px;border:1px solid var(--c-border);">
                    <div style="font-size:12px;font-weight:700;color:var(--c-text-2);margin-bottom:14px;text-transform:uppercase;letter-spacing:.05em;">
                        <i class="fas fa-chalkboard-teacher" style="margin-right:4px;"></i> Lecturer Data
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                Lecturer Name <span style="color:var(--c-red)">*</span>
                            </label>
                            <input type="text" name="dosen[nama]" class="sima-input mt-1" placeholder="Full name" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                NIDN
                            </label>
                            <input type="text" name="dosen[nidn]" class="sima-input mt-1" placeholder="National Lecturer ID">
                        </div>
                        <div class="col-12 col-md-6">
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                Lecturer Code
                            </label>
                            <input type="text" name="dosen[kodeDos]" class="sima-input mt-1" placeholder="Lecturer code">
                        </div>
                    </div>
                </div>

                <div id="createErr" style="display:none;font-size:13px;color:var(--c-red);padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid var(--c-red);border-radius:10px;"></div>

                {{-- Buttons --}}
                <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:12px;border-top:1px solid var(--c-border);">
                    <a href="{{ route('kln.users.page') }}" class="sima-btn sima-btn--outline">Cancel</a>
                    <button type="submit" class="sima-btn sima-btn--accent">
                        <i class="fas fa-plus me-1"></i> Create User
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('page_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
function handleRoleChange() {
    const role = document.getElementById('roleSelect').value;
    document.getElementById('mahasiswaSection').style.display = role === 'mahasiswa' ? 'block' : 'none';
    document.getElementById('dosenSection').style.display = role === 'dosen' ? 'block' : 'none';
}

function generateNpm() {
    const jurusanId = document.getElementById('jurusan_id').value;
    if (!jurusanId) { alert('Please select a jurusan first'); return; }

    const btn = document.getElementById('btnGenerateNpm');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch('{{ route("kln.users.generate-npm") }}?jurusan_id=' + jurusanId)
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                document.getElementById('npmInput').value = res.npm;
            } else {
                alert(res.message || 'Failed to generate NPM');
            }
        })
        .catch(err => alert('Error: ' + err.message))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-dice"></i> Generate';
        });
}

function buildUserData(formData) {
    const role = formData.get('role');
    const data = {
        role: role,
        email: formData.get('email'),
        password: formData.get('password'),
        status: formData.get('status'),
        jurusan_id: formData.get('jurusan_id'),
    };
    if (role === 'mahasiswa') {
        data.mahasiswa = {
            npm: formData.get('mahasiswa[npm]'),
            nama: formData.get('mahasiswa[nama]'),
            tipeMahasiswa: formData.get('mahasiswa[tipeMahasiswa]'),
        };
    }
    if (role === 'dosen') {
        data.dosen = {
            nama: formData.get('dosen[nama]'),
            nidn: formData.get('dosen[nidn]'),
            kodeDos: formData.get('dosen[kodeDos]'),
        };
    }
    return data;
}

function validateForm(data) {
    if (!data.role) { alert('Role is required'); return false; }
    if (!data.email || !data.email.includes('@')) { alert('Invalid email'); return false; }
    if (data.password && data.password.length < 6) { alert('Password must be at least 6 characters'); return false; }
    if (data.role === 'mahasiswa') {
        if (!data.mahasiswa?.nama) { alert('Student name is required'); return false; }
        if (!data.jurusan_id) { alert('Jurusan is required for students'); return false; }
    }
    if (data.role === 'dosen') {
        if (!data.dosen?.nama) { alert('Lecturer name is required'); return false; }
        if (!data.jurusan_id) { alert('Jurusan is required for lecturers'); return false; }
    }
    return true;
}

document.getElementById('createUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const errDiv = document.getElementById('createErr');
    errDiv.style.display = 'none';

    const formData = new FormData(this);
    const data = buildUserData(formData);
    if (!validateForm(data)) return;

    const btn = this.querySelector('[type=submit]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Creating...';

    fetch('{{ route("kln.users.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify(data),
    })
    .then(async res => {
        const text = await res.text();
        try { return JSON.parse(text); } catch { throw new Error('HTTP ' + res.status); }
    })
    .then(response => {
        if (response.success) {
            window.location.href = '{{ route("kln.users.page") }}?created=1';
        } else {
            let msg = response.message || 'Validation failed';
            if (response.errors) {
                msg += ': ' + Object.values(response.errors).flat().join(', ');
            }
            errDiv.textContent = msg;
            errDiv.style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus me-1"></i> Create User';
        }
    })
    .catch(error => {
        errDiv.textContent = 'Server error: ' + error.message;
        errDiv.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus me-1"></i> Create User';
    });
});
</script>
@endpush
