<?php

namespace App\Enums;

enum Role: string
{
    case MAHASISWA = 'mahasiswa';
    case DOSEN = 'dosen';
    case KLN = 'kln';
    case BIPA = 'bipa';
    case JURUSAN = 'jurusan';
}
