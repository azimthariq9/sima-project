<?php

namespace App\Services;

use App\Models\kelas;
use App\Models\Mahasiswa;
use App\Traits\LogsActivityTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| MahasiswaKelasService
|
| Tidak extend BaseService karena tidak ada model pivot sendiri.
| Operasi dilakukan via belongsToMany di model Kelas/Mahasiswa:
|   Kelas::mahasiswa() → belongsToMany(Mahasiswa, 'mahasiswa_kelas')
|--------------------------------------------------------------------------
*/
class MahasiswaKelasService
{
    use LogsActivityTrait;

    /*
    |--------------------------------------------------------------------------
    | GET KELAS DENGAN SEMUA MAHASISWANYA
    | Scope ke jurusan admin yang login via mahasiswa → user.jurusan_id
    |--------------------------------------------------------------------------
    */
    public function getByKelas(int $kelasId): kelas
    {
        // Load kelas dengan mahasiswa dan user mereka
        $kelas = kelas::with([
                'mahasiswa',
                'mahasiswa.user',
            ])
            ->findOrFail($kelasId);

        return $kelas;
    }

    /*
    |--------------------------------------------------------------------------
    | ADD MAHASISWA KE KELAS
    | Validasi: mahasiswa harus dari jurusan yang sama dengan admin
    |--------------------------------------------------------------------------
    */
    public function addMahasiswaToKelas($maker, int $kelasId, array $data): array
    {
        DB::beginTransaction();

        try {
            $kelas      = kelas::findOrFail($kelasId);
            $mahasiswaId = $data['mahasiswa_id'];

            // Validasi mahasiswa ada dan dari jurusan yang sama
            $mahasiswa = Mahasiswa::whereHas('user', function ($q) use ($maker) {
                    $q->where('jurusan_id', $maker->jurusan_id);
                })
                ->findOrFail($mahasiswaId);

            // Cek sudah terdaftar
            $sudahAda = $kelas->mahasiswa()->where('mahasiswa_id', $mahasiswaId)->exists();
            if ($sudahAda) {
                throw new \Exception("Mahasiswa sudah terdaftar di kelas ini");
            }

            // Attach via pivot
            $kelas->mahasiswa()->attach($mahasiswaId);

            // Log menggunakan kelas sebagai model (kelas punya id langsung)
            $this->logActivity(
                'CREATE',
                $kelas,
                "Menambahkan mahasiswa ID {$mahasiswaId} ke kelas {$kelas->kodeKelas}",
                $maker
            );

            DB::commit();

            return [
                'kelas_id'    => $kelas->id,
                'kodeKelas'   => $kelas->kodeKelas,
                'mahasiswa_id'=> $mahasiswaId,
                'mahasiswa'   => $mahasiswa->load('user'),
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding mahasiswa to kelas: ' . $e->getMessage(), [
                'kelas_id' => $kelasId,
                'data'     => $data,
            ]);
            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE MAHASISWA DARI KELAS
    |--------------------------------------------------------------------------
    */
    public function removeMahasiswaFromKelas($maker, int $kelasId, int $mahasiswaId): bool
    {
        DB::beginTransaction();

        try {
            $kelas = kelas::findOrFail($kelasId);

            // Pastikan mahasiswa memang ada di kelas
            $ada = $kelas->mahasiswa()->where('mahasiswa_id', $mahasiswaId)->exists();
            if (!$ada) {
                throw new \Exception("Mahasiswa tidak ditemukan di kelas ini");
            }

            $kelas->mahasiswa()->detach($mahasiswaId);

            $this->logActivity(
                'DELETE',
                $kelas,
                "Menghapus mahasiswa ID {$mahasiswaId} dari kelas {$kelas->kodeKelas}",
                $maker
            );

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error removing mahasiswa from kelas: ' . $e->getMessage());
            throw $e;
        }
    }
}