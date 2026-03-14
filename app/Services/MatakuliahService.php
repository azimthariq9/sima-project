<?php

namespace App\Services;

use App\Models\Matakuliah;
use App\Traits\LogsActivityTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MatakuliahService extends BaseService
{
    use LogsActivityTrait;

    public function __construct(Matakuliah $matakuliah)
    {
        parent::__construct($matakuliah);
    }

    /*
    |--------------------------------------------------------------------------
    | GET ALL — di-scope otomatis berdasarkan jurusan_id auth user
    |--------------------------------------------------------------------------
    */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Matakuliah::query()
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
    public function create($maker, array $data): Matakuliah
    {
        DB::beginTransaction();

        try {
            // Otomatis set jurusan_id dari admin yang login
            $data['jurusan_id'] = $maker->jurusan_id;

            $matakuliah = Matakuliah::create($data);

            $this->logActivity('CREATE', $matakuliah, "Membuat matakuliah: {$matakuliah->nama}", $maker);

            DB::commit();
            return $matakuliah;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating matakuliah: ' . $e->getMessage(), [
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
    public function update($maker, int $id, array $data): Matakuliah
    {
        DB::beginTransaction();

        try {
            $matakuliah = $this->findOrFail($id);
            $matakuliah->update($data);

            $this->logActivity('UPDATE', $matakuliah, "Mengupdate matakuliah: {$matakuliah->nama}", $maker);

            DB::commit();
            return $matakuliah->fresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating matakuliah: ' . $e->getMessage());
            throw $e;
        }
    }
}