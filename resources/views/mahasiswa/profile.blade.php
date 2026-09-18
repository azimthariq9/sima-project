@extends('layouts.sima')

@section('page_title','Student Profile')
@section('page_section','Mahasiswa')
@section('page_subtitle','Manage your profile data')

@section('main_content')

@php
    $mahasiswa = $mahasiswa ?? auth()->user()->mahasiswa;
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

@if (session('success'))
<div style="background:rgba(5,150,105,.08);border:1px solid rgba(5,150,105,.2);border-radius:10px;padding:13px 18px;margin-bottom:16px;font-size:13px;color:#065f46">
    <i class="fas fa-circle-check"></i> {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div style="background:rgba(220,38,38,.07);border:1px solid rgba(220,38,38,.2);border-radius:10px;padding:14px 18px;margin-bottom:16px">
    <div style="font-size:13px;font-weight:600;color:#dc2626;margin-bottom:6px">
        <i class="fas fa-circle-exclamation"></i> Please check the following:
    </div>
    @foreach ($errors->all() as $error)
        <div style="font-size:12.5px;color:#b91c1c;margin-top:3px">· {{ $error }}</div>
    @endforeach
</div>
@endif

<div class="row justify-content-center">
<div class="col-md-7">
<div class="sima-card">
<div class="sima-card__body">

<form method="POST" action="{{ route('mahasiswa.profile.update') }}" enctype="multipart/form-data">
@csrf
@method('PATCH')

{{-- Profile Photo --}}
<div class="mb-3" style="text-align:center">
    <div id="photoPreview" style="width:100px;height:100px;border-radius:50%;margin:0 auto 12px;overflow:hidden;border:3px solid var(--c-border);display:flex;align-items:center;justify-content:center;background:var(--c-accent);color:#fff;font-size:36px;font-weight:700;">
        @if($mahasiswa->fotoProfil)
            <img src="{{ route('mahasiswa.profile.foto') }}" style="width:100%;height:100%;object-fit:cover;" onerror="this.parentElement.innerHTML='{{ strtoupper(substr($mahasiswa->nama, 0, 1)) }}'">
        @else
            {{ strtoupper(substr($mahasiswa->nama ?? 'M', 0, 1)) }}
        @endif
    </div>
    <label class="sima-label" style="cursor:pointer;display:inline-block;padding:6px 16px;border:1px solid var(--c-border);border-radius:8px;font-size:12px;color:var(--c-text-2);background:var(--c-surface);">
        <i class="fas fa-camera me-1"></i> Change Photo
        <input type="file" name="fotoProfil" accept="image/*" style="display:none" onchange="previewPhoto(this)">
    </label>
    <div style="font-size:11px;color:var(--c-text-3);margin-top:4px">JPG, PNG, WebP — max 2MB</div>
</div>

<div class="mb-3">
<label class="sima-label">Full Name</label>
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
<label class="sima-label">No. Darurat</label>
<input type="text" name="noDarurat" class="sima-input"
       value="{{ old('noDarurat', $mahasiswa->noDarurat ?? '') }}">
</div>

<div class="mb-3">
<label class="sima-label">Date of Birth</label>
<input type="date" name="tglLahir" class="sima-input"
       value="{{ old('tglLahir', $mahasiswa->tglLahir ? \Carbon\Carbon::parse($mahasiswa->tglLahir)->format('Y-m-d') : '') }}">
</div>

<div class="mb-3">
<label class="sima-label">Nationality</label>
<select name="warNeg" class="sima-input">
    <option value="" disabled {{ old('warNeg', $mahasiswa->warNeg ?? '') === '' ? 'selected' : '' }}>-- Select Country --</option>
    @foreach($countries as $country)
        <option value="{{ $country }}" {{ old('warNeg', $mahasiswa->warNeg ?? '') === $country ? 'selected' : '' }}>
            {{ $country }}
        </option>
    @endforeach
</select>
</div>

<div class="mb-3">
<label class="sima-label">Home Address</label>
<input type="text" name="alamatAsal" class="sima-input"
       value="{{ old('alamatAsal', $mahasiswa->alamatAsal ?? '') }}">
</div>

<div class="mb-3">
<label class="sima-label">Indonesia Address</label>
<input type="text" name="alamatIndo" class="sima-input"
       value="{{ old('alamatIndo', $mahasiswa->alamatIndo ?? '') }}">
</div>

<div class="mb-3">
<label class="sima-label">Tahun Masuk</label>
<input type="text" name="tahunMasuk" class="sima-input" disabled
       value="{{ old('tahunMasuk', $mahasiswa->tahunMasuk ?? '') }}" placeholder="PTA 2026/2027">
<div style="font-size:11.5px;color:var(--c-text-3);margin-top:4px">Set by KLN admin</div>
</div>

<div class="mb-3">
<label class="sima-label">Active Period</label>
<input type="date" name="masaAktif" class="sima-input" disabled
       value="{{ old('masaAktif', $mahasiswa->masaAktif ? \Carbon\Carbon::parse($mahasiswa->masaAktif)->format('Y-m-d') : '') }}">
<div style="font-size:11.5px;color:var(--c-text-3);margin-top:4px">Set by KLN admin</div>
</div>

<hr>

<div class="mb-3">
<label class="sima-label">Change Password (Optional)</label>
<input type="password" name="password" class="sima-input">
</div>

<div class="mb-3">
<label class="sima-label">Confirm Password</label>
<input type="password" name="password_confirmation" class="sima-input">
</div>

<button type="submit" class="sima-btn sima-btn--full">
Save Changes
</button>

</form>

</div>
</div>
</div>
</div>

@endsection

@section('page_js')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreview').innerHTML =
                '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover;">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
