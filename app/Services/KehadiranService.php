<?php
// app/Services/KehadiranService.php

namespace App\Services;

use App\Models\Kehadiran;
use App\Traits\LogsActivityTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class KehadiranService extends BaseService
{
    use LogsActivityTrait;
    
    public function __construct(Kehadiran $kehadiran)
    {
        parent::__construct($kehadiran);
    }
    
    public function getAll(array $filters = [], int $perPage = 15):LengthAwarePaginator
    {
        $query = Kehadiran::with(['user', 'user.mahasiswa', 'jadwal'])
            ->when(isset($filters['tanggal']), function ($q) use ($filters) {
                $q->whereDate('tanggal', $filters['tanggal']);
            })
            ->when(isset($filters['status']), function ($q) use ($filters) {
                $q->where('status', $filters['status']);
            })
            ->when(isset($filters['user_id']), function ($q) use ($filters) {
                $q->where('user_id', $filters['user_id']);
            })
            ->when(isset($filters['jadwal_id']), function ($q) use ($filters) {
                $q->where('jadwal_id', $filters['jadwal_id']);
            });
            
        return $query->latest()->paginate($perPage);
    }
    
    public function getRekapitulasi(array $filters = [])
    {
        $query = Kehadiran::query()
            ->selectRaw('user_id, 
                         COUNT(*) as total_kehadiran,
                         SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as total_hadir,
                         SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as total_izin,
                         SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as total_sakit,
                         SUM(CASE WHEN status = "alpha" THEN 1 ELSE 0 END) as total_alpha')
            ->when(isset($filters['bulan']), function ($q) use ($filters) {
                $q->whereMonth('tanggal', $filters['bulan']);
            })
            ->when(isset($filters['tahun']), function ($q) use ($filters) {
                $q->whereYear('tanggal', $filters['tahun']);
            })
            ->groupBy('user_id')
            ->with('user', 'user.mahasiswa');
            
        return $query->paginate($filters['per_page'] ?? 15);
    }
}