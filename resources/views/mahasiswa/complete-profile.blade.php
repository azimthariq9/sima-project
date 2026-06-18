@extends('layouts.sima')

@section('page_title','Lengkapi Profil')
@section('page_section','Mahasiswa')
@section('page_subtitle','Silakan isi data Anda terlebih dahulu')

@section('main_content')

@if ($errors->any())
    <div style="background:#ffdddd;padding:10px;border-radius:6px;margin-bottom:15px;">
        @foreach ($errors->all() as $error)
            <div style="color:red;">{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="row justify-content-center">
<div class="col-md-7">
<div class="sima-card">
<div class="sima-card__body">

<form method="POST" action="{{ route('mahasiswa.complete-profile.store') }}">
@csrf

<div class="mb-3">
<label class="sima-label">Nama Lengkap</label>
<input type="text" name="nama" class="sima-input" required>
</div>

<div class="mb-3">
<label class="sima-label">NPM</label>
<input type="text" name="npm" class="sima-input" required>
</div>

<div class="mb-3">
<label class="sima-label">No WhatsApp</label>
<input type="text" name="noWa" class="sima-input">
</div>

<div class="mb-3">
<label class="sima-label">Tanggal Lahir</label>
<input type="date" name="tglLahir" class="sima-input">
</div>

<div class="mb-3">
<label class="sima-label">Kewarganegaraan</label>
<input type="text" name="warNeg" class="sima-input">
</div>

<div class="mb-3">
<label class="sima-label">Alamat Asal</label>
<input type="text" name="alamatAsal" class="sima-input">
</div>

<div class="mb-3">
<label class="sima-label">Alamat Indonesia</label>
<input type="text" name="alamatIndo" class="sima-input">
</div>

<hr>

<div class="mb-3">
<label class="sima-label">Password Baru</label>
<input type="password" name="password" class="sima-input" required>
</div>

<div class="mb-3">
<label class="sima-label">Konfirmasi Password</label>
<input type="password" name="password_confirmation" class="sima-input" required>
</div>

<button type="submit" class="sima-btn sima-btn--full">
Simpan & Masuk Dashboard
</button>

</form>

</div>
</div>
</div>
</div>

@endsection