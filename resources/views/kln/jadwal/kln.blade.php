@extends('layouts.sima')

@section('page_title',    'Jadwal KLN')
@section('page_section',  'JADWAL')
@section('page_subtitle', 'Jadwal yang dibuat oleh KLN')

@section('main_content')

{{-- ── STAT CARD ────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--purple">
            <div class="sima-stat__icon sima-stat__icon--purple"><i class="fas fa-calendar-alt"></i></div>
            <div class="sima-stat__label">Total Jadwal</div>
            <div class="sima-stat__value">{{ $jadwalList->total() }}</div>
        </div>
    </div>
</div>

{{-- ── TABLE CARD ───────────────────────────────────── --}}
<div class="sima-card">
    <div class="sima-card__header">
        <h5 class="sima-card__title">Jadwal KLN</h5>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <form method="GET" action="{{ route('kln.jadwal.kln') }}"
                  style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <select name="hari" class="sima-input" style="width:140px;" onchange="this.form.submit()">
                    <option value="">Semua Hari</option>
                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $h)
                        <option value="{{ $h }}" {{ request('hari') === $h ? 'selected' : '' }}>{{ $h }}</option>
                    @endforeach
                </select>
                <input type="text" name="search" value="{{ request('search') }}" class="sima-input" style="width:160px;"
                       placeholder="Cari kegiatan...">
                <button type="submit" class="sima-btn sima-btn--outline"><i class="fas fa-search"></i></button>
                @if(request('hari') || request('search'))
                <a href="{{ route('kln.jadwal.kln') }}" class="sima-btn sima-btn--outline"><i class="fas fa-times"></i></a>
                @endif
            </form>
            <button class="sima-btn sima-btn--accent" onclick="openModal()">
                <i class="fas fa-plus me-1"></i> Tambah Jadwal
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="sima-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kegiatan</th>
                    <th>Kelas</th>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Lokasi</th>
                    <th>Sesi</th>
                    <th>Tahun Ajar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwalList as $i => $j)
                <tr>
                    <td>{{ $jadwalList->firstItem() + $loop->index }}</td>
                    <td>
                        <div class="fw-600">{{ $j->namaMk ?? '-' }}</div>
                        <code style="font-size:11px;color:var(--c-text-3);">{{ $j->kodeMk }}</code>
                    </td>
                    <td>{{ $j->kodeKelas ?? '-' }}</td>
                    <td>
                        @if($j->hari)
                            <span class="sima-badge sima-badge--purple">{{ $j->hari }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ $j->jam ? str_replace(':', '.', $j->jam) : '-' }}</td>
                    <td>{{ $j->ruangan ?? '-' }}</td>
                    <td><span class="sima-badge sima-badge--amber">{{ $j->totalSesi }}</span></td>
                    <td>{{ $j->tahunAjar ?? '-' }}</td>
                    <td>
                        <a href="{{ route('kln.jadwal.detail', $j->id) }}"
                           class="sima-btn sima-btn--outline sima-btn--sm">
                            <i class="fas fa-eye me-1"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">Belum ada jadwal KLN.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($jadwalList->hasPages())
    <div style="padding:14px 20px;border-top:1px solid var(--c-border);">
        {{ $jadwalList->links('vendor.pagination.sima') }}
    </div>
    @endif
</div>

{{-- ── ADD JADWAL MODAL ─────────────────────────────── --}}
<div id="addModal" style="display:none;position:fixed;inset:0;z-index:1060;background:rgba(0,0,0,.5);
     align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;width:90%;max-width:580px;
                box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;
                    padding:16px 20px;border-bottom:1px solid var(--c-border);">
            <span style="font-weight:700;font-size:15px;font-family:var(--f-display);">Tambah Jadwal KLN</span>
            <button onclick="closeModal()" style="border:none;background:none;font-size:20px;cursor:pointer;color:var(--c-text-3);">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="addForm" style="padding:20px;display:flex;flex-direction:column;gap:14px;">
            <div class="row g-3">

                {{-- Kegiatan combobox --}}
                <div class="col-12">
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">Kegiatan</label>
                    <div style="position:relative;" id="kegiatanWrap">
                        <input type="text" id="fKegiatanText" class="sima-input mt-1"
                               placeholder="Cari atau ketik kegiatan baru..."
                               autocomplete="off"
                               oninput="onKegiatanInput(this.value)"
                               onfocus="onKegiatanInput(this.value)">
                        <input type="hidden" id="fKegiatanId"   name="matakuliah_id">
                        <input type="hidden" id="fKegiatanBaru" name="kegiatan_nama">
                        <div id="kegiatanDropdown"
                             style="display:none;position:absolute;top:100%;left:0;right:0;
                                    background:#fff;border:1px solid var(--c-border);border-radius:8px;
                                    box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:200;max-height:220px;overflow-y:auto;">
                        </div>
                    </div>
                    <small id="kegiatanHint" class="text-muted" style="font-size:11px;"></small>
                </div>

                <div class="col-12">
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">Kelas Mahasiswa Asing</label>
                    <select name="kelas_id" id="fKelas" class="sima-input mt-1" required>
                        <option value="">— Pilih Kelas —</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->kodeKelas }}{{ $k->tahunAjar ? ' (' . $k->tahunAjar . ')' : '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">Hari</label>
                    <select name="hari" id="fHari" class="sima-input mt-1" required>
                        <option value="">— Pilih Hari —</option>
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $h)
                            <option value="{{ $h }}">{{ $h }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">Jam</label>
                    <div class="d-flex gap-2 align-items-center mt-1">
                        <input type="time" id="fJamMulai"   class="sima-input" required style="flex:1;">
                        <span class="text-muted fw-600">—</span>
                        <input type="time" id="fJamSelesai" class="sima-input" required style="flex:1;">
                    </div>
                    <small class="text-muted" style="font-size:11px;">Contoh: 09.00 — 11.00</small>
                </div>

                <div class="col-12">
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">Lokasi</label>
                    <input type="text" name="ruangan" id="fRuangan" class="sima-input mt-1"
                           placeholder="Contoh: Aula Gedung B, Taman Kampus, Mall Artha Gading" required>
                </div>

                <div class="col-12 col-md-6">
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">Total Sesi</label>
                    <input type="number" name="totalSesi" id="fSesi" class="sima-input mt-1"
                           min="1" max="32" placeholder="1" required>
                </div>

                <div class="col-12 col-md-6">
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">Tahun Ajar</label>
                    <input type="text" name="tahunAjar" id="fTahunAjar" class="sima-input mt-1"
                           placeholder="2025/2026" maxlength="9">
                </div>
            </div>

            <div id="formError" class="text-danger" style="font-size:13px;display:none;"></div>

            <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:4px;">
                <button type="button" onclick="closeModal()" class="sima-btn sima-btn--outline">Batal</button>
                <button type="submit" id="btnSimpan" class="sima-btn sima-btn--accent">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('page_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
const KEGIATAN_URL = '{{ route('kln.jadwal.kegiatan') }}';

// ── MODAL ──────────────────────────────────────────────
function openModal() {
    document.getElementById('addModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    document.getElementById('addModal').style.display = 'none';
    document.body.style.overflow = '';
    document.getElementById('addForm').reset();
    document.getElementById('fJamMulai').value    = '';
    document.getElementById('fJamSelesai').value  = '';
    document.getElementById('fKegiatanText').value = '';
    document.getElementById('fKegiatanId').value   = '';
    document.getElementById('fKegiatanBaru').value = '';
    document.getElementById('kegiatanHint').textContent = '';
    document.getElementById('kegiatanDropdown').style.display = 'none';
    document.getElementById('formError').style.display = 'none';
}
document.getElementById('addModal').addEventListener('click', function (e) {
    if (e.target === this) closeModal();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

// ── KEGIATAN COMBOBOX ──────────────────────────────────
let kegiatanTimer = null;

function onKegiatanInput(val) {
    // Clear selection whenever user types
    document.getElementById('fKegiatanId').value   = '';
    document.getElementById('fKegiatanBaru').value = '';
    document.getElementById('kegiatanHint').textContent = '';

    clearTimeout(kegiatanTimer);
    kegiatanTimer = setTimeout(() => fetchKegiatan(val.trim()), 220);
}

function fetchKegiatan(q) {
    const dd = document.getElementById('kegiatanDropdown');
    fetch(`${KEGIATAN_URL}?q=${encodeURIComponent(q)}`)
        .then(r => r.json())
        .then(items => renderKegiatanDropdown(items, q))
        .catch(() => { dd.style.display = 'none'; });
}

function renderKegiatanDropdown(items, q) {
    const dd   = document.getElementById('kegiatanDropdown');
    const text = document.getElementById('fKegiatanText').value.trim();
    dd.innerHTML = '';

    items.forEach(item => {
        const el = document.createElement('div');
        el.style.cssText = 'padding:9px 12px;cursor:pointer;font-size:13px;border-bottom:1px solid var(--c-border);';
        el.innerHTML = `<span class="fw-600">${escHtml(item.namaMk)}</span>
                        <code style="font-size:11px;color:var(--c-text-3);margin-left:6px;">${escHtml(item.kodeMk)}</code>`;
        el.addEventListener('mousedown', e => {
            e.preventDefault();
            selectKegiatan(item.id, item.namaMk);
        });
        el.addEventListener('mouseover',  () => el.style.background = 'var(--c-bg-2)');
        el.addEventListener('mouseout',   () => el.style.background = '');
        dd.appendChild(el);
    });

    // "Tambah kegiatan baru" option — shown when typed text isn't an exact existing name
    const exactMatch = items.some(i => i.namaMk.toLowerCase() === text.toLowerCase());
    if (text && !exactMatch) {
        const el = document.createElement('div');
        el.style.cssText = 'padding:9px 12px;cursor:pointer;font-size:13px;color:var(--c-accent);font-weight:600;';
        el.innerHTML = `<i class="fas fa-plus me-1"></i> Tambah "<em>${escHtml(text)}</em>" sebagai kegiatan baru`;
        el.addEventListener('mousedown', e => {
            e.preventDefault();
            createKegiatan(text);
        });
        el.addEventListener('mouseover',  () => el.style.background = 'var(--c-bg-2)');
        el.addEventListener('mouseout',   () => el.style.background = '');
        dd.appendChild(el);
    }

    dd.style.display = dd.children.length ? '' : 'none';
}

function selectKegiatan(id, nama) {
    document.getElementById('fKegiatanText').value  = nama;
    document.getElementById('fKegiatanId').value    = id;
    document.getElementById('fKegiatanBaru').value  = '';
    document.getElementById('kegiatanHint').textContent = 'Kegiatan dipilih dari daftar.';
    document.getElementById('kegiatanDropdown').style.display = 'none';
}

function createKegiatan(nama) {
    document.getElementById('fKegiatanText').value  = nama;
    document.getElementById('fKegiatanId').value    = '';
    document.getElementById('fKegiatanBaru').value  = nama;
    document.getElementById('kegiatanHint').textContent = 'Kegiatan baru akan dibuat saat disimpan.';
    document.getElementById('kegiatanDropdown').style.display = 'none';
}

// Hide dropdown on outside click
document.addEventListener('click', e => {
    if (!document.getElementById('kegiatanWrap').contains(e.target)) {
        document.getElementById('kegiatanDropdown').style.display = 'none';
    }
});

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── SUBMIT ─────────────────────────────────────────────
document.getElementById('addForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = document.getElementById('btnSimpan');
    const err = document.getElementById('formError');
    err.style.display = 'none';

    const kegiatanId   = document.getElementById('fKegiatanId').value;
    const kegiatanBaru = document.getElementById('fKegiatanBaru').value.trim();

    if (!kegiatanId && !kegiatanBaru) {
        err.textContent = 'Pilih kegiatan dari daftar atau ketik nama kegiatan baru.';
        err.style.display = '';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';

    const jamMulai   = document.getElementById('fJamMulai').value.replace(':', '.');
    const jamSelesai = document.getElementById('fJamSelesai').value.replace(':', '.');

    const body = {
        kelas_id:      parseInt(document.getElementById('fKelas').value),
        hari:          document.getElementById('fHari').value,
        jam:           `${jamMulai} - ${jamSelesai}`,
        ruangan:       document.getElementById('fRuangan').value,
        totalSesi:     parseInt(document.getElementById('fSesi').value),
        tahunAjar:     document.getElementById('fTahunAjar').value || null,
    };

    if (kegiatanId) {
        body.matakuliah_id = parseInt(kegiatanId);
    } else {
        body.kegiatan_nama = kegiatanBaru;
    }

    fetch('{{ route('kln.jadwal.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(body),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeModal();
            window.location.reload();
        } else {
            err.textContent = data.message || 'Gagal menyimpan jadwal.';
            err.style.display = '';
        }
    })
    .catch(() => {
        err.textContent = 'Terjadi kesalahan. Coba lagi.';
        err.style.display = '';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> Simpan';
    });
});
</script>
@endpush
