@extends('layouts.sima')

@section('page_title',   'Request Dokumen')
@section('page_section', 'Mahasiswa')
@section('page_subtitle','Ajukan permintaan dokumen ke KLN')

@section('main_content')

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


                <div style="margin-bottom:18px">
                    <label class="sima-label">Tanggal Dibutuhkan</label>
                    <input type="date"
                        name="tanggal_dibutuhkan"
                        class="sima-input"
                        value="{{ old('tanggal_dibutuhkan') }}"
                        min="{{ now()->addDay()->format('Y-m-d') }}">
                </div>


                <div style="margin-bottom:18px">
                    <label class="sima-label">Upload Dokumen Pendukung</label>

                    <div style="border:2px dashed var(--c-border);border-radius:10px;padding:24px;text-align:center;cursor:pointer;transition:border-color .2s;background:var(--c-bg)"
                        onclick="document.getElementById('file-upload').click()"
                        ondragover="this.style.borderColor='var(--c-accent)'"
                        ondragleave="this.style.borderColor='var(--c-border)'">

                        <i class="fas fa-cloud-arrow-up"
                        style="font-size:28px;color:var(--c-text-4);margin-bottom:8px;display:block"></i>

                        <div style="font-size:13.5px;color:var(--c-text-2);font-weight:500">
                            Drag & drop atau klik untuk upload
                        </div>

                        <div style="font-size:12px;color:var(--c-text-3);margin-top:4px">
                            PDF, JPG, PNG — max 5MB
                        </div>
                    </div>

                    <input type="file"
                        id="file-upload"
                        name="file_pendukung[]"
                        multiple
                        accept=".pdf,.jpg,.jpeg,.png"
                        style="display:none"
                        onchange="showFiles(this)">

                    <div id="file-list"
                        style="margin-top:8px;font-size:12.5px;color:var(--c-text-3)"></div>
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

@section('page_js')
<script>
function showFiles(input) {
    const list = document.getElementById('file-list');
    list.innerHTML = '';
    Array.from(input.files).forEach(file => {
        const item = document.createElement('div');
        item.style.cssText = 'display:flex;align-items:center;gap:8px;padding:6px 10px;background:var(--c-bg);border-radius:7px;margin-bottom:4px;border:1px solid var(--c-border-soft)';
        item.innerHTML = `<i class="fas fa-file" style="color:var(--c-accent)"></i> <span style="flex:1;color:var(--c-text-2)">${file.name}</span> <span style="color:var(--c-text-3)">${(file.size/1024).toFixed(0)} KB</span>`;
        list.appendChild(item);
    });
}
</script>
@endsection
