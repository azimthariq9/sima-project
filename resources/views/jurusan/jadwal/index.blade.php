@extends('layouts.sima')

@section('page_title',    'Jadwal')
@section('page_section',  'ADMIN JURUSAN')
@section('page_subtitle', 'Pengelolaan jadwal perkuliahan di jurusan Anda')

@section('main_content')

@php
$tahunSekarang  = (int) date('Y');
$tahunAjarList  = [];
for ($i = -1; $i <= 10; $i++) {
    $a = $tahunSekarang + $i;
    $tahunAjarList[] = "{$a}/".($a+1);
}
$tahunAjarDefault = date('Y').'/'.(date('Y')+1);
$hariOrder = ['Senin'=>1,'Selasa'=>2,'Rabu'=>3,'Kamis'=>4,'Jumat'=>5,'Sabtu'=>6];
@endphp

<div class="sima-card sima-fade">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Daftar Jadwal</h5>
            <div class="sima-card__subtitle">Total {{ $jadwal->total() }} jadwal</div>
        </div>
        <button type="button" id="btnTambah" class="sima-btn sima-btn--sm">
            <i class="fas fa-plus"></i> Tambah Jadwal
        </button>
    </div>

    {{-- Filter --}}
    <div style="padding:12px 20px;border-bottom:1px solid var(--c-border-soft)">
        <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari MK / dosen / kelas…"
                   class="sima-input" style="width:240px;font-size:13px">
            <select name="hari" class="sima-input" style="width:140px;font-size:13px" onchange="this.form.submit()">
                <option value="">— Semua Hari —</option>
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                    <option value="{{ $hari }}" {{ request('hari') === $hari ? 'selected' : '' }}>{{ $hari }}</option>
                @endforeach
            </select>
            <button type="submit" class="sima-btn sima-btn--sm sima-btn--outline">
                <i class="fas fa-search"></i> Cari
            </button>
            @if($search || request('hari'))
                <a href="{{ route('jurusan.jadwal.page') }}" class="sima-btn sima-btn--sm sima-btn--outline">
                    <i class="fas fa-xmark"></i> Reset
                </a>
            @endif
        </form>
    </div>

    @if($jadwal->isEmpty())
        <div class="sima-card__body" style="text-align:center;padding:48px 20px;color:var(--c-text-3)">
            <i class="fas fa-calendar-xmark" style="font-size:36px;opacity:.3;display:block;margin-bottom:12px"></i>
            <div style="font-size:14px;font-weight:500;color:var(--c-text-2)">
                {{ ($search || request('hari')) ? 'Tidak ada jadwal yang cocok' : 'Belum ada jadwal' }}
            </div>
        </div>
    @else
        <div style="overflow-x:auto">
            <table class="sima-table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th style="width:90px">Hari</th>
                        <th style="width:110px">Jam</th>
                        <th>Mata Kuliah</th>
                        <th>Dosen</th>
                        <th style="width:90px">Kelas</th>
                        <th style="width:80px">Ruangan</th>
                        <th style="width:60px;text-align:center">Sesi</th>
                        <th style="width:100px"></th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $hariColor = [
                        'Senin'=>'#2563EB','Selasa'=>'#0D9488','Rabu'=>'#7C3AED',
                        'Kamis'=>'#D97706','Jumat'=>'#DC2626','Sabtu'=>'#059669',
                    ];
                    @endphp
                    @foreach($jadwal as $j)
                    @php $c = $hariColor[$j->hari] ?? '#888'; @endphp
                    <tr>
                        <td style="color:var(--c-text-3);font-size:12px">
                            {{ $jadwal->firstItem() + $loop->index }}
                        </td>
                        <td>
                            <span style="background:{{ $c }}18;color:{{ $c }};border:1px solid {{ $c }}40;
                                         padding:3px 10px;border-radius:6px;font-size:12px;font-weight:600">
                                {{ $j->hari ?? '—' }}
                            </span>
                        </td>
                        <td style="font-family:var(--f-mono);font-size:12px;font-weight:600">{{ $j->jam ?? '—' }}</td>
                        <td style="font-size:13px;font-weight:600">{{ $j->nama_matkul ?? '—' }}</td>
                        <td style="font-size:12.5px;color:var(--c-text-2)">{{ $j->nama_dosen ?? '—' }}</td>
                        <td>
                            <span style="font-family:var(--f-mono);background:var(--c-teal-lt);color:var(--c-teal);padding:3px 8px;border-radius:6px;font-size:12px">
                                {{ $j->kodeKelas ?? '—' }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:var(--c-text-3)">{{ $j->ruangan ?? '—' }}</td>
                        <td style="text-align:center;font-family:var(--f-mono);font-size:13px">{{ $j->totalSesi ?? '—' }}</td>
                        <td>
                            <div style="display:flex;gap:6px">
                                <button type="button" class="sima-btn sima-btn--sm sima-btn--outline btn-edit-jadwal"
                                        data-id="{{ $j->id }}" style="font-size:11.5px;padding:4px 10px">
                                    <i class="fas fa-pencil"></i>
                                </button>
                                <button type="button" class="sima-btn sima-btn--sm btn-del-jadwal"
                                        data-id="{{ $j->id }}"
                                        style="font-size:11.5px;padding:4px 10px;background:rgba(220,38,38,.08);color:#dc2626;border:1px solid rgba(220,38,38,.2)">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="padding:14px 20px">
            {{ $jadwal->links('vendor.pagination.sima') }}
        </div>
    @endif
</div>


{{-- ══════════════════════════════════════
     MODAL TAMBAH JADWAL
══════════════════════════════════════ --}}
<div id="modalTambah" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:18px;width:100%;max-width:560px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2);max-height:90vh;overflow-y:auto">
        <div style="padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:#fff;z-index:1">
            <div>
                <div style="font-size:15px;font-weight:700;color:#1e293b">Tambah Jadwal</div>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px">Isi kode — sistem akan mencari ID otomatis</div>
            </div>
            <button type="button" onclick="closeModals()" style="width:32px;height:32px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;cursor:pointer;color:#64748b">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <div style="padding:20px 24px">
            <form id="formTambah">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px">
                    <div>
                        <label class="sima-label">Hari</label>
                        <select name="hari" class="sima-input">
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="sima-label">Jam</label>
                        <input type="text" name="jam" class="sima-input" placeholder="08:00-10:00">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px">
                    <div>
                        <label class="sima-label">Ruangan</label>
                        <input type="text" name="ruangan" class="sima-input" placeholder="Gd.4 R.201">
                    </div>
                    <div>
                        <label class="sima-label">Total Sesi</label>
                        <input type="number" name="totalSesi" class="sima-input" value="16" min="1">
                    </div>
                </div>

                <hr style="border:none;border-top:1px solid #f1f5f9;margin:16px 0">
                <div style="font-size:11.5px;color:#94a3b8;margin-bottom:12px"><i class="fas fa-info-circle"></i> Isi kode — ID dicari otomatis</div>

                {{-- Kelas --}}
                <div style="background:#f8fafc;border:1px solid #f1f5f9;border-radius:12px;padding:14px;margin-bottom:12px">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;margin-bottom:10px">
                        <i class="fas fa-door-open"></i> Kelas
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                        <div>
                            <label class="sima-label">Kode Kelas</label>
                            <input type="text" name="kodeKelas" id="kodeKelas" class="sima-input" placeholder="3KA35" style="font-family:var(--f-mono)">
                        </div>
                        <div>
                            <label class="sima-label">Tahun Ajaran</label>
                            <select name="tahunAjar" id="tahunAjar" class="sima-input">
                                @foreach($tahunAjarList as $ta)
                                    <option value="{{ $ta }}" {{ $ta === $tahunAjarDefault ? 'selected' : '' }}>{{ $ta }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="kelasPreview" style="margin-top:6px;font-size:11.5px;min-height:16px"></div>
                </div>

                {{-- MK --}}
                <div style="background:#f8fafc;border:1px solid #f1f5f9;border-radius:12px;padding:14px;margin-bottom:12px">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;margin-bottom:10px">
                        <i class="fas fa-book"></i> Mata Kuliah
                    </div>
                    <label class="sima-label">Kode MK</label>
                    <input type="text" name="kodeMk" id="kodeMk" class="sima-input" placeholder="IT012236" style="font-family:var(--f-mono)">
                    <div id="mkPreview" style="margin-top:6px;font-size:11.5px;min-height:16px"></div>
                </div>

                {{-- Dosen --}}
                <div style="background:#f8fafc;border:1px solid #f1f5f9;border-radius:12px;padding:14px;margin-bottom:18px">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;margin-bottom:10px">
                        <i class="fas fa-chalkboard-teacher"></i> Dosen
                    </div>
                    <label class="sima-label">Kode Dosen / NIDN</label>
                    <input type="text" name="kodeDos" id="kodeDos" class="sima-input" placeholder="DOS001 atau 0123456789" style="font-family:var(--f-mono)">
                    <div id="dosenPreview" style="margin-top:6px;font-size:11.5px;min-height:16px"></div>
                </div>

                <div id="tambahErr" style="display:none;font-size:12.5px;color:#dc2626;margin-bottom:12px;padding:10px;background:#fef2f2;border-radius:8px"></div>
                <div style="display:flex;gap:8px">
                    <button type="submit" class="sima-btn"><i class="fas fa-save"></i> Simpan</button>
                    <button type="button" onclick="closeModals()" class="sima-btn sima-btn--outline">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════
     MODAL EDIT JADWAL
══════════════════════════════════════ --}}
<div id="modalEdit" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:18px;width:100%;max-width:560px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2);max-height:90vh;overflow-y:auto">
        <div style="padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:#fff;z-index:1">
            <div>
                <div style="font-size:15px;font-weight:700;color:#1e293b">Edit Jadwal</div>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px">Kosongkan field yang tidak ingin diubah</div>
            </div>
            <button type="button" onclick="closeModals()" style="width:32px;height:32px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;cursor:pointer;color:#64748b">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <div style="padding:20px 24px">
            <form id="formEdit">
                <input type="hidden" id="editId">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px">
                    <div>
                        <label class="sima-label">Hari</label>
                        <select name="hari" id="editHari" class="sima-input">
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="sima-label">Jam</label>
                        <input type="text" name="jam" id="editJam" class="sima-input">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px">
                    <div>
                        <label class="sima-label">Ruangan</label>
                        <input type="text" name="ruangan" id="editRuangan" class="sima-input">
                    </div>
                    <div>
                        <label class="sima-label">Total Sesi</label>
                        <input type="number" name="totalSesi" id="editTotalSesi" class="sima-input" min="1">
                    </div>
                </div>

                <hr style="border:none;border-top:1px solid #f1f5f9;margin:16px 0">
                <div style="font-size:11.5px;color:#94a3b8;margin-bottom:12px"><i class="fas fa-info-circle"></i> Kosongkan jika tidak ingin mengubah kelas / MK / dosen</div>

                <div style="background:#f8fafc;border:1px solid #f1f5f9;border-radius:12px;padding:14px;margin-bottom:12px">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;margin-bottom:10px">Kelas (opsional)</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                        <div>
                            <label class="sima-label">Kode Kelas</label>
                            <input type="text" name="kodeKelas" id="editKodeKelas" class="sima-input" style="font-family:var(--f-mono)">
                        </div>
                        <div>
                            <label class="sima-label">Tahun Ajaran</label>
                            <select name="tahunAjar" id="editTahunAjar" class="sima-input">
                                @foreach($tahunAjarList as $ta)
                                    <option value="{{ $ta }}" {{ $ta === $tahunAjarDefault ? 'selected' : '' }}>{{ $ta }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div style="background:#f8fafc;border:1px solid #f1f5f9;border-radius:12px;padding:14px;margin-bottom:12px">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;margin-bottom:10px">Mata Kuliah (opsional)</div>
                    <label class="sima-label">Kode MK</label>
                    <input type="text" name="kodeMk" id="editKodeMk" class="sima-input" style="font-family:var(--f-mono)">
                </div>

                <div style="background:#f8fafc;border:1px solid #f1f5f9;border-radius:12px;padding:14px;margin-bottom:18px">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;margin-bottom:10px">Dosen (opsional)</div>
                    <label class="sima-label">Kode Dosen / NIDN</label>
                    <input type="text" name="kodeDos" id="editKodeDos" class="sima-input" style="font-family:var(--f-mono)">
                </div>

                <div id="editErr" style="display:none;font-size:12.5px;color:#dc2626;margin-bottom:12px;padding:10px;background:#fef2f2;border-radius:8px"></div>
                <div style="display:flex;gap:8px">
                    <button type="submit" class="sima-btn"><i class="fas fa-save"></i> Update</button>
                    <button type="button" onclick="closeModals()" class="sima-btn sima-btn--outline">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('page_js')
<script>
const CSRF = '{{ csrf_token() }}';

['modalTambah','modalEdit'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) { if (e.target === this) closeModals(); });
});
function closeModals() {
    document.getElementById('modalTambah').style.display = 'none';
    document.getElementById('modalEdit').style.display   = 'none';
}

document.getElementById('btnTambah').addEventListener('click', function() {
    document.getElementById('formTambah').reset();
    document.getElementById('tambahErr').style.display = 'none';
    ['kelasPreview','mkPreview','dosenPreview'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.textContent = '';
    });
    document.getElementById('modalTambah').style.display = 'flex';
});

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
            pv.textContent = res.found ? `✓ Ditemukan: Kelas ${res.kodeKelas} (${res.tahunAjar})` : `✗ Kelas '${kode}' tidak ditemukan`;
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
            pv.textContent = res.found ? `✓ Ditemukan: ${res.namaMk} (${res.kodeMk})` : `✗ Kode MK '${kode}' tidak ditemukan`;
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
            pv.textContent = res.found ? `✓ Ditemukan: ${res.nama} (${res.kodeDos ?? res.nidn})` : `✗ Kode/NIDN '${kode}' tidak ditemukan`;
        }).catch(() => {});
}

document.getElementById('kodeKelas').addEventListener('input', debounce(previewKelas, 500));
document.getElementById('tahunAjar').addEventListener('change', debounce(previewKelas, 200));
document.getElementById('kodeMk').addEventListener('input', debounce(previewMk, 500));
document.getElementById('kodeDos').addEventListener('input', debounce(previewDosen, 500));

/* ── CREATE ─────────────────────────────────── */
document.getElementById('formTambah').addEventListener('submit', function(e) {
    e.preventDefault();
    const errDiv = document.getElementById('tambahErr');
    errDiv.style.display = 'none';
    const btn = this.querySelector('[type=submit]');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan…';

    const data = Object.fromEntries(new FormData(this));
    if (data.totalSesi) data.totalSesi = parseInt(data.totalSesi);

    fetch('{{ route("jurusan.jadwal.store") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) { window.location.reload(); }
        else { errDiv.textContent = res.message || 'Gagal menyimpan'; errDiv.style.display = 'block'; }
    })
    .catch(err => { errDiv.textContent = err.message; errDiv.style.display = 'block'; })
    .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Simpan'; });
});

/* ── EDIT ───────────────────────────────────── */
document.querySelectorAll('.btn-edit-jadwal').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        document.getElementById('editErr').style.display = 'none';
        fetch(`/jurusan/jadwal/${id}`, { headers: { Accept: 'application/json' } })
        .then(r => r.json())
        .then(res => {
            const j = res.data ?? res;
            document.getElementById('editId').value         = j.id;
            document.getElementById('editHari').value       = j.hari ?? '';
            document.getElementById('editJam').value        = j.jam ?? '';
            document.getElementById('editRuangan').value    = j.ruangan ?? '';
            document.getElementById('editTotalSesi').value  = j.totalSesi ?? '';
            // placeholder = nilai sekarang, value = kosong (agar tidak dikirim jika tidak diubah)
            const kelasEl = document.getElementById('editKodeKelas');
            kelasEl.placeholder = j.kelas?.kodeKelas ?? '';
            kelasEl.value = '';
            const mkEl = document.getElementById('editKodeMk');
            mkEl.placeholder = j.matakuliah?.kodeMk ?? '';
            mkEl.value = '';
            const dosEl = document.getElementById('editKodeDos');
            dosEl.placeholder = j.dosen?.kodeDos ?? j.dosen?.nidn ?? '';
            dosEl.value = '';
            document.getElementById('modalEdit').style.display = 'flex';
        })
        .catch(err => alert('Gagal memuat: ' + err.message));
    });
});

document.getElementById('formEdit').addEventListener('submit', function(e) {
    e.preventDefault();
    const errDiv = document.getElementById('editErr');
    errDiv.style.display = 'none';
    const id  = document.getElementById('editId').value;
    const btn = this.querySelector('[type=submit]');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan…';

    const data = {};
    ['hari','jam','ruangan','totalSesi','kodeKelas','tahunAjar','kodeMk','kodeDos'].forEach(f => {
        const el = this.querySelector(`[name="${f}"]`);
        if (el && el.value.trim() !== '') data[f] = el.value.trim();
    });
    if (data.totalSesi) data.totalSesi = parseInt(data.totalSesi);

    fetch(`/jurusan/jadwal/${id}`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) { window.location.reload(); }
        else { errDiv.textContent = res.message || 'Gagal update'; errDiv.style.display = 'block'; }
    })
    .catch(err => { errDiv.textContent = err.message; errDiv.style.display = 'block'; })
    .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Update'; });
});

/* ── DELETE ─────────────────────────────────── */
document.querySelectorAll('.btn-del-jadwal').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        if (!confirm('Hapus jadwal ini?')) return;
        fetch(`/jurusan/jadwal/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        })
        .then(r => r.json())
        .then(res => { if (res.success) window.location.reload(); else alert(res.message); })
        .catch(err => alert(err.message));
    });
});
</script>
@endsection
