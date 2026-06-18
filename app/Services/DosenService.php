<?php

namespace App\Services;

use App\Models\dosen;
use App\Traits\LogsActivityTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DosenService extends BaseService
{
    use LogsActivityTrait;

    public function __construct(dosen $dosen)
    {
        parent::__construct($dosen);
    }

    /*
    |--------------------------------------------------------------------------
    | GET ALL
    | Dosen tidak punya jurusan_id langsung di tabelnya.
    | Scope via relasi dosen → user → jurusan_id
    |--------------------------------------------------------------------------
    */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = dosen::with('user')
            ->whereHas('user', function ($q) {
                $q->where('jurusan_id', auth()->user()->jurusan_id);
            });

        if (isset($filters['nama'])) {
            $query->where('nama', 'like', "%{$filters['nama']}%");
        }

        if (isset($filters['nidn'])) {
            $query->where('nidn', 'like', "%{$filters['nidn']}%");
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
    | Kolom di tabel dosen: nama, nidn, kodeDos, user_id (FK ke users)
    |--------------------------------------------------------------------------
    */
    public function create($maker, array $data): dosen
    {
        DB::beginTransaction();

        try {
            $dosen = dosen::create([
                'user_id'  => $data['user_id']  ?? null,
                'nama'     => $data['nama']     ?? null,
                'nidn'     => $data['nidn']     ?? null,
                'kodeDos'  => $data['kodeDos']  ?? null,
            ]);

            $this->logActivity('CREATE', $dosen, "Membuat data dosen: {$dosen->nama}", $maker);

            DB::commit();
            return $dosen->load('user');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating dosen: ' . $e->getMessage(), [
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
    public function update($maker, int $id, array $data): dosen
    {
        DB::beginTransaction();

        try {
            $dosen = $this->findOrFail($id);

            $dosen->update([
                'nama'    => $data['nama']    ?? $dosen->nama,
                'nidn'    => $data['nidn']    ?? $dosen->nidn,
                'kodeDos' => $data['kodeDos'] ?? $dosen->kodeDos,
            ]);

            $this->logActivity('UPDATE', $dosen, "Mengupdate dosen: {$dosen->nama}", $maker);

            DB::commit();
            return $dosen->fresh('user');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating dosen: ' . $e->getMessage());
            throw $e;
        }
    }
}