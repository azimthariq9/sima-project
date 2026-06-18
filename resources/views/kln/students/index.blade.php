@extends('layouts.sima')

@section('page_title',    'Students & Lecturers')
@section('page_section',  'KERJA SAMA LUAR NEGERI')
@section('page_subtitle', 'Data mahasiswa dan dosen per jurusan')

@section('main_content')

{{-- ── STAT CARDS ─────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="sima-stat sima-stat--blue">
            <div class="sima-stat__icon sima-stat__icon--blue"><i class="fas fa-user-graduate"></i></div>
            <div class="sima-stat__label">Total Mahasiswa</div>
            <div class="sima-stat__value">{{ $totalMahasiswa }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="sima-stat sima-stat--green">
            <div class="sima-stat__icon sima-stat__icon--green"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="sima-stat__label">Total Dosen</div>
            <div class="sima-stat__value">{{ $totalDosen }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="sima-stat sima-stat--purple">
            <div class="sima-stat__icon sima-stat__icon--purple"><i class="fas fa-university"></i></div>
            <div class="sima-stat__label">Jurusan Aktif</div>
            <div class="sima-stat__value">{{ $totalJurusan }}</div>
        </div>
    </div>
</div>

{{-- ── MAHASISWA TABLE ──────────────────────────────── --}}
<div class="sima-card mb-4">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Mahasiswa</h5>
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
            <select id="filterJurusanM" class="sima-input" style="width:160px;" onchange="filterTable('m')">
                <option value="">Semua Jurusan</option>
                @foreach($jurusan as $j)
                    <option value="{{ $j->id }}">{{ $j->namaJurusan }}</option>
                @endforeach
            </select>
            <select id="filterDokM" class="sima-input" style="width:150px;" onchange="filterTable('m')">
                <option value="">Semua Status Dok</option>
                <option value="expired">Expired</option>
                <option value="warning">Warning</option>
                <option value="aman">Aman</option>
            </select>
            <input type="text" id="searchM" class="sima-input" style="width:160px;"
                   placeholder="Cari nama..." oninput="filterTable('m')">
            <button class="sima-btn sima-btn--outline" onclick="resetFilter('m')">
                <i class="fas fa-redo"></i>
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="sima-table" id="tblMahasiswa">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>NPM</th>
                    <th>Jurusan</th>
                    <th>Akun</th>
                    <th>Status Dokumen</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mahasiswaList as $idx => $m)
                @php
                    $docLevel = $m->doc_expiry_level;
                    if ($docLevel === null) {
                        $docBadge = 'sima-badge--amber';
                        $docLabel = 'Tidak Ada';
                        $docKey   = '';
                    } elseif ($docLevel == 1) {
                        $docBadge = 'sima-badge--red';
                        $docLabel = 'Expired';
                        $docKey   = 'expired';
                    } elseif ($docLevel == 2) {
                        $docBadge = 'sima-badge--amber';
                        $docLabel = 'Warning';
                        $docKey   = 'warning';
                    } else {
                        $docBadge = 'sima-badge--green';
                        $docLabel = 'Aman';
                        $docKey   = 'aman';
                    }
                    $akunBadge = $m->status === 'active' ? 'sima-badge--green' : 'sima-badge--red';
                    $akunLabel = $m->status === 'active' ? 'Aktif' : 'Nonaktif';
                @endphp
                <tr data-jurusan="{{ $m->jurusan_id ?? '' }}"
                    data-dok="{{ $docKey }}"
                    data-nama="{{ strtolower($m->nama) }}">
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $m->nama }}</td>
                    <td><span style="font-family:var(--f-mono);font-size:13px;">{{ $m->identifier ?? '-' }}</span></td>
                    <td>{{ $m->namaJurusan ?? '-' }}</td>
                    <td><span class="sima-badge {{ $akunBadge }}">{{ $akunLabel }}</span></td>
                    <td>
                        <span class="sima-badge {{ $docBadge }}">
                            @if($docLevel == 1)<i class="fas fa-exclamation-circle me-1"></i>
                            @elseif($docLevel == 2)<i class="fas fa-exclamation-triangle me-1"></i>
                            @elseif($docLevel == 3)<i class="fas fa-check-circle me-1"></i>
                            @endif
                            {{ $docLabel }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('kln.students.mahasiswa', $m->id) }}"
                           class="sima-btn sima-btn--outline sima-btn--sm">
                            <i class="fas fa-eye me-1"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data mahasiswa.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div id="emptyM" class="text-center text-muted py-4" style="display:none;">Tidak ada data yang cocok.</div>
</div>

{{-- ── DOSEN TABLE ──────────────────────────────────── --}}
<div class="sima-card">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Dosen</h5>
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
            <select id="filterJurusanD" class="sima-input" style="width:160px;" onchange="filterTable('d')">
                <option value="">Semua Jurusan</option>
                @foreach($jurusan as $j)
                    <option value="{{ $j->id }}">{{ $j->namaJurusan }}</option>
                @endforeach
            </select>
            <input type="text" id="searchD" class="sima-input" style="width:160px;"
                   placeholder="Cari nama..." oninput="filterTable('d')">
            <button class="sima-btn sima-btn--outline" onclick="resetFilter('d')">
                <i class="fas fa-redo"></i>
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="sima-table" id="tblDosen">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>NIDN</th>
                    <th>Jurusan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dosenList as $idx => $d)
                @php
                    $badgeSts = $d->status === 'active' ? 'sima-badge--green' : 'sima-badge--red';
                    $labelSts = $d->status === 'active' ? 'Aktif' : 'Nonaktif';
                @endphp
                <tr data-jurusan="{{ $d->jurusan_id ?? '' }}"
                    data-nama="{{ strtolower($d->nama) }}">
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $d->nama }}</td>
<td><span style="font-family:var(--f-mono);font-size:13px;">{{ $d->identifier ?? '-' }}</span></td>
                    <td>{{ $d->namaJurusan ?? '-' }}</td>
                    <td><span class="sima-badge {{ $badgeSts }}">{{ $labelSts }}</span></td>
                    <td>
                        <a href="{{ route('kln.students.dosen', $d->id) }}"
                           class="sima-btn sima-btn--outline sima-btn--sm">
                            <i class="fas fa-eye me-1"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data dosen.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div id="emptyD" class="text-center text-muted py-4" style="display:none;">Tidak ada data yang cocok.</div>
</div>

@endsection

@push('page_js')
<script>
function filterTable(which) {
    if (which === 'm') {
        const jurusan = document.getElementById('filterJurusanM').value;
        const dok     = document.getElementById('filterDokM').value;
        const search  = document.getElementById('searchM').value.toLowerCase();
        let visible   = 0;
        document.querySelectorAll('#tblMahasiswa tbody tr[data-nama]').forEach(row => {
            const ok = (!jurusan || row.dataset.jurusan === jurusan)
                    && (!dok     || row.dataset.dok      === dok)
                    && (!search  || row.dataset.nama.includes(search));
            row.style.display = ok ? '' : 'none';
            if (ok) visible++;
        });
        document.getElementById('emptyM').style.display = visible === 0 ? '' : 'none';
    } else {
        const jurusan = document.getElementById('filterJurusanD').value;
        const search  = document.getElementById('searchD').value.toLowerCase();
        let visible   = 0;
        document.querySelectorAll('#tblDosen tbody tr[data-nama]').forEach(row => {
            const ok = (!jurusan || row.dataset.jurusan === jurusan)
                    && (!search  || row.dataset.nama.includes(search));
            row.style.display = ok ? '' : 'none';
            if (ok) visible++;
        });
        document.getElementById('emptyD').style.display = visible === 0 ? '' : 'none';
    }
}

function resetFilter(which) {
    if (which === 'm') {
        document.getElementById('filterJurusanM').value = '';
        document.getElementById('filterDokM').value     = '';
        document.getElementById('searchM').value        = '';
    } else {
        document.getElementById('filterJurusanD').value = '';
        document.getElementById('searchD').value        = '';
    }
    filterTable(which);
}
</script>
@endpush
