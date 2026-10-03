@extends('layouts.sima')
@section('page_title', $document ? 'Edit Dokumen Akademik' : 'Upload Dokumen Akademik')
@section('page_section', 'JURUSAN')
@section('page_subtitle', 'KRS/FRS, daftar nilai, dan laporan kehadiran mahasiswa')
@section('main_content')
<div class="sima-card" style="max-width:850px;margin:auto">
    <div class="sima-card__header"><div><h5 class="sima-card__title">{{ $document ? 'Perbarui Dokumen' : 'Dokumen Akademik Baru' }}</h5><div class="sima-card__subtitle">Dokumen langsung tersedia untuk mahasiswa yang dipilih.</div></div></div>
    <div class="sima-card__body">
        @if($errors->any())
        <div class="sima-alert sima-alert--red" role="alert"><div>@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div></div>
        @endif
        @if($students->isEmpty())
        <div class="sima-alert sima-alert--blue">Belum ada mahasiswa di jurusan ini. Hubungi KLN untuk menghubungkan akun mahasiswa dengan jurusan.</div>
        @else
        <form method="POST" action="{{ $document ? route('jurusan.dokumen.update', $document->id) : route('jurusan.dokumen.store') }}" enctype="multipart/form-data">
            @csrf
            @if($document) @method('PATCH') @endif
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="sima-label" for="mahasiswa_id">Mahasiswa *</label>
                    <select id="mahasiswa_id" name="mahasiswa_id" class="sima-input" required>
                        <option value="">Pilih mahasiswa</option>
                        @foreach($students as $student)
                        <option value="{{ $student->id }}" @selected((string) old('mahasiswa_id', $document->mahasiswa_id ?? request('mahasiswa_id')) === (string) $student->id)>{{ $student->npm }} — {{ $student->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="sima-label" for="tipeDkmn">Jenis dokumen *</label>
                    <select id="tipeDkmn" name="tipeDkmn" class="sima-input" required>
                        <option value="">Pilih jenis dokumen</option>
                        @foreach($types as $value => $label)<option value="{{ $value }}" @selected(old('tipeDkmn', $document->tipeDkmn ?? '') === $value)>{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="sima-label" for="noDkmn">Nomor dokumen (opsional)</label>
                    <input id="noDkmn" name="noDkmn" class="sima-input" maxlength="100" value="{{ old('noDkmn', $document->noDkmn ?? '') }}" placeholder="Otomatis jika dikosongkan">
                </div>
                <div class="col-md-12">
                    <label class="sima-label" for="namaDkmn">Judul / periode akademik *</label>
                    <input id="namaDkmn" name="namaDkmn" class="sima-input" required maxlength="150" value="{{ old('namaDkmn', $document->namaDkmn ?? '') }}" placeholder="Contoh: KRS Semester Ganjil 2026/2027">
                </div>
                <div class="col-md-6">
                    <label class="sima-label" for="tglTerbit">Tanggal terbit *</label>
                    <input id="tglTerbit" type="date" name="tglTerbit" class="sima-input" required value="{{ old('tglTerbit', $document->tglTerbit ?? now()->toDateString()) }}">
                </div>
                <div class="col-md-6">
                    <label class="sima-label" for="tglKdlwrs">Akhir periode berlaku / semester *</label>
                    <input id="tglKdlwrs" type="date" name="tglKdlwrs" class="sima-input" required value="{{ old('tglKdlwrs', $document->tglKdlwrs ?? '') }}">
                </div>
                <div class="col-md-12">
                    <label class="sima-label" for="file">{{ $document ? 'Ganti file (opsional)' : 'File dokumen *' }}</label>
                    <input id="file" type="file" name="file" class="sima-input" accept=".pdf,.jpg,.jpeg,.png,.xls,.xlsx,.csv" @required(!$document)>
                    <small style="color:var(--c-text-3)">PDF, JPG, PNG, XLS, XLSX, atau CSV. Maksimal 10 MB. {{ $document ? 'Kosongkan untuk mempertahankan file saat ini.' : '' }}</small>
                </div>
            </div>
            <div style="display:flex;gap:10px;margin-top:24px;flex-wrap:wrap">
                <button class="sima-btn" type="submit"><i class="fas fa-upload"></i> {{ $document ? 'Simpan Perubahan' : 'Upload Dokumen' }}</button>
                <a href="{{ route('jurusan.dokumen.index') }}" class="sima-btn sima-btn--outline">Kembali</a>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection
