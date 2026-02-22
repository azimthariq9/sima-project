<?php

namespace App;

enum role:string
{
    case KLN = 'kln';
    case adminJurusan = 'Jurusan';
    case adminBipa = 'Bipa';
    case dosen = 'dosen';
    case mahasiswa = 'mahasiswa';
}
