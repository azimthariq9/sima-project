@extends('layouts.sima')

@section('page_title','Lengkapi Profil')
@section('page_section','Mahasiswa')
@section('page_subtitle','Silakan isi data Anda terlebih dahulu')

@section('main_content')

@if ($errors->any())
<div style="background:rgba(220,38,38,.07);border:1px solid rgba(220,38,38,.2);border-radius:10px;padding:14px 18px;margin-bottom:16px">
    <div style="font-size:13px;font-weight:600;color:#dc2626;margin-bottom:6px">
        <i class="fas fa-circle-exclamation"></i> Periksa isian berikut:
    </div>
    @foreach ($errors->all() as $error)
        <div style="font-size:12.5px;color:#b91c1c;margin-top:3px">· {{ $error }}</div>
    @endforeach
</div>
@endif

@php
$countries = [
    'Afghanistan','Albania','Algeria','Andorra','Angola','Antigua and Barbuda','Argentina','Armenia','Australia',
    'Austria','Azerbaijan','Bahamas','Bahrain','Bangladesh','Barbados','Belarus','Belgium','Belize','Benin',
    'Bhutan','Bolivia','Bosnia and Herzegovina','Botswana','Brazil','Brunei','Bulgaria','Burkina Faso','Burundi',
    'Cabo Verde','Cambodia','Cameroon','Canada','Central African Republic','Chad','Chile','China','Colombia',
    'Comoros','Congo','Costa Rica','Croatia','Cuba','Cyprus','Czech Republic','Denmark','Djibouti','Dominica',
    'Dominican Republic','Ecuador','Egypt','El Salvador','Equatorial Guinea','Eritrea','Estonia','Eswatini',
    'Ethiopia','Fiji','Finland','France','Gabon','Gambia','Georgia','Germany','Ghana','Greece','Grenada',
    'Guatemala','Guinea','Guinea-Bissau','Guyana','Haiti','Honduras','Hungary','Iceland','India','Indonesia',
    'Iran','Iraq','Ireland','Israel','Italy','Jamaica','Japan','Jordan','Kazakhstan','Kenya','Kiribati',
    'Kuwait','Kyrgyzstan','Laos','Latvia','Lebanon','Lesotho','Liberia','Libya','Liechtenstein','Lithuania',
    'Luxembourg','Madagascar','Malawi','Malaysia','Maldives','Mali','Malta','Marshall Islands','Mauritania',
    'Mauritius','Mexico','Micronesia','Moldova','Monaco','Mongolia','Montenegro','Morocco','Mozambique',
    'Myanmar','Namibia','Nauru','Nepal','Netherlands','New Zealand','Nicaragua','Niger','Nigeria',
    'North Korea','North Macedonia','Norway','Oman','Pakistan','Palau','Palestine','Panama','Papua New Guinea',
    'Paraguay','Peru','Philippines','Poland','Portugal','Qatar','Romania','Russia','Rwanda',
    'Saint Kitts and Nevis','Saint Lucia','Saint Vincent and the Grenadines','Samoa','San Marino',
    'Sao Tome and Principe','Saudi Arabia','Senegal','Serbia','Seychelles','Sierra Leone','Singapore',
    'Slovakia','Slovenia','Solomon Islands','Somalia','South Africa','South Korea','South Sudan','Spain',
    'Sri Lanka','Sudan','Suriname','Sweden','Switzerland','Syria','Taiwan','Tajikistan','Tanzania','Thailand',
    'Timor-Leste','Togo','Tonga','Trinidad and Tobago','Tunisia','Turkey','Turkmenistan','Tuvalu','Uganda',
    'Ukraine','United Arab Emirates','United Kingdom','United States','Uruguay','Uzbekistan','Vanuatu',
    'Vatican City','Venezuela','Vietnam','Yemen','Zambia','Zimbabwe',
];
@endphp

<div class="row justify-content-center">
<div class="col-md-7">
<div class="sima-card">
<div class="sima-card__body">

<form method="POST" action="{{ route('mahasiswa.complete-profile.store') }}">
@csrf

<div class="mb-3">
<label class="sima-label">Nama Lengkap</label>
<input type="text" name="nama" class="sima-input" value="{{ old('nama', $mahasiswa->nama ?? '') }}" required>
</div>

<div class="mb-3">
<label class="sima-label">NPM</label>
<input type="text" name="npm" class="sima-input" value="{{ old('npm', $mahasiswa->npm ?? '') }}" required>
</div>

<div class="mb-3">
<label class="sima-label">No WhatsApp</label>
<input type="text" name="noWa" class="sima-input" value="{{ old('noWa', $mahasiswa->noWa ?? '') }}" placeholder="+62...">
</div>

<div class="mb-3">
<label class="sima-label">Tanggal Lahir</label>
<input type="date" name="tglLahir" class="sima-input"
       value="{{ old('tglLahir', $mahasiswa->tglLahir ? \Carbon\Carbon::parse($mahasiswa->tglLahir)->format('Y-m-d') : '') }}">
</div>

<div class="mb-3">
<label class="sima-label">Kewarganegaraan</label>
<select name="warNeg" class="sima-input" required>
    <option value="" disabled {{ old('warNeg', $mahasiswa->warNeg ?? '') === '' ? 'selected' : '' }}>-- Pilih Negara --</option>
    @foreach($countries as $country)
        <option value="{{ $country }}" {{ old('warNeg', $mahasiswa->warNeg ?? '') === $country ? 'selected' : '' }}>
            {{ $country }}
        </option>
    @endforeach
</select>
</div>

<div class="mb-3">
<label class="sima-label">Alamat Asal</label>
<input type="text" name="alamatAsal" class="sima-input" value="{{ old('alamatAsal', $mahasiswa->alamatAsal ?? '') }}" placeholder="Alamat di negara asal">
</div>

<div class="mb-3">
<label class="sima-label">Alamat di Indonesia</label>
<input type="text" name="alamatIndo" class="sima-input" value="{{ old('alamatIndo', $mahasiswa->alamatIndo ?? '') }}" placeholder="Alamat kos/tempat tinggal di Indonesia">
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
