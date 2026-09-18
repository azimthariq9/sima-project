@extends('layouts.sima')

@section('page_title',    'Jadwal BIPA')
@section('page_section',  'JADWAL')
@section('page_subtitle', 'Schedules created by the BIPA program')

@section('main_content')

{{-- ── STAT CARD ────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--blue">
            <div class="sima-stat__icon sima-stat__icon--blue"><i class="fas fa-calendar-alt"></i></div>
            <div class="sima-stat__label">Total Schedules</div>
            <div class="sima-stat__value">{{ $jadwalList->count() }}</div>
        </div>
    </div>
</div>

{{-- ── TABLE CARD ───────────────────────────────────── --}}
<div class="sima-card">
    <div class="sima-card__header">
        <h5 class="sima-card__title">Jadwal BIPA</h5>
        <form method="GET" action="{{ route('kln.jadwal.bipa') }}"
              style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <select name="hari" class="sima-input" style="width:140px;" onchange="this.form.submit()">
                <option value="">All Days</option>
                @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $h)
                    <option value="{{ $h }}" {{ request('hari') === $h ? 'selected' : '' }}>{{ $h }}</option>
                @endforeach
            </select>
            @if(request('hari'))
            <a href="{{ route('kln.jadwal.bipa') }}" class="sima-btn sima-btn--outline"><i class="fas fa-times"></i></a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="sima-table" data-datatable>
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Class</th>
                    <th>Lecturer</th>
                    <th>Day</th>
                    <th>Time</th>
                    <th>Room</th>
                    <th>Sessions</th>
                    <th>Academic Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwalList as $j)
                <tr>
                    <td>
                        <div class="fw-600">{{ $j->namaMk ?? '-' }}</div>
                        <code style="font-size:11px;color:var(--c-text-3);">{{ $j->kodeMk }}</code>
                    </td>
                    <td>{{ $j->kodeKelas ?? '-' }}</td>
                    <td>{{ $j->namaDosen ?? '-' }}</td>
                    <td>
                        @if($j->hari)
                            <span class="sima-badge sima-badge--blue">{{ $j->hari }}</span>
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
                    <td colspan="9" class="text-center text-muted py-4">No BIPA schedules yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
