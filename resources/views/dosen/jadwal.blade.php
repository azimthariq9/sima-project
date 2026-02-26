@extends('layouts.sima')

@section('page_title', 'Jadwal Mengajar')
@section('page_section', 'Dosen')
@section('page_subtitle', 'Daftar jadwal perkuliahan Anda')

@section('main_content')

<div class="sima-card">
    <div class="sima-card__header">
        <h5 class="sima-card__title">Jadwal Mengajar</h5>
    </div>

    <div class="sima-card__body">
        @php
        $jadwal = [
            ['mk' => 'Pemrograman Web', 'hari' => 'Senin', 'jam' => '08:00 - 10:00', 'ruang' => 'Lab 3'],
            ['mk' => 'Struktur Data', 'hari' => 'Rabu', 'jam' => '10:00 - 12:00', 'ruang' => 'Ruang 402'],
            ['mk' => 'Basis Data', 'hari' => 'Jumat', 'jam' => '13:00 - 15:00', 'ruang' => 'Lab 2'],
        ];
        @endphp

        <table class="sima-table">
            <thead>
                <tr>
                    <th>Mata Kuliah</th>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Ruangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jadwal as $j)
                <tr>
                    <td>{{ $j['mk'] }}</td>
                    <td>{{ $j['hari'] }}</td>
                    <td>{{ $j['jam'] }}</td>
                    <td>{{ $j['ruang'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection