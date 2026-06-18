@extends('layouts.sima')

@section('page_title','Biodata Mahasiswa')
@section('page_section','Mahasiswa')
@section('page_subtitle','Kelola data profil Anda')

@section('main_content')

@php
    $mahasiswa = $mahasiswa ?? auth()->user()->mahasiswa;
@endphp

<div class="row justify-content-center">
<div class="col-md-7">
<div class="sima-card">
<div class="sima-card__body">

<form method="POST" action="{{ route('mahasiswa.profile.update') }}">
@csrf
@method('PATCH')

<div class="mb-3">
<label class="sima-label">Nama Lengkap</label>
<input type="text" name="nama" class="sima-input"
       value="{{ old('nama', $mahasiswa->nama ?? '') }}" required>
</div>

<div class="mb-3">
<label class="sima-label">NPM</label>
<input type="text" name="npm" class="sima-input"
       value="{{ old('npm', $mahasiswa->npm ?? '') }}" readonly>
</div>

<div class="mb-3">
<label class="sima-label">No WhatsApp</label>
<input type="text" name="noWa" class="sima-input"
       value="{{ old('noWa', $mahasiswa->noWa ?? '') }}">
</div>

<div class="mb-3">
<label class="sima-label">Tanggal Lahir</label>
<input type="date" name="tglLahir" class="sima-input"
       value="{{ old('tglLahir', $mahasiswa->tglLahir ?? '') }}">
</div>

<div class="mb-3">
<label class="sima-label">Kewarganegaraan</label>
<input type="text" name="warNeg" class="sima-input"
       value="{{ old('warNeg', $mahasiswa->warNeg ?? '') }}">
</div>

<div class="mb-3">
<label class="sima-label">Alamat Asal</label>
<input type="text" name="alamatAsal" class="sima-input"
       value="{{ old('alamatAsal', $mahasiswa->alamatAsal ?? '') }}">
</div>

<div class="mb-3">
<label class="sima-label">Alamat Indonesia</label>
<input type="text" name="alamatIndo" class="sima-input"
       value="{{ old('alamatIndo', $mahasiswa->alamatIndo ?? '') }}">
</div>

<hr>

<div class="mb-3">
<label class="sima-label">Ganti Password (Opsional)</label>
<input type="password" name="password" class="sima-input">
</div>

<div class="mb-3">
<label class="sima-label">Konfirmasi Password</label>
<input type="password" name="password_confirmation" class="sima-input">
</div>

<button type="submit" class="sima-btn sima-btn--full">
Simpan Perubahan
</button>

</form>

</div>
</div>
</div>
</div>

@endsection