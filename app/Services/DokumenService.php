<?php
// app/Services/DokumenService.php

namespace App\Services;

use App\Models\Dokumen;
use App\Traits\LogsActivityTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class DokumenService extends BaseService
{
    use LogsActivityTrait;
    
    public function __construct(Dokumen $dokumen)
    {
        parent::__construct($dokumen);
    }
    
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Dokumen::with(['user', 'user.mahasiswa'])
            ->when(isset($filters['status']), function ($q) use ($filters) {
                $q->where('status', $filters['status']);
            })
            ->when(isset($filters['user_id']), function ($q) use ($filters) {
                $q->where('user_id', $filters['user_id']);
            })
            ->when(isset($filters['jenis_dokumen']), function ($q) use ($filters) {
                $q->where('jenis_dokumen', $filters['jenis_dokumen']);
            });
            
        return $query->latest()->paginate($perPage);
    }
    
    public function updateStatus(int $id, string $status, ?string $catatan = null): Dokumen
    {
        DB::beginTransaction();
        
        try {
            $maker = auth()->user();
            $dokumen = $this->findOrFail($id);
            $oldStatus = $dokumen->status;
            
            $dokumen->update([
                'status' => $status,
                'catatan_revisi' => $status === 'revisi' ? $catatan : $dokumen->catatan_revisi,
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);
            
            $this->logActivity('UPDATE_STATUS', $dokumen, 
                "Mengupdate status dokumen {$dokumen->jenis_dokumen} dari {$oldStatus} menjadi {$status}", $maker, $dokumen
            );
            
            DB::commit();
            return $dokumen->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function downloadDokumen(int $id)
    {
        $dokumen = $this->findOrFail($id);
        $maker = auth()->user();
        
        if (!Storage::disk('public')->exists($dokumen->file_path)) {
            throw new \Exception('File tidak ditemukan');
        }
        
        $this->logActivity('DOWNLOAD', $dokumen, "Mendownload dokumen: {$dokumen->nama_file}", $maker, $dokumen);
        
        return Storage::disk('public')->download($dokumen->file_path, $dokumen->nama_file);
    }
}