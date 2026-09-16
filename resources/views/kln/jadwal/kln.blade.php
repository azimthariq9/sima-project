@extends('layouts.sima')

@section('page_title',    'Jadwal KLN')
@section('page_section',  'JADWAL')
@section('page_subtitle', 'Schedules created by KLN')

@section('main_content')

{{-- ── STAT CARD ────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--purple">
            <div class="sima-stat__icon sima-stat__icon--purple"><i class="fas fa-calendar-alt"></i></div>
            <div class="sima-stat__label">Total Jadwal</div>
            <div class="sima-stat__value">{{ $jadwalList->count() }}</div>
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
                    <option value="">All Days</option>
                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $h)
                        <option value="{{ $h }}" {{ request('hari') === $h ? 'selected' : '' }}>{{ $h }}</option>
                    @endforeach
                </select>
                @if(request('hari'))
                <a href="{{ route('kln.jadwal.kln') }}" class="sima-btn sima-btn--outline"><i class="fas fa-times"></i></a>
                @endif
            </form>
            <a href="{{ route('kln.jadwal.create') }}" class="sima-btn sima-btn--accent">
                <i class="fas fa-plus me-1"></i> Add Schedule
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="sima-table" data-datatable>
            <thead>
                <tr>
                    <th>Activity</th>
                    <th>Class</th>
                    <th>Day</th>
                    <th>Time</th>
                    <th>Location</th>
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
                    <td colspan="8" class="text-center text-muted py-4">No KLN schedules yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('page_js')
<script>
// Flash message on redirect
const params = new URLSearchParams(window.location.search);
if (params.has('created')) {
    const toast = document.createElement('div');
    toast.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;padding:14px 20px;background:rgba(5,150,105,.95);color:#fff;border-radius:10px;font-size:13px;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,.2);';
    toast.innerHTML = '<i class="fas fa-check-circle me-2"></i> Schedule created successfully.';
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
    history.replaceState(null, '', window.location.pathname);
}
</script>
@endpush
