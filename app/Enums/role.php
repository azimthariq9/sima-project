<?php

namespace App\Enums;

enum Role:string
{
    case KLN = 'kln';
    case adminJurusan = 'Jurusan';
    case adminBipa = 'Bipa';
    case DOSEN = 'dosen';
    case MAHASISWA = 'mahasiswa';
}
