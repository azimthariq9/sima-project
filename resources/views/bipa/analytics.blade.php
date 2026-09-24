@extends('layouts.sima')

@section('page_title', 'Analytics')
@section('page_section', 'BIPA')
@section('page_subtitle', 'BIPA program statistics')

@section('main_content')

<div class="row g-3">

    <div class="col-md-4">
        <div class="sima-stat sima-stat--blue">
            <div class="sima-stat__label">Average Attendance</div>
            <div class="sima-stat__value">88%</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="sima-stat sima-stat--green">
            <div class="sima-stat__label">Graduation Rate</div>
            <div class="sima-stat__value">94%</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="sima-stat sima-stat--amber">
            <div class="sima-stat__label">Active Students</div>
            <div class="sima-stat__value">128</div>
        </div>
    </div>

</div>

@endsection