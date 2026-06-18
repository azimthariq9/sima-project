@extends('layouts.sima')

@section('page_title',   'Notifikasi')
@section('page_section', 'MAHASISWA')
@section('page_subtitle','Informasi & aktivitas terbaru akun Anda')

@section('main_content')

@php
$typeConfig = [
    'document'     => ['ic' => 'fa-file-alt',       'cl' => '#059669', 'bg' => '#ECFDF5', 'label' => 'Dokumen'],
    'account'      => ['ic' => 'fa-user-check',      'cl' => '#7C3AED', 'bg' => '#F5F3FF', 'label' => 'Akun'],
    'announcement' => ['ic' => 'fa-bullhorn',         'cl' => '#2563EB', 'bg' => '#EFF6FF', 'label' => 'Pengumuman'],
    'broadcast'    => ['ic' => 'fa-paper-plane',      'cl' => '#0D9488', 'bg' => '#F0FDFA', 'label' => 'Pesan'],
];
@endphp

<div class="row g-3">
    <div class="col-md-8 sima-fade">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Semua Notifikasi</h5>
                    <div class="sima-card__subtitle">
                        @if($unreadCount > 0)
                            <span style="color:var(--c-accent);font-weight:600;">{{ $unreadCount }}</span> belum dibaca
                        @else
                            Semua sudah dibaca
                        @endif
                    </div>
                </div>
                @if($unreadCount > 0)
                <form method="POST" action="{{ route('mahasiswa.notifikasi.mark-read') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="sima-btn sima-btn--outline sima-btn--sm">
                        <i class="fas fa-check-double me-1"></i> Tandai Semua Dibaca
                    </button>
                </form>
                @endif
            </div>

            <div id="notif-list">
                @forelse($notifications as $notif)
                @php
                    $tc  = $typeConfig[$notif->type] ?? ['ic' => 'fa-bell', 'cl' => '#6c8fff', 'bg' => '#eff3ff', 'label' => 'Info'];
                    $isUnread = !$notif->is_read;
                @endphp
                <div style="display:flex;align-items:flex-start;gap:14px;padding:16px 20px;
                            border-bottom:1px solid var(--c-border);
                            background:{{ $isUnread ? 'rgba(108,143,255,.04)' : 'transparent' }};
                            transition:background .13s;">
                    {{-- Icon --}}
                    <div style="width:38px;height:38px;border-radius:11px;
                                background:{{ $tc['bg'] }};color:{{ $tc['cl'] }};
                                display:flex;align-items:center;justify-content:center;
                                font-size:14px;flex-shrink:0;">
                        <i class="fas {{ $tc['ic'] }}"></i>
                    </div>

                    {{-- Content --}}
                    <div style="flex:1;min-width:0;padding-top:2px;">
                        <div style="display:flex;align-items:center;gap:6px;margin-bottom:3px;flex-wrap:wrap;">
                            <span style="font-size:11px;font-weight:600;color:{{ $tc['cl'] }};
                                         background:{{ $tc['bg'] }};padding:1px 7px;border-radius:999px;">
                                {{ $tc['label'] }}
                            </span>
                            <span style="font-size:13.5px;color:var(--c-text-1);{{ $isUnread ? 'font-weight:600;' : '' }}">
                                {{ $notif->subject }}
                            </span>
                        </div>
                        <div style="font-size:13px;color:var(--c-text-2);line-height:1.5;margin-bottom:5px;">
                            {{ $notif->message }}
                        </div>
                        <div style="font-family:var(--f-mono);font-size:11px;color:var(--c-text-3);">
                            <i class="fas fa-clock" style="font-size:10px;"></i>
                            {{ \Carbon\Carbon::parse($notif->received_at)->diffForHumans() }}
                            &middot; {{ \Carbon\Carbon::parse($notif->received_at)->isoFormat('D MMM YYYY, HH:mm') }}
                        </div>
                    </div>

                    {{-- Unread dot --}}
                    @if($isUnread)
                    <div style="width:8px;height:8px;border-radius:50%;background:var(--c-accent);flex-shrink:0;margin-top:6px;"></div>
                    @endif
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="fas fa-bell-slash fa-2x d-block mb-2" style="opacity:.3;"></i>
                    Belum ada notifikasi.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── PANEL KANAN: STATISTIK ──────────────────────── --}}
    <div class="col-md-4 sima-fade sima-fade--1">
        <div class="sima-card">
            <div class="sima-card__header">
                <h5 class="sima-card__title">Ringkasan</h5>
            </div>
            <div style="padding:16px 20px;">
                @php
                    $total     = $notifications->count();
                    $byType    = $notifications->groupBy('type');
                @endphp
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--c-border);font-size:13px;">
                    <span style="color:var(--c-text-2);">Total</span>
                    <span class="sima-badge sima-badge--blue">{{ $total }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--c-border);font-size:13px;">
                    <span style="color:var(--c-text-2);">Belum Dibaca</span>
                    <span class="sima-badge {{ $unreadCount > 0 ? 'sima-badge--red' : 'sima-badge--green' }}">{{ $unreadCount }}</span>
                </div>
                @foreach($typeConfig as $key => $tc)
                @php $cnt = ($byType[$key] ?? collect())->count(); @endphp
                @if($cnt > 0)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--c-border);font-size:13px;">
                    <span style="color:var(--c-text-2);">
                        <i class="fas {{ $tc['ic'] }} me-1" style="color:{{ $tc['cl'] }};width:14px;"></i>
                        {{ $tc['label'] }}
                    </span>
                    <span style="font-weight:600;">{{ $cnt }}</span>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
