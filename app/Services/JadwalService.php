<?php
// app/Services/JadwalService.php

namespace App\Services;

use App\Models\Jadwal;
use App\Traits\LogsActivityTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class JadwalService extends BaseService
{
    use LogsActivityTrait;
    
    public function __construct(Jadwal $jadwal)
    {
        parent::__construct($jadwal);
    }
    
    public function getAll(array $filters = [], int $perPage = 15):LengthAwarePaginator
    {
        $query = Jadwal::with(['prodi', 'matakuliah', 'dosen'])
            ->when(isset($filters['prodi_id']), function ($q) use ($filters) {
                $q->where('prodi_id', $filters['prodi_id']);
            })
            ->when(isset($filters['semester']), function ($q) use ($filters) {
                $q->where('semester', $filters['semester']);
            })
            ->when(isset($filters['tahun_akademik']), function ($q) use ($filters) {
                $q->where('tahun_akademik', $filters['tahun_akademik']);
            });
            
        return $query->latest()->paginate($perPage);
    }
    
    public function create($maker, array $data): Jadwal
    {
        $jadwal = parent::create($maker, $data);
        $this->logActivity('CREATE', $jadwal, "Membuat jadwal baru: {$jadwal->matakuliah->nama_mk}", $maker, $jadwal);
        return $jadwal;
    }
    
    public function update($maker, int $id, array $data): Jadwal
    {
        $jadwal = parent::update($maker, $id, $data);
        $this->logActivity('UPDATE', $jadwal, "Mengupdate jadwal: {$jadwal->matakuliah->nama_mk}", $maker, $jadwal);
        return $jadwal;
    }
}