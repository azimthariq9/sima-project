@extends('layouts.sima')

@section('page_title',    'Create Course')
@section('page_section',  'JURUSAN')
@section('page_subtitle', 'Add a new course')

@section('main_content')

<div class="mb-3">
    <a href="{{ route('jurusan.matakuliah.page') }}" class="sima-btn sima-btn--outline sima-btn--sm">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="sima-card">
    <div style="padding:20px 24px;border-bottom:1px solid var(--c-border);">
        <h5 style="font-weight:700;font-family:var(--f-display);margin:0;">Create New Course</h5>
        <div style="font-size:13px;color:var(--c-text-3);margin-top:2px;">
            Fill in the details below to add a new course.
        </div>
    </div>

    <div style="padding:24px;">
        <form id="createMkForm">
            <div style="display:flex;flex-direction:column;gap:22px;">

                {{-- Kode MK --}}
                <div>
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                        Course Code
                    </label>
                    <input type="text" name="kodeMk" class="sima-input mt-1"
                           placeholder="e.g., IT012236">
                </div>

                {{-- Nama MK --}}
                <div>
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                        Course Name <span style="color:var(--c-red)">*</span>
                    </label>
                    <input type="text" name="namaMk" class="sima-input mt-1"
                           placeholder="e.g., Data Structures" required>
                </div>

                {{-- SKS --}}
                <div>
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                        Credits
                    </label>
                    <input type="number" name="sks" class="sima-input mt-1"
                           min="1" max="6" placeholder="1–6">
                </div>

                {{-- Keterangan --}}
                <div>
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                        Description
                        <span style="font-weight:400;color:var(--c-text-3);font-size:11px;">(Max 5 characters)</span>
                    </label>
                    <input type="text" name="keterangan" class="sima-input mt-1"
                           maxlength="5" placeholder="Max 5 characters">
                </div>

                <div id="createErr" style="display:none;font-size:13px;color:var(--c-red);padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid var(--c-red);border-radius:10px;"></div>

                {{-- Buttons --}}
                <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:12px;border-top:1px solid var(--c-border);">
                    <a href="{{ route('jurusan.matakuliah.page') }}" class="sima-btn sima-btn--outline">Cancel</a>
                    <button type="submit" class="sima-btn sima-btn--accent">
                        <i class="fas fa-plus me-1"></i> Create Course
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
document.getElementById('createMkForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const errDiv = document.getElementById('createErr');
    errDiv.style.display = 'none';

    const data = Object.fromEntries(new FormData(this));

    const btn = this.querySelector('[type=submit]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Creating...';

    fetch('{{ route("jurusan.matakuliah.store") }}', {
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
            window.location.href = '{{ route("jurusan.matakuliah.page") }}?created=1';
        } else {
            let msg = response.message || 'Validation failed';
            if (response.errors) {
                msg += ': ' + Object.values(response.errors).flat().join(', ');
            }
            errDiv.textContent = msg;
            errDiv.style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus me-1"></i> Create Course';
        }
    })
    .catch(error => {
        errDiv.textContent = 'Server error: ' + error.message;
        errDiv.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus me-1"></i> Create Course';
    });
});
</script>
@endpush
