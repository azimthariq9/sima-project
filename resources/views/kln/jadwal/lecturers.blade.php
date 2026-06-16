@extends('layouts.sima')

@section('page_title',    'Jadwal Lecturers')
@section('page_section',  'JADWAL')
@section('page_subtitle', 'Jadwal yang dibuat oleh admin jurusan')

@section('main_content')

{{-- ── STAT CARD ────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--green">
            <div class="sima-stat__icon sima-stat__icon--green"><i class="fas fa-calendar-alt"></i></div>
            <div class="sima-stat__label">Total Jadwal</div>
            <div class="sima-stat__value">{{ $jadwalList->count() }}</div>
        </div>
    </div>
</div>

{{-- ── TABLE CARD ───────────────────────────────────── --}}
<div class="sima-card">
    <div class="sima-card__header">
        <h5 class="sima-card__title">Jadwal Lecturers</h5>
        <div style="display:flex;gap:8px;align-items:center;">
            <select id="filterHari" class="sima-input" style="width:140px;" onchange="filterJadwal()">
                <option value="">Semua Hari</option>
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                    <option value="{{ $h }}">{{ $h }}</option>
                @endforeach
            </select>
            <input type="text" id="searchJadwal" class="sima-input" style="width:160px;"
                   placeholder="Cari matakuliah / dosen..." oninput="filterJadwal()">
            <button class="sima-btn sima-btn--outline" onclick="resetFilter()">
                <i class="fas fa-redo"></i>
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="sima-table" id="tblJadwal">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Matakuliah</th>
                    <th>Kelas</th>
                    <th>Dosen</th>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Ruangan</th>
                    <th>Sesi</th>
                    <th>Tahun Ajar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwalList as $i => $j)
                <tr data-hari="{{ $j->hari }}"
                    data-nama="{{ strtolower(($j->namaMk ?? '') . ' ' . ($j->namaDosen ?? '')) }}">
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <div class="fw-600">{{ $j->namaMk ?? '-' }}</div>
                        <code style="font-size:11px;color:var(--c-text-3);">{{ $j->kodeMk }}</code>
                    </td>
                    <td>{{ $j->kodeKelas ?? '-' }}</td>
                    <td>{{ $j->namaDosen ?? '-' }}</td>
                    <td>
                        @if($j->hari)
                            <span class="sima-badge sima-badge--green">{{ $j->hari }}</span>
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
                    <td colspan="10" class="text-center text-muted py-4">Belum ada jadwal dari jurusan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div id="emptyMsg" class="text-center text-muted py-4" style="display:none;">
        Tidak ada data yang cocok.
    </div>
</div>

@endsection

@push('page_js')
<script>
function filterJadwal() {
    const hari   = document.getElementById('filterHari').value;
    const search = document.getElementById('searchJadwal').value.toLowerCase();
    let visible  = 0;
    document.querySelectorAll('#tblJadwal tbody tr[data-nama]').forEach(row => {
        const ok = (!hari   || row.dataset.hari === hari)
                && (!search || row.dataset.nama.includes(search));
        row.style.display = ok ? '' : 'none';
        if (ok) visible++;
    });
    document.getElementById('emptyMsg').style.display = visible === 0 ? '' : 'none';
}
function resetFilter() {
    document.getElementById('filterHari').value   = '';
    document.getElementById('searchJadwal').value = '';
    filterJadwal();
}
</script>
@endpush
