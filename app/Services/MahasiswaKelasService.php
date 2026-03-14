<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\MahasiswaKelas;
use App\Models\Mahasiswa;
use App\Traits\LogsActivityTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MahasiswaKelasService extends BaseService
{
    use LogsActivityTrait;

    /*
    |--------------------------------------------------------------------------
    | GET KELAS DENGAN SEMUA MAHASISWANYA
    | Di-scope ke jurusan auth user
    |--------------------------------------------------------------------------
    */
    public function getByKelas(int $kelasId): Kelas
    {
        return Kelas::with('mahasiswa.user')
            ->where('jurusan_id', Auth::user()->jurusan_id)
            ->findOrFail($kelasId);
    }

    /*
    |--------------------------------------------------------------------------
    | ADD MAHASISWA KE KELAS
    | Validasi: kelas & mahasiswa harus dari jurusan yang sama dengan admin
    |--------------------------------------------------------------------------
    */
    public function addMahasiswaToKelas($maker, int $kelasId, array $data): void
    {
        $kelas = Kelas::where('jurusan_id', $maker->jurusan_id)->findOrFail($kelasId);
        
        // Cek sudah ada
        if ($kelas->mahasiswa()->where('mahasiswa_id', $data['mahasiswa_id'])->exists()) {
            throw new \Exception("Mahasiswa sudah terdaftar di kelas ini");
        }
        
        $kelas->mahasiswa()->attach($data['mahasiswa_id']);
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE MAHASISWA DARI KELAS
    |--------------------------------------------------------------------------
    */
    public function removeMahasiswaFromKelas($maker, int $kelasId, int $mahasiswaId): void
    {
        $kelas = Kelas::where('jurusan_id', $maker->jurusan_id)->findOrFail($kelasId);
        $kelas->mahasiswa()->detach($mahasiswaId);
    }


}