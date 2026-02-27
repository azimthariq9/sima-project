@extends('layouts.sima')

@section('page_title', 'Dashboard Dosen')
@section('page_section', 'Dosen')
@section('page_subtitle', 'Selamat datang kembali, ' . (auth()->user()->email ?? 'Dosen'))

@section('main_content')

<div class="row g-4">

    {{-- CARD 1 --}}
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h6 class="text-muted">Total Mahasiswa Bimbingan</h6>
                <h2 class="fw-bold mt-2">12</h2>
            </div>
        </div>
    </div>

    {{-- CARD 2 --}}
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h6 class="text-muted">Jadwal Hari Ini</h6>
                <h2 class="fw-bold mt-2">3</h2>
            </div>
        </div>
    </div>

    {{-- CARD 3 --}}
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h6 class="text-muted">Dokumen Perlu Review</h6>
                <h2 class="fw-bold mt-2">5</h2>
            </div>
        </div>
    </div>

</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h5 class="mb-3">Aktivitas Terbaru</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Mahasiswa A mengirim revisi proposal</li>
                    <li class="list-group-item">Mahasiswa B upload dokumen KITAS</li>
                    <li class="list-group-item">Jadwal bimbingan ditambahkan</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection