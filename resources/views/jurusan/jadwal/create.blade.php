@extends('layouts.sima')

@section('page_title',    'Create Schedule')
@section('page_section',  'JURUSAN')
@section('page_subtitle', 'Add a new class schedule')

@section('main_content')

<div class="mb-3">
    <a href="{{ route('jurusan.jadwal.page') }}" class="sima-btn sima-btn--outline sima-btn--sm">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="sima-card">
    <div style="padding:20px 24px;border-bottom:1px solid var(--c-border);">
        <h5 style="font-weight:700;font-family:var(--f-display);margin:0;">Add New Schedule</h5>
        <div style="font-size:13px;color:var(--c-text-3);margin-top:2px;">
            Create a new class schedule for your department.
        </div>
    </div>

    <div style="padding:24px;">
        <form id="formTambah">
            <div style="display:flex;flex-direction:column;gap:22px;">

                {{-- Hari & Jam --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div>
                        <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                            Day <span style="color:var(--c-red)">*</span>
                        </label>
                        <select name="hari" class="sima-input mt-1" required>
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                            Time <span style="color:var(--c-red)">*</span>
                        </label>
                        <input type="text" name="jam" class="sima-input mt-1" placeholder="08:00-10:00" required>
                    </div>
                </div>

                {{-- Ruangan & Total Sesi --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div>
                        <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                            Room <span style="color:var(--c-red)">*</span>
                        </label>
                        <input type="text" name="ruangan" class="sima-input mt-1" placeholder="Gd.4 R.201" required>
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                            Total Sessions <span style="color:var(--c-red)">*</span>
                        </label>
                        <input type="number" name="totalSesi" class="sima-input mt-1" value="16" min="1" required>
                    </div>
                </div>

                <hr style="border:none;border-top:1px solid var(--c-border);margin:4px 0;">
                <div style="font-size:11.5px;color:var(--c-text-3);margin-bottom:-8px;"><i class="fas fa-info-circle"></i> Enter code — ID will be looked up automatically</div>

                {{-- Kelas --}}
                <div style="background:var(--c-bg-2);border:1px solid var(--c-border-soft);border-radius:12px;padding:14px;">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--c-text-3);margin-bottom:10px">
                        <i class="fas fa-door-open"></i> Class
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div>
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">Class Code</label>
                            <input type="text" name="kodeKelas" id="kodeKelas" class="sima-input mt-1" placeholder="3KA35" style="font-family:var(--f-mono)" required>
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">Academic Year</label>
                            <select name="tahunAjar" id="tahunAjar" class="sima-input mt-1">
                                @foreach($tahunAjarList as $ta)
                                    <option value="{{ $ta }}" {{ $ta === $tahunAjarDefault ? 'selected' : '' }}>{{ $ta }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="kelasPreview" style="margin-top:6px;font-size:11.5px;min-height:16px"></div>
                </div>

                {{-- MK --}}
                <div style="background:var(--c-bg-2);border:1px solid var(--c-border-soft);border-radius:12px;padding:14px;">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--c-text-3);margin-bottom:10px">
                        <i class="fas fa-book"></i> Course
                    </div>
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">Course Code</label>
                    <input type="text" name="kodeMk" id="kodeMk" class="sima-input mt-1" placeholder="IT012236" style="font-family:var(--f-mono)" required>
                    <div id="mkPreview" style="margin-top:6px;font-size:11.5px;min-height:16px"></div>
                </div>

                {{-- Dosen --}}
                <div style="background:var(--c-bg-2);border:1px solid var(--c-border-soft);border-radius:12px;padding:14px;">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--c-text-3);margin-bottom:10px">
                        <i class="fas fa-chalkboard-teacher"></i> Lecturer
                    </div>
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">Lecturer Code / NIDN</label>
                    <input type="text" name="kodeDos" id="kodeDos" class="sima-input mt-1" placeholder="DOS001 or 0123456789" style="font-family:var(--f-mono)" required>
                    <div id="dosenPreview" style="margin-top:6px;font-size:11.5px;min-height:16px"></div>
                </div>

                <div id="tambahErr" style="display:none;font-size:12.5px;color:var(--c-red);padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid var(--c-red);border-radius:10px;"></div>

                {{-- Buttons --}}
                <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:12px;border-top:1px solid var(--c-border);">
                    <a href="{{ route('jurusan.jadwal.page') }}" class="sima-btn sima-btn--outline">Cancel</a>
                    <button type="submit" id="btnSimpan" class="sima-btn sima-btn--accent">
                        <i class="fas fa-save me-1"></i> Save Schedule
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
const CSRF = '{{ csrf_token() }}';

/* ── PREVIEW ────────────────────────────────── */
function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }

function previewKelas() {
    const kode = document.getElementById('kodeKelas').value.trim();
    const ta   = document.getElementById('tahunAjar').value.trim();
    const pv   = document.getElementById('kelasPreview');
    if (!kode || !ta) { pv.textContent = ''; return; }
    fetch(`/jurusan/kelas/preview?kodeKelas=${encodeURIComponent(kode)}&tahunAjar=${encodeURIComponent(ta)}`, { headers: { Accept: 'application/json' } })
        .then(r => r.json())
        .then(res => {
            pv.style.color = res.found ? '#059669' : '#dc2626';
            pv.textContent = res.found ? `✓ Found: Class ${res.kodeKelas} (${res.tahunAjar})` : `✗ Class '${kode}' not found`;
        }).catch(() => {});
}

function previewMk() {
    const kode = document.getElementById('kodeMk').value.trim();
    const pv   = document.getElementById('mkPreview');
    if (!kode) { pv.textContent = ''; return; }
    fetch(`/jurusan/matakuliah/preview?kodeMk=${encodeURIComponent(kode)}`, { headers: { Accept: 'application/json' } })
        .then(r => r.json())
        .then(res => {
            pv.style.color = res.found ? '#059669' : '#dc2626';
            pv.textContent = res.found ? `✓ Found: ${res.namaMk} (${res.kodeMk})` : `✗ Course code '${kode}' not found`;
        }).catch(() => {});
}

function previewDosen() {
    const kode = document.getElementById('kodeDos').value.trim();
    const pv   = document.getElementById('dosenPreview');
    if (!kode) { pv.textContent = ''; return; }
    fetch(`/jurusan/dosen/preview?kodeDos=${encodeURIComponent(kode)}`, { headers: { Accept: 'application/json' } })
        .then(r => r.json())
        .then(res => {
            pv.style.color = res.found ? '#059669' : '#dc2626';
            pv.textContent = res.found ? `✓ Found: ${res.nama} (${res.kodeDos ?? res.nidn})` : `✗ Code/NIDN '${kode}' not found`;
        }).catch(() => {});
}

document.getElementById('kodeKelas').addEventListener('input', debounce(previewKelas, 500));
document.getElementById('tahunAjar').addEventListener('change', debounce(previewKelas, 200));
document.getElementById('kodeMk').addEventListener('input', debounce(previewMk, 500));
document.getElementById('kodeDos').addEventListener('input', debounce(previewDosen, 500));

/* ── FORM SUBMIT ────────────────────────────── */
document.getElementById('formTambah').addEventListener('submit', function(e) {
    e.preventDefault();
    const errDiv = document.getElementById('tambahErr');
    errDiv.style.display = 'none';

    const data = Object.fromEntries(new FormData(this));
    if (data.totalSesi) data.totalSesi = parseInt(data.totalSesi);

    const btn = document.getElementById('btnSimpan');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

    fetch('{{ route("jurusan.jadwal.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
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
            window.location.href = '{{ route("jurusan.jadwal.page") }}?created=1';
        } else {
            let msg = response.message || 'Validation failed';
            if (response.errors) {
                msg += ': ' + Object.values(response.errors).flat().join(', ');
            }
            errDiv.textContent = msg;
            errDiv.style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> Save Schedule';
        }
    })
    .catch(error => {
        errDiv.textContent = 'Server error: ' + error.message;
        errDiv.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> Save Schedule';
    });
});
</script>
@endpush
