@extends('layouts.sima')

@section('page_title', 'Upload Document')
@section('page_section', 'MAHASISWA')
@section('page_subtitle', 'Upload an important document for KLN verification')

@section('main_content')

@if($errors->any())
<div style="background:rgba(220,38,38,.07);border:1px solid rgba(220,38,38,.2);border-radius:10px;padding:14px 18px;margin-bottom:16px">
    <div style="font-size:13px;font-weight:600;color:#dc2626;margin-bottom:6px"><i class="fas fa-circle-exclamation"></i> Please check the following:</div>
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
                <div>
                    <h5 class="sima-card__title">Upload Important Document</h5>
                    <div class="sima-card__subtitle">Fill in document data completely and correctly</div>
                </div>
            </div>
            <div class="sima-card__body">
                <form method="POST" action="{{ route('mahasiswa.dokumen.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div style="margin-bottom:18px">
                        <label class="sima-label">
                            Document Type <span style="color:var(--c-red)">*</span>
                        </label>
                        <select name="tipeDkmn" class="sima-input" required>
                            <option value="">— Select —</option>
                            @foreach (\App\Enums\TipeDok::cases() as $dok)
                                <option value="{{ $dok->value }}" {{ old('tipeDkmn') == $dok->value ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', $dok->value) }}
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
                            Document Number <span style="color:var(--c-red)">*</span>
                        </label>
                        <input type="text" name="noDkmn" class="sima-input" placeholder="e.g. A1234567" value="{{ old('noDkmn') }}" required>
                        @error('noDkmn')
                            <div style="font-size:12px;color:var(--c-red);margin-top:5px">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:18px">
                        <div>
                            <label class="sima-label">
                                Issue Date <span style="color:var(--c-red)">*</span>
                            </label>
                            <input type="date" name="tglTerbit" class="sima-input" value="{{ old('tglTerbit') }}" required>
                            @error('tglTerbit')
                                <div style="font-size:12px;color:var(--c-red);margin-top:5px">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div>
                            <label class="sima-label">
                                Valid Until <span style="color:var(--c-red)">*</span>
                            </label>
                            <input type="date" name="tglKdlwrs" class="sima-input" value="{{ old('tglKdlwrs') }}" required>
                            @error('tglKdlwrs')
                                <div style="font-size:12px;color:var(--c-red);margin-top:5px">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div style="margin-bottom:18px">
                        <label class="sima-label">
                            Issuer <span style="color:var(--c-red)">*</span>
                        </label>
                        <select name="penerbit" class="sima-input" required>
                            <option value="">— Select issuer —</option>
                            @foreach (\App\Enums\Penerbit::cases() as $p)
                                <option value="{{ $p->value }}" {{ old('penerbit') == $p->value ? 'selected' : '' }}>
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
                        <label class="sima-label">Document File</label>
                        <input type="file" name="file" class="sima-input" accept=".pdf,.jpg,.jpeg,.png"
                            style="padding:7px 12px;font-size:12.5px">
                        <div style="font-size:11.5px;color:var(--c-text-3);margin-top:4px">PDF, JPG, PNG — maks. 5MB</div>
                        @error('file')
                            <div style="font-size:12px;color:var(--c-red);margin-top:5px">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div style="display:flex;gap:10px;margin-top:24px">
                        <button type="submit" class="sima-btn"><i class="fas fa-upload"></i> Upload</button>
                        <a href="{{ route('mahasiswa.dokumen.index') }}" class="sima-btn sima-btn--outline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4 sima-fade sima-fade--1">
        <div class="sima-alert sima-alert--blue">
            <i class="fas fa-info-circle sima-alert__icon"></i>
            <div class="sima-alert__text" style="font-size:12.5px">
                <strong>Note:</strong> Documents will be verified by KLN within 1–3 business days. Make sure the data entered is correct.
            </div>
        </div>
    </div>
</div>

@endsection
