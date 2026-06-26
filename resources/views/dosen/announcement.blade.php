@extends('layouts.sima')

@section('page_title',    'Pengumuman')
@section('page_section',  'DOSEN')
@section('page_subtitle', 'Informasi dan pengumuman terbaru')

@section('main_content')

<div class="sima-card sima-fade">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Pengumuman</h5>
            <div class="sima-card__subtitle">Daftar pengumuman aktif untuk civitas akademik</div>
        </div>
    </div>

    <div class="sima-card__body">
        @if($announcements->isEmpty())
            <div style="text-align:center;padding:48px 20px;color:var(--c-text-3)">
                <i class="fas fa-bullhorn" style="font-size:36px;opacity:.3;display:block;margin-bottom:12px"></i>
                <div style="font-size:14px;font-weight:500;color:var(--c-text-2)">Belum ada pengumuman</div>
                <div style="font-size:12.5px;margin-top:4px">Pengumuman dari KLN dan jurusan akan tampil di sini.</div>
            </div>
        @else
            <div style="display:flex;flex-direction:column;gap:10px">
                @foreach($announcements as $ann)
                @php $isPenting = (bool)($ann->is_penting ?? false); @endphp
                <div style="
                    border:1px solid {{ $isPenting ? 'rgba(var(--c-accent-rgb),.3)' : 'var(--c-border-soft)' }};
                    border-left-width:{{ $isPenting ? '4px' : '1px' }};
                    border-left-color:{{ $isPenting ? 'var(--c-accent)' : 'var(--c-border-soft)' }};
                    border-radius:12px;padding:16px 20px;
                    background:{{ $isPenting ? 'rgba(var(--c-accent-rgb),.04)' : 'var(--c-surface)' }};
                    transition:box-shadow .15s"
                     onmouseover="this.style.boxShadow='0 3px 12px rgba(0,0,0,.08)'"
                     onmouseout="this.style.boxShadow='none'">

                    <div style="display:flex;align-items:flex-start;gap:12px">
                        <div style="width:38px;height:38px;border-radius:10px;flex-shrink:0;
                                    background:{{ $isPenting ? 'rgba(var(--c-accent-rgb),.12)' : 'var(--c-bg)' }};
                                    display:flex;align-items:center;justify-content:center;
                                    color:{{ $isPenting ? 'var(--c-accent)' : 'var(--c-text-3)' }};
                                    font-size:15px;margin-top:2px">
                            <i class="fas {{ $isPenting ? 'fa-thumbtack' : 'fa-bullhorn' }}"></i>
                        </div>

                        <div style="flex:1;min-width:0">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:5px;flex-wrap:wrap">
                                <div style="font-size:14px;font-weight:700;color:var(--c-text-1)">{{ $ann->subject }}</div>
                                @if($isPenting)
                                    <span class="sima-badge sima-badge--amber" style="font-size:10px">
                                        <i class="fas fa-thumbtack"></i> Penting
                                    </span>
                                @endif
                                <span class="sima-badge" style="font-size:10px;background:var(--c-bg);color:var(--c-text-3);border:1px solid var(--c-border)">
                                    {{ strtoupper($ann->sumber ?? 'KLN') }}
                                </span>
                            </div>
                            <div style="font-size:13px;color:var(--c-text-2);line-height:1.6;margin-bottom:8px">
                                {{ $ann->message }}
                            </div>
                            <div style="font-size:11.5px;color:var(--c-text-3)">
                                <i class="fas fa-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($ann->created_at)->diffForHumans() }}
                                · {{ \Carbon\Carbon::parse($ann->created_at)->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div style="margin-top:20px">
                {{ $announcements->links('vendor.pagination.sima') }}
            </div>
        @endif
    </div>
</div>

@endsection
