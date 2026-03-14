<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Traits\LogsActivityTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class JadwalService extends BaseService
{
    use LogsActivityTrait;

    public function __construct(Jadwal $jadwal)
    {
        parent::__construct($jadwal);
    }

    /*
    |--------------------------------------------------------------------------
    | GET ALL — di-scope otomatis berdasarkan jurusan_id auth user
    | via relasi ke kelas
    |--------------------------------------------------------------------------
    */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Jadwal::with(['kelas', 'dosen', 'matakuliah'])
            ->whereHas('kelas', function ($q) {
                $q->where('jurusan_id', Auth::user()->jurusan_id);
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
    | CREATE
    | Validasi: kelas yang dipilih harus milik jurusan admin yang login
    |--------------------------------------------------------------------------
    */
    public function create($maker, array $data): Jadwal
    {
        DB::beginTransaction();

        try {
            // Pastikan kelas yang dipilih milik jurusan admin
            $kelasExists = \App\Models\Kelas::where('id', $data['kelas_id'])
                ->where('jurusan_id', $maker->jurusan_id)
                ->exists();

            if (!$kelasExists) {
                throw new \Exception("Kelas tidak ditemukan atau bukan milik jurusan Anda");
            }

            $jadwal = Jadwal::create($data);

            $this->logActivity('CREATE', $jadwal, "Membuat jadwal untuk kelas ID {$data['kelas_id']}", $maker);

            DB::commit();
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
            $jadwal->update($data);

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