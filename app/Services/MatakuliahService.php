<?php

namespace App\Services;

use App\Models\Matakuliah;
use App\Traits\LogsActivityTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MatakuliahService extends BaseService
{
    use LogsActivityTrait;

    public function __construct(Matakuliah $matakuliah)
    {
        parent::__construct($matakuliah);
    }

    /*
    |--------------------------------------------------------------------------
    | GET ALL
    | Scope langsung via jurusan_id di tabel matakuliah
    |--------------------------------------------------------------------------
    */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Matakuliah::query()
            ->where('jurusan_id', auth()->user()->jurusan_id);

        if (isset($filters['nama'])) {
            $query->where('namaMk', 'like', "%{$filters['nama']}%");
        }

        if (isset($filters['kode'])) {
            $query->where('kodeMk', 'like', "%{$filters['kode']}%");
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
    | Kolom: namaMk, kodeMk, sks, keterangan, jurusan_id
    |--------------------------------------------------------------------------
    */
    public function create($maker, array $data): Matakuliah
    {
        DB::beginTransaction();

        try {
            $matakuliah = Matakuliah::create([
                'namaMk'     => $data['namaMk'],
                'kodeMk'     => $data['kodeMk'],
                'sks'        => $data['sks'],
                'keterangan' => $data['keterangan'] ?? null,
                'jurusan_id' => $maker->jurusan_id, // otomatis dari admin yang login
            ]);

            $this->logActivity('CREATE', $matakuliah, "Membuat matakuliah: {$matakuliah->namaMk}", $maker);

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

            $matakuliah->update([
                'namaMk'     => $data['namaMk']     ?? $matakuliah->namaMk,
                'kodeMk'     => $data['kodeMk']     ?? $matakuliah->kodeMk,
                'sks'        => $data['sks']        ?? $matakuliah->sks,
                'keterangan' => $data['keterangan'] ?? $matakuliah->keterangan,
            ]);

            $this->logActivity('UPDATE', $matakuliah, "Mengupdate matakuliah: {$matakuliah->namaMk}", $maker);

            DB::commit();
            return $matakuliah->fresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating matakuliah: ' . $e->getMessage());
            throw $e;
        }
    }
}