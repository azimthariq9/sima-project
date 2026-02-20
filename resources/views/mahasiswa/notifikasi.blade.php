@extends('layouts.sima')

@section('page_title', 'Notifikasi')

@section('main_content')
<div class="sima-card">
    <div class="sima-card__header">
        <div>
            <h3 class="sima-card__title">Notifikasi</h3>
            <div class="sima-card__subtitle">
                Informasi aktivitas terbaru
            </div>
        </div>
    </div>

    <div class="sima-card__body">

        <div class="sima-alert sima-alert--blue">
            <i class="fas fa-info-circle"></i>
            Tidak ada notifikasi baru.
        </div>

    </div>
</div>
@endsection
