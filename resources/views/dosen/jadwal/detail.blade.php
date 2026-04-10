@extends('layouts.sima')

@section('page_title',    'Detail Kelas')
@section('page_section',  'DOSEN')
@section('page_subtitle', ($jadwal->matakuliah->namaMk ?? '-') . ' — Kelas ' . ($jadwal->kelas->kodeKelas ?? '-'))

@section('main_content')

@php
$hariColor = [
    'Senin'  => '#2563EB', 'Selasa' => '#0D9488', 'Rabu'  => '#7C3AED',
    'Kamis'  => '#D97706', 'Jumat'  => '#DC2626',  'Sabtu' => '#059669',
];
$color = $hariColor[$jadwal->hari] ?? '#888';
@endphp

{{-- ── BREADCRUMB BACK ─────────────────────────────── --}}
<div style="margin-bottom:16px;">
    <a href="{{ route('dosen.jadwal.index') }}"
       style="display:inline-flex;align-items:center;gap:8px;
              font-size:13px;color:var(--c-text-3);text-decoration:none;
              transition:color .15s"
       onmouseover="this.style.color='var(--c-text-1)'"
       onmouseout="this.style.color='var(--c-text-3)'">
        <i class="fas fa-arrow-left"></i> Kembali ke Jadwal
    </a>
</div>

{{-- ── INFO JADWAL ─────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="sima-card">
            <div class="sima-card__body">
                <div style="display:flex;align-items:flex-start;gap:20px;flex-wrap:wrap">

                    <div style="padding:12px 18px;background:{{ $color }}18;border-radius:12px;
                                border:1px solid {{ $color }}30;text-align:center;min-width:90px;">
                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;
                                    letter-spacing:.08em;color:{{ $color }}">{{ $jadwal->hari }}</div>
                        <div style="font-family:var(--f-mono);font-size:18px;font-weight:700;
                                    color:{{ $color }};margin-top:2px" id="displayJam">{{ $jadwal->jam }}</div>
                    </div>

                    <div style="flex:1;min-width:200px">
                        <div style="font-size:18px;font-weight:700;color:var(--c-text-1);
                                    font-family:var(--f-display)">
                            {{ $jadwal->matakuliah->namaMk ?? '-' }}
                        </div>
                        <div style="display:flex;gap:16px;margin-top:8px;flex-wrap:wrap">
                            <span style="font-size:12px;color:var(--c-text-3)">
                                <i class="fas fa-tag"></i>
                                {{ $jadwal->matakuliah->kodeMk ?? '-' }}
                            </span>
                            <span style="font-size:12px;color:var(--c-text-3)">
                                <i class="fas fa-door-open"></i>
                                Kelas {{ $jadwal->kelas->kodeKelas ?? '-' }}
                            </span>
                            <span style="font-size:12px;color:var(--c-text-3)">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $jadwal->ruangan }}
                            </span>
                            <span style="font-size:12px;color:var(--c-text-3)">
                                <i class="fas fa-layer-group"></i>
                                {{ $jadwal->totalSesi }} sesi total
                            </span>
                            <span style="font-size:12px;color:var(--c-text-3)">
                                <i class="fas fa-users"></i>
                                {{ $mahasiswa->count() }} mahasiswa
                            </span>
                        </div>
                        {{-- Sesi yang sudah diisi --}}
                        @if(!empty($sesiTerisi))
                        <div style="margin-top:10px;display:flex;gap:6px;flex-wrap:wrap">
                            <span style="font-size:11px;color:var(--c-text-3)">Sesi sudah diisi:</span>
                            @foreach($sesiTerisi as $s)
                            <span class="sima-badge sima-badge--green">Sesi {{ $s }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    {{-- Update Jam --}}
                    <div style="flex-shrink:0">
                        <button onclick="document.getElementById('updateJamForm').style.display =
                                         document.getElementById('updateJamForm').style.display === 'none' ? 'flex' : 'none'"
                                class="sima-btn sima-btn--outline sima-btn--sm">
                            <i class="fas fa-clock"></i> Ubah Jam
                        </button>
                    </div>
                </div>

                {{-- Form update jam (hidden by default) --}}
                <div id="updateJamForm"
                     style="display:none;align-items:center;gap:10px;margin-top:16px;
                            padding-top:16px;border-top:1px solid var(--c-border-soft)">
                    <label style="font-size:13px;color:var(--c-text-2);font-weight:500;
                                  white-space:nowrap">Jam Mengajar:</label>
                    <input type="text" id="inputJam" value="{{ $jadwal->jam }}"
                           placeholder="Contoh: 08:00-10:00"
                           class="sima-input" style="max-width:180px;">
                    <button onclick="updateJam()" class="sima-btn sima-btn--blue sima-btn--sm">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <button onclick="document.getElementById('updateJamForm').style.display='none'"
                            class="sima-btn sima-btn--outline sima-btn--sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── FORM INPUT KEHADIRAN ────────────────────────── --}}
<div class="sima-card">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Input Kehadiran Mahasiswa</h5>
            <div class="sima-card__subtitle">Submit semua kehadiran sekaligus</div>
        </div>
    </div>
    <div class="sima-card__body">

        {{-- Kontrol sesi dan tanggal --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
                    gap:16px;margin-bottom:24px;padding:16px;
                    background:var(--c-surface-2);border-radius:12px;
                    border:1px solid var(--c-border-soft)">
            <div>
                <label class="sima-label">Sesi Ke-</label>
                <select id="inputSesi" class="sima-input">
                    @for($i = 1; $i <= $jadwal->totalSesi; $i++)
                        <option value="{{ $i }}"
                            {{ in_array($i, $sesiTerisi) ? 'disabled' : '' }}
                            style="{{ in_array($i, $sesiTerisi) ? 'color:#94a3b8' : '' }}">
                            Sesi {{ $i }}{{ in_array($i, $sesiTerisi) ? ' ✓' : '' }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="sima-label">Tanggal Sesi</label>
                <input type="date" id="inputTglSesi" class="sima-input"
                       value="{{ now()->format('Y-m-d') }}">
            </div>
        </div>

        @if($mahasiswa->isEmpty())
            <div style="padding:32px;text-align:center;color:var(--c-text-3)">
                <i class="fas fa-users" style="font-size:32px;margin-bottom:12px;display:block"></i>
                Belum ada mahasiswa terdaftar di kelas ini
            </div>
        @else
            {{-- Tombol select all --}}
            <div style="display:flex;align-items:center;justify-content:space-between;
                        margin-bottom:12px;flex-wrap:wrap;gap:8px">
                <div style="font-size:13px;color:var(--c-text-2);font-weight:500">
                    {{ $mahasiswa->count() }} mahasiswa
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    <button onclick="setAllStatus('present')"
                            class="sima-btn sima-btn--sm"
                            style="background:var(--c-green-lt);color:var(--c-green);
                                   box-shadow:none;border:1px solid rgba(5,150,105,.2)">
                        <i class="fas fa-check-circle"></i> Semua Hadir
                    </button>
                    <button onclick="setAllStatus('absent')"
                            class="sima-btn sima-btn--sm"
                            style="background:var(--c-red-lt);color:var(--c-red);
                                   box-shadow:none;border:1px solid rgba(220,38,38,.2)">
                        <i class="fas fa-times-circle"></i> Semua Absen
                    </button>
                </div>
            </div>

            {{-- Tabel kehadiran --}}
            <div style="overflow-x:auto">
                <table class="sima-table" id="kehadiranTable">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>NPM</th>
                            <th>NAMA MAHASISWA</th>
                            <th style="min-width:280px">STATUS KEHADIRAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mahasiswa as $i => $mhs)
                        <tr>
                            <td style="font-family:var(--f-mono);color:var(--c-text-3)">
                                {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                            </td>
                            <td style="font-family:var(--f-mono);font-size:12px">
                                {{ $mhs->npm ?? '-' }}
                            </td>
                            <td style="font-weight:500">
                                <div style="display:flex;align-items:center;gap:10px">
                                    <div class="sima-avatar"
                                         style="width:28px;height:28px;font-size:10px;border-radius:8px;
                                                background:linear-gradient(135deg,var(--c-accent),var(--c-accent-2))">
                                        {{ strtoupper(substr($mhs->nama ?? 'X', 0, 1)) }}
                                    </div>
                                    {{ $mhs->nama ?? '-' }}
                                </div>
                            </td>
                            <td>
                                <div style="display:flex;gap:8px" class="status-group"
                                     data-mahasiswa-id="{{ $mhs->id }}">
                                    {{-- PRESENT --}}
                                    <label style="flex:1;cursor:pointer">
                                        <input type="radio" name="status_{{ $mhs->id }}"
                                               value="present" class="status-radio" style="display:none"
                                               checked>
                                        <div class="status-btn present-btn"
                                             style="text-align:center;padding:8px 4px;border-radius:8px;
                                                    font-size:12px;font-weight:600;border:2px solid var(--c-green);
                                                    background:var(--c-green-lt);color:var(--c-green);
                                                    transition:all .15s">
                                            <i class="fas fa-check"></i><br>Hadir
                                        </div>
                                    </label>
                                    {{-- EXCUSED --}}
                                    <label style="flex:1;cursor:pointer">
                                        <input type="radio" name="status_{{ $mhs->id }}"
                                               value="excused" class="status-radio" style="display:none">
                                        <div class="status-btn excused-btn"
                                             style="text-align:center;padding:8px 4px;border-radius:8px;
                                                    font-size:12px;font-weight:600;border:2px solid var(--c-border);
                                                    background:var(--c-bg);color:var(--c-text-3);
                                                    transition:all .15s">
                                            <i class="fas fa-file-alt"></i><br>Izin
                                        </div>
                                    </label>
                                    {{-- ABSENT --}}
                                    <label style="flex:1;cursor:pointer">
                                        <input type="radio" name="status_{{ $mhs->id }}"
                                               value="absent" class="status-radio" style="display:none">
                                        <div class="status-btn absent-btn"
                                             style="text-align:center;padding:8px 4px;border-radius:8px;
                                                    font-size:12px;font-weight:600;border:2px solid var(--c-border);
                                                    background:var(--c-bg);color:var(--c-text-3);
                                                    transition:all .15s">
                                            <i class="fas fa-times"></i><br>Absen
                                        </div>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Submit --}}
            <div style="display:flex;justify-content:flex-end;margin-top:20px;gap:10px">
                <button onclick="submitKehadiran()" class="sima-btn sima-btn--blue">
                    <i class="fas fa-paper-plane"></i> Simpan Kehadiran
                </button>
            </div>
        @endif
    </div>
</div>

@endsection

@section('page_js')
<style>
/* Status button active states */
input[type="radio"][value="present"]:checked ~ .status-btn.present-btn {
    background: var(--c-green-lt) !important;
    border-color: var(--c-green) !important;
    color: var(--c-green) !important;
}
input[type="radio"][value="excused"]:checked ~ .status-btn.excused-btn {
    background: var(--c-amber-lt) !important;
    border-color: var(--c-amber) !important;
    color: var(--c-amber) !important;
}
input[type="radio"][value="absent"]:checked ~ .status-btn.absent-btn {
    background: var(--c-red-lt) !important;
    border-color: var(--c-red) !important;
    color: var(--c-red) !important;
}
/* Non-active siblings dim */
label:has(input[type="radio"]:not(:checked)) .status-btn {
    opacity: .55;
}
label:has(input[type="radio"]:checked) .status-btn {
    opacity: 1;
}
</style>
<script>
const JADWAL_ID = {{ $jadwal->id }};
const CSRF      = '{{ csrf_token() }}';

/* ── SET ALL STATUS ──────────────────────────────── */
window.setAllStatus = function (status) {
    document.querySelectorAll(`.status-radio[value="${status}"]`).forEach(r => {
        r.checked = true;
        updateButtonStyles(r.closest('.status-group'));
    });
};

/* ── UPDATE BUTTON STYLES ────────────────────────── */
function updateButtonStyles(group) {
    const checked = group.querySelector('.status-radio:checked');
    if (!checked) return;

    const styleMap = {
        present: { bg: 'var(--c-green-lt)',  border: 'var(--c-green)',  color: 'var(--c-green)'  },
        excused: { bg: 'var(--c-amber-lt)',  border: 'var(--c-amber)',  color: 'var(--c-amber)'  },
        absent:  { bg: 'var(--c-red-lt)',    border: 'var(--c-red)',    color: 'var(--c-red)'    },
    };
    const dim = { bg: 'var(--c-bg)', border: 'var(--c-border)', color: 'var(--c-text-3)' };

    group.querySelectorAll('label').forEach(label => {
        const radio = label.querySelector('.status-radio');
        const btn   = label.querySelector('.status-btn');
        const st    = styleMap[radio.value];
        const apply = radio.checked ? st : dim;
        btn.style.background   = apply.bg;
        btn.style.borderColor  = apply.border;
        btn.style.color        = apply.color;
        btn.style.opacity      = radio.checked ? '1' : '0.55';
    });
}

/* ── RADIO CHANGE LISTENER ───────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    // Init all rows (present is default checked)
    document.querySelectorAll('.status-group').forEach(updateButtonStyles);

    document.querySelectorAll('.status-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            updateButtonStyles(this.closest('.status-group'));
        });
    });

    // Disable past sesi
    const sesiSelect = document.getElementById('inputSesi');
    if (sesiSelect && sesiSelect.options.length > 0) {
        // Auto pilih sesi pertama yang belum diisi
        for (let opt of sesiSelect.options) {
            if (!opt.disabled) { opt.selected = true; break; }
        }
    }
});

/* ── SUBMIT KEHADIRAN ────────────────────────────── */
window.submitKehadiran = function () {
    const sesi    = document.getElementById('inputSesi').value;
    const tglSesi = document.getElementById('inputTglSesi').value;

    if (!sesi || !tglSesi) {
        alert('Sesi dan tanggal harus diisi');
        return;
    }

    // Kumpulkan data kehadiran
    const kehadiran = [];
    document.querySelectorAll('.status-group').forEach(group => {
        const mhsId  = group.getAttribute('data-mahasiswa-id');
        const checked = group.querySelector('.status-radio:checked');
        if (mhsId && checked) {
            kehadiran.push({ mahasiswa_id: parseInt(mhsId), status: checked.value });
        }
    });

    if (!kehadiran.length) {
        alert('Tidak ada data kehadiran untuk disimpan');
        return;
    }

    // Ambil jam jika diubah
    const jamInput = document.getElementById('inputJam');
    const jam      = jamInput && document.getElementById('updateJamForm').style.display !== 'none'
                     ? jamInput.value : null;

    const payload = { jadwal_id: JADWAL_ID, sesi, tglSesi, kehadiran };
    if (jam) payload.jam = jam;

    // Disable tombol saat proses
    const btn = event.currentTarget;
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...'; }

    fetch('{{ route("dosen.jadwal.kehadiran.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify(payload),
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            // Tandai sesi sebagai sudah terisi
            const opt = document.querySelector(`#inputSesi option[value="${sesi}"]`);
            if (opt) { opt.disabled = true; opt.textContent += ' ✓'; }
            alert('Kehadiran sesi ' + sesi + ' berhasil disimpan!');
            // Refresh page untuk update badge sesi
            window.location.reload();
        } else {
            alert(res.message || 'Gagal menyimpan kehadiran');
        }
    })
    .catch(e => alert('Error: ' + e.message))
    .finally(() => {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-paper-plane"></i> Simpan Kehadiran'; }
    });
};

/* ── UPDATE JAM ──────────────────────────────────── */
window.updateJam = function () {
    const jam = document.getElementById('inputJam').value;
    if (!jam) { alert('Jam harus diisi'); return; }

    fetch(`/dosen/jadwal/${JADWAL_ID}/jam`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ jam }),
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            document.getElementById('displayJam').textContent = jam;
            document.getElementById('updateJamForm').style.display = 'none';
            alert('Jam jadwal berhasil diupdate');
        } else {
            alert(res.message || 'Gagal update jam');
        }
    })
    .catch(e => alert('Error: ' + e.message));
};
</script>
@endsection