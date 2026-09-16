@extends('layouts.sima')

@section('page_title',   'Announcements')
@section('page_section', 'Students')
@section('page_subtitle','Latest updates from KLN, Department & BIPA')

@section('main_content')

<div class="sima-card sima-fade">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">All Announcements</h5>
            <div class="sima-card__subtitle">From KLN, Department &amp; BIPA — priority announcements shown at top</div>
        </div>
    </div>
    <div class="sima-card__body">

        @forelse($announcements as $ann)
        @php
            $isPenting  = (bool)($ann->is_penting ?? false);
            $sumberBadge = match(strtolower($ann->sumber ?? '')) {
                'kln'     => 'sima-badge--teal',
                'jurusan' => 'sima-badge--purple',
                'bipa'    => 'sima-badge--amber',
                default   => 'sima-badge--blue',
            };
        @endphp

        {{-- Opsi A: left accent border + background tint untuk is_penting --}}
        <div class="{{ $isPenting ? '' : 'sima-announce' }}"
             onclick="window.location='{{ route('mahasiswa.announcement.show', $ann->id) }}'"
             style="cursor:pointer;
                    {{ $isPenting
                        ? 'border:1px solid rgba(var(--c-accent-rgb),.25);border-left:4px solid var(--c-accent);border-radius:12px;padding:16px 18px;background:rgba(var(--c-accent-rgb),.04);margin-bottom:10px;transition:box-shadow .15s'
                        : '' }}"
             onmouseover="this.style.boxShadow='0 3px 12px rgba(0,0,0,.08)'"
             onmouseout="this.style.boxShadow='none'">

            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px">
                <div style="display:flex;align-items:center;gap:7px;flex:1;min-width:0">
                    @if($isPenting)
                        <i class="fas fa-thumbtack" style="font-size:13px;color:var(--c-accent);flex-shrink:0;margin-top:1px"></i>
                    @endif
                    <div class="sima-announce__title" style="font-size:{{ $isPenting ? '14px' : '13.5px' }};font-weight:{{ $isPenting ? '700' : '600' }}">
                        {{ $ann->subject }}
                    </div>
                </div>
                <div style="display:flex;gap:6px;flex-shrink:0;margin-left:10px">
                    <span class="sima-badge {{ $sumberBadge }}" style="font-size:10.5px">{{ strtoupper($ann->sumber ?? '') }}</span>
                    @if($isPenting)
                        <span class="sima-badge sima-badge--amber" style="font-size:10.5px">
                            <i class="fas fa-thumbtack"></i> Priority
                        </span>
                    @endif
                </div>
            </div>

            <div class="sima-announce__body" style="{{ $isPenting ? 'color:var(--c-text-1)' : '' }}">
                {{ Str::limit($ann->message, 180) }}
            </div>

            <div class="sima-announce__meta" style="margin-top:8px">
                <i class="fas fa-clock"></i>
                {{ \Carbon\Carbon::parse($ann->created_at)->diffForHumans() }}
                · {{ \Carbon\Carbon::parse($ann->created_at)->format('d M Y') }}
                <a href="{{ route('mahasiswa.announcement.show', $ann->id) }}"
                   style="margin-left:auto;font-size:12px;color:var(--c-accent);font-weight:600"
                   onclick="event.stopPropagation()">Read more →</a>
            </div>
        </div>

        @empty
        <div style="text-align:center;padding:48px 20px;color:var(--c-text-3)">
            <i class="fas fa-bullhorn" style="font-size:36px;opacity:.3;display:block;margin-bottom:12px"></i>
            <div style="font-size:14px;font-weight:500;color:var(--c-text-2)">No announcements yet</div>
            <div style="font-size:12.5px;margin-top:4px">Announcements from KLN, Department, and BIPA will appear here.</div>
        </div>
        @endforelse

    </div>
</div>

@endsection
