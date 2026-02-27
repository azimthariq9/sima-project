@extends('layouts.sima')

@section('page_title',    'Monitoring Mahasiswa')
@section('page_section',  'KLN')
@section('page_subtitle', 'Daftar seluruh mahasiswa asing terdaftar')

@section('main_content')

{{-- Filter & Search Bar --}}
<div class="sima-card sima-fade mb-4" style="padding:14px 20px">
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">

        {{-- Search --}}
        <div style="position:relative;flex:1;min-width:200px">
            <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--c-text-3);font-size:13px"></i>
            <input type="text" id="search-input" class="sima-input" placeholder="Cari nama, NIM, atau negara…"
                   style="padding-left:36px;font-size:13px" oninput="filterTable()">
        </div>

        {{-- Filter negara --}}
        <select id="filter-negara" class="sima-input" style="width:180px;font-size:13px" onchange="filterTable()">
            <option value="">Semua Negara</option>
            <option>Uzbekistan</option>
            <option>Vietnam</option>
            <option>Korea Selatan</option>
            <option>Tajikistan</option>
            <option>Senegal</option>
            <option>Malaysia</option>
            <option>Jepang</option>
            <option>China</option>
        </select>

        {{-- Filter status --}}
        <select id="filter-status" class="sima-input" style="width:150px;font-size:13px" onchange="filterTable()">
            <option value="">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="inactive">Nonaktif</option>
            <option value="kritis">Dok. Kritis</option>
        </select>

        {{-- Export --}}
        <a href="#" class="sima-btn sima-btn--outline sima-btn--sm">
            <i class="fas fa-download"></i> Export Excel
        </a>
        <a href="{{ route('kln.validasi') }}" class="sima-btn sima-btn--gold sima-btn--sm">
            <i class="fas fa-clipboard-check"></i> Validasi Dokumen
        </a>

    </div>
</div>

{{-- Stat ringkasan --}}
<div class="row g-3 mb-4">
    @foreach([
        ['label'=>'Total Mahasiswa', 'val'=>$totalMahasiswa??120, 'icon'=>'fa-users',            'cl'=>'blue',  'sub'=>'Terdaftar aktif'],
        ['label'=>'Dok. Kritis',     'val'=>$criticalDocs??5,     'icon'=>'fa-triangle-exclamation','cl'=>'red',  'sub'=>'Expired / expiring'],
        ['label'=>'Dok. Pending',    'val'=>$pendingDocs??8,       'icon'=>'fa-clock',             'cl'=>'amber', 'sub'=>'Menunggu validasi'],
        ['label'=>'Negara Asal',     'val'=>$totalNegara??14,      'icon'=>'fa-earth-asia',         'cl'=>'teal',  'sub'=>'Negara terdaftar'],
    ] as $s)
    <div class="col-6 col-md-3 sima-fade sima-fade--{{ $loop->index + 1 }}">
        <div class="sima-stat sima-stat--{{ $s['cl'] }}">
            <div class="sima-stat__icon sima-stat__icon--{{ $s['cl'] }}"><i class="fas {{ $s['icon'] }}"></i></div>
            <div class="sima-stat__label">{{ $s['label'] }}</div>
            <span class="sima-stat__value" data-count="{{ $s['val'] }}">{{ $s['val'] }}</span>
            <div class="sima-stat__delta sima-stat__delta--flat"><i class="fas fa-circle" style="font-size:5px"></i> {{ $s['sub'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Tabel utama --}}
<div class="sima-card sima-fade sima-fade--5">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Daftar Mahasiswa Asing</h5>
            <div class="sima-card__subtitle" id="table-count">Menampilkan semua mahasiswa</div>
        </div>
    </div>
    <div style="overflow-x:auto">

        @php
        $mahasiswaList = $mahasiswaList ?? [
            ['init'=>'AR','name'=>'Ahmad Rahimov',  'nim'=>'50421001','flag'=>'🇺🇿','negara'=>'Uzbekistan',    'prodi'=>'Teknik Informatika','smt'=>6,'kitas'=>'03/03/2026','attendance'=>87,'status'=>'active',  'dok_status'=>'expiring'],
            ['init'=>'BL','name'=>'Bui Thi Lan',    'nim'=>'50421002','flag'=>'🇻🇳','negara'=>'Vietnam',       'prodi'=>'Sistem Informasi',  'smt'=>4,'kitas'=>'20/03/2026','attendance'=>82,'status'=>'active',  'dok_status'=>'expiring'],
            ['init'=>'PJ','name'=>'Park Jisoo',     'nim'=>'50421003','flag'=>'🇰🇷','negara'=>'Korea Selatan', 'prodi'=>'Manajemen',         'smt'=>2,'kitas'=>'01/06/2026','attendance'=>96,'status'=>'active',  'dok_status'=>'valid'],
            ['init'=>'YT','name'=>'Yuki Tanaka',    'nim'=>'50421004','flag'=>'🇯🇵','negara'=>'Jepang',        'prodi'=>'Teknik Informatika','smt'=>6,'kitas'=>'12/07/2026','attendance'=>90,'status'=>'active',  'dok_status'=>'valid'],
            ['init'=>'LM','name'=>'Li Xiao Ming',   'nim'=>'50421005','flag'=>'🇨🇳','negara'=>'China',         'prodi'=>'Akuntansi',         'smt'=>4,'kitas'=>'25/04/2026','attendance'=>79,'status'=>'active',  'dok_status'=>'valid'],
            ['init'=>'AD','name'=>'Amara Diallo',   'nim'=>'50421006','flag'=>'🇸🇳','negara'=>'Senegal',       'prodi'=>'Psikologi',         'smt'=>2,'kitas'=>'01/08/2026','attendance'=>81,'status'=>'active',  'dok_status'=>'expired'],
            ['init'=>'DM','name'=>'Diallo Mamadou', 'nim'=>'50421007','flag'=>'🇸🇳','negara'=>'Senegal',       'prodi'=>'Teknik Sipil',      'smt'=>3,'kitas'=>'15/09/2026','attendance'=>88,'status'=>'active',  'dok_status'=>'valid'],
            ['init'=>'PA','name'=>'Pham Duc Anh',   'nim'=>'50421008','flag'=>'🇻🇳','negara'=>'Vietnam',       'prodi'=>'Teknik Informatika','smt'=>4,'kitas'=>'05/03/2026','attendance'=>73,'status'=>'inactive','dok_status'=>'pending'],
        ];
        $ds = ['valid'=>['cls'=>'green','ico'=>'fa-circle-check','label'=>'Valid'],
               'expiring'=>['cls'=>'amber','ico'=>'fa-circle-exclamation','label'=>'Expiring'],
               'expired'=>['cls'=>'red','ico'=>'fa-circle-xmark','label'=>'Expired'],
               'pending'=>['cls'=>'blue','ico'=>'fa-clock','label'=>'Pending']];
        @endphp

        <table class="sima-table" id="monitoring-table">
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>NIM</th>
                    <th>Negara</th>
                    <th>Program Studi</th>
                    <th style="text-align:center">Smt</th>
                    <th>KITAS Berlaku</th>
                    <th>Kehadiran</th>
                    <th>Dok. Status</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mahasiswaList as $mhs)
                @php
                    $m  = is_array($mhs) ? $mhs : $mhs->toArray();
                    $di = $ds[$m['dok_status']] ?? $ds['pending'];
                @endphp
                <tr data-name="{{ strtolower($m['name']) }}"
                    data-negara="{{ strtolower($m['negara']) }}"
                    data-status="{{ $m['status'] }}"
                    data-dok="{{ $m['dok_status'] }}">
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="sima-avatar"
                                 style="width:32px;height:32px;font-size:10px;border-radius:9px;
                                        background:linear-gradient(135deg,#0D1B2A,#1B3256)">
                                {{ $m['init'] }}
                            </div>
                            <span style="font-weight:600;font-size:13px">{{ $m['name'] }}</span>
                        </div>
                    </td>
                    <td style="font-family:var(--f-mono);font-size:11.5px;color:var(--c-text-3)">{{ $m['nim'] }}</td>
                    <td>
                        <span style="font-size:18px;margin-right:5px">{{ $m['flag'] }}</span>
                        <span style="font-size:12.5px;color:var(--c-text-2)">{{ $m['negara'] }}</span>
                    </td>
                    <td style="font-size:12.5px;color:var(--c-text-2)">{{ $m['prodi'] }}</td>
                    <td style="font-family:var(--f-mono);text-align:center;font-weight:600">{{ $m['smt'] }}</td>
                    <td style="font-family:var(--f-mono);font-size:11.5px;
                               color:{{ $m['dok_status'] === 'expired' ? 'var(--c-red)' : ($m['dok_status'] === 'expiring' ? 'var(--c-amber)' : 'var(--c-text-3)') }}">
                        {{ $m['kitas'] }}
                    </td>
                    <td style="min-width:110px">
                        <div style="display:flex;align-items:center;gap:7px">
                            <div style="flex:1">
                                <div class="sima-prog">
                                    <div class="sima-prog__bar" data-w="{{ $m['attendance'] }}"
                                         style="background:{{ $m['attendance'] >= 85 ? '#059669' : ($m['attendance'] >= 80 ? '#D97706' : '#DC2626') }}">
                                    </div>
                                </div>
                            </div>
                            <span style="font-family:var(--f-mono);font-size:11px;font-weight:700;min-width:30px;
                                         color:{{ $m['attendance'] >= 85 ? '#059669' : ($m['attendance'] >= 80 ? '#D97706' : '#DC2626') }}">
                                {{ $m['attendance'] }}%
                            </span>
                        </div>
                    </td>
                    <td>
                        <span class="sima-badge sima-badge--{{ $di['cls'] }}">
                            <i class="fas {{ $di['ico'] }}"></i> {{ $di['label'] }}
                        </span>
                    </td>
                    <td>
                        @if($m['status'] === 'active')
                            <span class="sima-badge sima-badge--green"><i class="fas fa-circle" style="font-size:5px"></i> Aktif</span>
                        @else
                            <span class="sima-badge sima-badge--grey"><i class="fas fa-circle" style="font-size:5px"></i> Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:5px">
                            <button class="sima-btn sima-btn--outline sima-btn--sm" title="Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="{{ route('kln.validasi') }}" class="sima-btn sima-btn--blue sima-btn--sm" title="Validasi">
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
                <i class="fas fa-info-circle"></i>
                Total {{ count($mahasiswaList ?? []) }} mahasiswa ditampilkan
            </span>
            <div style="display:flex;gap:6px">
                <button class="sima-btn sima-btn--outline sima-btn--sm"><i class="fas fa-chevron-left"></i></button>
                <button class="sima-btn sima-btn--sm" style="min-width:34px">1</button>
                <button class="sima-btn sima-btn--outline sima-btn--sm">2</button>
                <button class="sima-btn sima-btn--outline sima-btn--sm"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page_js')
<script>
function filterTable() {
    const q       = document.getElementById('search-input').value.toLowerCase();
    const negara  = document.getElementById('filter-negara').value.toLowerCase();
    const status  = document.getElementById('filter-status').value;
    const rows    = document.querySelectorAll('#monitoring-table tbody tr');
    let visible   = 0;

    rows.forEach(function (row) {
        const name    = row.dataset.name    || '';
        const neg     = row.dataset.negara  || '';
        const st      = row.dataset.status  || '';
        const dok     = row.dataset.dok     || '';

        const matchQ  = !q      || name.includes(q) || neg.includes(q);
        const matchN  = !negara || neg.includes(negara);
        const matchS  = !status || st === status || (status === 'kritis' && (dok === 'expired' || dok === 'expiring'));

        const show = matchQ && matchN && matchS;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    document.getElementById('table-count').textContent =
        'Menampilkan ' + visible + ' mahasiswa';
}
</script>
@endsection
