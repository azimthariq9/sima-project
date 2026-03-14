<?php

namespace App\Services;

use App\Models\Kelas;
use App\Traits\LogsActivityTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class KelasService extends BaseService
{
    use LogsActivityTrait;

    public function __construct(Kelas $kelas)
    {
        parent::__construct($kelas);
    }

    /*
    |--------------------------------------------------------------------------
    | GET ALL — di-scope otomatis berdasarkan jurusan_id auth user
    |--------------------------------------------------------------------------
    */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Kelas::with(['matakuliah', 'dosen'])
            ->where('jurusan_id', Auth::user()->jurusan_id);

        if (isset($filters['nama'])) {
            $query->where('nama', 'like', "%{$filters['nama']}%");
        }

        if (isset($filters['kode'])) {
            $query->where('kode', 'like', "%{$filters['kode']}%");
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
    public function create($maker, array $data): Kelas
    {
        DB::beginTransaction();

        try {
            // Otomatis set jurusan_id dari admin yang login
            $data['jurusan_id'] = $maker->jurusan_id;

            $kelas = Kelas::create($data);

            $this->logActivity('CREATE', $kelas, "Membuat kelas: {$kelas->nama}", $maker);

            DB::commit();
            return $kelas->load(['matakuliah', 'dosen']);

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
    public function update($maker, int $id, array $data): Kelas
    {
        DB::beginTransaction();

        try {
            $kelas = $this->findOrFail($id);
            $kelas->update($data);

            $this->logActivity('UPDATE', $kelas, "Mengupdate kelas: {$kelas->nama}", $maker);

            DB::commit();
            return $kelas->fresh(['matakuliah', 'dosen']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating kelas: ' . $e->getMessage());
            throw $e;
        }
    }
}