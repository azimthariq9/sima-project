@extends('layouts.sima')

@section('page_title',    'Dashboard Jurusan')
@section('page_section',  'ADMIN JURUSAN')
@section('page_subtitle', 'Pengelolaan akademik — ' . (auth()->user()->jurusan->namaJurusan ?? 'Jurusan'))

@section('main_content')

@if(session('success'))
<div style="background:rgba(5,150,105,.08);border:1px solid rgba(5,150,105,.25);border-radius:10px;
            padding:12px 16px;margin-bottom:16px;font-size:13px;color:#065f46;display:flex;align-items:center;gap:8px">
    <i class="fas fa-circle-check"></i> {{ session('success') }}
</div>
@endif

{{-- ═══════════════════════════════════════════════════
     STAT CARDS
     ═══════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    <div class="col-6 col-md-3 sima-fade sima-fade--1">
        <a href="{{ route('jurusan.mahasiswa.page') }}" class="sima-stat sima-stat--blue d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--blue">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="sima-stat__label">Mahasiswa</div>
            <span class="sima-stat__value">{{ $totalMahasiswa }}</span>
        </a>
    </div>

    <div class="col-6 col-md-3 sima-fade sima-fade--2">
        <a href="{{ route('jurusan.jadwal.page') }}" class="sima-stat sima-stat--teal d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--teal">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="sima-stat__label">Jadwal Aktif</div>
            <span class="sima-stat__value">{{ $jadwalAktif }}</span>
        </a>
    </div>

    <div class="col-6 col-md-3 sima-fade sima-fade--3">
        <a href="{{ route('jurusan.matakuliah.page') }}" class="sima-stat sima-stat--amber d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--amber">
                <i class="fas fa-book"></i>
            </div>
            <div class="sima-stat__label">Mata Kuliah</div>
            <span class="sima-stat__value">{{ $totalMatakuliah }}</span>
        </a>
    </div>

    <div class="col-6 col-md-3 sima-fade sima-fade--4">
        <a href="{{ route('jurusan.dosen.page') }}" class="sima-stat sima-stat--purple d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--purple">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="sima-stat__label">Dosen</div>
            <span class="sima-stat__value">{{ $totalDosen }}</span>
        </a>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════
     ROW 2 — Jadwal Hari Ini + Request Terbaru
     ═══════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- ── JADWAL HARI INI ──────────────────────── --}}
    <div class="col-md-7 sima-fade sima-fade--5">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Jadwal Hari Ini</h5>
                    <div class="sima-card__subtitle">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</div>
                </div>
                <a href="{{ route('jurusan.jadwal.page') }}" class="sima-card__action">
                    <i class="fas fa-calendar-alt"></i> Lihat Semua
                </a>
            </div>
            <div style="overflow-x:auto">
                @if($jadwalHariIni->isEmpty())
                    <div class="sima-card__body" style="text-align:center;padding:36px 20px;color:var(--c-text-3)">
                        <i class="fas fa-calendar-xmark" style="font-size:28px;opacity:.3;display:block;margin-bottom:10px"></i>
                        <div style="font-size:13.5px;font-weight:500;color:var(--c-text-2)">Tidak ada jadwal hari ini</div>
                    </div>
                @else
                    <table class="sima-table">
                        <thead>
                            <tr>
                                <th>Mata Kuliah</th>
                                <th>Jam</th>
                                <th>Ruang</th>
                                <th>Dosen</th>
                                <th>Kelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalHariIni as $j)
                            <tr>
                                <td style="font-weight:600">{{ $j->nama_matkul ?? '—' }}</td>
                                <td style="font-family:var(--f-mono);font-size:12px;color:var(--c-text-3);white-space:nowrap">{{ $j->jam }}</td>
                                <td style="font-size:12px;color:var(--c-text-2)">{{ $j->ruangan ?? '—' }}</td>
                                <td style="font-size:12.5px">{{ $j->nama_dosen ?? '—' }}</td>
                                <td>
                                    <span style="font-family:var(--f-mono);font-size:11px;font-weight:700;
                                                 color:var(--c-accent);background:rgba(var(--c-accent-rgb),.08);
                                                 padding:2px 7px;border-radius:5px">
                                        {{ $j->kodeKelas ?? '—' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    {{-- ── REQUEST DOKUMEN TERBARU ──────────────── --}}
    <div class="col-md-5 sima-fade sima-fade--6">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Request Dokumen</h5>
                    <div class="sima-card__subtitle">
                        {{ $pendingRequest }} request pending
                    </div>
                </div>
                <a href="{{ route('jurusan.request.index') }}" class="sima-card__action">Semua</a>
            </div>
            <div class="sima-card__body" style="padding:0">
                @forelse($requestTerbaru as $req)
                @php
                    $stMap = [
                        'pending'  => ['cls'=>'blue',  'label'=>'Pending'],
                        'approved' => ['cls'=>'green', 'label'=>'Disetujui'],
                        'rejected' => ['cls'=>'red',   'label'=>'Ditolak'],
                    ];
                    $st = $stMap[$req->status] ?? $stMap['pending'];
                @endphp
                <div style="display:flex;align-items:center;gap:12px;padding:11px 16px;border-bottom:1px solid var(--c-border-soft)">
                    <div style="flex:1;min-width:0">
                        <div style="font-size:13px;font-weight:600;color:var(--c-text-1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            {{ $req->nama_mahasiswa }}
                        </div>
                        <div style="font-size:11.5px;color:var(--c-text-3)">
                            {{ $req->npm }} · {{ str_replace('_', ' ', $req->tipeDkmn ?? '-') }}
                        </div>
                    </div>
                    <span class="sima-badge sima-badge--{{ $st['cls'] }}">{{ $st['label'] }}</span>
                </div>
                @empty
                    <div style="text-align:center;padding:32px 20px;color:var(--c-text-3);font-size:13px">
                        Belum ada request
                    </div>
                @endforelse
                <div style="padding:12px 16px">
                    <a href="{{ route('jurusan.request.index') }}"
                       class="sima-btn sima-btn--outline sima-btn--sm sima-btn--full">
                        <i class="fas fa-list"></i> Lihat Semua Request
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════
     ROW 3 — Jadwal Minggu Ini + Pengumuman
     ═══════════════════════════════════════════════════ --}}
<div class="row g-3">

    {{-- ── JADWAL MINGGU INI ────────────────────── --}}
    <div class="col-md-6 sima-fade sima-fade--5">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Jadwal Mingguan</h5>
                    <div class="sima-card__subtitle">Semua jadwal berulang per minggu</div>
                </div>
                <a href="{{ route('jurusan.jadwal.page') }}" class="sima-card__action">
                    <i class="fas fa-plus"></i> Tambah
                </a>
            </div>
            <div class="sima-card__body" style="padding:0">
                @if($jadwalMingguIni->isEmpty())
                    <div style="text-align:center;padding:36px 20px;color:var(--c-text-3)">
                        <i class="fas fa-calendar-plus" style="font-size:28px;opacity:.3;display:block;margin-bottom:10px"></i>
                        <div style="font-size:13.5px;font-weight:500;color:var(--c-text-2)">Belum ada jadwal terdaftar</div>
                    </div>
                @else
                @php
                    $dayColors = [
                        'Senin'=>'#2563EB','Selasa'=>'#0D9488','Rabu'=>'#7C3AED',
                        'Kamis'=>'#D97706','Jumat'=>'#DC2626','Sabtu'=>'#059669','Minggu'=>'#6B7280'
                    ];
                @endphp
                @foreach($jadwalMingguIni as $j)
                @php $clr = $dayColors[$j->hari] ?? '#6B7280'; @endphp
                <div style="display:flex;align-items:center;gap:12px;padding:10px 16px;
                            border-bottom:1px solid var(--c-border-soft)">
                    <div style="min-width:54px;padding:4px 0;text-align:center;
                                background:{{ $clr }}18;border-radius:8px;border:1px solid {{ $clr }}30">
                        <div style="font-size:9px;font-weight:700;letter-spacing:.08em;
                                    text-transform:uppercase;color:{{ $clr }}">
                            {{ $j->hari }}
                        </div>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:13px;font-weight:600;color:var(--c-text-1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            {{ $j->nama_matkul ?? '—' }}
                        </div>
                        <div style="font-size:11px;color:var(--c-text-3)">
                            {{ $j->kode_kelas ?? '—' }} · {{ $j->ruangan ?? '—' }}
                        </div>
                    </div>
                    <div style="font-family:var(--f-mono);font-size:11px;color:var(--c-text-3);white-space:nowrap">
                        {{ $j->jam }}
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- ── PENGUMUMAN JURUSAN ───────────────────── --}}
    <div class="col-md-6 sima-fade sima-fade--6">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Pengumuman Jurusan</h5>
                    <div class="sima-card__subtitle">3 pengumuman terbaru</div>
                </div>
                <a href="{{ route('jurusan.announcement.index') }}" class="sima-card__action">
                    <i class="fas fa-plus"></i> Buat Baru
                </a>
            </div>
            <div class="sima-card__body" style="padding:0">
                @forelse($announcements as $ann)
                @php $isPenting = (bool)($ann->is_penting ?? false); @endphp
                <div style="padding:14px 16px;border-bottom:1px solid var(--c-border-soft);
                            {{ $isPenting ? 'border-left:3px solid var(--c-accent)' : '' }}">
                    <div style="display:flex;align-items:flex-start;gap:10px">
                        <div style="flex:1;min-width:0">
                            <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px">
                                @if($isPenting)<i class="fas fa-thumbtack" style="font-size:10px;color:var(--c-accent)"></i>@endif
                                <div style="font-size:13px;font-weight:600;color:var(--c-text-1);
                                            white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                    {{ $ann->subject }}
                                </div>
                            </div>
                            <div style="font-size:12px;color:var(--c-text-3)">
                                {{ \Carbon\Carbon::parse($ann->created_at)->diffForHumans() }}
                            </div>
                        </div>
                        <span class="sima-badge {{ $ann->status === 'active' ? 'sima-badge--green' : 'sima-badge--grey' }}" style="font-size:10px;flex-shrink:0">
                            {{ $ann->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>
                @empty
                    <div style="text-align:center;padding:36px 20px;color:var(--c-text-3);font-size:13px">
                        Belum ada pengumuman. <a href="{{ route('jurusan.announcement.index') }}" style="color:var(--c-accent)">Buat sekarang →</a>
                    </div>
                @endforelse
                @if($announcements->count() > 0)
                <div style="padding:12px 16px">
                    <a href="{{ route('jurusan.announcement.index') }}"
                       class="sima-btn sima-btn--outline sima-btn--sm sima-btn--full">
                        <i class="fas fa-bullhorn"></i> Kelola Pengumuman
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection

@section('page_js')
<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50%       { opacity: .6; }
}
</style>
@endsection
