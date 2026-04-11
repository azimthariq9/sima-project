<?php

namespace App\Services;

use App\Models\kelas;
use App\Traits\LogsActivityTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KelasService extends BaseService
{
    use LogsActivityTrait;

    public function __construct(kelas $kelas)
    {
        parent::__construct($kelas);
    }

    /*
    |--------------------------------------------------------------------------
    | GET ALL
    | Scope: kelas tidak punya jurusan_id langsung.
    | Scope via jadwal → dosen → user.jurusan_id
    | ATAU: via mahasiswa → user.jurusan_id
    |
    | Solusi paling clean: scope via user admin yang login,
    | karena kelas dibuat oleh admin jurusan tertentu.
    | Untuk sekarang: tampilkan semua kelas, filter jurusan bisa ditambah
    | jika tabel kelas ditambah kolom jurusan_id di masa depan.
    |
    | UPDATE: Karena kelas tidak punya jurusan_id, scope dilakukan
    | dengan mengambil kelas yang jadwalnya diajar oleh dosen
    | dari jurusan yang sama — atau lebih simpel: kelas yang mahasiswanya
    | berasal dari jurusan admin yang login.
    |--------------------------------------------------------------------------
    */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $jurusanId = auth()->user()->jurusan_id;

        $query = kelas::withCount('mahasiswa')
            // Scope: kelas yang memiliki minimal satu mahasiswa dari jurusan ini
            ->whereHas('mahasiswa', function ($q) use ($jurusanId) {
                $q->whereHas('user', fn($u) => $u->where('jurusan_id', $jurusanId));
            })
            ->orWhereHas('jadwal', function ($q) use ($jurusanId) {
                // Atau kelas yang jadwalnya punya dosen dari jurusan ini
                $q->whereHas('dosen', fn($d) => $d->whereHas('user', fn($u) => $u->where('jurusan_id', $jurusanId)));
            });

        if (isset($filters['kode'])) {
            $query->where('kodeKelas', 'like', "%{$filters['kode']}%");
        }

        if (request()->has('sort_by') && request()->has('sort_direction')) {
            $query->orderBy(request('sort_by'), request('sort_direction'));
        } else {
            $query->latest();
        }

        return $query->paginate($perPage);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create($maker, array $data): kelas
    {
        DB::beginTransaction();

        try {
            $kelas = kelas::create([
                'kodeKelas' => $data['kodeKelas'],
            ]);

            $this->logActivity('CREATE', $kelas, "Membuat kelas: {$kelas->kodeKelas}", $maker);

            DB::commit();
            // Kelas hanya punya relasi mahasiswa (belongsToMany) dan jadwal (hasMany)
            // Tidak ada matakuliah/dosen langsung di model kelas
            return $kelas->load('mahasiswa');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating kelas: ' . $e->getMessage(), [
                'data'  => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update($maker, int $id, array $data): kelas
    {
        DB::beginTransaction();

        try {
            $kelas = $this->findOrFail($id);
            $kelas->update([
                'kodeKelas' => $data['kodeKelas'] ?? $kelas->kodeKelas,
            ]);

            $this->logActivity('UPDATE', $kelas, "Mengupdate kelas: {$kelas->kodeKelas}", $maker);

            DB::commit();
            return $kelas->fresh('mahasiswa');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating kelas: ' . $e->getMessage());
            throw $e;
        }
    }
}