<?php

namespace App\Enums;

enum TipeDok:string
{
    case suratKeterangan = 'Surat Keterangan';
    case suratIzin = 'Surat Izin';
    case suratTugas = 'Surat Tugas';
    case suratUndangan = 'Surat Undangan';
    case suratPernyataan = 'Surat Pernyataan';
    case kitas = 'KITAS';
    case kitap = 'KITAP';
    case paspor = 'Paspor';
    case ktp = 'KTP';
    case polis = 'Polis Asuransi';
    case foto_profile = 'Foto Profil';
}
