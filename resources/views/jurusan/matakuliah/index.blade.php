@extends('layouts.sima')

@section('page_title',    'Mata Kuliah')
@section('page_section',  'ADMIN JURUSAN')
@section('page_subtitle', 'Pengelolaan mata kuliah di jurusan Anda')

@section('main_content')

<div class="sima-card sima-fade">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Daftar Mata Kuliah</h5>
            <div class="sima-card__subtitle">Total {{ $matakuliah->total() }} mata kuliah</div>
        </div>
        <button type="button" id="btnTambah" class="sima-btn sima-btn--sm">
            <i class="fas fa-plus"></i> Tambah MK
        </button>
    </div>

    {{-- Filter --}}
    <div style="padding:12px 20px;border-bottom:1px solid var(--c-border-soft)">
        <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / kode MK…"
                   class="sima-input" style="width:280px;font-size:13px">
            <button type="submit" class="sima-btn sima-btn--sm sima-btn--outline">
                <i class="fas fa-search"></i> Cari
            </button>
            @if($search)
                <a href="{{ route('jurusan.matakuliah.page') }}" class="sima-btn sima-btn--sm sima-btn--outline">
                    <i class="fas fa-xmark"></i> Reset
                </a>
            @endif
        </form>
    </div>

    @if($matakuliah->isEmpty())
        <div class="sima-card__body" style="text-align:center;padding:48px 20px;color:var(--c-text-3)">
            <i class="fas fa-book-open" style="font-size:36px;opacity:.3;display:block;margin-bottom:12px"></i>
            <div style="font-size:14px;font-weight:500;color:var(--c-text-2)">
                {{ $search ? 'Tidak ada MK yang cocok' : 'Belum ada mata kuliah' }}
            </div>
        </div>
    @else
        <div style="overflow-x:auto">
            <table class="sima-table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th style="width:110px">Kode MK</th>
                        <th>Nama MK</th>
                        <th style="width:60px;text-align:center">SKS</th>
                        <th style="width:80px">Ket.</th>
                        <th style="width:100px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($matakuliah as $mk)
                    <tr>
                        <td style="color:var(--c-text-3);font-size:12px">
                            {{ $matakuliah->firstItem() + $loop->index }}
                        </td>
                        <td>
                            <span style="font-family:var(--f-mono);font-size:12px;background:var(--c-blue-lt);color:var(--c-blue);padding:3px 8px;border-radius:6px">
                                {{ $mk->kodeMk ?? '—' }}
                            </span>
                        </td>
                        <td style="font-weight:600;font-size:13.5px">{{ $mk->namaMk ?? '—' }}</td>
                        <td style="text-align:center;font-family:var(--f-mono);font-size:13px">{{ $mk->sks ?? '—' }}</td>
                        <td style="font-size:12px;color:var(--c-text-3)">{{ $mk->keterangan ?? '—' }}</td>
                        <td>
                            <div style="display:flex;gap:6px">
                                <button type="button" class="sima-btn sima-btn--sm sima-btn--outline btn-edit-mk"
                                        data-id="{{ $mk->id }}"
                                        style="font-size:11.5px;padding:4px 10px">
                                    <i class="fas fa-pencil"></i>
                                </button>
                                <button type="button" class="sima-btn sima-btn--sm btn-del-mk"
                                        data-id="{{ $mk->id }}" data-nama="{{ $mk->namaMk }}"
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
            {{ $matakuliah->links('vendor.pagination.sima') }}
        </div>
    @endif
</div>

{{-- MODAL TAMBAH --}}
<div id="modalTambah" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:18px;width:100%;max-width:460px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2)">
        <div style="padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
            <div style="font-size:15px;font-weight:700;color:#1e293b">Tambah Mata Kuliah</div>
            <button type="button" onclick="closeModals()" style="width:32px;height:32px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;cursor:pointer;color:#64748b">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div style="padding:20px 24px">
            <form id="formTambah">
                <div style="margin-bottom:14px">
                    <label class="sima-label">Kode MK</label>
                    <input type="text" name="kodeMk" class="sima-input" placeholder="Contoh: IT012236">
                </div>
                <div style="margin-bottom:14px">
                    <label class="sima-label">Nama Mata Kuliah <span style="color:var(--c-red)">*</span></label>
                    <input type="text" name="namaMk" class="sima-input" required placeholder="Contoh: Struktur Data">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:18px">
                    <div>
                        <label class="sima-label">SKS</label>
                        <input type="number" name="sks" class="sima-input" min="1" max="6">
                    </div>
                    <div>
                        <label class="sima-label">Keterangan</label>
                        <input type="text" name="keterangan" class="sima-input" maxlength="5" placeholder="Maks 5 karakter">
                    </div>
                </div>
                <div id="tambahErr" style="display:none;font-size:12.5px;color:#dc2626;margin-bottom:12px;padding:10px;background:#fef2f2;border-radius:8px"></div>
                <div style="display:flex;gap:8px">
                    <button type="submit" class="sima-btn"><i class="fas fa-plus"></i> Simpan</button>
                    <button type="button" onclick="closeModals()" class="sima-btn sima-btn--outline">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="modalEdit" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:18px;width:100%;max-width:460px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2)">
        <div style="padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
            <div style="font-size:15px;font-weight:700;color:#1e293b">Edit Mata Kuliah</div>
            <button type="button" onclick="closeModals()" style="width:32px;height:32px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;cursor:pointer;color:#64748b">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div style="padding:20px 24px">
            <form id="formEdit">
                <input type="hidden" id="editId">
                <div style="margin-bottom:14px">
                    <label class="sima-label">Kode MK</label>
                    <input type="text" name="kodeMk" id="editKodeMk" class="sima-input">
                </div>
                <div style="margin-bottom:14px">
                    <label class="sima-label">Nama Mata Kuliah <span style="color:var(--c-red)">*</span></label>
                    <input type="text" name="namaMk" id="editNamaMk" class="sima-input" required>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:18px">
                    <div>
                        <label class="sima-label">SKS</label>
                        <input type="number" name="sks" id="editSks" class="sima-input" min="1" max="6">
                    </div>
                    <div>
                        <label class="sima-label">Keterangan</label>
                        <input type="text" name="keterangan" id="editKeterangan" class="sima-input" maxlength="5">
                    </div>
                </div>
                <div id="editErr" style="display:none;font-size:12.5px;color:#dc2626;margin-bottom:12px;padding:10px;background:#fef2f2;border-radius:8px"></div>
                <div style="display:flex;gap:8px">
                    <button type="submit" class="sima-btn"><i class="fas fa-save"></i> Simpan</button>
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
    document.getElementById('modalTambah').style.display = 'flex';
});

document.getElementById('formTambah').addEventListener('submit', function(e) {
    e.preventDefault();
    const errDiv = document.getElementById('tambahErr');
    errDiv.style.display = 'none';
    const btn = this.querySelector('[type=submit]'); btn.disabled = true;
    fetch("{{ route('jurusan.matakuliah.store') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(Object.fromEntries(new FormData(this))),
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) { window.location.reload(); }
        else {
            errDiv.textContent = res.errors ? Object.values(res.errors).flat().join(' ') : (res.message || 'Gagal');
            errDiv.style.display = 'block';
        }
    })
    .catch(err => { errDiv.textContent = err.message; errDiv.style.display = 'block'; })
    .finally(() => { btn.disabled = false; });
});

document.querySelectorAll('.btn-edit-mk').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        document.getElementById('editErr').style.display = 'none';
        fetch(`/jurusan/matakuliah/${id}`, { headers: { Accept: 'application/json' } })
        .then(r => r.json())
        .then(res => {
            const mk = res.data ?? res;
            document.getElementById('editId').value           = mk.id;
            document.getElementById('editKodeMk').value       = mk.kodeMk ?? '';
            document.getElementById('editNamaMk').value       = mk.namaMk ?? '';
            document.getElementById('editSks').value          = mk.sks ?? '';
            document.getElementById('editKeterangan').value   = mk.keterangan ?? '';
            document.getElementById('modalEdit').style.display = 'flex';
        });
    });
});

document.getElementById('formEdit').addEventListener('submit', function(e) {
    e.preventDefault();
    const errDiv = document.getElementById('editErr');
    errDiv.style.display = 'none';
    const id = document.getElementById('editId').value;
    const btn = this.querySelector('[type=submit]'); btn.disabled = true;
    fetch(`/jurusan/matakuliah/${id}`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(Object.fromEntries(new FormData(this))),
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) { window.location.reload(); }
        else { errDiv.textContent = res.message || 'Gagal'; errDiv.style.display = 'block'; }
    })
    .catch(err => { errDiv.textContent = err.message; errDiv.style.display = 'block'; })
    .finally(() => { btn.disabled = false; });
});

document.querySelectorAll('.btn-del-mk').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id; const nama = this.dataset.nama;
        if (!confirm(`Hapus mata kuliah "${nama}"?`)) return;
        fetch(`/jurusan/matakuliah/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        })
        .then(r => r.json())
        .then(res => { if (res.success) window.location.reload(); else alert(res.message); });
    });
});
</script>
@endsection
