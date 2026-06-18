@extends('layouts.sima')

@section('page_title', 'Notifikasi')
@section('page_section', 'Dosen')
@section('page_subtitle', 'Semua notifikasi terbaru untuk Anda')

@section('main_content')

<div class="sima-card">
    <div class="sima-card__header">
        <h5 class="sima-card__title">Notifikasi</h5>
    </div>

    <div>
        @php
        $notifs = [
            ['text' => 'Mahasiswa mengajukan bimbingan skripsi.', 'time' => '10 menit lalu', 'unread' => true],
            ['text' => 'Jadwal UTS telah diperbarui oleh jurusan.', 'time' => '1 jam lalu', 'unread' => true],
            ['text' => 'Pengumuman baru dari KLN.', 'time' => 'Kemarin, 14:20', 'unread' => false],
        ];
        @endphp

        @foreach($notifs as $n)
        <div style="padding:14px 20px;border-bottom:1px solid var(--c-border-soft);
                    background:{{ $n['unread'] ? 'rgba(108,143,255,.04)' : 'transparent' }}">
            <div style="font-size:13px;color:var(--c-text-1);font-weight:{{ $n['unread'] ? '600' : '400' }}">
                {{ $n['text'] }}
            </div>
            <div style="font-size:11px;color:var(--c-text-3);margin-top:4px">
                {{ $n['time'] }}
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection