@extends('layouts.sima')

@section('page_title', 'Dashboard KLN')
@section('page_subtitle', 'Monitoring mahasiswa asing — Kantor Layanan Internasional')

@section('main_content')

{{-- ═══════════════════════════════════════════ --}}
{{-- STAT ROW                                     --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row">

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--1">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--navy"></div>
            <div class="sima-stat__icon sima-stat__icon--navy">
                <i class="fas fa-users"></i>
            </div>
            <div class="sima-stat__label">Total Mahasiswa Asing</div>
            <div class="sima-stat__value" data-count="120">120</div>
            <div class="sima-stat__delta sima-stat__delta--up">
                <i class="fas fa-arrow-up"></i> +8 dari semester lalu
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--2">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--amber"></div>
            <div class="sima-stat__icon sima-stat__icon--amber">
                <i class="fas fa-file-clock"></i>
            </div>
            <div class="sima-stat__label">Dokumen Pending</div>
            <div class="sima-stat__value" data-count="8">8</div>
            <div class="sima-stat__delta sima-stat__delta--down">
                <i class="fas fa-arrow-down"></i> -2 dari kemarin
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--3">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--red"></div>
            <div class="sima-stat__icon sima-stat__icon--red">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="sima-stat__label">Dokumen Expired</div>
            <div class="sima-stat__value" data-count="5">5</div>
            <div class="sima-stat__delta sima-stat__delta--down">
                <i class="fas fa-exclamation"></i> Butuh tindakan segera
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--4">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--emerald"></div>
            <div class="sima-stat__icon sima-stat__icon--emerald">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="sima-stat__label">Absensi Hari Ini</div>
            <div class="sima-stat__value" data-count="14">14</div>
            <div class="sima-stat__delta sima-stat__delta--flat">
                <i class="fas fa-circle" style="font-size:7px"></i> dari 18 terjadwal
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- SECONDARY STATS                              --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row">

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--3">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--teal"></div>
            <div class="sima-stat__icon sima-stat__icon--teal">
                <i class="fas fa-globe"></i>
            </div>
            <div class="sima-stat__label">Negara Asal</div>
            <div class="sima-stat__value" data-count="14">14</div>
            <div class="sima-stat__delta sima-stat__delta--flat">Negara berbeda</div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--4">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--purple"></div>
            <div class="sima-stat__icon sima-stat__icon--purple">
                <i class="fas fa-check-double"></i>
            </div>
            <div class="sima-stat__label">Divalidasi Hari Ini</div>
            <div class="sima-stat__value" data-count="3">3</div>
            <div class="sima-stat__delta sima-stat__delta--up">
                <i class="fas fa-arrow-up"></i> Dokumen selesai
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--5">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--gold"></div>
            <div class="sima-stat__icon sima-stat__icon--gold">
                <i class="fas fa-star"></i>
            </div>
            <div class="sima-stat__label">Mahasiswa Beasiswa</div>
            <div class="sima-stat__value" data-count="32">32</div>
            <div class="sima-stat__delta sima-stat__delta--flat">26.7% dari total</div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4 sima-fade sima-fade--6">
        <div class="sima-stat">
            <div class="sima-stat__accent sima-stat__accent--blue"></div>
            <div class="sima-stat__icon sima-stat__icon--blue">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="sima-stat__label">Jadwal Aktif Minggu Ini</div>
            <div class="sima-stat__value" data-count="24">24</div>
            <div class="sima-stat__delta sima-stat__delta--flat">Sesi terjadwal</div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- MAIN ROW                                    --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row">

    {{-- ── Expired Documents Table ─────────── --}}
    <div class="col-md-8 mb-4 sima-fade sima-fade--5">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Dokumen Kritis — Perlu Tindakan</h5>
                    <div class="sima-card__subtitle">Expired &amp; akan expired dalam 30 hari</div>
                </div>
                <a href="#" class="sima-card__action">Validasi Semua</a>
            </div>
            <div class="sima-card__body" style="padding:0">
                <table class="sima-table">
                    <thead>
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Negara</th>
                            <th>Dokumen</th>
                            <th>Masa Berlaku</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $rows = [
                            ['John Doe',      'UZ', 'Uzbekistan', 'KITAS',       '03/03/2026', 'expiring'],
                            ['Ahmad Rahimov', 'TJ', 'Tajikistan', 'Paspor',      '15/01/2026', 'expired'],
                            ['Bui Thi Lan',   'VN', 'Vietnam',    'KITAS',       '20/03/2026', 'expiring'],
                            ['Park Jisoo',    'KR', 'Korea',      'Asuransi',    '28/02/2026', 'expiring'],
                            ['Amara Diallo',  'SN', 'Senegal',    'SKCK',        '10/01/2026', 'expired'],
                        ];
                        @endphp

                        @foreach($rows as $r)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <div class="sima-avatar" style="background:linear-gradient(135deg,#0D1B2A,#1B3256);color:white;font-size:11px">
                                        {{ strtoupper(substr($r[0],0,1)) }}{{ strtoupper(substr(explode(' ',$r[0])[1]??'',0,1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:500;font-size:13px">{{ $r[0] }}</div>
                                        <div style="font-size:11px;color:var(--sima-text-muted)">{{ $r[1] }} · {{ $r[2] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-size:20px">
                                    @if($r[2]=='Uzbekistan')🇺🇿@elseif($r[2]=='Tajikistan')🇹🇯@elseif($r[2]=='Vietnam')🇻🇳@elseif($r[2]=='Korea')🇰🇷@else🇸🇳@endif
                                </span>
                            </td>
                            <td style="font-weight:500">{{ $r[3] }}</td>
                            <td style="font-family:var(--font-mono);font-size:12px;color:var(--sima-text-muted)">{{ $r[4] }}</td>
                            <td>
                                @if($r[5]=='expired')
                                    <span class="sima-badge sima-badge--red"><i class="fas fa-times-circle"></i> Expired</span>
                                @else
                                    <span class="sima-badge sima-badge--amber"><i class="fas fa-exclamation-circle"></i> Expiring</span>
                                @endif
                            </td>
                            <td>
                                <a href="#" style="font-size:12px;color:var(--sima-blue);font-weight:500;white-space:nowrap">
                                    <i class="fas fa-external-link-alt"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Country Distribution ──────────── --}}
    <div class="col-md-4 mb-4 sima-fade sima-fade--6">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Sebaran Negara</h5>
                    <div class="sima-card__subtitle">Top 6 negara asal</div>
                </div>
            </div>
            <div class="sima-card__body">

                @php
                $countries = [
                    ['Uzbekistan',  38, '#2563EB', '🇺🇿'],
                    ['Vietnam',     22, '#0D9488', '🇻🇳'],
                    ['Korea',       18, '#7C3AED', '🇰🇷'],
                    ['Tajikistan',  15, '#D97706', '🇹🇯'],
                    ['Senegal',     12, '#DC2626', '🇸🇳'],
                    ['Malaysia',     9, '#059669', '🇲🇾'],
                    ['Lainnya',      6, '#94A3B8', '🌍'],
                ];
                $total = array_sum(array_column($countries, 1));
                @endphp

                @foreach($countries as $c)
                <div style="margin-bottom:14px">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px">
                        <span style="font-size:13px;font-weight:500;color:var(--sima-text-primary)">
                            {{ $c[3] }} {{ $c[0] }}
                        </span>
                        <span style="font-family:var(--font-mono);font-size:12px;color:var(--sima-text-muted)">
                            {{ $c[1] }} <span style="color:var(--sima-text-muted);font-size:10px">({{ round($c[1]/$total*100) }}%)</span>
                        </span>
                    </div>
                    <div class="sima-progress">
                        <div class="sima-progress__bar" style="width:{{ round($c[1]/$total*100) }}%;background:{{ $c[2] }}"></div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- BOTTOM ROW: Pending Validasi + Quick Post   --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="row">

    <div class="col-md-6 mb-4 sima-fade sima-fade--7">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Antrian Validasi</h5>
                    <div class="sima-card__subtitle">8 dokumen menunggu review</div>
                </div>
                <a href="#" class="sima-card__action">Validasi Sekarang</a>
            </div>
            <div class="sima-card__body">

                @php
                $queue = [
                    ['Sung Ji-Ho',    'Surat Aktif',          '08:24', 'blue'],
                    ['Pham Duc Anh',  'Perpanjangan KITAS',   '09:11', 'amber'],
                    ['Diallo Mamadou','SKCK Baru',             '10:03', 'purple'],
                    ['Li Xiao Ming',  'Asuransi Kesehatan',   '11:30', 'teal'],
                    ['Yuki Tanaka',   'Surat Domisili',        '13:00', 'emerald'],
                ];
                @endphp

                @foreach($queue as $i => $q)
                <div style="display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid var(--sima-border-soft)">
                    <div style="font-family:var(--font-mono);font-size:20px;font-weight:700;color:var(--sima-border);min-width:32px">
                        {{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}
                    </div>
                    <div style="flex:1">
                        <div style="font-size:13px;font-weight:500;color:var(--sima-text-primary)">{{ $q[0] }}</div>
                        <div style="font-size:12px;color:var(--sima-text-muted)">{{ $q[1] }}</div>
                    </div>
                    <div style="font-family:var(--font-mono);font-size:11px;color:var(--sima-text-muted);text-align:right;margin-right:8px">
                        {{ $q[2] }}
                    </div>
                    <a href="#" style="padding:6px 12px;background:var(--sima-blue-soft);color:var(--sima-blue);border-radius:8px;font-size:12px;font-weight:500;text-decoration:none;white-space:nowrap">
                        Review
                    </a>
                </div>
                @endforeach

            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4 sima-fade sima-fade--8">
        <div class="sima-card h-100">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Post Pengumuman Cepat</h5>
                    <div class="sima-card__subtitle">Broadcast ke seluruh mahasiswa asing</div>
                </div>
            </div>
            <div class="sima-card__body">

                <div style="margin-bottom:16px">
                    <label style="font-size:11px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:var(--sima-text-muted);display:block;margin-bottom:6px">Judul Pengumuman</label>
                    <input type="text" placeholder="e.g. Perpanjangan Izin Tinggal..."
                        style="width:100%;padding:10px 14px;border:1px solid var(--sima-border);border-radius:10px;font-family:var(--font-body);font-size:13px;color:var(--sima-text-primary);outline:none;transition:border-color 0.15s"
                        onfocus="this.style.borderColor='var(--sima-blue)'"
                        onblur="this.style.borderColor='var(--sima-border)'">
                </div>

                <div style="margin-bottom:16px">
                    <label style="font-size:11px;font-weight:600;letter-spacing:0.06em;text-transform:uppercase;color:var(--sima-text-muted);display:block;margin-bottom:6px">Pesan</label>
                    <textarea rows="4" placeholder="Tulis isi pengumuman di sini..."
                        style="width:100%;padding:10px 14px;border:1px solid var(--sima-border);border-radius:10px;font-family:var(--font-body);font-size:13px;color:var(--sima-text-primary);outline:none;resize:vertical;transition:border-color 0.15s"
                        onfocus="this.style.borderColor='var(--sima-blue)'"
                        onblur="this.style.borderColor='var(--sima-border)'"></textarea>
                </div>

                <div style="display:flex;gap:10px">
                    <select style="flex:1;padding:10px 14px;border:1px solid var(--sima-border);border-radius:10px;font-family:var(--font-body);font-size:13px;color:var(--sima-text-secondary);outline:none;background:white">
                        <option>Semua Mahasiswa Asing</option>
                        <option>Uzbekistan</option>
                        <option>Vietnam</option>
                        <option>Korea</option>
                    </select>
                    <button style="padding:10px 20px;background:linear-gradient(135deg,var(--sima-gold),#A67C30);color:white;border:none;border-radius:10px;font-family:var(--font-body);font-size:13px;font-weight:600;cursor:pointer;letter-spacing:0.02em;white-space:nowrap">
                        <i class="fas fa-paper-plane"></i> Kirim
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection
