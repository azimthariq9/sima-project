@extends('layouts.sima')

@section('page_title',    'Dashboard KLN')
@section('page_section',  'Kantor Layanan Internasional')
@section('page_subtitle', 'Monitoring mahasiswa asing — Selamat datang, ' . (auth()->user()->name ?? 'Staff KLN'))

@section('main_content')

{{-- ═══════════════════════════════════════════════════
     ALERT KRITIS — Dokumen Expired Urgent
     ═══════════════════════════════════════════════════ --}}
@if(($criticalCount ?? 5) > 0)
<div class="sima-alert sima-alert--red sima-fade" style="margin-bottom:22px">
    <i class="fas fa-shield-exclamation sima-alert__icon"></i>
    <div class="sima-alert__text">
        <strong>{{ $criticalCount ?? 5 }} mahasiswa memiliki dokumen expired atau expiring dalam 7 hari.</strong>
        Tindakan validasi segera diperlukan untuk menghindari masalah izin tinggal.
    </div>
    <a href="{{ route('kln.validasi') }}" class="sima-alert__action" style="white-space:nowrap">
        Proses Sekarang →
    </a>
</div>
@endif

{{-- ═══════════════════════════════════════════════════
     STAT CARDS — 6 metrics KLN
     ═══════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    <div class="col-6 col-md-2 sima-fade sima-fade--1">
        <a href="{{ route('kln.monitoring') }}" class="sima-stat sima-stat--navy d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--navy">
                <i class="fas fa-users"></i>
            </div>
            <div class="sima-stat__label">Total Mahasiswa</div>
            <span class="sima-stat__value" data-count="{{ $totalMahasiswa ?? 120 }}">{{ $totalMahasiswa ?? 120 }}</span>
            <div class="sima-stat__delta sima-stat__delta--up">
                <i class="fas fa-arrow-up"></i> +8 semester ini
            </div>
        </a>
    </div>

    <div class="col-6 col-md-2 sima-fade sima-fade--2">
        <a href="{{ route('kln.validasi') }}" class="sima-stat sima-stat--amber d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--amber">
                <i class="fas fa-file-circle-exclamation"></i>
            </div>
            <div class="sima-stat__label">Dokumen Pending</div>
            <span class="sima-stat__value" data-count="{{ $pendingDocs ?? 8 }}">{{ $pendingDocs ?? 8 }}</span>
            <div class="sima-stat__delta sima-stat__delta--flat">
                <i class="fas fa-clock"></i> Menunggu review
            </div>
        </a>
    </div>

    <div class="col-6 col-md-2 sima-fade sima-fade--3">
        <a href="{{ route('kln.monitoring') }}?filter=kritis" class="sima-stat sima-stat--red d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--red">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div class="sima-stat__label">Dokumen Kritis</div>
            <span class="sima-stat__value" data-count="{{ $criticalDocs ?? 5 }}">{{ $criticalDocs ?? 5 }}</span>
            <div class="sima-stat__delta sima-stat__delta--down">
                <i class="fas fa-exclamation"></i> Expired / expiring
            </div>
        </a>
    </div>

    <div class="col-6 col-md-2 sima-fade sima-fade--4">
        <a href="{{ route('kln.validasi') }}?status=done" class="sima-stat sima-stat--green d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--green">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="sima-stat__label">Divalidasi Hari Ini</div>
            <span class="sima-stat__value" data-count="{{ $validatedToday ?? 3 }}">{{ $validatedToday ?? 3 }}</span>
            <div class="sima-stat__delta sima-stat__delta--up">
                <i class="fas fa-check-double"></i> Selesai diproses
            </div>
        </a>
    </div>

    <div class="col-6 col-md-2 sima-fade sima-fade--5">
        <a href="{{ route('kln.monitoring') }}?view=negara" class="sima-stat sima-stat--teal d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--teal">
                <i class="fas fa-earth-asia"></i>
            </div>
            <div class="sima-stat__label">Negara Asal</div>
            <span class="sima-stat__value" data-count="{{ $totalNegara ?? 14 }}">{{ $totalNegara ?? 14 }}</span>
            <div class="sima-stat__delta sima-stat__delta--up">
                <i class="fas fa-arrow-up"></i> +2 negara baru
            </div>
        </a>
    </div>

    <div class="col-6 col-md-2 sima-fade sima-fade--6">
        <a href="{{ route('kln.schedule') }}" class="sima-stat sima-stat--purple d-block text-decoration-none">
            <div class="sima-stat__icon sima-stat__icon--purple">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div class="sima-stat__label">Jadwal Minggu Ini</div>
            <span class="sima-stat__value" data-count="{{ $weeklySchedule ?? 24 }}">{{ $weeklySchedule ?? 24 }}</span>
            <div class="sima-stat__delta sima-stat__delta--flat">
                <i class="fas fa-calendar-alt"></i> Sesi terjadwal
            </div>
        </a>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════
     ROW 2 — Tabel Dokumen Kritis + Sebaran Negara
     ═══════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- ── DOKUMEN KRITIS ──────────────────────────── --}}
    <div class="col-md-8 sima-fade sima-fade--5">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Dokumen Kritis — Perlu Tindakan</h5>
                    <div class="sima-card__subtitle">Expired &amp; akan expired dalam 30 hari ke depan</div>
                </div>
                <a href="{{ route('kln.validasi') }}" class="sima-card__action">
                    <i class="fas fa-clipboard-check"></i> Proses Semua
                </a>
            </div>
            <div style="overflow-x:auto">

                @php
                $critDocs = $criticalDocuments ?? [
                    ['init'=>'AR','name'=>'Ahmad Rahimov',   'flag'=>'🇺🇿','doc'=>'KITAS',   'exp'=>'03/03/2026','status'=>'expiring'],
                    ['init'=>'AR','name'=>'Ahmad Rahimov',   'flag'=>'🇺🇿','doc'=>'SKCK',    'exp'=>'15/01/2026','status'=>'expired'],
                    ['init'=>'BL','name'=>'Bui Thi Lan',     'flag'=>'🇻🇳','doc'=>'KITAS',   'exp'=>'20/03/2026','status'=>'expiring'],
                    ['init'=>'PJ','name'=>'Park Jisoo',      'flag'=>'🇰🇷','doc'=>'Asuransi','exp'=>'28/02/2026','status'=>'expiring'],
                    ['init'=>'AD','name'=>'Amara Diallo',    'flag'=>'🇸🇳','doc'=>'Paspor',  'exp'=>'10/01/2026','status'=>'expired'],
                ];
                @endphp

                <table class="sima-table">
                    <thead>
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Negara</th>
                            <th>Dokumen</th>
                            <th>Berlaku s/d</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($critDocs as $cd)
                        @php $r = is_array($cd) ? $cd : $cd->toArray(); @endphp
                        <tr onclick="window.location='{{ route('kln.validasi') }}'">
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <div class="sima-avatar"
                                         style="width:30px;height:30px;font-size:10px;border-radius:8px;
                                                background:linear-gradient(135deg,#0D1B2A,#1B3256)">
                                        {{ $r['init'] }}
                                    </div>
                                    <span style="font-weight:500">{{ $r['name'] }}</span>
                                </div>
                            </td>
                            <td style="font-size:20px;text-align:center">{{ $r['flag'] }}</td>
                            <td style="font-weight:500">{{ $r['doc'] }}</td>
                            <td style="font-family:var(--f-mono);font-size:12px;color:var(--c-text-3)">
                                {{ $r['exp'] }}
                            </td>
                            <td>
                                @if($r['status'] === 'expired')
                                    <span class="sima-badge sima-badge--red">
                                        <i class="fas fa-circle-xmark"></i> Expired
                                    </span>
                                @else
                                    <span class="sima-badge sima-badge--amber">
                                        <i class="fas fa-circle-exclamation"></i> Expiring
                                    </span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('kln.validasi') }}"
                                   class="sima-btn sima-btn--blue sima-btn--sm"
                                   onclick="event.stopPropagation()">
                                    <i class="fas fa-arrow-right"></i> Validasi
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── SEBARAN NEGARA ───────────────────────────── --}}
    <div class="col-md-4 sima-fade sima-fade--6">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Sebaran Negara</h5>
                    <div class="sima-card__subtitle">Top 7 negara asal mahasiswa</div>
                </div>
                <a href="{{ route('kln.monitoring') }}?view=negara" class="sima-card__action">Detail</a>
            </div>
            <div class="sima-card__body">

                @php
                $countries = $negaraData ?? [
                    ['flag'=>'🇺🇿','name'=>'Uzbekistan',    'count'=>38,'pct'=>32,'color'=>'#2563EB'],
                    ['flag'=>'🇻🇳','name'=>'Vietnam',       'count'=>22,'pct'=>18,'color'=>'#0D9488'],
                    ['flag'=>'🇰🇷','name'=>'Korea Selatan', 'count'=>18,'pct'=>15,'color'=>'#7C3AED'],
                    ['flag'=>'🇹🇯','name'=>'Tajikistan',    'count'=>15,'pct'=>12,'color'=>'#D97706'],
                    ['flag'=>'🇸🇳','name'=>'Senegal',       'count'=>12,'pct'=>10,'color'=>'#DC2626'],
                    ['flag'=>'🇲🇾','name'=>'Malaysia',      'count'=>9, 'pct'=>7, 'color'=>'#059669'],
                    ['flag'=>'🌍','name'=>'Lainnya',        'count'=>6, 'pct'=>5, 'color'=>'#94A3B8'],
                ];
                @endphp

                @foreach($countries as $c)
                @php $cn = is_array($c) ? $c : $c->toArray(); @endphp
                <div style="margin-bottom:13px">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px">
                        <div style="display:flex;align-items:center;gap:8px">
                            <span style="font-size:18px">{{ $cn['flag'] }}</span>
                            <span style="font-size:12.5px;font-weight:500;color:var(--c-text-1)">{{ $cn['name'] }}</span>
                        </div>
                        <div style="font-family:var(--f-mono);font-size:11.5px;color:var(--c-text-3)">
                            {{ $cn['count'] }} <span style="font-size:10px">({{ $cn['pct'] }}%)</span>
                        </div>
                    </div>
                    <div class="sima-prog">
                        <div class="sima-prog__bar" data-w="{{ $cn['pct'] }}"
                             style="background:{{ $cn['color'] }}"></div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════
     ROW 3 — Antrian Validasi + Quick Post Pengumuman
     ═══════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- ── ANTRIAN VALIDASI ──────────────────────── --}}
    <div class="col-md-6 sima-fade sima-fade--5">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Antrian Validasi Dokumen</h5>
                    <div class="sima-card__subtitle">{{ $pendingDocs ?? 8 }} dokumen menunggu review KLN</div>
                </div>
                <a href="{{ route('kln.validasi') }}" class="sima-card__action">
                    <i class="fas fa-external-link-alt"></i> Buka Semua
                </a>
            </div>

            @php
            $queue = $validationQueue ?? [
                ['init'=>'SJ','name'=>'Sung Ji-Ho',      'doc'=>'Surat Aktif',        'time'=>'08:24','priority'=>'high'],
                ['init'=>'PA','name'=>'Pham Duc Anh',    'doc'=>'Perpanjangan KITAS', 'time'=>'09:11','priority'=>'high'],
                ['init'=>'DM','name'=>'Diallo Mamadou',  'doc'=>'SKCK Baru',          'time'=>'10:03','priority'=>'medium'],
                ['init'=>'LM','name'=>'Li Xiao Ming',    'doc'=>'Asuransi Kesehatan', 'time'=>'11:30','priority'=>'medium'],
                ['init'=>'YT','name'=>'Yuki Tanaka',     'doc'=>'Surat Domisili',     'time'=>'13:00','priority'=>'low'],
                ['init'=>'PJ','name'=>'Park Jisoo',      'doc'=>'Surat Keterangan',   'time'=>'14:00','priority'=>'low'],
            ];
            @endphp

            <div>
                @foreach($queue as $i => $q)
                @php $qr = is_array($q) ? $q : $q->toArray(); @endphp
                <div onclick="window.location='{{ route('kln.validasi') }}'"
                     style="display:flex;align-items:center;gap:14px;padding:13px 20px;
                            border-bottom:1px solid var(--c-border-soft);cursor:pointer;
                            transition:background .13s"
                     onmouseover="this.style.background='var(--c-surface-2)'"
                     onmouseout="this.style.background='transparent'">

                    <div style="font-family:var(--f-mono);font-size:15px;font-weight:700;
                                color:var(--c-border);min-width:24px;text-align:center">
                        {{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}
                    </div>

                    <div style="width:8px;height:8px;border-radius:50%;flex-shrink:0;
                                background:{{ $qr['priority'] === 'high' ? '#F87171' : ($qr['priority'] === 'medium' ? '#FCD34D' : '#34D399') }}">
                    </div>

                    <div class="sima-avatar"
                         style="width:32px;height:32px;font-size:10.5px;border-radius:9px;
                                background:linear-gradient(135deg,#0D1B2A,#1B3256);flex-shrink:0">
                        {{ $qr['init'] }}
                    </div>

                    <div style="flex:1;min-width:0">
                        <div style="font-size:13px;font-weight:500;color:var(--c-text-1)">{{ $qr['name'] }}</div>
                        <div style="font-size:11.5px;color:var(--c-text-3)">{{ $qr['doc'] }}</div>
                    </div>

                    <div style="font-family:var(--f-mono);font-size:10.5px;color:var(--c-text-3);flex-shrink:0">
                        {{ $qr['time'] }}
                    </div>

                    <a href="{{ route('kln.validasi') }}"
                       class="sima-btn sima-btn--outline sima-btn--sm"
                       style="flex-shrink:0"
                       onclick="event.stopPropagation()">
                        Review
                    </a>
                </div>
                @endforeach
            </div>

            <div style="padding:14px 20px;border-top:1px solid var(--c-border-soft);display:flex;align-items:center;justify-content:space-between">
                <div style="display:flex;align-items:center;gap:16px;font-size:11px">
                    <span style="display:flex;align-items:center;gap:5px;color:var(--c-text-3)">
                        <span style="width:7px;height:7px;border-radius:50%;background:#F87171;display:inline-block"></span> Prioritas Tinggi
                    </span>
                    <span style="display:flex;align-items:center;gap:5px;color:var(--c-text-3)">
                        <span style="width:7px;height:7px;border-radius:50%;background:#FCD34D;display:inline-block"></span> Sedang
                    </span>
                    <span style="display:flex;align-items:center;gap:5px;color:var(--c-text-3)">
                        <span style="width:7px;height:7px;border-radius:50%;background:#34D399;display:inline-block"></span> Normal
                    </span>
                </div>
                <a href="{{ route('kln.validasi') }}" class="sima-btn sima-btn--gold sima-btn--sm">
                    <i class="fas fa-play"></i> Mulai Validasi
                </a>
            </div>
        </div>
    </div>

    {{-- ── QUICK POST PENGUMUMAN ─────────────────── --}}
    <div class="col-md-6 sima-fade sima-fade--6">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Post Pengumuman Cepat</h5>
                    <div class="sima-card__subtitle">Broadcast ke mahasiswa asing</div>
                </div>
                <a href="{{ route('kln.announcement') }}" class="sima-card__action">
                    <i class="fas fa-megaphone"></i> Semua Pengumuman
                </a>
            </div>
            <div class="sima-card__body">
                <form action="{{ route('kln.announcement.store') }}" method="POST">
                    @csrf

                    <div style="margin-bottom:13px">
                        <label class="sima-label">Judul Pengumuman</label>
                        <input type="text" name="judul" class="sima-input"
                               placeholder="Contoh: Pengingat Perpanjangan KITAS Maret 2026..."
                               required>
                    </div>

                    <div style="margin-bottom:13px">
                        <label class="sima-label">Isi Pengumuman</label>
                        <textarea name="isi" class="sima-input" rows="4"
                                  placeholder="Tulis isi pengumuman di sini..."
                                  required style="resize:vertical"></textarea>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:13px">
                        <div>
                            <label class="sima-label">Target Penerima</label>
                            <select name="target" class="sima-input" style="appearance:none;cursor:pointer">
                                <option value="all">Semua Mahasiswa Asing</option>
                                <option value="uzbekistan">🇺🇿 Uzbekistan</option>
                                <option value="vietnam">🇻🇳 Vietnam</option>
                                <option value="korea">🇰🇷 Korea Selatan</option>
                                <option value="tajikistan">🇹🇯 Tajikistan</option>
                                <option value="senegal">🇸🇳 Senegal</option>
                                <option value="malaysia">🇲🇾 Malaysia</option>
                                <option value="lainnya">Negara Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="sima-label">Prioritas</label>
                            <select name="is_penting" class="sima-input" style="appearance:none;cursor:pointer">
                                <option value="0">ℹ️ Informasi Umum</option>
                                <option value="1">⚠️ Penting</option>
                            </select>
                        </div>
                    </div>

                    <div style="display:flex;gap:10px;align-items:center">
                        <button type="submit" class="sima-btn sima-btn--gold">
                            <i class="fas fa-paper-plane"></i> Kirim Pengumuman
                        </button>
                        <a href="{{ route('kln.announcement') }}" class="sima-btn sima-btn--outline">
                            <i class="fas fa-list"></i> Riwayat
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════
     ROW 4 — Monitoring Mahasiswa (ringkasan tabel)
     ═══════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    <div class="col-12 sima-fade sima-fade--5">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Monitoring Mahasiswa Asing</h5>
                    <div class="sima-card__subtitle">{{ $totalMahasiswa ?? 120 }} mahasiswa terdaftar aktif</div>
                </div>
                <a href="{{ route('kln.monitoring') }}" class="sima-card__action">
                    <i class="fas fa-table"></i> Lihat Semua
                </a>
            </div>
            <div style="overflow-x:auto">

                @php
                $mahasiswaList = $mahasiswaPreview ?? [
                    ['init'=>'AR','name'=>'Ahmad Rahimov',  'flag'=>'🇺🇿','prodi'=>'Teknik Informatika','smt'=>6,'kitas'=>'03/03/2026','attendance'=>87,'status'=>'active'],
                    ['init'=>'BL','name'=>'Bui Thi Lan',    'flag'=>'🇻🇳','prodi'=>'Sistem Informasi',  'smt'=>4,'kitas'=>'20/03/2026','attendance'=>82,'status'=>'active'],
                    ['init'=>'PJ','name'=>'Park Jisoo',     'flag'=>'🇰🇷','prodi'=>'Manajemen',         'smt'=>2,'kitas'=>'01/06/2026','attendance'=>96,'status'=>'active'],
                    ['init'=>'YT','name'=>'Yuki Tanaka',    'flag'=>'🇯🇵','prodi'=>'Teknik Informatika','smt'=>6,'kitas'=>'12/07/2026','attendance'=>90,'status'=>'active'],
                    ['init'=>'LM','name'=>'Li Xiao Ming',   'flag'=>'🇨🇳','prodi'=>'Akuntansi',         'smt'=>4,'kitas'=>'25/04/2026','attendance'=>79,'status'=>'active'],
                    ['init'=>'AD','name'=>'Amara Diallo',   'flag'=>'🇸🇳','prodi'=>'Psikologi',         'smt'=>2,'kitas'=>'01/08/2026','attendance'=>81,'status'=>'active'],
                    ['init'=>'JD','name'=>'John Doe',       'flag'=>'🇺🇿','prodi'=>'Teknik Informatika','smt'=>8,'kitas'=>'03/03/2026','attendance'=>75,'status'=>'inactive'],
                ];
                @endphp

                <table class="sima-table">
                    <thead>
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Negara</th>
                            <th>Program Studi</th>
                            <th style="text-align:center">Smt</th>
                            <th>KITAS Berlaku</th>
                            <th>Kehadiran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mahasiswaList as $mhs)
                        @php $m = is_array($mhs) ? $mhs : $mhs->toArray(); @endphp
                        <tr onclick="window.location='{{ route('kln.monitoring') }}'">
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <div class="sima-avatar"
                                         style="width:30px;height:30px;font-size:10px;border-radius:8px;
                                                background:linear-gradient(135deg,#0D1B2A,#1B3256)">
                                        {{ $m['init'] }}
                                    </div>
                                    <span style="font-weight:500;font-size:13px">{{ $m['name'] }}</span>
                                </div>
                            </td>
                            <td style="font-size:20px;text-align:center">{{ $m['flag'] }}</td>
                            <td style="font-size:12.5px;color:var(--c-text-2)">{{ $m['prodi'] }}</td>
                            <td style="font-family:var(--f-mono);text-align:center">{{ $m['smt'] }}</td>
                            <td style="font-family:var(--f-mono);font-size:11.5px;color:var(--c-text-3)">
                                {{ $m['kitas'] }}
                            </td>
                            <td style="min-width:120px">
                                <div style="display:flex;align-items:center;gap:8px">
                                    <div style="flex:1">
                                        <div class="sima-prog">
                                            <div class="sima-prog__bar" data-w="{{ $m['attendance'] }}"
                                                 style="background:{{ $m['attendance'] >= 85 ? '#059669' : ($m['attendance'] >= 80 ? '#D97706' : '#DC2626') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <span style="font-family:var(--f-mono);font-size:11px;font-weight:600;min-width:30px;
                                                 color:{{ $m['attendance'] >= 85 ? '#059669' : ($m['attendance'] >= 80 ? '#D97706' : '#DC2626') }}">
                                        {{ $m['attendance'] }}%
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if($m['status'] === 'active')
                                    <span class="sima-badge sima-badge--green">
                                        <i class="fas fa-circle" style="font-size:5px"></i> Aktif
                                    </span>
                                @else
                                    <span class="sima-badge sima-badge--grey">
                                        <i class="fas fa-circle" style="font-size:5px"></i> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td onclick="event.stopPropagation()">
                                <div style="display:flex;gap:6px">
                                    <a href="{{ route('kln.monitoring') }}"
                                       class="sima-btn sima-btn--outline sima-btn--sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('kln.validasi') }}"
                                       class="sima-btn sima-btn--blue sima-btn--sm">
                                        <i class="fas fa-file-check"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="padding:14px 20px;border-top:1px solid var(--c-border-soft);display:flex;align-items:center;justify-content:space-between">
                    <span style="font-size:12px;color:var(--c-text-3)">
                        Menampilkan {{ count($mahasiswaList ?? []) }} dari {{ $totalMahasiswa ?? 120 }} mahasiswa
                    </span>
                    <div style="display:flex;gap:8px">
                        <a href="{{ route('kln.monitoring') }}" class="sima-btn sima-btn--outline sima-btn--sm">
                            <i class="fas fa-download"></i> Export
                        </a>
                        <a href="{{ route('kln.monitoring') }}" class="sima-btn sima-btn--gold sima-btn--sm">
                            <i class="fas fa-list"></i> Lihat Semua
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════
     ROW 5 — Analitik Cepat + Activity Log
     ═══════════════════════════════════════════════════ --}}
<div class="row g-3">

    {{-- ── CHART KEHADIRAN BULANAN ──────────────── --}}
    <div class="col-md-6 sima-fade sima-fade--5">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Tren Mahasiswa 6 Bulan</h5>
                    <div class="sima-card__subtitle">Aktif &amp; dokumen valid per bulan</div>
                </div>
                <a href="{{ route('kln.analytics') }}" class="sima-card__action">
                    <i class="fas fa-chart-line"></i> Analitik
                </a>
            </div>
            <div class="sima-card__body">

                <div style="display:flex;gap:18px;margin-bottom:16px">
                    <div style="display:flex;align-items:center;gap:7px;font-size:12px;color:var(--c-text-2)">
                        <div style="width:10px;height:10px;border-radius:3px;background:rgba(37,99,235,.75)"></div>
                        Mahasiswa Aktif
                    </div>
                    <div style="display:flex;align-items:center;gap:7px;font-size:12px;color:var(--c-text-2)">
                        <div style="width:10px;height:10px;border-radius:3px;background:rgba(196,151,58,.75)"></div>
                        Dok. Valid
                    </div>
                </div>

                @php
                $chartData = $monthlyChart ?? [
                    ['label'=>'Okt','a'=>108,'b'=>72],
                    ['label'=>'Nov','b'=>78,'a'=>112],
                    ['label'=>'Des','a'=>110,'b'=>75],
                    ['label'=>'Jan','a'=>115,'b'=>80],
                    ['label'=>'Feb','a'=>118,'b'=>85],
                    ['label'=>'Mar','a'=>120,'b'=>87],
                ];
                $maxVal = max(array_merge(array_column($chartData,'a'), array_column($chartData,'b')));
                @endphp

                <div style="display:flex;align-items:flex-end;gap:8px;height:120px;border-bottom:1px solid var(--c-border-soft);padding-bottom:4px;margin-bottom:6px">
                    @foreach($chartData as $cd)
                    @php $d = is_array($cd) ? $cd : $cd->toArray(); @endphp
                    <div style="flex:1;display:flex;align-items:flex-end;gap:2px">
                        <div class="sima-bar" data-h="{{ round(($d['a']/$maxVal)*100) }}" data-val="{{ $d['a'] }}"
                             style="flex:1;border-radius:4px 4px 0 0;min-height:3px;
                                    background:rgba(37,99,235,.75);cursor:pointer">
                        </div>
                        <div class="sima-bar" data-h="{{ round(($d['b']/$maxVal)*100) }}" data-val="{{ $d['b'] }}"
                             style="flex:1;border-radius:4px 4px 0 0;min-height:3px;
                                    background:rgba(196,151,58,.75);cursor:pointer">
                        </div>
                    </div>
                    @endforeach
                </div>

                <div style="display:flex;gap:8px">
                    @foreach($chartData as $cd)
                    @php $d = is_array($cd) ? $cd : $cd->toArray(); @endphp
                    <div style="flex:1;text-align:center;font-family:var(--f-mono);font-size:9.5px;color:var(--c-text-3)">
                        {{ $d['label'] }}
                    </div>
                    @endforeach
                </div>

                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:18px">
                    @foreach([
                        ['label'=>'Rata-rata Aktif','val'=>($avgActive??114),'color'=>'var(--c-blue)'],
                        ['label'=>'Dok. Valid Avg', 'val'=>($avgValid??80),  'color'=>'var(--c-gold)'],
                        ['label'=>'Negara Aktif',   'val'=>($totalNegara??14),'color'=>'var(--c-teal)'],
                    ] as $s)
                    <div style="padding:11px;background:var(--c-surface-2);border-radius:10px;text-align:center;border:1px solid var(--c-border-soft)">
                        <div style="font-family:var(--f-display);font-size:22px;font-weight:700;color:{{ $s['color'] }}">{{ $s['val'] }}</div>
                        <div style="font-size:9.5px;color:var(--c-text-3);text-transform:uppercase;letter-spacing:.07em;margin-top:2px">{{ $s['label'] }}</div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    {{-- ── ACTIVITY LOG KLN ─────────────────────── --}}
    <div class="col-md-6 sima-fade sima-fade--6">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Log Aktivitas KLN</h5>
                    <div class="sima-card__subtitle">Tindakan staff hari ini</div>
                </div>
            </div>
            <div class="sima-card__body">
                <ul class="sima-timeline">

                    @forelse($activityLog ?? [] as $log)
                    <li class="sima-tl-item">
                        <div class="sima-tl-dot" style="background:{{ $log->bg }};color:{{ $log->color }}">
                            <i class="fas {{ $log->icon }}"></i>
                        </div>
                        <div class="sima-tl-content">
                            <div class="sima-tl-title">{{ $log->action }}</div>
                            <div class="sima-tl-time">{{ $log->actor }} · {{ $log->time }}</div>
                        </div>
                    </li>
                    @empty
                    <li class="sima-tl-item">
                        <div class="sima-tl-dot" style="background:#ECFDF5;color:#059669">
                            <i class="fas fa-circle-check"></i>
                        </div>
                        <div class="sima-tl-content">
                            <div class="sima-tl-title">Validasi KITAS Ahmad Rahimov disetujui</div>
                            <div class="sima-tl-time">Siti Rahayu · Hari ini, 09:14</div>
                        </div>
                    </li>
                    <li class="sima-tl-item">
                        <div class="sima-tl-dot" style="background:#EFF6FF;color:#2563EB">
                            <i class="fas fa-file-circle-plus"></i>
                        </div>
                        <div class="sima-tl-content">
                            <div class="sima-tl-title">Request baru: Perpanjangan KITAS — Pham Duc Anh</div>
                            <div class="sima-tl-time">Sistem · Hari ini, 09:11</div>
                        </div>
                    </li>
                    <li class="sima-tl-item">
                        <div class="sima-tl-dot" style="background:#FFFBEB;color:#D97706">
                            <i class="fas fa-triangle-exclamation"></i>
                        </div>
                        <div class="sima-tl-content">
                            <div class="sima-tl-title">Alert: SKCK Amara Diallo expired sejak 15 Jan</div>
                            <div class="sima-tl-time">Sistem · Hari ini, 07:00</div>
                        </div>
                    </li>
                    <li class="sima-tl-item">
                        <div class="sima-tl-dot" style="background:#F5F3FF;color:#7C3AED">
                            <i class="fas fa-megaphone"></i>
                        </div>
                        <div class="sima-tl-content">
                            <div class="sima-tl-title">Pengumuman orientasi dikirim ke 120 mahasiswa</div>
                            <div class="sima-tl-time">Siti Rahayu · Kemarin, 15:30</div>
                        </div>
                    </li>
                    <li class="sima-tl-item">
                        <div class="sima-tl-dot" style="background:#F0FDFA;color:#0D9488">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="sima-tl-content">
                            <div class="sima-tl-title">Mahasiswa baru Park Jisoo terdaftar dari Korea 🇰🇷</div>
                            <div class="sima-tl-time">Siti Rahayu · Kemarin, 10:20</div>
                        </div>
                    </li>
                    @endforelse

                </ul>
            </div>
        </div>
    </div>

</div>

@endsection

@section('page_js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const bars = document.querySelectorAll('.sima-bar[data-h]');
    if (!bars.length) return;

    const io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            const el = entry.target;
            setTimeout(function () {
                el.style.height = el.getAttribute('data-h') + '%';
            }, 80);
            io.unobserve(el);
        });
    }, { threshold: 0.1 });

    bars.forEach(function (b) {
        b.style.height = '0';
        b.style.transition = 'height .85s cubic-bezier(0.16,1,0.3,1)';
        io.observe(b);
    });

    bars.forEach(function (b) {
        b.addEventListener('mouseenter', function () { this.style.filter = 'brightness(1.18)'; this.title = this.getAttribute('data-val'); });
        b.addEventListener('mouseleave', function () { this.style.filter = ''; });
    });
});
</script>
@endsection
