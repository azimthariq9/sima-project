<?php

namespace App\Enums;

enum Role:string
{
    case KLN = 'kln';
    case adminJurusan = 'Jurusan';
    case adminBipa = 'Bipa';
    case dosen = 'dosen';
    case mahasiswa = 'mahasiswa';
}
