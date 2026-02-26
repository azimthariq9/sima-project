@extends('layouts.sima')

@section('page_title',    'Analitik & Laporan')
@section('page_section',  'KLN')
@section('page_subtitle', 'Data statistik dan tren mahasiswa asing')

@section('main_content')

{{-- Ringkasan KPI --}}
<div class="row g-3 mb-4">
    @foreach([
        ['label'=>'Total Mahasiswa',   'val'=>$totalMahasiswa??120,'ico'=>'fa-users',             'cl'=>'navy',  'sub'=>'+8 semester ini'],
        ['label'=>'Dok. Valid',         'val'=>$validDocs??87,      'ico'=>'fa-shield-check',      'cl'=>'green', 'sub'=>'72% dari total'],
        ['label'=>'Rata-rata Kehadiran','val'=>($avgAttendance??85).'%','ico'=>'fa-chart-bar',     'cl'=>'blue',  'sub'=>'Seluruh mahasiswa'],
        ['label'=>'Negara Terdaftar',   'val'=>$totalNegara??14,    'ico'=>'fa-earth-asia',        'cl'=>'teal',  'sub'=>'+2 negara baru'],
    ] as $s)
    <div class="col-6 col-md-3 sima-fade sima-fade--{{ $loop->index+1 }}">
        <div class="sima-stat sima-stat--{{ $s['cl'] }}">
            <div class="sima-stat__icon sima-stat__icon--{{ $s['cl'] }}"><i class="fas {{ $s['ico'] }}"></i></div>
            <div class="sima-stat__label">{{ $s['label'] }}</div>
            <span class="sima-stat__value {{ is_numeric($s['val']) ? '' : '' }}" {{ is_numeric($s['val']) ? "data-count={$s['val']}" : '' }}>{{ $s['val'] }}</span>
            <div class="sima-stat__delta sima-stat__delta--up"><i class="fas fa-arrow-up"></i> {{ $s['sub'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-3 mb-4">

    {{-- Chart tren 6 bulan --}}
    <div class="col-md-7 sima-fade sima-fade--5">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Tren Mahasiswa 6 Bulan Terakhir</h5>
                    <div class="sima-card__subtitle">Aktif vs Dokumen Valid</div>
                </div>
                <select class="sima-input" style="width:130px;font-size:12px">
                    <option>6 Bulan</option>
                    <option>1 Tahun</option>
                    <option>2 Tahun</option>
                </select>
            </div>
            <div class="sima-card__body">
                <div style="display:flex;gap:18px;margin-bottom:16px">
                    <div style="display:flex;align-items:center;gap:7px;font-size:12px;color:var(--c-text-2)">
                        <div style="width:10px;height:10px;border-radius:3px;background:rgba(37,99,235,.75)"></div>
                        Mahasiswa Aktif
                    </div>
                    <div style="display:flex;align-items:center;gap:7px;font-size:12px;color:var(--c-text-2)">
                        <div style="width:10px;height:10px;border-radius:3px;background:rgba(5,150,105,.7)"></div>
                        Dok. Valid
                    </div>
                    <div style="display:flex;align-items:center;gap:7px;font-size:12px;color:var(--c-text-2)">
                        <div style="width:10px;height:10px;border-radius:3px;background:rgba(220,38,38,.6)"></div>
                        Dok. Kritis
                    </div>
                </div>

                @php
                $chartData = $monthlyChart ?? [
                    ['label'=>'Okt','a'=>108,'b'=>72,'c'=>8],
                    ['label'=>'Nov','a'=>112,'b'=>78,'c'=>7],
                    ['label'=>'Des','a'=>110,'b'=>75,'c'=>9],
                    ['label'=>'Jan','a'=>115,'b'=>80,'c'=>6],
                    ['label'=>'Feb','a'=>118,'b'=>85,'c'=>5],
                    ['label'=>'Mar','a'=>120,'b'=>87,'c'=>5],
                ];
                $maxVal = 130;
                @endphp

                <div style="display:flex;align-items:flex-end;gap:6px;height:140px;border-bottom:1px solid var(--c-border-soft);padding-bottom:4px;margin-bottom:6px">
                    @foreach($chartData as $cd)
                    @php $d = is_array($cd) ? $cd : $cd->toArray(); @endphp
                    <div style="flex:1;display:flex;align-items:flex-end;gap:2px">
                        <div class="sima-bar" data-h="{{ round(($d['a']/$maxVal)*100) }}" data-val="{{ $d['a'] }}"
                             style="flex:1;border-radius:4px 4px 0 0;min-height:3px;background:rgba(37,99,235,.75);cursor:pointer">
                        </div>
                        <div class="sima-bar" data-h="{{ round(($d['b']/$maxVal)*100) }}" data-val="{{ $d['b'] }}"
                             style="flex:1;border-radius:4px 4px 0 0;min-height:3px;background:rgba(5,150,105,.7);cursor:pointer">
                        </div>
                        <div class="sima-bar" data-h="{{ round(($d['c']/$maxVal)*100) }}" data-val="{{ $d['c'] }}"
                             style="flex:1;border-radius:4px 4px 0 0;min-height:3px;background:rgba(220,38,38,.6);cursor:pointer">
                        </div>
                    </div>
                    @endforeach
                </div>
                <div style="display:flex;gap:6px">
                    @foreach($chartData as $cd)
                    @php $d = is_array($cd) ? $cd : $cd->toArray(); @endphp
                    <div style="flex:1;text-align:center;font-family:var(--f-mono);font-size:9.5px;color:var(--c-text-3)">
                        {{ $d['label'] }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Sebaran negara --}}
    <div class="col-md-5 sima-fade sima-fade--6">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <h5 class="sima-card__title">Sebaran Negara</h5>
                <div class="sima-card__subtitle">Top 7 dari {{ $totalNegara ?? 14 }} negara</div>
            </div>
            <div class="sima-card__body">
                @foreach([
                    ['flag'=>'🇺🇿','name'=>'Uzbekistan',    'count'=>38,'pct'=>32,'color'=>'#2563EB'],
                    ['flag'=>'🇻🇳','name'=>'Vietnam',       'count'=>22,'pct'=>18,'color'=>'#0D9488'],
                    ['flag'=>'🇰🇷','name'=>'Korea Selatan', 'count'=>18,'pct'=>15,'color'=>'#7C3AED'],
                    ['flag'=>'🇹🇯','name'=>'Tajikistan',    'count'=>15,'pct'=>12,'color'=>'#D97706'],
                    ['flag'=>'🇸🇳','name'=>'Senegal',       'count'=>12,'pct'=>10,'color'=>'#DC2626'],
                    ['flag'=>'🇲🇾','name'=>'Malaysia',      'count'=>9, 'pct'=>7, 'color'=>'#059669'],
                    ['flag'=>'🌍','name'=>'Lainnya',        'count'=>6, 'pct'=>5, 'color'=>'#94A3B8'],
                ] as $c)
                <div style="margin-bottom:11px">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px">
                        <div style="display:flex;align-items:center;gap:7px">
                            <span style="font-size:16px">{{ $c['flag'] }}</span>
                            <span style="font-size:12.5px;font-weight:500;color:var(--c-text-1)">{{ $c['name'] }}</span>
                        </div>
                        <div style="font-family:var(--f-mono);font-size:11.5px;color:var(--c-text-3)">
                            {{ $c['count'] }} <span style="font-size:10px">({{ $c['pct'] }}%)</span>
                        </div>
                    </div>
                    <div class="sima-prog">
                        <div class="sima-prog__bar" data-w="{{ $c['pct'] }}" style="background:{{ $c['color'] }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

{{-- Kehadiran per prodi --}}
<div class="row g-3">
    <div class="col-md-6 sima-fade">
        <div class="sima-card">
            <div class="sima-card__header">
                <h5 class="sima-card__title">Kehadiran per Program Studi</h5>
            </div>
            <div class="sima-card__body">
                @foreach([
                    ['name'=>'Teknik Informatika',  'mhs'=>32,'avg'=>88,'color'=>'#059669'],
                    ['name'=>'Sistem Informasi',    'mhs'=>18,'avg'=>83,'color'=>'#D97706'],
                    ['name'=>'Manajemen',           'mhs'=>22,'avg'=>91,'color'=>'#059669'],
                    ['name'=>'Akuntansi',           'mhs'=>15,'avg'=>79,'color'=>'#DC2626'],
                    ['name'=>'Psikologi',           'mhs'=>12,'avg'=>86,'color'=>'#059669'],
                    ['name'=>'Teknik Sipil',        'mhs'=>10,'avg'=>82,'color'=>'#D97706'],
                ] as $prodi)
                <div style="padding:12px 0;border-bottom:1px solid var(--c-border-soft)">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:7px">
                        <div>
                            <div style="font-size:13px;font-weight:600;color:var(--c-text-1)">{{ $prodi['name'] }}</div>
                            <div style="font-size:11px;color:var(--c-text-3)">{{ $prodi['mhs'] }} mahasiswa</div>
                        </div>
                        <div style="font-family:var(--f-mono);font-size:18px;font-weight:700;color:{{ $prodi['color'] }}">
                            {{ $prodi['avg'] }}%
                        </div>
                    </div>
                    <div class="sima-prog">
                        <div class="sima-prog__bar" data-w="{{ $prodi['avg'] }}" style="background:{{ $prodi['color'] }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Export & laporan --}}
    <div class="col-md-6 sima-fade sima-fade--1">
        <div class="sima-card">
            <div class="sima-card__header">
                <h5 class="sima-card__title">Ekspor Laporan</h5>
                <div class="sima-card__subtitle">Download data dalam berbagai format</div>
            </div>
            <div class="sima-card__body">
                @foreach([
                    ['title'=>'Laporan Kehadiran Semester Ini',  'desc'=>'Kehadiran semua mahasiswa per matkul','ico'=>'fa-chart-bar', 'color'=>'#2563EB','bg'=>'#EFF6FF'],
                    ['title'=>'Daftar Dokumen Expired/Expiring', 'desc'=>'Semua dokumen kritis yang perlu tindakan','ico'=>'fa-file-exclamation','color'=>'#DC2626','bg'=>'#FEF2F2'],
                    ['title'=>'Sebaran Mahasiswa per Negara',    'desc'=>'Data demografis mahasiswa asing','ico'=>'fa-earth-asia','color'=>'#0D9488','bg'=>'#F0FDFA'],
                    ['title'=>'Riwayat Validasi Dokumen',        'desc'=>'Log semua tindakan validasi KLN','ico'=>'fa-clipboard-list','color'=>'#7C3AED','bg'=>'#F5F3FF'],
                    ['title'=>'Laporan Aktivitas BIPA',          'desc'=>'Kehadiran kelas BIPA per sesi','ico'=>'fa-language','color'=>'#D97706','bg'=>'#FFFBEB'],
                ] as $rep)
                <div style="display:flex;align-items:center;gap:12px;padding:12px;background:var(--c-bg);border-radius:10px;border:1px solid var(--c-border-soft);margin-bottom:8px">
                    <div style="width:38px;height:38px;border-radius:10px;background:{{ $rep['bg'] }};color:{{ $rep['color'] }};display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0">
                        <i class="fas {{ $rep['ico'] }}"></i>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:13px;font-weight:600;color:var(--c-text-1)">{{ $rep['title'] }}</div>
                        <div style="font-size:11.5px;color:var(--c-text-3)">{{ $rep['desc'] }}</div>
                    </div>
                    <div style="display:flex;gap:5px;flex-shrink:0">
                        <button class="sima-btn sima-btn--outline sima-btn--sm" title="Excel">
                            <i class="fas fa-file-excel" style="color:#059669"></i>
                        </button>
                        <button class="sima-btn sima-btn--outline sima-btn--sm" title="PDF">
                            <i class="fas fa-file-pdf" style="color:#DC2626"></i>
                        </button>
                    </div>
                </div>
                @endforeach
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
            setTimeout(function () { el.style.height = el.getAttribute('data-h') + '%'; }, 80);
            io.unobserve(el);
        });
    }, { threshold: 0.1 });
    bars.forEach(function (b) {
        b.style.height = '0';
        b.style.transition = 'height .85s cubic-bezier(0.16,1,0.3,1)';
        io.observe(b);
    });
    bars.forEach(function (b) {
        b.addEventListener('mouseenter', function () { this.style.filter='brightness(1.18)'; this.title=this.getAttribute('data-val'); });
        b.addEventListener('mouseleave', function () { this.style.filter=''; });
    });
});
</script>
@endsection
