<?php

namespace App\Services;

use App\Models\Dosen;
use App\Traits\LogsActivityTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\support\Facades\Auth;
class DosenService extends BaseService
{
    use LogsActivityTrait;

    public function __construct(Dosen $dosen)
    {
        parent::__construct($dosen);
    }

    /*
    |--------------------------------------------------------------------------
    | GET ALL — di-scope otomatis berdasarkan jurusan_id auth user
    |--------------------------------------------------------------------------
    */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Dosen::with('user')
            ->whereHas('user', function ($q) {
                $q->where('jurusan_id', Auth::user()->jurusan_id);
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
    |--------------------------------------------------------------------------
    */
    public function create($maker, array $data): Dosen
    {
        DB::beginTransaction();

        try {
            $dosen = Dosen::create([
                'user_id'  => $data['user_id'],
                'nama'     => $data['nama'],
                'nidn'     => $data['nidn'] ?? null,
                'kodeDos'  => $data['kodeDos'] ?? null,
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
    public function update($maker, int $id, array $data): Dosen
    {
        DB::beginTransaction();

        try {
            $dosen = $this->findOrFail($id);

            $dosen->update([
                'nama'    => $data['nama']    ?? $dosen->nama,
                'nidn'    => $data['nidn']    ?? $dosen->nidn,
                'kodeDos' => $data['kodeDos'] ?? $dosen->kodeDos,
            ]);

            $this->logActivity('UPDATE', $dosen, "Mengupdate data dosen: {$dosen->nama}", $maker);

            DB::commit();
            return $dosen->fresh('user');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating dosen: ' . $e->getMessage());
            throw $e;
        }
    }
}