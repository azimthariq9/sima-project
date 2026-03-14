<?php

namespace App\Services;

use App\Models\jurusan;
use App\Traits\LogsActivityTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JurusanService extends BaseService
{
    use LogsActivityTrait;

    public function __construct(jurusan $jurusan)
    {
        parent::__construct($jurusan);
    }

    /*
    |--------------------------------------------------------------------------
    | GET ALL
    | Tidak di-scope jurusan karena ini dikelola oleh KLN (lihat semua)
    |--------------------------------------------------------------------------
    */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = jurusan::query();

        if (isset($filters['namaJurusan'])) {
            $query->where('namaJurusan', 'like', "%{$filters['namaJurusan']}%");
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
    public function create($maker, array $data): jurusan
    {
        DB::beginTransaction();

        try {
            $jurusan = jurusan::create([
                'namaJurusan' => $data['namaJurusan'],
            ]);

            $this->logActivity('CREATE', $jurusan, "Membuat jurusan: {$jurusan->namaJurusan}", $maker);

            DB::commit();
            return $jurusan;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating jurusan: ' . $e->getMessage(), [
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
    public function update($maker, int $id, array $data): jurusan
    {
        DB::beginTransaction();

        try {
            $jurusan = $this->findOrFail($id);
            $jurusan->update([
                'namaJurusan' => $data['namaJurusan'] ?? $jurusan->namaJurusan,
            ]);

            $this->logActivity('UPDATE', $jurusan, "Mengupdate jurusan: {$jurusan->namaJurusan}", $maker);

            DB::commit();
            return $jurusan->fresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating jurusan: ' . $e->getMessage());
            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    | Perhatian: hapus jurusan akan berdampak ke semua user, kelas, matakuliah,
    | jadwal yang terhubung. Pastikan ada foreign key constraint atau
    | soft delete di tabel-tabel terkait.
    |--------------------------------------------------------------------------
    */
    public function delete($maker, int $id): bool
    {
        DB::beginTransaction();

        try {
            $jurusan = $this->findOrFail($id);
            $namaJurusan = $jurusan->namaJurusan;

            // Cek apakah jurusan masih punya user aktif
            $hasUsers = $jurusan->user()->exists();
            if ($hasUsers) {
                throw new \Exception("Jurusan '{$namaJurusan}' masih memiliki user terdaftar dan tidak bisa dihapus");
            }

            $result = $jurusan->delete();

            $this->logActivity('DELETE', $jurusan, "Menghapus jurusan: {$namaJurusan}", $maker);

            DB::commit();
            return $result;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting jurusan: ' . $e->getMessage());
            throw $e;
        }
    }
}