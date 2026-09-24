@extends('layouts.sima')

@section('page_title',   'Create Lecturer')
@section('page_section', 'JURUSAN')
@section('page_subtitle','Add a new lecturer')

@section('main_content')

<div class="mb-3">
    <a href="{{ route('jurusan.dosen.page') }}" class="sima-btn sima-btn--outline sima-btn--sm">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="sima-card">
    <div style="padding:20px 24px;border-bottom:1px solid var(--c-border);">
        <h5 style="font-weight:700;font-family:var(--f-display);margin:0;">Add New Lecturer</h5>
        <div style="font-size:13px;color:var(--c-text-3);margin-top:2px;">
            Fill in the details below to register a new lecturer.
        </div>
    </div>

    <div style="padding:24px;">
        <form id="formTambah">
            <div style="display:flex;flex-direction:column;gap:22px;">

                {{-- Nama --}}
                <div>
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                        Lecturer Name <span style="color:var(--c-red)">*</span>
                    </label>
                    <input type="text" name="nama" class="sima-input mt-1"
                           placeholder="Full lecturer name" required>
                </div>

                {{-- NIDN --}}
                <div>
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                        NIDN
                    </label>
                    <input type="text" name="nidn" class="sima-input mt-1"
                           placeholder="National Lecturer Identification Number">
                </div>

                {{-- Kode Dos --}}
                <div>
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                        Lecturer Code
                    </label>
                    <input type="text" name="kodeDos" class="sima-input mt-1"
                           placeholder="Short lecturer code">
                </div>

                {{-- User Account --}}
                <div>
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                        User Account <span style="color:var(--c-red)">*</span>
                    </label>
                    <select name="user_id" id="selectUser" class="sima-input mt-1" required>
                        <option value="">— Loading accounts... —</option>
                    </select>
                    <div style="font-size:11px;color:var(--c-text-3);margin-top:4px;">Select a user account with Lecturer role</div>
                </div>

                <div id="tambahErr" style="display:none;font-size:13px;color:var(--c-red);padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid var(--c-red);border-radius:10px;"></div>

                {{-- Buttons --}}
                <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:12px;border-top:1px solid var(--c-border);">
                    <a href="{{ route('jurusan.dosen.page') }}" class="sima-btn sima-btn--outline">Cancel</a>
                    <button type="submit" class="sima-btn sima-btn--accent">
                        <i class="fas fa-plus me-1"></i> Save Lecturer
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
fetch('/jurusan/users/data?role=dosen', { headers: { Accept: 'application/json' } })
    .then(r => r.json())
    .then(users => {
        const sel = document.getElementById('selectUser');
        sel.innerHTML = '<option value="">— Select Account —</option>';
        users.forEach(u => {
            sel.innerHTML += `<option value="${u.id}">${u.email}</option>`;
        });
    });

document.getElementById('formTambah').addEventListener('submit', function(e) {
    e.preventDefault();
    const errDiv = document.getElementById('tambahErr');
    errDiv.style.display = 'none';

    const data = Object.fromEntries(new FormData(this));

    const btn = this.querySelector('[type=submit]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

    fetch('{{ route("jurusan.dosen.store") }}', {
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
            window.location.href = '{{ route("jurusan.dosen.page") }}?created=1';
        } else {
            let msg = response.message || 'Validation failed';
            if (response.errors) {
                msg += ': ' + Object.values(response.errors).flat().join(', ');
            }
            errDiv.textContent = msg;
            errDiv.style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus me-1"></i> Save Lecturer';
        }
    })
    .catch(error => {
        errDiv.textContent = 'Server error: ' + error.message;
        errDiv.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus me-1"></i> Save Lecturer';
    });
});
</script>
@endpush
