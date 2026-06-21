@extends('layouts.sima')

@section('page_title',   'Request Dokumen')
@section('page_section', 'Mahasiswa')
@section('page_subtitle','Ajukan permintaan dokumen ke KLN')

@section('main_content')

@if($errors->any())
<div style="background:rgba(220,38,38,.07);border:1px solid rgba(220,38,38,.2);border-radius:10px;padding:14px 18px;margin-bottom:16px">
    <div style="font-size:13px;font-weight:600;color:#dc2626;margin-bottom:6px"><i class="fas fa-circle-exclamation"></i> Periksa isian berikut:</div>
    @foreach($errors->all() as $error)
        <div style="font-size:12.5px;color:#b91c1c;margin-top:3px">· {{ $error }}</div>
    @endforeach
</div>
@endif

<div class="row g-3">

    <div class="col-md-7 sima-fade">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Form Request Dokumen</h5>
                    <div class="sima-card__subtitle">Isi data dengan lengkap dan benar</div>
                </div>
            </div>
            <div class="sima-card__body">
            <form method="POST" action="{{ route('mahasiswa.request.store') }}" enctype="multipart/form-data">
                @csrf

                <div style="margin-bottom:18px">
                    <label class="sima-label">
                        Jenis Dokumen <span style="color:var(--c-red)">*</span>
                    </label>

                    <select name="tipeDkmn" class="sima-input" required>
                        <option value="">— Pilih jenis dokumen —</option>

                        @foreach(\App\Enums\TipeDok::cases() as $dok)
                            <option value="{{ $dok->value }}"
                                {{ old('tipeDkmn') == $dok->value ? 'selected' : '' }}>
                                {{ str_replace('_',' ', $dok->value) }}
                            </option>
                        @endforeach
                    </select>

                    @error('tipeDkmn')
                        <div style="font-size:12px;color:var(--c-red);margin-top:5px">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>


                <div style="margin-bottom:18px">
                    <label class="sima-label">
                        Keperluan / Keterangan <span style="color:var(--c-red)">*</span>
                    </label>

                    <textarea name="message"
                            class="sima-input"
                            rows="3"
                            placeholder="Jelaskan keperluan dokumen ini…"
                            required
                            style="resize:vertical">{{ old('message') }}</textarea>

                    @error('message')
                        <div style="font-size:12px;color:var(--c-red);margin-top:5px">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>


                <div style="display:flex;gap:10px;margin-top:24px">
                    <button type="submit" class="sima-btn">
                        <i class="fas fa-paper-plane"></i> Kirim Request
                    </button>

                    <a href="{{ route('mahasiswa.dashboard') }}"
                    class="sima-btn sima-btn--outline">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                </div>

            </form>            </div>
        </div>
    </div>

    <div class="col-md-5 sima-fade sima-fade--1">

        {{-- Status request sebelumnya --}}
        <div class="sima-card" style="margin-bottom:12px">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Riwayat Request</h5>
                    <div class="sima-card__subtitle">5 request terakhir</div>
                </div>
            </div>
            <div class="sima-card__body" style="padding:0">
                @php
                $requests = $recentRequests ?? [
                    ['code'=>'REQ-003','name'=>'Surat Keterangan Aktif','status'=>'approved','date'=>'20 Feb 2026'],
                    ['code'=>'REQ-002','name'=>'Perpanjangan KITAS','status'=>'pending','date'=>'18 Feb 2026'],
                    ['code'=>'REQ-001','name'=>'Asuransi Kesehatan','status'=>'approved','date'=>'10 Jan 2026'],
                ];
                $rstatus = ['approved'=>['label'=>'Disetujui','cls'=>'green'],'pending'=>['label'=>'Menunggu','cls'=>'blue'],'rejected'=>['label'=>'Ditolak','cls'=>'red'],'processing'=>['label'=>'Diproses','cls'=>'amber']];
                @endphp
                @foreach($requests as $req)
                @php $r=is_array($req)?$req:$req->toArray();$rs=$rstatus[$r['status']]??$rstatus['pending']; @endphp
                <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid var(--c-border-soft)">
                    <div style="flex:1;min-width:0">
                        <div style="font-size:13px;font-weight:600;color:var(--c-text-1)">{{ $r['name'] }}</div>
                        <div style="font-family:var(--f-mono);font-size:10.5px;color:var(--c-text-3);margin-top:2px">{{ $r['code'] }} · {{ $r['date'] }}</div>
                    </div>
                    <span class="sima-badge sima-badge--{{ $rs['cls'] }}">{{ $rs['label'] }}</span>
                </div>
                @endforeach
                <div style="padding:12px 16px">
                    <a href="{{ route('mahasiswa.request.index') }}" class="sima-btn sima-btn--outline sima-btn--sm sima-btn--full">
                        <i class="fas fa-list"></i> Lihat Semua Request
                    </a>
                </div>
            </div>
        </div>

        {{-- Info box --}}
        <div class="sima-alert sima-alert--blue">
            <i class="fas fa-info-circle sima-alert__icon"></i>
            <div class="sima-alert__text" style="font-size:12.5px">
                <strong>Catatan:</strong> Request akan diproses dalam 1–3 hari kerja. Pastikan dokumen pendukung lengkap untuk mempercepat proses.
            </div>
        </div>

    </div>
</div>

@endsection

