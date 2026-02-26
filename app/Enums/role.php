<?php

namespace App\Enums;

enum Role:string
{
    case KLN = 'Kln';
    case adminJurusan = 'Jurusan';
    case adminBipa = 'Bipa';
    case DOSEN = 'Dosen';
    case MAHASISWA = 'Mahasiswa';
}
