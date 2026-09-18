@extends('layouts.sima')

@section('page_title',    'Attendance List')
@section('page_section',  'KEHADIRAN')
@section('page_subtitle', 'Attendance summary for foreign students per course')

@section('main_content')

{{-- ── STAT CARDS ───────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="sima-stat sima-stat--blue">
            <div class="sima-stat__icon sima-stat__icon--blue"><i class="fas fa-users"></i></div>
            <div class="sima-stat__label">Total Students</div>
            <div class="sima-stat__value">{{ $stats['total'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="sima-stat sima-stat--red">
            <div class="sima-stat__icon sima-stat__icon--red"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="sima-stat__label">Attendance &lt; 75%</div>
            <div class="sima-stat__value">{{ $stats['below75'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="sima-stat sima-stat--amber">
            <div class="sima-stat__icon sima-stat__icon--amber"><i class="fas fa-clock"></i></div>
            <div class="sima-stat__label">No Data Yet</div>
            <div class="sima-stat__value">{{ $stats['noData'] }}</div>
        </div>
    </div>
</div>

{{-- ── TABLE CARD ───────────────────────────────────── --}}
<div class="sima-card">
    <div class="sima-card__header">
        <h5 class="sima-card__title">Student Attendance Recap</h5>
    </div>

    <div class="table-responsive">
        <table class="sima-table" data-datatable>
            <thead>
                <tr>
                    <th>Student</th>
                    <th style="text-align:center;">Course</th>
                    <th style="text-align:center;">Present</th>
                    <th style="text-align:center;">Absent</th>
                    <th style="text-align:center;">Excused</th>
                    <th style="text-align:center;">Pending</th>
                    <th style="text-align:center;">% Attendance</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendanceList as $row)
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
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="fas fa-users fa-2x d-block mb-2" style="opacity:.3;"></i>
                        @if($search)
                            No matching students.
                        @else
                            No student data yet.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
