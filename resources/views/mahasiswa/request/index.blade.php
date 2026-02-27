@extends('layouts.sima')

@section('page_title',   'Profil Saya')
@section('page_section', 'Mahasiswa')
@section('page_subtitle','Biodata dan kelengkapan dokumen Anda')

@section('main_content')

<div class="row g-3">

    {{-- Profile Card --}}
    <div class="col-md-4 sima-fade">
        <div class="sima-card">
            <div class="sima-card__body" style="text-align:center;padding:28px 20px">
                <div class="sima-avatar" style="width:80px;height:80px;border-radius:20px;font-size:28px;background:linear-gradient(135deg,#2563EB,#7C3AED);margin:0 auto 16px">
                    {{ strtoupper(substr(auth()->user()->name ?? 'M', 0, 2)) }}
                </div>
                <div style="font-family:var(--f-display);font-size:18px;font-weight:700;color:var(--c-text-1);margin-bottom:4px">
                    {{ auth()->user()->name ?? 'Ahmad Rahimov' }}
                </div>
                <div style="font-size:12.5px;color:var(--c-text-3);margin-bottom:12px">
                    NIM: {{ auth()->user()->nim ?? '50421001' }}
                </div>
                <span class="sima-badge sima-badge--green" style="margin-bottom:20px;padding:5px 14px">
                    <i class="fas fa-circle" style="font-size:6px"></i> Mahasiswa Aktif
                </span>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:16px">
                    <div style="padding:12px;background:var(--c-bg);border-radius:10px;border:1px solid var(--c-border-soft)">
                        <div style="font-family:var(--f-display);font-size:24px;font-weight:700;color:var(--c-text-1)">{{ $semester ?? 6 }}</div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:var(--c-text-3)">Semester</div>
                    </div>
                    <div style="padding:12px;background:var(--c-bg);border-radius:10px;border:1px solid var(--c-border-soft)">
                        <div style="font-family:var(--f-display);font-size:24px;font-weight:700;color:var(--c-text-1)">{{ $ipk ?? '3.72' }}</div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:var(--c-text-3)">IPK</div>
                    </div>
                </div>

                <div style="margin-top:16px;display:flex;flex-direction:column;gap:6px">
                    <a href="{{ route('profile.edit') }}" class="sima-btn sima-btn--outline sima-btn--sm sima-btn--full">
                        <i class="fas fa-pen"></i> Edit Profil
                    </a>
                </div>
            </div>
        </div>

        {{-- Contact info --}}
        <div class="sima-card" style="margin-top:12px">
            <div class="sima-card__header"><h5 class="sima-card__title">Kontak</h5></div>
            <div class="sima-card__body">
                @foreach([
                    ['fas fa-envelope','Email',auth()->user()->email ?? 'ahmad@mail.com'],
                    ['fas fa-phone','No. HP','+62 812 3456 7890'],
                    ['fas fa-flag','Kewarganegaraan','Uzbekistan 🇺🇿'],
                    ['fas fa-map-marker-alt','Alamat','Jl. Margonda No. 45, Depok'],
                ] as [$icon,$label,$val])
                <div style="display:flex;gap:11px;padding:9px 0;border-bottom:1px solid var(--c-border-soft)">
                    <div style="width:30px;height:30px;border-radius:8px;background:var(--c-bg);display:flex;align-items:center;justify-content:center;color:var(--c-accent);font-size:12px;flex-shrink:0">
                        <i class="{{ $icon }}"></i>
                    </div>
                    <div>
                        <div style="font-size:11px;color:var(--c-text-3);font-weight:500">{{ $label }}</div>
                        <div style="font-size:13px;color:var(--c-text-1);font-weight:500;margin-top:1px">{{ $val }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Biodata + Dokumen --}}
    <div class="col-md-8 sima-fade sima-fade--1">

        {{-- Biodata --}}
        <div class="sima-card" style="margin-bottom:12px">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Biodata Akademik</h5>
                    <div class="sima-card__subtitle">Informasi resmi terdaftar di universitas</div>
                </div>
                <a href="{{ route('profile.edit') }}" class="sima-card__action"><i class="fas fa-pen"></i> Edit</a>
            </div>
            <div class="sima-card__body">
                <div class="row g-3">
                    @foreach([
                        ['Nama Lengkap', auth()->user()->name ?? 'Ahmad Rahimov'],
                        ['NIM', auth()->user()->nim ?? '50421001'],
                        ['Program Studi','Teknik Informatika'],
                        ['Fakultas','Ilmu Komputer'],
                        ['Jenjang','S1'],
                        ['Tahun Masuk','2022'],
                        ['Status','Aktif'],
                        ['Pembimbing','Dr. Siti Rahayu, M.T.'],
                    ] as [$label, $val])
                    <div class="col-md-6">
                        <div style="padding:12px;background:var(--c-bg);border-radius:10px;border:1px solid var(--c-border-soft)">
                            <div style="font-size:11px;color:var(--c-text-3);font-weight:500;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px">{{ $label }}</div>
                            <div style="font-size:14px;font-weight:600;color:var(--c-text-1)">{{ $val }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Dokumen --}}
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Dokumen Saya</h5>
                    <div class="sima-card__subtitle">Status kelengkapan dokumen resmi</div>
                </div>
                <a href="{{ route('mahasiswa.request.create') }}" class="sima-btn sima-btn--sm">
                    <i class="fas fa-plus"></i> Upload
                </a>
            </div>
            <div class="sima-card__body" style="padding:0">
                @php
                $docs = $documents ?? [
                    ['name'=>'Paspor','status'=>'valid','exp'=>'12 Jan 2028','icon'=>'fa-passport','note'=>'Sesuai'],
                    ['name'=>'KITAS','status'=>'expiring','exp'=>'03 Mar 2026','icon'=>'fa-id-card','note'=>'Segera perpanjang'],
                    ['name'=>'Surat Keterangan Aktif','status'=>'pending','exp'=>'Menunggu','icon'=>'fa-file-signature','note'=>'Proses KLN'],
                    ['name'=>'Asuransi Kesehatan','status'=>'valid','exp'=>'30 Jun 2026','icon'=>'fa-heart-pulse','note'=>'Sesuai'],
                    ['name'=>'SKCK','status'=>'expired','exp'=>'15 Jan 2026','icon'=>'fa-shield-halved','note'=>'Perlu diperbarui'],
                ];
                $sm = ['valid'=>['label'=>'Valid','cls'=>'green','ico'=>'fa-circle-check'],'expiring'=>['label'=>'Segera Expired','cls'=>'amber','ico'=>'fa-circle-exclamation'],'pending'=>['label'=>'Pending','cls'=>'blue','ico'=>'fa-clock'],'expired'=>['label'=>'Expired','cls'=>'red','ico'=>'fa-circle-xmark']];
                @endphp
                <table class="sima-table">
                    <thead><tr><th>Dokumen</th><th>Berlaku s/d</th><th>Keterangan</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($docs as $doc)
                        @php $s=is_array($doc)?$doc:$doc->toArray();$st=$s['status'];$info=$sm[$st]??$sm['pending']; @endphp
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <div style="width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:13px;
                                         background:{{ $st==='valid'?'#ECFDF5':($st==='expired'?'#FEF2F2':($st==='expiring'?'#FFFBEB':'#EFF6FF')) }};
                                         color:{{ $st==='valid'?'#059669':($st==='expired'?'#DC2626':($st==='expiring'?'#D97706':'#2563EB')) }}">
                                        <i class="fas {{ $s['icon']??'fa-file' }}"></i>
                                    </div>
                                    <span style="font-weight:500;font-size:13.5px">{{ $s['name'] }}</span>
                                </div>
                            </td>
                            <td style="font-family:var(--f-mono);font-size:12px;color:var(--c-text-3)">{{ $s['exp'] }}</td>
                            <td style="font-size:12.5px;color:var(--c-text-3)">{{ $s['note']??'-' }}</td>
                            <td><span class="sima-badge sima-badge--{{ $info['cls'] }}"><i class="fas {{ $info['ico'] }}"></i> {{ $info['label'] }}</span></td>
                            <td>
                                @if($st==='expired'||$st==='expiring')
                                <a href="{{ route('mahasiswa.request.create') }}" class="sima-btn sima-btn--sm sima-btn--gold" style="font-size:11px;padding:4px 10px"><i class="fas fa-rotate"></i> Perbarui</a>
                                @elseif($st==='valid')
                                <button class="sima-btn sima-btn--sm sima-btn--outline" style="font-size:11px;padding:4px 10px"><i class="fas fa-eye"></i> Lihat</button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
