@extends('layouts.sima')

@section('page_title',    'Jadwal BIPA')
@section('page_section',  'JADWAL')
@section('page_subtitle', 'Jadwal yang dibuat oleh program BIPA')

@section('main_content')

{{-- ── STAT CARD ────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="sima-stat sima-stat--blue">
            <div class="sima-stat__icon sima-stat__icon--blue"><i class="fas fa-calendar-alt"></i></div>
            <div class="sima-stat__label">Total Jadwal</div>
            <div class="sima-stat__value">{{ $jadwalList->total() }}</div>
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
                <option value="">Semua Hari</option>
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                    <option value="{{ $h }}" {{ request('hari') === $h ? 'selected' : '' }}>{{ $h }}</option>
                @endforeach
            </select>
            <input type="text" name="search" value="{{ request('search') }}" class="sima-input" style="width:160px;"
                   placeholder="Cari matakuliah / dosen...">
            <button type="submit" class="sima-btn sima-btn--outline"><i class="fas fa-search"></i></button>
            @if(request('hari') || request('search'))
            <a href="{{ route('kln.jadwal.bipa') }}" class="sima-btn sima-btn--outline"><i class="fas fa-times"></i></a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="sima-table">
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
                <tr>
                    <td>{{ $jadwalList->firstItem() + $loop->index }}</td>
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
                    <td colspan="10" class="text-center text-muted py-4">Belum ada jadwal BIPA.</td>
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

@endsection
