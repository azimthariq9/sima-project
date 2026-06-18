@extends('layouts.sima')

@section('page_title', 'Jadwal')
@section('page_section', 'BIPA')
@section('page_subtitle', 'Jadwal kelas BIPA')

@section('main_content')

<div class="sima-card">
    <div class="sima-card__body">

        <table class="sima-table">
            <thead>
                <tr>
                    <th>Kelas</th>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Ruangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>B1</td>
                    <td>Senin</td>
                    <td>08:00 - 10:00</td>
                    <td>R.301</td>
                </tr>
                <tr>
                    <td>B2</td>
                    <td>Rabu</td>
                    <td>10:00 - 12:00</td>
                    <td>R.304</td>
                </tr>
                <tr>
                    <td>C1</td>
                    <td>Jumat</td>
                    <td>13:00 - 15:00</td>
                    <td>Lab Bahasa</td>
                </tr>
            </tbody>
        </table>

    </div>
</div>

@endsection