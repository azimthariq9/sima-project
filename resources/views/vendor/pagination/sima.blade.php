@if ($paginator->hasPages())
@php
    $baseBtn  = 'display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 10px;border-radius:7px;border:1px solid var(--c-border);font-size:13px;font-weight:500;text-decoration:none;cursor:pointer;transition:border-color .13s,background .13s;background:transparent;color:var(--c-text-1);';
    $activeBtn = $baseBtn . 'background:var(--c-accent);color:#fff;border-color:var(--c-accent);cursor:default;';
    $disabledBtn = $baseBtn . 'opacity:.35;cursor:default;pointer-events:none;';
@endphp
<div style="display:flex;align-items:center;gap:4px;flex-wrap:wrap;">

    {{-- Prev --}}
    @if ($paginator->onFirstPage())
        <span style="{{ $disabledBtn }}">&lsaquo;</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" style="{{ $baseBtn }}">&lsaquo;</a>
    @endif

    {{-- Pages --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span style="{{ $baseBtn }}opacity:.5;cursor:default;">…</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span style="{{ $activeBtn }}">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" style="{{ $baseBtn }}"
                       onmouseover="this.style.borderColor='var(--c-accent)'"
                       onmouseout="this.style.borderColor='var(--c-border)'">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" style="{{ $baseBtn }}">&rsaquo;</a>
    @else
        <span style="{{ $disabledBtn }}">&rsaquo;</span>
    @endif

    <span style="font-size:12px;color:var(--c-text-3);margin-left:8px;">
        {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }}
    </span>
</div>
@endif
