<?php
// app/Services/JurusanService.php

namespace App\Services;

use App\Models\Jurusan;
use App\Traits\LogsActivityTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class JurusanService extends BaseService
{
    use LogsActivityTrait;
    
    public function __construct(Jurusan $jurusan)
    {
        parent::__construct($jurusan);
    }
    
    public function getAll(array $filters = [], int $perPage = 15):LengthAwarePaginator
    {
        $query = Jurusan::withCount(['prodis', 'mahasiswas']);
        
        if (isset($filters['search'])) {
            $query->where('nama_jurusan', 'like', "%{$filters['search']}%")
                  ->orWhere('kode_jurusan', 'like', "%{$filters['search']}%");
        }
        
        return $query->latest()->paginate($perPage);
    }
    
    public function create($maker,array $data): Jurusan
    {
        $jurusan = parent::create($maker,$data);
        $this->logActivity('CREATE', $jurusan, "Membuat jurusan baru: {$jurusan->nama_jurusan}", $maker);
        return $jurusan;
    }
    
    public function update($maker, int $id, array $data): Jurusan
    {
        $jurusan = parent::update($maker, $id, $data);
        $this->logActivity('UPDATE', $jurusan, "Mengupdate jurusan: {$jurusan->nama_jurusan}", $maker);
        return $jurusan;
    }
    
    public function delete($maker, int $id): bool
    {
        $jurusan = $this->findOrFail($id);
        $nama = $jurusan->nama_jurusan;
        $result = parent::delete($maker, $id);
        
        if ($result) {
            $this->logActivity('DELETE', $jurusan, "Menghapus jurusan: {$nama}", $maker);
        }
        
        return $result;
    }
}