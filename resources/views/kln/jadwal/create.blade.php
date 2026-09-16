@extends('layouts.sima')

@section('page_title',    'Create Schedule')
@section('page_section',  'JADWAL')
@section('page_subtitle', 'Add a new KLN schedule')

@section('main_content')

<div class="mb-3">
    <a href="{{ route('kln.jadwal.kln') }}" class="sima-btn sima-btn--outline sima-btn--sm">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="sima-card">
    <div style="padding:20px 24px;border-bottom:1px solid var(--c-border);">
        <h5 style="font-weight:700;font-family:var(--f-display);margin:0;">Add New KLN Schedule</h5>
        <div style="font-size:13px;color:var(--c-text-3);margin-top:2px;">
            Create a new activity schedule for KLN programs.
        </div>
    </div>

    <div style="padding:24px;">
        <form id="addJadwalForm">
            <div style="display:flex;flex-direction:column;gap:22px;">

                {{-- Kegiatan combobox --}}
                <div>
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                        Activity
                    </label>
                    <div style="position:relative;margin-top:4px;" id="kegiatanWrap">
                        <input type="text" id="fKegiatanText" class="sima-input"
                               placeholder="Search or type new activity..."
                               autocomplete="off"
                               oninput="onKegiatanInput(this.value)"
                               onfocus="onKegiatanInput(this.value)">
                        <input type="hidden" id="fKegiatanId" name="matakuliah_id">
                        <input type="hidden" id="fKegiatanBaru" name="kegiatan_nama">
                        <div id="kegiatanDropdown"
                             style="display:none;position:absolute;top:100%;left:0;right:0;
                                    background:#fff;border:1px solid var(--c-border);border-radius:8px;
                                    box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:200;max-height:220px;overflow-y:auto;">
                        </div>
                    </div>
                    <small id="kegiatanHint" class="text-muted" style="font-size:11px;"></small>
                </div>

                {{-- Kelas --}}
                <div>
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                        Student Class <span style="color:var(--c-red)">*</span>
                    </label>
                    <select name="kelas_id" id="fKelas" class="sima-input mt-1" required>
                        <option value="">— Select Class —</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->kodeKelas }}{{ $k->tahunAjar ? ' (' . $k->tahunAjar . ')' : '' }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Hari --}}
                <div>
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                        Day <span style="color:var(--c-red)">*</span>
                    </label>
                    <select name="hari" id="fHari" class="sima-input mt-1" required>
                        <option value="">— Select Day —</option>
                        @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $h)
                            <option value="{{ $h }}">{{ $h }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Jam --}}
                <div>
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                        Time <span style="color:var(--c-red)">*</span>
                    </label>
                    <div style="display:flex;gap:12px;align-items:center;margin-top:4px;">
                        <input type="time" id="fJamMulai" class="sima-input" required style="flex:1;">
                        <span style="color:var(--c-text-3);font-weight:600;">—</span>
                        <input type="time" id="fJamSelesai" class="sima-input" required style="flex:1;">
                    </div>
                    <small class="text-muted" style="font-size:11px;">Example: 09:00 — 11:00</small>
                </div>

                {{-- Lokasi --}}
                <div>
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                        Location <span style="color:var(--c-red)">*</span>
                    </label>
                    <input type="text" name="ruangan" id="fRuangan" class="sima-input mt-1"
                           placeholder="e.g., Auditorium Bldg, Campus Park, Mall Artha Gading" required>
                </div>

                {{-- Total Sesi --}}
                <div>
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                        Total Sessions <span style="color:var(--c-red)">*</span>
                    </label>
                    <input type="number" name="totalSesi" id="fSesi" class="sima-input mt-1"
                           min="1" max="32" placeholder="1" required>
                </div>

                {{-- Tahun Ajar --}}
                <div>
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                        Academic Year
                    </label>
                    <input type="text" name="tahunAjar" id="fTahunAjar" class="sima-input mt-1"
                           placeholder="2025/2026" maxlength="9">
                </div>

                <div id="formError" style="display:none;font-size:13px;color:var(--c-red);padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid var(--c-red);border-radius:10px;"></div>

                {{-- Buttons --}}
                <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:12px;border-top:1px solid var(--c-border);">
                    <a href="{{ route('kln.jadwal.kln') }}" class="sima-btn sima-btn--outline">Cancel</a>
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
const KEGIATAN_URL = '{{ route("kln.jadwal.kegiatan") }}';

// ── Kegiatan Autocomplete ──────────────────────────────────
let kegiatanTimeout = null;
let kegiatanItems = [];

function onKegiatanInput(val) {
    clearTimeout(kegiatanTimeout);
    if (val.length < 1) { hideDropdown(); return; }
    kegiatanTimeout = setTimeout(() => searchKegiatan(val), 250);
}

function searchKegiatan(q) {
    fetch(KEGIATAN_URL + '?q=' + encodeURIComponent(q))
        .then(r => r.json())
        .then(data => {
            kegiatanItems = data;
            renderDropdown(data);
        });
}

function renderDropdown(items) {
    const dd = document.getElementById('kegiatanDropdown');
    const hint = document.getElementById('kegiatanHint');

    if (items.length === 0) {
        dd.style.display = 'none';
        hint.textContent = 'No matches — press Enter or type a new activity name';
        hint.style.color = 'var(--c-accent)';
        return;
    }

    dd.innerHTML = '';
    dd.style.display = 'block';
    hint.textContent = '';

    items.forEach(item => {
        const div = document.createElement('div');
        div.style.cssText = 'padding:10px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid var(--c-border);';
        div.innerHTML = `<div style="font-weight:600;">${item.namaMk}</div><code style="font-size:11px;color:var(--c-text-3);">${item.kodeMk}</code>`;
        div.onmouseenter = () => div.style.background = 'var(--c-bg-2)';
        div.onmouseleave = () => div.style.background = '';
        div.onclick = () => selectKegiatan(item);
        dd.appendChild(div);
    });
}

function selectKegiatan(item) {
    document.getElementById('fKegiatanText').value = item.namaMk;
    document.getElementById('fKegiatanId').value = item.id;
    document.getElementById('fKegiatanBaru').value = '';
    document.getElementById('kegiatanDropdown').style.display = 'none';
    document.getElementById('kegiatanHint').textContent = item.kodeMk;
    document.getElementById('kegiatanHint').style.color = 'var(--c-text-3)';
}

function hideDropdown() {
    document.getElementById('kegiatanDropdown').style.display = 'none';
}

// Close dropdown on outside click
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('kegiatanWrap');
    if (!wrap.contains(e.target)) hideDropdown();
});

// ── Form Submit ────────────────────────────────────────────
document.getElementById('addJadwalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const errDiv = document.getElementById('formError');
    errDiv.style.display = 'none';

    const jamMulai   = document.getElementById('fJamMulai').value;
    const jamSelesai = document.getElementById('fJamSelesai').value;
    const jam        = jamMulai + ' - ' + jamSelesai;

    const data = {
        matakuliah_id: document.getElementById('fKegiatanId').value || null,
        kegiatan_nama: document.getElementById('fKegiatanBaru').value || null,
        kelas_id:      document.querySelector('select[name="kelas_id"]').value,
        hari:          document.querySelector('select[name="hari"]').value,
        jam:           jam,
        ruangan:       document.querySelector('input[name="ruangan"]').value,
        totalSesi:     parseInt(document.querySelector('input[name="totalSesi"]').value) || 1,
        tahunAjar:     document.querySelector('input[name="tahunAjar"]').value || null,
    };

    if (!data.kelas_id) { alert('Class is required'); return; }
    if (!data.hari) { alert('Day is required'); return; }
    if (!jamMulai || !jamSelesai) { alert('Both start and end time are required'); return; }
    if (!data.ruangan) { alert('Location is required'); return; }
    if (!data.matakuliah_id && !data.kegiatan_nama) { alert('Activity is required — search or type a new name'); return; }

    const btn = document.getElementById('btnSimpan');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

    fetch('{{ route("kln.jadwal.store") }}', {
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
            window.location.href = '{{ route("kln.jadwal.kln") }}?created=1';
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
