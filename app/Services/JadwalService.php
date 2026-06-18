<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Models\kelas;
use App\Traits\LogsActivityTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JadwalService extends BaseService
{
    use LogsActivityTrait;

    public function __construct(Jadwal $jadwal)
    {
        parent::__construct($jadwal);
    }

    /*
    |--------------------------------------------------------------------------
    | GET ALL
    | Model Jadwal: belongsTo(Kelas), belongsTo(dosen), belongsTo(Matakuliah)
    | Scope jurusan: via dosen → user.jurusan_id
    |--------------------------------------------------------------------------
    */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Jadwal::with(['kelas', 'dosen', 'matakuliah'])
            ->whereHas('dosen', function ($q) {
                $q->whereHas('user', function ($u) {
                    $u->where('jurusan_id', auth()->user()->jurusan_id);
                });
            });

        if (isset($filters['hari'])) {
            $query->where('hari', $filters['hari']);
        }

        if (isset($filters['kelas_id'])) {
            $query->where('kelas_id', $filters['kelas_id']);
        }

        if (isset($filters['dosen_id'])) {
            $query->where('dosen_id', $filters['dosen_id']);
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
    | SHOW (single dengan relasi lengkap)
    |--------------------------------------------------------------------------
    */
    public function findWithRelations(int $id): Jadwal
    {
        $jadwal = Jadwal::with(['kelas', 'dosen.user', 'matakuliah'])
            ->findOrFail($id);

        return $jadwal;
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    | Kolom: kelas_id, matakuliah_id, dosen_id, hari, jam, ruangan, totalSesi
    | Validasi: kelas yang dipilih harus ada
    |--------------------------------------------------------------------------
    */
    public function create($maker, array $data): Jadwal
    {
        DB::beginTransaction();

        try {
            // Pastikan kelas ada
            kelas::findOrFail($data['kelas_id']);

            $jadwal = Jadwal::create([
                'kelas_id'       => $data['kelas_id'],
                'matakuliah_id'  => $data['matakuliah_id'],
                'dosen_id'       => $data['dosen_id'],
                'hari'           => $data['hari'],
                'jam'            => $data['jam'],
                'ruangan'        => $data['ruangan'],
                'totalSesi'      => $data['totalSesi'],
            ]);

            $this->logActivity(
                'CREATE',
                $jadwal,
                "Membuat jadwal {$jadwal->hari} {$jadwal->jam} untuk kelas ID {$jadwal->kelas_id}",
                $maker
            );

            DB::commit();
            // Load relasi yang benar sesuai model Jadwal
            return $jadwal->load(['kelas', 'dosen', 'matakuliah']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating jadwal: ' . $e->getMessage(), [
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
    public function update($maker, int $id, array $data): Jadwal
    {
        DB::beginTransaction();

        try {
            $jadwal = $this->findOrFail($id);

            $jadwal->update([
                'kelas_id'      => $data['kelas_id']      ?? $jadwal->kelas_id,
                'matakuliah_id' => $data['matakuliah_id'] ?? $jadwal->matakuliah_id,
                'dosen_id'      => $data['dosen_id']      ?? $jadwal->dosen_id,
                'hari'          => $data['hari']          ?? $jadwal->hari,
                'jam'           => $data['jam']           ?? $jadwal->jam,
                'ruangan'       => $data['ruangan']       ?? $jadwal->ruangan,
                'totalSesi'     => $data['totalSesi']     ?? $jadwal->totalSesi,
            ]);

            $this->logActivity('UPDATE', $jadwal, "Mengupdate jadwal ID {$jadwal->id}", $maker);

            DB::commit();
            return $jadwal->fresh(['kelas', 'dosen', 'matakuliah']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating jadwal: ' . $e->getMessage());
            throw $e;
        }
    }
}