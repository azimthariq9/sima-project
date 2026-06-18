@extends('layouts.sima')

@section('page_title',    $jadwal->namaMk)
@section('page_section',  'JADWAL')
@section('page_subtitle', 'Detail jadwal & rekap kehadiran mahasiswa')

@section('main_content')

{{-- ── BACK BUTTON ─────────────────────────────────── --}}
<div class="mb-3">
    <a href="javascript:history.back()" class="sima-btn sima-btn--outline sima-btn--sm">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

{{-- ── JADWAL INFO ──────────────────────────────────── --}}
<div class="sima-card mb-4" style="padding:24px;">
    <h6 class="fw-700 mb-3" style="font-family:var(--f-display);letter-spacing:.5px;">
        <i class="fas fa-calendar-alt me-2" style="color:var(--c-accent);"></i>Informasi Jadwal
    </h6>
    <div class="row g-3">
        <div class="col-12 col-md-6">
            <div class="d-flex flex-column gap-3">
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Matakuliah</div>
                    <div class="fw-600">
                        {{ $jadwal->namaMk ?? '-' }}
                        <code style="font-size:12px;margin-left:6px;">{{ $jadwal->kodeMk }}</code>
                    </div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Jurusan</div>
                    <div class="fw-600">{{ $jadwal->namaJurusan ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">SKS</div>
                    <div class="fw-600">{{ $jadwal->sks ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Kelas</div>
                    <div class="fw-600">{{ $jadwal->kodeKelas ?? '-' }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="d-flex flex-column gap-3">
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Dosen</div>
                    <div class="fw-600">
                        {{ $jadwal->namaDosen ?? '-' }}
                        @if($jadwal->nidn)
                            <span class="text-muted" style="font-size:12px;">· {{ $jadwal->nidn }}</span>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Hari & Jam</div>
                    <div class="fw-600">
                        @if($jadwal->hari)
                            <span class="sima-badge sima-badge--blue me-1">{{ $jadwal->hari }}</span>
                        @endif
                        {{ $jadwal->jam ? str_replace(':', '.', $jadwal->jam) : '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Ruangan</div>
                    <div class="fw-600">{{ $jadwal->ruangan ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.8px;">Total Sesi · Tahun Ajar</div>
                    <div class="fw-600">
                        <span class="sima-badge sima-badge--amber me-1">{{ $jadwal->totalSesi }} sesi</span>
                        {{ $jadwal->tahunAjar ?? '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── KEHADIRAN REKAP ──────────────────────────────── --}}
<div class="sima-card">
    <h6 class="fw-700" style="font-family:var(--f-display);letter-spacing:.5px;padding:24px 24px 0;">
        <i class="fas fa-clipboard-list me-2" style="color:var(--c-accent);"></i>Rekap Kehadiran Mahasiswa
        <span class="sima-badge sima-badge--blue ms-2">{{ $kehadiran->count() }}</span>
    </h6>

    @if($kehadiran->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="fas fa-users fa-2x mb-2 d-block" style="opacity:.3;"></i>
            Belum ada data kehadiran untuk jadwal ini.
        </div>
    @else
    <div class="table-responsive">
        <table class="sima-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Mahasiswa</th>
                    <th>NPM</th>
                    <th style="text-align:center;">Hadir</th>
                    <th style="text-align:center;">Absen</th>
                    <th style="text-align:center;">Izin</th>
                    <th style="text-align:center;">Belum</th>
                    <th style="text-align:center;">% Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kehadiran as $i => $k)
                @php
                    $totalSesi = $jadwal->totalSesi ?: 1;
                    $pct       = round(($k->hadir / $totalSesi) * 100);
                    $pctBadge  = $pct >= 75 ? 'sima-badge--green' : ($pct >= 50 ? 'sima-badge--amber' : 'sima-badge--red');
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="fw-600">{{ $k->nama }}</td>
                    <td><code>{{ $k->npm ?? '-' }}</code></td>
                    <td style="text-align:center;">
                        <span class="sima-badge sima-badge--green">{{ $k->hadir }}</span>
                    </td>
                    <td style="text-align:center;">
                        <span class="sima-badge sima-badge--red">{{ $k->absen }}</span>
                    </td>
                    <td style="text-align:center;">
                        <span class="sima-badge sima-badge--amber">{{ $k->izin }}</span>
                    </td>
                    <td style="text-align:center;">
                        <span class="sima-badge" style="background:var(--c-border);color:var(--c-text-2);">{{ $k->belum_hadir }}</span>
                    </td>
                    <td style="text-align:center;">
                        <span class="sima-badge {{ $pctBadge }}">{{ $pct }}%</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection
