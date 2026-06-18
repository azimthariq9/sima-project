@extends('layouts.sima')

@section('page_title',    'Daftar Kehadiran')
@section('page_section',  'KEHADIRAN')
@section('page_subtitle', 'Rekap kehadiran mahasiswa asing per matakuliah')

@section('main_content')

{{-- ── STAT CARDS ───────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="sima-stat sima-stat--blue">
            <div class="sima-stat__icon sima-stat__icon--blue"><i class="fas fa-users"></i></div>
            <div class="sima-stat__label">Total Mahasiswa</div>
            <div class="sima-stat__value">{{ $stats['total'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="sima-stat sima-stat--red">
            <div class="sima-stat__icon sima-stat__icon--red"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="sima-stat__label">Kehadiran &lt; 75%</div>
            <div class="sima-stat__value">{{ $stats['below75'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="sima-stat sima-stat--amber">
            <div class="sima-stat__icon sima-stat__icon--amber"><i class="fas fa-clock"></i></div>
            <div class="sima-stat__label">Belum Ada Data</div>
            <div class="sima-stat__value">{{ $stats['noData'] }}</div>
        </div>
    </div>
</div>

{{-- ── TABLE CARD ───────────────────────────────────── --}}
<div class="sima-card">
    <div class="sima-card__header">
        <h5 class="sima-card__title">Rekap Kehadiran Mahasiswa</h5>
        <div style="display:flex;gap:8px;align-items:center;">
            <form method="GET" action="{{ route('kln.attendance') }}"
                  style="display:flex;gap:6px;align-items:center;">
                <input type="text" name="q" class="sima-input" style="width:200px;"
                       value="{{ $search }}" placeholder="Cari nama / NPM...">
                <button type="submit" class="sima-btn sima-btn--outline">
                    <i class="fas fa-search"></i>
                </button>
                @if($search)
                <a href="{{ route('kln.attendance') }}" class="sima-btn sima-btn--outline">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="sima-table" id="attTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mahasiswa</th>
                    <th style="text-align:center;">Matakuliah</th>
                    <th style="text-align:center;">Hadir</th>
                    <th style="text-align:center;">Absen</th>
                    <th style="text-align:center;">Izin</th>
                    <th style="text-align:center;">Belum</th>
                    <th style="text-align:center;">% Kehadiran</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendanceList as $i => $row)
                @php
                    if ($row->pct === null) {
                        $pctBadge = 'sima-badge--amber';
                        $pctLabel = '—';
                    } elseif ($row->pct >= 80) {
                        $pctBadge = 'sima-badge--green';
                        $pctLabel = $row->pct . '%';
                    } elseif ($row->pct >= 60) {
                        $pctBadge = 'sima-badge--amber';
                        $pctLabel = $row->pct . '%';
                    } else {
                        $pctBadge = 'sima-badge--red';
                        $pctLabel = $row->pct . '%';
                    }
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <div class="fw-600">{{ $row->nama }}</div>
                        <code style="font-size:11px;color:var(--c-text-3);">{{ $row->npm }}</code>
                    </td>
                    <td style="text-align:center;">
                        @if($row->total_jadwal > 0)
                            <span class="sima-badge sima-badge--blue">{{ $row->total_jadwal }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <span class="sima-badge sima-badge--green">{{ $row->hadir }}</span>
                    </td>
                    <td style="text-align:center;">
                        <span class="sima-badge sima-badge--red">{{ $row->absen }}</span>
                    </td>
                    <td style="text-align:center;">
                        <span class="sima-badge sima-badge--amber">{{ $row->izin }}</span>
                    </td>
                    <td style="text-align:center;">
                        <span class="sima-badge" style="background:var(--c-border);color:var(--c-text-2);">
                            {{ $row->belum }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex;flex-direction:column;align-items:center;gap:4px;">
                            <span class="sima-badge {{ $pctBadge }}">{{ $pctLabel }}</span>
                            @if($row->pct !== null)
                            <div style="width:80px;height:4px;background:var(--c-border);border-radius:999px;overflow:hidden;">
                                <div style="height:100%;border-radius:999px;
                                    width:{{ min($row->pct, 100) }}%;
                                    background:{{ $row->pct >= 80 ? 'var(--c-green)' : ($row->pct >= 60 ? 'var(--c-amber)' : 'var(--c-red)') }};">
                                </div>
                            </div>
                            @endif
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('kln.attendance.detail', $row->id) }}"
                           class="sima-btn sima-btn--outline sima-btn--sm">
                            <i class="fas fa-eye me-1"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">
                        <i class="fas fa-users fa-2x d-block mb-2" style="opacity:.3;"></i>
                        @if($search)
                            Tidak ada mahasiswa yang cocok dengan "{{ $search }}".
                        @else
                            Belum ada data mahasiswa.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
