@extends('layouts.sima')

@section('page_title', 'Schedule')
@section('page_section', 'BIPA')
@section('page_subtitle', 'BIPA class schedule')

@section('main_content')

<div class="sima-card">
    <div class="sima-card__body">

        <table class="sima-table">
            <thead>
                <tr>
                    <th>Class</th>
                    <th>Day</th>
                    <th>Time</th>
                    <th>Room</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>B1</td>
                    <td>Monday</td>
                    <td>08:00 - 10:00</td>
                    <td>R.301</td>
                </tr>
                <tr>
                    <td>B2</td>
                    <td>Wednesday</td>
                    <td>10:00 - 12:00</td>
                    <td>R.304</td>
                </tr>
                <tr>
                    <td>C1</td>
                    <td>Friday</td>
                    <td>13:00 - 15:00</td>
                    <td>Language Lab</td>
                </tr>
            </tbody>
        </table>

    </div>
</div>

@endsection