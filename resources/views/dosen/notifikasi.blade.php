@extends('layouts.sima')

@section('page_title',    'Notifications')
@section('page_section',  'DOSEN')
@section('page_subtitle', 'All notifications sent to your account')

@section('main_content')

<div class="sima-card sima-fade">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Notifications</h5>
            <div class="sima-card__subtitle">Notification history from the system</div>
        </div>
    </div>

    <div class="sima-card__body" style="padding:0">
        @if($notif->isEmpty())
            <div style="text-align:center;padding:48px 20px;color:var(--c-text-3)">
                <i class="fas fa-bell-slash" style="font-size:36px;opacity:.3;display:block;margin-bottom:12px"></i>
                <div style="font-size:14px;font-weight:500;color:var(--c-text-2)">No notifications</div>
                <div style="font-size:12.5px;margin-top:4px">Notifications will appear here when there are updates.</div>
            </div>
        @else
            @foreach($notif as $n)
            @php
                $typeIcon = match($n->type ?? '') {
                    'document' => 'fa-file-lines',
                    'jadwal'   => 'fa-calendar',
                    'system'   => 'fa-gear',
                    default    => 'fa-bell',
                };
            @endphp
            <div style="display:flex;align-items:flex-start;gap:14px;padding:16px 20px;
                        border-bottom:1px solid var(--c-border-soft);
                        {{ !$n->is_read ? 'background:rgba(var(--c-accent-rgb),.03)' : '' }}">

                <div style="width:36px;height:36px;border-radius:10px;flex-shrink:0;
                            background:var(--c-bg);display:flex;align-items:center;
                            justify-content:center;color:var(--c-accent);font-size:14px">
                    <i class="fas {{ $typeIcon }}"></i>
                </div>

                <div style="flex:1;min-width:0">
                    <div style="font-size:13.5px;font-weight:600;color:var(--c-text-1);margin-bottom:3px">
                        {{ $n->subject }}
                    </div>
                    <div style="font-size:12.5px;color:var(--c-text-2);line-height:1.55;margin-bottom:6px">
                        {{ $n->message }}
                    </div>
                    <div style="font-size:11px;color:var(--c-text-3)">
                        <i class="fas fa-clock me-1"></i>
                        {{ \Carbon\Carbon::parse($n->received_at)->diffForHumans() }}
                        · {{ \Carbon\Carbon::parse($n->received_at)->format('d M Y, H:i') }}
                    </div>
                </div>
            </div>
            @endforeach

            <div style="padding:16px 20px">
                {{ $notif->links('vendor.pagination.sima') }}
            </div>
        @endif
    </div>
</div>

@endsection
