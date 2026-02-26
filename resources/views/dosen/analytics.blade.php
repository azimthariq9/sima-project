@extends('layouts.sima')

@section('page_title', 'Analytics')
@section('page_section', 'Dosen')
@section('page_subtitle', 'Ringkasan performa akademik')

@section('main_content')

<div class="row g-3">

    <div class="col-md-4">
        <div class="sima-stat sima-stat--blue">
            <div class="sima-stat__label">Total Kelas</div>
            <div class="sima-stat__value">6</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="sima-stat sima-stat--green">
            <div class="sima-stat__label">Total Mahasiswa</div>
            <div class="sima-stat__value">182</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="sima-stat sima-stat--amber">
            <div class="sima-stat__label">Rata-rata Kehadiran</div>
            <div class="sima-stat__value">86%</div>
        </div>
    </div>

</div>

<div class="sima-card mt-4">
    <div class="sima-card__header">
        <h5 class="sima-card__title">Insight</h5>
    </div>
    <div class="sima-card__body">
        <p style="font-size:13px;color:var(--c-text-2)">
            Kehadiran mahasiswa stabil di atas 80%. Disarankan melakukan evaluasi 
            untuk mata kuliah dengan tingkat partisipasi rendah.
        </p>
    </div>
</div>

@endsection