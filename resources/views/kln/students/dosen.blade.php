@extends('layouts.sima')

@section('page_title',    $dosen->nama)
@section('page_section',  'STUDENTS & LECTURERS')
@section('page_subtitle', 'Detail data dosen')

@section('main_content')

{{-- ── BACK BUTTON ─────────────────────────────────── --}}
<div class="mb-3">
    <a href="{{ route('kln.students.page') }}" class="sima-btn sima-btn--outline sima-btn--sm">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

{{-- ── PROFILE HEADER ──────────────────────────────── --}}
<div class="sima-card mb-4" style="padding: 24px;">
    <div class="d-flex align-items-center gap-4 flex-wrap">
        <div style="width:72px;height:72px;border-radius:50%;background:var(--c-accent);
                    display:flex;align-items:center;justify-content:center;
                    font-size:28px;font-weight:700;color:#fff;flex-shrink:0;">
            {{ strtoupper(substr($dosen->nama, 0, 1)) }}
        </div>
        <div>
            <h4 class="mb-1" style="font-family:var(--f-display);font-weight:700;">{{ $dosen->nama }}</h4>
            <div class="d-flex gap-2 flex-wrap">
                <span class="sima-badge sima-badge--purple">Dosen</span>
                <span class="sima-badge {{ $dosen->status === 'active' ? 'sima-badge--green' : 'sima-badge--red' }}">
                    {{ $dosen->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                </span>
                @if($dosen->namaJurusan)
                    <span class="sima-badge sima-badge--amber">{{ $dosen->namaJurusan }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── INFO ────────────────────────────────────────── --}}
<div class="sima-card" style="padding: 24px;">
    <h6 class="fw-700 mb-3" style="font-family:var(--f-display);letter-spacing:.5px;">
        <i class="fas fa-id-card me-2" style="color:var(--c-accent);"></i>Informasi Dosen
    </h6>
    <div class="row g-4">
        <div class="col-12 col-md-6">
            <div class="d-flex flex-column gap-3">
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">NIDN</div>
                    <div class="fw-600">{{ $dosen->nidn ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Kode Dosen</div>
                    <div class="fw-600">{{ $dosen->kodeDos ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Email</div>
                    <div class="fw-600">{{ $dosen->email ?? '-' }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="d-flex flex-column gap-3">
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Jurusan</div>
                    <div class="fw-600">{{ $dosen->namaJurusan ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Status</div>
                    <div class="fw-600">
                        <span class="sima-badge {{ $dosen->status === 'active' ? 'sima-badge--green' : 'sima-badge--red' }}">
                            {{ $dosen->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Terdaftar</div>
                    <div class="fw-600">
                        {{ $dosen->created_at ? \Carbon\Carbon::parse($dosen->created_at)->translatedFormat('d F Y') : '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
