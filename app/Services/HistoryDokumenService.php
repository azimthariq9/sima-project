<?php

namespace App\Services;

use App\Models\HistoryDokumen;
use App\Models\Dokumen;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HistoryDokumenService extends BaseService
{
    public function __construct(HistoryDokumen $historyDokumen)
    {
        parent::__construct($historyDokumen);
    }
    
    /**
     * Catat history upload dokumen (dari mahasiswa)
     */
    public function logUpload(Dokumen $dokumen, Mahasiswa $mahasiswa, array $metadata = []): HistoryDokumen
    {
        $maker = null;
        return $this->create($maker, [
            'dokumen_id' => $dokumen->id,
            'mahasiswa_id' => $mahasiswa->id,
            'user_id' => null, // dari mahasiswa, user_id kosong
            'action' => 'UPLOAD',
            'status_from' => null,
            'status_to' => $dokumen->status,
            'message' => 'Dokumen diupload oleh mahasiswa',
            'metadata' => array_merge($metadata, [
                'file_name' => $dokumen->namaDkmn,
                'tipe' => $dokumen->tipeDkmn,
            ]),
        ]);
    }
    
    /**
     * Catat history update dokumen (revisi/upload ulang)
     */
    public function logUpdate(Dokumen $dokumen, Mahasiswa $mahasiswa, string $oldStatus, array $metadata = []): HistoryDokumen
    {
        $maker = null;
        return $this->create($maker, [
            'dokumen_id' => $dokumen->id,
            'mahasiswa_id' => $mahasiswa->id,
            'user_id' => null,
            'action' => 'UPDATE',
            'status_from' => $oldStatus,
            'status_to' => $dokumen->status,
            'message' => 'Dokumen diperbarui oleh mahasiswa',
            'metadata' => $metadata,
        ]);
    }
    
    /**
     * Catat history verifikasi oleh admin (KLN/Jurusan)
     */
    public function logVerification(Dokumen $dokumen, User $admin, string $action, string $message = null, array $metadata = []): HistoryDokumen
    {
        $validActions = ['VERIFY', 'REJECT', 'REVISION', 'APPROVE'];
        $maker = null;
        if (!in_array($action, $validActions)) {
            throw new \InvalidArgumentException("Action must be one of: " . implode(', ', $validActions));
        }
        
        return $this->create($maker, [
            'dokumen_id' => $dokumen->id,
            'mahasiswa_id' => $dokumen->mahasiswa_id,
            'user_id' => $admin->id,
            'action' => $action,
            'status_from' => $dokumen->getOriginal('status'), // status sebelum diubah
            'status_to' => $dokumen->status,
            'message' => $message ?? "Dokumen di{$action} oleh admin",
            'metadata' => $metadata,
        ]);
    }
    
    /**
     * Catat history soft delete dokumen
     */
    public function logSoftDelete(Dokumen $dokumen, $actor, bool $isMahasiswa = true): HistoryDokumen
    {
        $maker = null;
        $data = [
            'dokumen_id' => $dokumen->id,
            'mahasiswa_id' => $dokumen->mahasiswa_id,
            'action' => 'DELETE',
            'status_from' => $dokumen->status,
            'status_to' => 'deleted',
            'message' => 'Dokumen dihapus',
            'metadata' => [
                'deleted_at' => now()->toDateTimeString(),
                'deleted_by' => $isMahasiswa ? 'mahasiswa' : 'admin',
            ],
        ];
        
        if ($isMahasiswa) {
            $data['user_id'] = null;
            $data['message'] = 'Dokumen dihapus oleh mahasiswa';
        } else {
            $data['user_id'] = $actor->id;
            $data['message'] = "Dokumen dihapus oleh admin {$actor->name}";
        }
        
        return $this->create($maker, $data);
    }
    
    /**
     * Get history untuk satu dokumen
     */
    public function getHistoryForDokumen(int $dokumenId, array $filters = [])
    {
        $query = $this->model->with(['mahasiswa', 'user'])
            ->where('dokumen_id', $dokumenId);
        
        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }
        
        if (isset($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }
        
        if (isset($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }
        
        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }
    
    /**
     * Get timeline history untuk mahasiswa
     */
    public function getMahasiswaTimeline(int $mahasiswaId, array $filters = [])
    {
        $query = $this->model->with(['dokumen', 'user'])
            ->where('mahasiswa_id', $mahasiswaId);
        
        if (isset($filters['dokumen_id'])) {
            $query->where('dokumen_id', $filters['dokumen_id']);
        }
        
        return $query->latest()->paginate($filters['per_page'] ?? 20);
    }
}