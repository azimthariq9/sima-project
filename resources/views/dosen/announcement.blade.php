@extends('layouts.sima')

@section('page_title', 'Pengumuman')
@section('page_section', 'Dosen')
@section('page_subtitle', 'Informasi resmi dari kampus')

@section('main_content')

<div class="sima-card">
    <div class="sima-card__header">
        <h5 class="sima-card__title">Pengumuman</h5>
    </div>

    <div class="sima-card__body">
        @php
        $ann = [
            ['title' => 'Workshop Penelitian Internasional', 'body' => 'Workshop akan dilaksanakan 5 Maret 2026 di Aula Utama.', 'badge' => 'KLN'],
            ['title' => 'Perubahan Jadwal UTS', 'body' => 'Beberapa mata kuliah mengalami penyesuaian jadwal.', 'badge' => 'Jurusan'],
        ];
        @endphp

        @foreach($ann as $a)
        <div style="margin-bottom:18px;padding:14px;background:var(--c-surface-2);
                    border-radius:12px;border:1px solid var(--c-border-soft)">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                <div style="font-weight:600;font-size:14px;color:var(--c-text-1)">
                    {{ $a['title'] }}
                </div>
                <span class="sima-badge sima-badge--blue">{{ $a['badge'] }}</span>
            </div>
            <div style="font-size:12.5px;color:var(--c-text-2)">
                {{ $a['body'] }}
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection