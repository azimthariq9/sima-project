<?php

namespace App\Enums;

enum TipeDok:string
{
    case suratKeterangan = 'Surat_Keterangan';
    case suratIzin = 'Surat_Izin';
    case suratTugas = 'Surat_Tugas';
    case suratUndangan = 'Surat_Undangan';
    case suratPernyataan = 'Surat_Pernyataan';
    case kitas = 'KITAS';
    case kitap = 'KITAP';
    case paspor = 'Paspor';
    case ktp = 'KTP';
    case polis = 'Polis_Asuransi';
    case foto_profile = 'Foto_Profil';
}
