@extends('layouts.sima')

@section('page_title', 'Notifikasi')
@section('page_section', 'BIPA')
@section('page_subtitle', 'Notifikasi terbaru program BIPA')

@section('main_content')

<div class="sima-card">
    <div class="sima-card__body">

        <div class="mb-3">
            <strong>Pendaftaran Kelas B2 Ditutup</strong>
            <div style="font-size:12px;color:var(--c-text-3)">1 jam lalu</div>
        </div>

        <div class="mb-3">
            <strong>Jadwal placement test diperbarui</strong>
            <div style="font-size:12px;color:var(--c-text-3)">Kemarin</div>
        </div>

        <div>
            <strong>Pengumuman workshop budaya Indonesia</strong>
            <div style="font-size:12px;color:var(--c-text-3)">3 hari lalu</div>
        </div>

    </div>
</div>

@endsection