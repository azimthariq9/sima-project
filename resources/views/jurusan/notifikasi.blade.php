@extends('layouts.sima')

@section('page_title',    'Notifications')
@section('page_section',  'DEPARTMENT ADMIN')
@section('page_subtitle', 'Incoming notifications')

@section('content')
<div class="container-fluid px-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Notifications</h5>
        </div>
        <div class="card-body p-0">
            @forelse ($notifikasi as $item)
                <div class="d-flex align-items-start p-3 border-bottom {{ $item->is_read ? '' : 'bg-light' }}">
                    <div class="me-3 mt-1">
                        <i class="bi bi-bell{{ $item->is_read ? '' : '-fill text-primary' }} fs-5"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $item->judul }}</div>
                        <div class="text-muted small">{{ $item->pesan }}</div>
                        <div class="text-muted small mt-1">
                            {{ \Carbon\Carbon::parse($item->received_at)->diffForHumans() }}
                        </div>
                    </div>
                    @if (!$item->is_read)
                        <span class="badge bg-primary ms-2">New</span>
                    @endif
                </div>
            @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-bell-slash fs-1"></i>
                    <p class="mt-2">No notifications yet.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
