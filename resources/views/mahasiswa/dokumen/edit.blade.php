@extends('layouts.sima')

@section('page_title', 'Edit Document')
@section('page_section', 'MAHASISWA')
@section('page_subtitle', 'Update document details')

@section('main_content')

@if($errors->any())
<div style="background:rgba(220,38,38,.07);border:1px solid rgba(220,38,38,.2);border-radius:10px;padding:14px 18px;margin-bottom:16px">
    <div style="font-size:13px;font-weight:600;color:#dc2626;margin-bottom:6px"><i class="fas fa-circle-exclamation"></i> Periksa isian berikut:</div>
    @foreach($errors->all() as $error)
        <div style="font-size:12.5px;color:#b91c1c;margin-top:3px">· {{ $error }}</div>
    @endforeach
</div>
@endif

<div style="margin-bottom:16px">
    <a href="{{ route('mahasiswa.dokumen.index') }}" class="sima-btn sima-btn--outline sima-btn--sm"><i class="fas fa-arrow-left me-1"></i> Back</a>
</div>

<div class="row g-3">
    <div class="col-md-8 sima-fade">
        <div class="sima-card">
            <div class="sima-card__header">
                <div style="flex:1;min-width:0">
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                        <h5 class="sima-card__title" style="margin-bottom:0">Edit Dokumen</h5>
                        <span class="sima-badge sima-badge--{{ $dok->status === 'approved' ? 'green' : ($dok->status === 'rejected' ? 'red' : 'blue') }}">
                            {{ ucfirst($dok->status) }}
                        </span>
                    </div>
                    <div class="sima-card__subtitle">ID: {{ $dok->id }}</div>
                </div>
            </div>
            <div class="sima-card__body">

                @if($dok->status === 'approved')
                <div style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.25);border-radius:10px;padding:12px 16px;margin-bottom:18px">
                    <div style="font-size:12.5px;color:#b45309">
                        <i class="fas fa-triangle-exclamation"></i> Dokumen yang sudah disetujui akan kembali ke status <strong>Pending</strong> setelah diedit.
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('mahasiswa.dokumen.update', $dok->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div style="margin-bottom:18px">
                        <label class="sima-label">
                            Jenis Dokumen <span style="color:var(--c-red)">*</span>
                        </label>
                        <select name="tipeDkmn" class="sima-input" required>
                            <option value="">— Pilih —</option>
                            @foreach (\App\Enums\TipeDok::cases() as $tipe)
                                <option value="{{ $tipe->value }}" {{ old('tipeDkmn', $dok->tipeDkmn) == $tipe->value ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', $tipe->value) }}
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
                            Nomor Dokumen <span style="color:var(--c-red)">*</span>
                        </label>
                        <input type="text" name="noDkmn" class="sima-input" value="{{ old('noDkmn', $dok->noDkmn) }}" required>
                        @error('noDkmn')
                            <div style="font-size:12px;color:var(--c-red);margin-top:5px">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:18px">
                        <div>
                            <label class="sima-label">
                                Tanggal Terbit <span style="color:var(--c-red)">*</span>
                            </label>
                            <input type="date" name="tglTerbit" class="sima-input"
                                value="{{ old('tglTerbit', $dok->tglTerbit ? date('Y-m-d', strtotime($dok->tglTerbit)) : '') }}" required>
                            @error('tglTerbit')
                                <div style="font-size:12px;color:var(--c-red);margin-top:5px">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div>
                            <label class="sima-label">
                                Berlaku s/d <span style="color:var(--c-red)">*</span>
                            </label>
                            <input type="date" name="tglKdlwrs" class="sima-input"
                                value="{{ old('tglKdlwrs', $dok->tglKdlwrs ? date('Y-m-d', strtotime($dok->tglKdlwrs)) : '') }}" required>
                            @error('tglKdlwrs')
                                <div style="font-size:12px;color:var(--c-red);margin-top:5px">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div style="margin-bottom:18px">
                        <label class="sima-label">
                            Penerbit <span style="color:var(--c-red)">*</span>
                        </label>
                        <select name="penerbit" class="sima-input" required>
                            <option value="">— Pilih penerbit —</option>
                            @foreach (\App\Enums\Penerbit::cases() as $p)
                                <option value="{{ $p->value }}" {{ old('penerbit', $dok->penerbit) == $p->value ? 'selected' : '' }}>
                                    {{ $p->value }}
                                </option>
                            @endforeach
                        </select>
                        @error('penerbit')
                            <div style="font-size:12px;color:var(--c-red);margin-top:5px">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div style="margin-bottom:18px">
                        <label class="sima-label">Ganti File (opsional)</label>
                        <input type="file" name="file" class="sima-input" accept=".pdf,.jpg,.jpeg,.png"
                            style="padding:7px 12px;font-size:12.5px">
                        <div style="font-size:11.5px;color:var(--c-text-3);margin-top:4px">Kosongkan jika tidak ingin mengganti file</div>
                        @error('file')
                            <div style="font-size:12px;color:var(--c-red);margin-top:5px">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div style="display:flex;gap:10px;margin-top:24px">
                        <button type="submit" class="sima-btn"><i class="fas fa-save"></i> Simpan</button>
                        <a href="{{ route('mahasiswa.dokumen.index') }}" class="sima-btn sima-btn--outline">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4 sima-fade sima-fade--1">
        <div class="sima-alert sima-alert--blue">
            <i class="fas fa-info-circle sima-alert__icon"></i>
            <div class="sima-alert__text" style="font-size:12.5px">
                <strong>Catatan:</strong> Perubahan dokumen akan memicu ulang verifikasi oleh KLN. Pastikan data sudah benar sebelum menyimpan.
            </div>
        </div>
    </div>
</div>

@endsection
