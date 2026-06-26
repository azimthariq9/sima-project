@extends('layouts.sima')

@section('page_title',    'Detail Sesi ' . $sesi)
@section('page_section',  'DOSEN')
@section('page_subtitle', ($jadwal->matakuliah->namaMk ?? '-') . ' — Kelas ' . ($jadwal->kelas->kodeKelas ?? '-') . ' — Sesi ' . $sesi)

@section('main_content')

@php
$hariColor = [
    'Senin'  => '#2563EB', 'Selasa' => '#0D9488', 'Rabu'  => '#7C3AED',
    'Kamis'  => '#D97706', 'Jumat'  => '#DC2626',  'Sabtu' => '#059669',
];
$color = $hariColor[$jadwal->hari] ?? '#888';
@endphp

{{-- ── BREADCRUMB BACK ─────────────────────────────── --}}
<div style="margin-bottom:16px;display:flex;align-items:center;gap:8px">
    <a href="{{ route('dosen.jadwal.detail', $jadwal->id) }}"
       style="display:inline-flex;align-items:center;gap:8px;
              font-size:13px;color:var(--c-text-3);text-decoration:none;transition:color .15s"
       onmouseover="this.style.color='var(--c-text-1)'"
       onmouseout="this.style.color='var(--c-text-3)'">
        <i class="fas fa-arrow-left"></i> Kembali ke Detail Kelas
    </a>
    <span style="color:var(--c-border);font-size:12px">/</span>
    <span style="font-size:13px;color:var(--c-text-2)">Sesi {{ $sesi }}</span>
</div>

{{-- ── INFO SESI ────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="sima-card">
            <div class="sima-card__body">
                <div style="display:flex;align-items:flex-start;gap:20px;flex-wrap:wrap">

                    {{-- Sesi badge --}}
                    <div style="padding:12px 20px;background:{{ $color }}18;border-radius:12px;
                                border:1px solid {{ $color }}30;text-align:center;min-width:90px">
                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;
                                    letter-spacing:.08em;color:{{ $color }}">SESI KE-</div>
                        <div style="font-size:28px;font-weight:800;color:{{ $color }};line-height:1.1;margin-top:4px">
                            {{ $sesi }}
                        </div>
                    </div>

                    <div style="flex:1;min-width:200px">
                        <div style="font-size:18px;font-weight:700;color:var(--c-text-1);font-family:var(--f-display)">
                            {{ $jadwal->matakuliah->namaMk ?? '-' }}
                        </div>
                        <div style="display:flex;gap:16px;margin-top:8px;flex-wrap:wrap">
                            <span style="font-size:12px;color:var(--c-text-3)">
                                <i class="fas fa-door-open"></i> Kelas {{ $jadwal->kelas->kodeKelas ?? '-' }}
                            </span>
                            <span style="font-size:12px;color:var(--c-text-3)">
                                <i class="fas fa-calendar-day"></i>
                                {{ $jadwal->hari }} · {{ $jadwal->jam }}
                            </span>
                            <span style="font-size:12px;color:var(--c-text-3)">
                                <i class="fas fa-map-marker-alt"></i> {{ $jadwal->ruangan }}
                            </span>
                            <span style="font-size:12px;color:var(--c-text-3)">
                                <i class="fas fa-users"></i> {{ $mahasiswa->count() }} mahasiswa
                            </span>
                        </div>

                        {{-- Tanggal sesi --}}
                        <div style="margin-top:12px;display:flex;align-items:center;gap:10px">
                            <label class="sima-label" style="margin:0;white-space:nowrap">Tanggal Sesi:</label>
                            <input type="date" id="inputTglSesi" class="sima-input"
                                   value="{{ $tglSesi ?? now()->format('Y-m-d') }}"
                                   style="max-width:180px">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── TABEL KEHADIRAN (EDITABLE) ──────────────────── --}}
<div class="sima-card">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Kehadiran Mahasiswa — Sesi {{ $sesi }}</h5>
            <div class="sima-card__subtitle">Ubah status kehadiran lalu klik Simpan Perubahan</div>
        </div>
    </div>
    <div class="sima-card__body">

        @if($mahasiswa->isEmpty())
            <div style="padding:48px;text-align:center;color:var(--c-text-3)">
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
                        @php
                            $existing = $kehadiranSesi[$mhs->id] ?? null;
                            $currentStatus = $existing->status ?? 'present';
                        @endphp
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
                                               {{ $currentStatus === 'present' ? 'checked' : '' }}>
                                        <div class="status-btn present-btn"
                                             style="text-align:center;padding:8px 4px;border-radius:8px;
                                                    font-size:12px;font-weight:600;border:2px solid var(--c-green);
                                                    background:var(--c-green-lt);color:var(--c-green);transition:all .15s">
                                            <i class="fas fa-check"></i><br>Hadir
                                        </div>
                                    </label>
                                    {{-- EXCUSED --}}
                                    <label style="flex:1;cursor:pointer">
                                        <input type="radio" name="status_{{ $mhs->id }}"
                                               value="excused" class="status-radio" style="display:none"
                                               {{ $currentStatus === 'excused' ? 'checked' : '' }}>
                                        <div class="status-btn excused-btn"
                                             style="text-align:center;padding:8px 4px;border-radius:8px;
                                                    font-size:12px;font-weight:600;border:2px solid var(--c-border);
                                                    background:var(--c-bg);color:var(--c-text-3);transition:all .15s">
                                            <i class="fas fa-file-alt"></i><br>Izin
                                        </div>
                                    </label>
                                    {{-- ABSENT --}}
                                    <label style="flex:1;cursor:pointer">
                                        <input type="radio" name="status_{{ $mhs->id }}"
                                               value="absent" class="status-radio" style="display:none"
                                               {{ $currentStatus === 'absent' ? 'checked' : '' }}>
                                        <div class="status-btn absent-btn"
                                             style="text-align:center;padding:8px 4px;border-radius:8px;
                                                    font-size:12px;font-weight:600;border:2px solid var(--c-border);
                                                    background:var(--c-bg);color:var(--c-text-3);transition:all .15s">
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
            <div style="display:flex;justify-content:flex-end;align-items:center;
                        margin-top:20px;gap:10px;flex-wrap:wrap">
                <div id="saveMsg" style="display:none;font-size:13px;color:var(--c-green);
                                          display:flex;align-items:center;gap:6px">
                    <i class="fas fa-circle-check"></i> <span></span>
                </div>
                <a href="{{ route('dosen.jadwal.detail', $jadwal->id) }}"
                   class="sima-btn sima-btn--outline">
                    Kembali
                </a>
                <button onclick="simpanPerubahan(event)" class="sima-btn sima-btn--blue">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        @endif
    </div>
</div>

@endsection

@section('page_js')
<style>
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
label:has(input[type="radio"]:not(:checked)) .status-btn { opacity: .55; }
label:has(input[type="radio"]:checked) .status-btn       { opacity: 1;   }
</style>
<script>
const JADWAL_ID  = {{ $jadwal->id }};
const SESI       = {{ $sesi }};
const CSRF       = '{{ csrf_token() }}';
const STORE_URL  = '{{ route("dosen.jadwal.kehadiran.store", ["jadwalId" => $jadwal->id]) }}';

const styleMap = {
    present: { bg: 'var(--c-green-lt)', border: 'var(--c-green)', color: 'var(--c-green)'  },
    excused: { bg: 'var(--c-amber-lt)', border: 'var(--c-amber)', color: 'var(--c-amber)'  },
    absent:  { bg: 'var(--c-red-lt)',   border: 'var(--c-red)',   color: 'var(--c-red)'    },
};
const dim = { bg: 'var(--c-bg)', border: 'var(--c-border)', color: 'var(--c-text-3)' };

function updateButtonStyles(group) {
    const checked = group.querySelector('.status-radio:checked');
    if (!checked) return;
    group.querySelectorAll('label').forEach(label => {
        const radio = label.querySelector('.status-radio');
        const btn   = label.querySelector('.status-btn');
        const apply = radio.checked ? styleMap[radio.value] : dim;
        btn.style.background  = apply.bg;
        btn.style.borderColor = apply.border;
        btn.style.color       = apply.color;
        btn.style.opacity     = radio.checked ? '1' : '0.55';
    });
}

window.setAllStatus = function (status) {
    document.querySelectorAll(`.status-radio[value="${status}"]`).forEach(r => {
        r.checked = true;
        updateButtonStyles(r.closest('.status-group'));
    });
};

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.status-group').forEach(updateButtonStyles);
    document.querySelectorAll('.status-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            updateButtonStyles(this.closest('.status-group'));
        });
    });
});

window.simpanPerubahan = function (e) {
    const tglSesi = document.getElementById('inputTglSesi').value;
    if (!tglSesi) { alert('Tanggal sesi harus diisi'); return; }

    const kehadiran = [];
    document.querySelectorAll('.status-group').forEach(group => {
        const mhsId  = group.getAttribute('data-mahasiswa-id');
        const checked = group.querySelector('.status-radio:checked');
        if (mhsId && checked) {
            kehadiran.push({ mahasiswa_id: parseInt(mhsId), status: checked.value });
        }
    });

    if (!kehadiran.length) { alert('Tidak ada data kehadiran'); return; }

    const btn = e.currentTarget;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    fetch(STORE_URL, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ jadwal_id: JADWAL_ID, sesi: SESI, tglSesi, kehadiran }),
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const msg = document.getElementById('saveMsg');
            msg.querySelector('span').textContent = 'Kehadiran sesi ' + SESI + ' berhasil diperbarui';
            msg.style.display = 'flex';
            setTimeout(() => { msg.style.display = 'none'; }, 4000);
        } else {
            alert(res.message || 'Gagal menyimpan');
        }
    })
    .catch(e => alert('Error: ' + e.message))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan';
    });
};
</script>
@endsection
