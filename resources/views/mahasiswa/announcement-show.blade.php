@extends('layouts.sima')

@section('page_title', $ann->subject)
@section('page_section', 'Mahasiswa')
@section('page_subtitle', 'Detail Pengumuman')

@push('styles')
<style>
.ann-body {
    font-size: 14.5px;
    line-height: 1.8;
    color: var(--c-text-1);
}
.ann-body p  { margin-bottom: 1em; }
.ann-body ul,
.ann-body ol { padding-left: 1.4em; margin-bottom: 1em; }
.ann-body li { margin-bottom: .3em; }

.ann-file {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border: 1px solid var(--c-border-soft);
    border-radius: 10px;
    background: var(--c-surface);
    text-decoration: none;
    transition: background .15s, border-color .15s;
    color: inherit;
}
.ann-file:hover {
    background: var(--c-bg);
    border-color: var(--c-accent);
    text-decoration: none;
}
.ann-file__icon {
    width: 38px;
    height: 38px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.ann-file__name {
    font-size: 13px;
    font-weight: 600;
    color: var(--c-text-1);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ann-file__meta {
    font-size: 11.5px;
    color: var(--c-text-3);
    margin-top: 1px;
}
</style>
@endpush

@section('main_content')

@php
$sumberBadge = match(strtolower($ann->sumber ?? 'kln')) {
    'kln'    => 'sima-badge--teal',
    'jurusan'=> 'sima-badge--purple',
    'bipa'   => 'sima-badge--amber',
    default  => 'sima-badge--blue',
};

$fileIcon = fn($mime) => match(true) {
    !$mime                                                        => ['fa-file-lines',   '#64748b', '#f1f5f9'],
    str_contains($mime, 'pdf')                                    => ['fa-file-pdf',     '#ef4444', '#fef2f2'],
    str_contains($mime, 'image')                                  => ['fa-file-image',   '#8b5cf6', '#f5f3ff'],
    str_contains($mime, 'word') || str_contains($mime, 'document')=> ['fa-file-word',    '#2563eb', '#eff6ff'],
    str_contains($mime, 'sheet') || str_contains($mime, 'excel') => ['fa-file-excel',   '#059669', '#ecfdf5'],
    str_contains($mime, 'zip')  || str_contains($mime, 'rar')    => ['fa-file-zipper',  '#d97706', '#fffbeb'],
    default                                                       => ['fa-file-lines',   '#64748b', '#f1f5f9'],
};

$fileSize = function($bytes) {
    if (!$bytes) return '';
    if ($bytes < 1024)    return $bytes . ' B';
    if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
    return round($bytes / 1048576, 1) . ' MB';
};
@endphp

<div style="max-width:780px;margin:0 auto">

    {{-- Back --}}
    <div class="sima-fade" style="margin-bottom:16px">
        <a href="{{ route('mahasiswa.announcement') }}" class="sima-btn sima-btn--sm sima-btn--outline">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Artikel utama --}}
    <div class="sima-card sima-fade">
        <div class="sima-card__body" style="padding:32px 36px">

            {{-- Badge + penting --}}
            <div style="display:flex;gap:6px;margin-bottom:16px;flex-wrap:wrap">
                <span class="sima-badge {{ $sumberBadge }}">{{ $ann->sumber ?? 'KLN' }}</span>
                @if($ann->is_penting)
                    <span class="sima-badge sima-badge--amber">
                        <i class="fas fa-triangle-exclamation"></i> Penting
                    </span>
                @endif
            </div>

            {{-- Judul --}}
            <h1 style="font-family:var(--f-display);font-size:22px;font-weight:800;color:var(--c-text-1);line-height:1.35;margin-bottom:16px">
                {{ $ann->subject }}
            </h1>

            {{-- Meta: tanggal + penulis --}}
            <div style="display:flex;align-items:center;gap:18px;padding:12px 0;border-top:1px solid var(--c-border-soft);border-bottom:1px solid var(--c-border-soft);margin-bottom:28px;flex-wrap:wrap">
                <div style="display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--c-text-3)">
                    <i class="fas fa-calendar-days" style="color:var(--c-accent)"></i>
                    <span>{{ \Carbon\Carbon::parse($ann->created_at)->translatedFormat('l, d F Y') }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--c-text-3)">
                    <i class="fas fa-clock" style="color:var(--c-accent)"></i>
                    <span>{{ \Carbon\Carbon::parse($ann->created_at)->format('H:i') }} WIB</span>
                </div>
                <div style="display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--c-text-3)">
                    <i class="fas fa-user-pen" style="color:var(--c-accent)"></i>
                    <span>{{ $ann->sumber ?? 'KLN' }} — {{ $author ?? 'Admin' }}</span>
                </div>
            </div>

            {{-- Isi pengumuman --}}
            <div class="ann-body">
                {!! nl2br(e($ann->message)) !!}
            </div>

        </div>
    </div>

    {{-- Lampiran --}}
    @if($files->isNotEmpty())
    <div class="sima-card sima-fade sima-fade--1" style="margin-top:14px">
        <div class="sima-card__header">
            <div>
                <h5 class="sima-card__title">Lampiran</h5>
                <div class="sima-card__subtitle">{{ $files->count() }} file terlampir</div>
            </div>
        </div>
        <div class="sima-card__body">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:10px">
                @foreach($files as $file)
                    @php
                        [$ico, $color, $bg] = $fileIcon($file->mimeType ?? '');
                        $originalName = $file->originalName ?? basename($file->path);
                        $size = $fileSize($file->size ?? 0);
                    @endphp
                    <a href="{{ route('mahasiswa.announcement.file', [$ann->id, $file->id]) }}"
                       class="ann-file" target="_blank">
                        <div class="ann-file__icon" style="background:{{ $bg }};color:{{ $color }}">
                            <i class="fas {{ $ico }}"></i>
                        </div>
                        <div style="flex:1;min-width:0">
                            <div class="ann-file__name" title="{{ $originalName }}">{{ $originalName }}</div>
                            @if($size)
                                <div class="ann-file__meta">{{ $size }}</div>
                            @endif
                        </div>
                        <i class="fas fa-download" style="color:var(--c-text-3);font-size:13px;flex-shrink:0"></i>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>

@endsection
