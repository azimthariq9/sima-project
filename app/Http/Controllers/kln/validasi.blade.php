@extends('layouts.sima')

@section('page_title',    'Validasi Dokumen')
@section('page_section',  'KLN')
@section('page_subtitle', 'Review & verifikasi dokumen mahasiswa asing')

@section('main_content')

{{-- Filter bar --}}
<div class="sima-card sima-fade mb-4" style="padding:14px 20px">
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            @foreach(['Semua'=>'','Pending'=>'pending','Disetujui'=>'approved','Ditolak'=>'rejected'] as $label => $val)
            <a href="{{ route('kln.validasi', ['status' => $val]) }}"
               class="sima-badge {{ (request('status','') === $val) ? 'sima-badge--blue' : 'sima-badge--grey' }}"
               style="padding:6px 14px;cursor:pointer;font-size:12.5px">
                {{ $label }}
                @if($label === 'Pending')
                    <span style="background:var(--c-amber);color:#fff;border-radius:100px;padding:0 6px;font-size:10px;margin-left:3px">
                        {{ $pendingDocs ?? 8 }}
                    </span>
                @endif
            </a>
            @endforeach
        </div>
        <div style="margin-left:auto;display:flex;gap:8px">
            <div style="position:relative">
                <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--c-text-3);font-size:12px"></i>
                <input class="sima-input" placeholder="Cari nama mahasiswa…" style="padding-left:34px;width:220px;font-size:13px">
            </div>
        </div>
    </div>
</div>

{{-- Queue items --}}
@php
$queue = $validationQueue ?? [
    ['id'=>'REQ-008','init'=>'SJ','name'=>'Sung Ji-Ho',     'flag'=>'🇰🇷','doc'=>'Surat Keterangan Aktif',   'submitted'=>'Hari ini, 08:24','priority'=>'high',  'note'=>'Diperlukan untuk perpanjangan visa pelajar.'],
    ['id'=>'REQ-007','init'=>'PA','name'=>'Pham Duc Anh',   'flag'=>'🇻🇳','doc'=>'Perpanjangan KITAS',       'submitted'=>'Hari ini, 09:11','priority'=>'high',  'note'=>'KITAS expired dalam 5 hari.'],
    ['id'=>'REQ-006','init'=>'DM','name'=>'Diallo Mamadou', 'flag'=>'🇸🇳','doc'=>'SKCK Baru',               'submitted'=>'Hari ini, 10:03','priority'=>'medium','note'=>'Perlu untuk proses ijin kerja paruh waktu.'],
    ['id'=>'REQ-005','init'=>'LM','name'=>'Li Xiao Ming',   'flag'=>'🇨🇳','doc'=>'Asuransi Kesehatan',      'submitted'=>'Hari ini, 11:30','priority'=>'medium','note'=>'Asuransi lama habis bulan depan.'],
    ['id'=>'REQ-004','init'=>'YT','name'=>'Yuki Tanaka',    'flag'=>'🇯🇵','doc'=>'Surat Domisili',          'submitted'=>'Hari ini, 13:00','priority'=>'low',   'note'=>'Keperluan pembukaan rekening bank.'],
    ['id'=>'REQ-003','init'=>'PJ','name'=>'Park Jisoo',     'flag'=>'🇰🇷','doc'=>'Surat Keterangan Mahasiswa','submitted'=>'Hari ini, 14:00','priority'=>'low',  'note'=>'Keperluan pendaftaran beasiswa.'],
];
$prioColor = ['high'=>'#F87171','medium'=>'#FCD34D','low'=>'#34D399'];
$prioLabel = ['high'=>'Tinggi','medium'=>'Sedang','low'=>'Normal'];
@endphp

<div class="row g-3">

    {{-- List validasi --}}
    <div class="col-md-8 sima-fade">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Antrian Validasi</h5>
                    <div class="sima-card__subtitle">{{ count($queue) }} dokumen menunggu review</div>
                </div>
                <span class="sima-badge sima-badge--amber">
                    <i class="fas fa-clock"></i> {{ count($queue) }} Pending
                </span>
            </div>

            <div>
                @foreach($queue as $i => $q)
                @php $qr = is_array($q) ? $q : $q->toArray(); @endphp
                <div style="padding:16px 20px;border-bottom:1px solid var(--c-border-soft);transition:background .13s"
                     onmouseover="this.style.background='var(--c-surface-2)'"
                     onmouseout="this.style.background='transparent'">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">

                        {{-- No. antrian --}}
                        <div style="font-family:var(--f-mono);font-size:14px;font-weight:700;color:var(--c-border);min-width:26px">
                            {{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}
                        </div>

                        {{-- Priority dot --}}
                        <div style="width:9px;height:9px;border-radius:50%;flex-shrink:0;
                                    background:{{ $prioColor[$qr['priority']] ?? '#34D399' }}"></div>

                        {{-- Avatar --}}
                        <div class="sima-avatar"
                             style="width:36px;height:36px;font-size:11px;border-radius:10px;
                                    background:linear-gradient(135deg,#0D1B2A,#1B3256)">
                            {{ $qr['init'] }}
                        </div>

                        <div style="flex:1;min-width:0">
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                                <span style="font-size:14px;font-weight:600;color:var(--c-text-1)">
                                    {{ $qr['flag'] }} {{ $qr['name'] }}
                                </span>
                                <span class="sima-badge sima-badge--grey" style="font-size:10.5px">{{ $qr['id'] }}</span>
                                <span class="sima-badge" style="font-size:10px;background:{{ $prioColor[$qr['priority']] }}22;color:{{ $prioColor[$qr['priority']] }}">
                                    Prioritas {{ $prioLabel[$qr['priority']] ?? 'Normal' }}
                                </span>
                            </div>
                            <div style="font-size:12.5px;color:var(--c-text-2);margin-top:2px">
                                <i class="fas fa-file-alt" style="color:var(--c-accent)"></i>
                                <strong>{{ $qr['doc'] }}</strong>
                                &nbsp;·&nbsp;
                                <i class="fas fa-clock" style="font-size:10px"></i>
                                {{ $qr['submitted'] }}
                            </div>
                        </div>
                    </div>

                    {{-- Note --}}
                    <div style="padding:9px 12px;background:var(--c-bg);border-radius:8px;font-size:12.5px;color:var(--c-text-3);margin-bottom:12px;border-left:3px solid var(--c-border)">
                        <i class="fas fa-quote-left" style="font-size:10px;margin-right:5px"></i>{{ $qr['note'] }}
                    </div>

                    {{-- Actions --}}
                    <div style="display:flex;gap:8px">
                        <form method="POST" action="{{ route('kln.validasi') }}" style="display:contents">
                            @csrf
                            <input type="hidden" name="id" value="{{ $qr['id'] }}">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="sima-btn sima-btn--sm"
                                    style="background:linear-gradient(135deg,#059669,#34d399);box-shadow:0 2px 10px rgba(5,150,105,.25)">
                                <i class="fas fa-circle-check"></i> Setujui
                            </button>
                        </form>
                        <form method="POST" action="{{ route('kln.validasi') }}" style="display:contents">
                            @csrf
                            <input type="hidden" name="id" value="{{ $qr['id'] }}">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="sima-btn sima-btn--danger sima-btn--sm">
                                <i class="fas fa-circle-xmark"></i> Tolak
                            </button>
                        </form>
                        <button class="sima-btn sima-btn--outline sima-btn--sm">
                            <i class="fas fa-eye"></i> Lihat Dokumen
                        </button>
                        <button class="sima-btn sima-btn--outline sima-btn--sm">
                            <i class="fas fa-comment"></i> Catatan
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <div style="padding:14px 20px;border-top:1px solid var(--c-border-soft);display:flex;justify-content:space-between;align-items:center">
                <span style="font-size:12px;color:var(--c-text-3)">Tekan Setujui / Tolak untuk memproses dokumen</span>
                <button class="sima-btn sima-btn--gold sima-btn--sm">
                    <i class="fas fa-check-double"></i> Setujui Semua Normal
                </button>
            </div>
        </div>
    </div>

    {{-- Sidebar info --}}
    <div class="col-md-4 sima-fade sima-fade--1">

        {{-- Statistik validasi hari ini --}}
        <div class="sima-card" style="margin-bottom:12px">
            <div class="sima-card__header">
                <h5 class="sima-card__title">Statistik Hari Ini</h5>
            </div>
            <div class="sima-card__body">
                @foreach([
                    ['label'=>'Disetujui','val'=>$approvedToday??3,'ico'=>'fa-circle-check',       'cl'=>'#059669','bg'=>'#ECFDF5'],
                    ['label'=>'Ditolak',  'val'=>$rejectedToday??1,'ico'=>'fa-circle-xmark',        'cl'=>'#DC2626','bg'=>'#FEF2F2'],
                    ['label'=>'Pending',  'val'=>$pendingDocs??8,  'ico'=>'fa-clock',               'cl'=>'#D97706','bg'=>'#FFFBEB'],
                    ['label'=>'Total Hari Ini','val'=>($approvedToday??3)+($rejectedToday??1),'ico'=>'fa-list-check','cl'=>'#2563EB','bg'=>'#EFF6FF'],
                ] as $st)
                <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--c-border-soft)">
                    <div style="width:34px;height:34px;border-radius:9px;background:{{ $st['bg'] }};color:{{ $st['cl'] }};display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0">
                        <i class="fas {{ $st['ico'] }}"></i>
                    </div>
                    <div style="flex:1">
                        <div style="font-size:12.5px;color:var(--c-text-3)">{{ $st['label'] }}</div>
                    </div>
                    <div style="font-family:var(--f-display);font-size:22px;font-weight:700;color:{{ $st['cl'] }}">
                        {{ $st['val'] }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Riwayat terakhir --}}
        <div class="sima-card">
            <div class="sima-card__header">
                <h5 class="sima-card__title">Riwayat Validasi</h5>
            </div>
            <div class="sima-card__body">
                <ul class="sima-timeline">
                    @foreach([
                        ['icon'=>'fa-circle-check','bg'=>'#ECFDF5','cl'=>'#059669','title'=>'KITAS Ahmad Rahimov — Disetujui','time'=>'Hari ini, 09:14'],
                        ['icon'=>'fa-circle-xmark','bg'=>'#FEF2F2','cl'=>'#DC2626','title'=>'Paspor (scan buram) — Ditolak',   'time'=>'Hari ini, 08:50'],
                        ['icon'=>'fa-circle-check','bg'=>'#ECFDF5','cl'=>'#059669','title'=>'Surat Aktif Bui Thi Lan — Disetujui','time'=>'Kemarin, 16:30'],
                    ] as $log)
                    <li class="sima-tl-item">
                        <div class="sima-tl-dot" style="background:{{ $log['bg'] }};color:{{ $log['cl'] }}">
                            <i class="fas {{ $log['icon'] }}"></i>
                        </div>
                        <div class="sima-tl-content">
                            <div class="sima-tl-title">{{ $log['title'] }}</div>
                            <div class="sima-tl-time">{{ $log['time'] }}</div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>
</div>

@endsection
