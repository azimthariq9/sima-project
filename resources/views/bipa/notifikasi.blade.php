@extends('layouts.sima')

@section('page_title', 'Notifications')
@section('page_section', 'BIPA')
@section('page_subtitle', 'Latest BIPA program notifications')

@section('main_content')

<div class="sima-card">
    <div class="sima-card__body">

        <div class="mb-3">
            <strong>B2 Class Registration Closed</strong>
            <div style="font-size:12px;color:var(--c-text-3)">1 hour ago</div>
        </div>

        <div class="mb-3">
            <strong>Placement test schedule updated</strong>
            <div style="font-size:12px;color:var(--c-text-3)">Yesterday</div>
        </div>

        <div>
            <strong>Indonesian culture workshop announcement</strong>
            <div style="font-size:12px;color:var(--c-text-3)">3 days ago</div>
        </div>

    </div>
</div>

@endsection