<?php
// app/Services/DokumenService.php

namespace App\Services;

use App\Models\Dokumen;
use App\Models\User;
use App\Traits\LogsActivityTrait;
use App\Traits\FileValidationTrait;
use App\Services\FileDetailService;
use App\Enums\Status;
use App\Models\ReqDokumen;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DokumenService extends BaseService
{
    use FileValidationTrait,LogsActivityTrait;

    protected $fileDetailService;
    protected $historyDokumenService;

    public function __construct(
        Dokumen $dokumen,
        FileDetailService $fileDetailService,
        HistoryDokumenService $historyDokumenService

    )
    {
        parent::__construct($dokumen);
        $this->fileDetailService = $fileDetailService;
        $this->historyDokumenService = $historyDokumenService;

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
                "Mengupdate status dokumen {$dokumen->jenis_dokumen} dari {$oldStatus} menjadi {$status}", $maker);
            
            DB::commit();
            return $dokumen->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
 * Upload dokumen baru dari request (ALWAYS CREATE NEW, NO CHECK EXISTING)
 */
    public function uploadDokumenFromRequest(array $dokumenData, UploadedFile $file, int $reqDokumenId): ReqDokumen
    {
        DB::beginTransaction();
        
        try {
            // 1. Validasi file
            $this->validatePdfFile($file, 2);
            $maker = Auth::user();
            
            // 2. Set default status untuk dokumen baru
            $dokumenData['status'] = $dokumenData['status'] ?? 'approved'; // Langsung approved karena dari admin
            
            // 3. Create dokumen (ALWAYS CREATE NEW)
            $Dokumen = $this->create($maker, $dokumenData);
            
            // 4. Upload file dan simpan detail dengan reqDokumen_id
            $this->fileDetailService->uploadForDokumen(
                $file, 
                $Dokumen->id, 
                $reqDokumenId // Simpan reqDokumen_id di fileDetail
            );
            
            
            DB::commit();
            
            return $Dokumen->load('fileDetail', 'history');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error uploading dokumen from request: ' . $e->getMessage(), [
                'dokumen_data' => $dokumenData,
                'reqDokumen_id' => $reqDokumenId,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

     /**
     * Upload dokumen baru dengan file
     */
    public function uploadOrUpdateDokumen(array $dokumenData, UploadedFile $file, ?int $reqDokumenId = null): Dokumen
    {
        DB::beginTransaction();
        
        try {
            // 1. Validasi file
            $this->validatePdfFile($file, 2);
            $maker = Auth::user();
            
            // 2. Cek apakah sudah ada dokumen dengan kriteria tertentu
            $existingDokumen = $this->findExistingDokumen(
                $dokumenData['mahasiswa_id'],
                $dokumenData['tipeDkmn'] ?? null,
                $reqDokumenId
            );
            
            // 3. Jika sudah ada, lakukan UPDATE
            if ($existingDokumen) {
                Log::info('Existing dokumen found, performing update', [
                    'dokumen_id' => $existingDokumen->id,
                    'old_file' => $existingDokumen->namaDkmn
                ]);
                
                $result = $this->updateExistingDokumen(
                    $existingDokumen,
                    $dokumenData,
                    $file,
                    $reqDokumenId,
                    $maker
                );
            } 
            // 4. Jika belum ada, lakukan CREATE
            else {
                Log::info('No existing dokumen, performing create');
                
                $result = $this->createNewDokumen(
                    $dokumenData,
                    $file,
                    $reqDokumenId,
                    $maker
                );
            }
            
            DB::commit();
            return $result->load('fileDetail', 'history');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in uploadOrUpdateDokumen: ' . $e->getMessage(), [
                'dokumen_data' => $dokumenData,
                'file_name' => $file->getClientOriginalName(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Update dokumen dengan file baru (opsional)
     */
    /**
     * Create dokumen baru
     */
    private function createNewDokumen(array $dokumenData, UploadedFile $file, ?int $reqDokumenId, $maker): Dokumen
    {
        // 1. Set default status untuk dokumen baru
        $dokumenData['status'] = $dokumenData['status'] ?? 'pending';
        
        // 2. Create dokumen
        $dokumen = $this->create($maker, $dokumenData);
        
        // 3. Upload file
        $this->fileDetailService->uploadForDokumen(
            $file, 
            $dokumen->id, 
            $reqDokumenId
        );
        
        // 4. Catat history UPLOAD
        $mahasiswa = $dokumen->mahasiswa;
        $this->historyDokumenService->logUpload($dokumen, $mahasiswa, [
            'file_size' => $file->getSize(),
            'file_name' => $file->getClientOriginalName(),
            'reqDokumen_id' => $reqDokumenId,
            'action_by' => $maker->name,
            'action_by_id' => $maker->id
        ]);
        
        return $dokumen;
    }

    /**
     * Update dokumen yang sudah ada
     */
    private function updateExistingDokumen(Dokumen $dokumen, array $dokumenData, UploadedFile $newFile, ?int $reqDokumenId, $maker): Dokumen
    {
            $oldStatus = $dokumen->status;

            $oldStatusString = $oldStatus instanceof \App\Enums\status 
            ? $oldStatus->value 
            : (string) $oldStatus;



        $oldNama = $dokumen->namaDkmn;
        $oldFileDetails = $dokumen->fileDetail()->latest()->first();
        
        // 1. Update data dokumen (kecuali field tertentu)
        $updateData = array_merge($dokumenData, [
            'status' => 'pending' // Reset status jadi pending karena upload ulang
        ]);
        $dokumen->update($updateData);
        
        // 2. Soft delete file lama
        $this->fileDetailService->softDeleteForDokumen($dokumen->id);
        
        // 3. Upload file baru
        $this->fileDetailService->uploadForDokumen(
            $newFile, 
            $dokumen->id, 
            $reqDokumenId
        );
        
        // 4. Catat history UPDATE
        $mahasiswa = $dokumen->mahasiswa;
        $this->historyDokumenService->logUpdate($dokumen, $mahasiswa, $oldStatusString, [
            'old_file' => [
                'name' => $oldNama,
                'id' => $oldFileDetails?->id,
                'path' => $oldFileDetails?->path
            ],
            'new_file' => [
                'name' => $dokumen->namaDkmn,
                'size' => $newFile->getSize(),
                'mime' => $newFile->getMimeType()
            ],
            'has_new_file' => true,
            'reason' => 'Upload ulang oleh mahasiswa',
            'action_by' => $maker->name,
            'action_by_id' => $maker->id
        ]);
        
        return $dokumen;
    }
        /**
     * Get dokumen by mahasiswa
     */
    public function getByMahasiswa(int $mahasiswaId, array $filters = [])
    {
        $query = Dokumen::with('fileDetail')
            ->where('mahasiswa_id', $mahasiswaId);
        
        if (isset($filters['tipeDkmn'])) {
            $query->where('tipeDkmn', $filters['tipeDkmn']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

     /**
     * Download file dokumen
     */
    public function downloadDokumen(int $id)
    {
        $dokumen = $this->findOrFail($id);
        $fileDetail = $dokumen->fileDetail()->latest()->first();
        $maker = Auth::user();
        
        if (!$fileDetail || !Storage::disk('local')->exists($fileDetail->path)) {
            throw new \Exception('File tidak ditemukan');
        }
        
        $this->logActivity('DOWNLOAD', $dokumen, "Download dokumen {$dokumen->namaDkmn}",$maker);
        
        return Storage::disk('local')->download(
            $fileDetail->path,
            $this->generateDownloadFilename($dokumen)
        );
    }
    
    /**
     * Generate filename untuk download
     */
    protected function generateDownloadFilename(Dokumen $dokumen): string
    {
        $nama = str_replace(' ', '_', $dokumen->namaDkmn);
        $tgl = now()->format('Y-m-d');
        
        return "{$nama}_{$tgl}.pdf";
    }

    /**
     * Verifikasi dokumen oleh admin
     */
    public function verifyDokumen(int $id, User $admin, string $action, ?string $message = null): Dokumen
    {
        DB::beginTransaction();
        
        try {
            $dokumen = $this->findOrFail($id);
            
            // Update status berdasarkan action
            switch ($action) {
                case 'APPROVE':
                    $dokumen->status = 'approved';
                    break;
                case 'REJECT':
                    $dokumen->status = 'rejected';
                    break;
                case 'REVISION':
                    $dokumen->status = 'revision';
                    break;
                case 'VERIFY':
                    $dokumen->status = 'verified';
                    break;
                default:
                    throw new \InvalidArgumentException("Invalid action: {$action}");
            }
            
            $dokumen->save();
            
            // Catat history verifikasi
            $this->historyDokumenService->logVerification(
                $dokumen, 
                $admin, 
                $action, 
                $message,
                ['ip_address' => request()->ip()]
            );
            
            DB::commit();
            
            return $dokumen->load('histories');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error verifying dokumen: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Soft delete dokumen
     */
    public function softDeleteDokumen(int $id, $actor, bool $isMahasiswa = true): bool
    {
        DB::beginTransaction();
        
        try {
            $dokumen = $this->findOrFail($id);
            
            // Catat history sebelum delete
            $this->historyDokumenService->logSoftDelete($dokumen, $actor, $isMahasiswa);
            
            // Soft delete file details
            $this->fileDetailService->softDeleteForDokumen($dokumen->id);
            
            // Soft delete dokumen
            $result = $dokumen->delete();
            
            DB::commit();
            
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error soft deleting dokumen: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get dokumen dengan history
     */
    public function getWithHistory(int $id): Dokumen
    {
        return $this->model->with([
            'mahasiswa', 
            'fileDetail' => function($q) {
                $q->withTrashed(); // termasuk yang soft deleted
            },
            'histories' => function($q) {
                $q->with(['mahasiswa', 'user'])->latest();
            }
        ])->withTrashed()->findOrFail($id);
    }
    
    /**
     * Restore dokumen yang soft deleted
     */
    public function restoreDokumen(int $id, User $admin): Dokumen
    {
        DB::beginTransaction();
        
        try {
            $dokumen = $this->model->withTrashed()->findOrFail($id);
            $maker = null;
            // Restore dokumen
            $dokumen->restore();
            
            // Restore file details
            FileDetailService::withTrashed()->where('dokumen_id', $id)->restore();
            
            // Catat history restore
            $this->historyDokumenService->create($maker,[
                'dokumen_id' => $dokumen->id,
                'mahasiswa_id' => $dokumen->mahasiswa_id,
                'user_id' => $admin->id,
                'action' => 'RESTORE',
                'status_from' => 'deleted',
                'status_to' => $dokumen->status,
                'message' => "Dokumen direstore oleh admin {$admin->name}",
            ]);
            
            DB::commit();
            
            return $dokumen;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error restoring dokumen: ' . $e->getMessage());
            throw $e;
        }
    }

        /**
     * Cari dokumen yang sudah ada
     */
    private function findExistingDokumen(int $mahasiswaId, ?string $tipeDkmn = null, ?int $reqDokumenId = null): ?Dokumen
    {
        $query = Dokumen::where('mahasiswa_id', $mahasiswaId)
            ->whereNull('deleted_at'); // hanya yang tidak terhapus
        
        // Cari berdasarkan tipe dokumen jika ada
        if ($tipeDkmn) {
            $query->where('tipeDkmn', $tipeDkmn);
        }
        
        // Cari berdasarkan reqDokumen_id jika ada
        if ($reqDokumenId) {
            // Cek melalui relasi fileDetail
            $query->whereHas('fileDetail', function($q) use ($reqDokumenId) {
                $q->where('reqDokumen_id', $reqDokumenId);
            });
        }
        
        // Prioritaskan yang statusnya pending/revision (bukan approved)
        $dokumen = $query->latest()->first();
        
        return $dokumen;
    }

    /**
 * Get history dokumen untuk mahasiswa tertentu
 */
    public function getHistoryForMahasiswa(int $mahasiswaId, array $filters = [])
    {
        $query = \App\Models\HistoryDokumen::with(['dokumen', 'user'])
            ->whereHas('dokumen', function($q) use ($mahasiswaId) {
                $q->where('mahasiswa_id', $mahasiswaId);
            });
        
        if (isset($filters['dokumen_id'])) {
            $query->where('dokumen_id', $filters['dokumen_id']);
        }
        
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
     * Count pending documents
     */
    public function countPending(): int
    {
        return Dokumen::where('status', status::PENDING->value)
            ->count();
    }

    /**
     * Count critical documents (expired or expiring soon)
     */
    public function countCritical(): int
    {
        $today = now();
        $criticalDate = now()->addDays(30);
        
        return Dokumen::where(function($query) use ($today, $criticalDate) {
                $query->where('tglKdlwrs', '<', $today) // expired
                    ->orWhereBetween('tglKdlwrs', [$today, $criticalDate]); // expiring soon
            })
            ->count();
    }

    /**
     * Count documents validated today
     */
    public function countValidatedToday(): int
    {
        return Dokumen::whereDate('updated_at', today())
            ->whereIn('status', [status::APPROVED->value])
            ->count();
    }

    /**
     * Get critical documents list
     */
    public function getCriticalDocuments(int $limit = 10): array
    {
        $today = now();
        $criticalDate = now()->addDays(30);
        
        $documents = Dokumen::with(['mahasiswa'])
            ->where(function($query) use ($today, $criticalDate) {
                $query->where('tglkdlwrs', '<', $today)
                    ->orWhereBetween('tglkdlwrs', [$today, $criticalDate]);
            })
            ->orderBy('tglkdlwrs')
            ->limit($limit)
            ->get();
        
        return $documents->map(function($doc) {
            $isExpired = $doc->tglkdlwrs < now();
            
            return [
                'id' => $doc->id,
                'init' => strtoupper(substr($doc->mahasiswa->nama ?? 'NA', 0, 2)),
                'name' => $doc->mahasiswa->nama ?? 'Unknown',
                'flag' => $this->getFlagEmoji($doc->mahasiswa->warNeg ?? ''),
                'doc' => $doc->tipeDkmn->value ?? $doc->tipeDkmn,
                'exp' => $doc->tglkdlwrs->format('d/m/Y'),
                'status' => $isExpired ? 'expired' : 'expiring',
            ];
        })->toArray();
    }

    /**
     * Get validation queue
     */
    public function getValidationQueue(int $limit = 10): array
    {
        $documents = Dokumen::with(['mahasiswa'])
            ->where('status', status::PENDING->value)
            ->orderBy('created_at')
            ->limit($limit)
            ->get();
        
        return $documents->map(function($doc, $index) {
            $priority = $index < 2 ? 'high' : ($index < 5 ? 'medium' : 'low');
            
            return [
                'init' => strtoupper(substr($doc->mahasiswa->nama ?? 'NA', 0, 2)),
                'name' => $doc->mahasiswa->nama ?? 'Unknown',
                'doc' => $doc->tipeDkmn->value ?? $doc->tipeDkmn,
                'time' => $doc->created_at->format('H:i'),
                'priority' => $priority,
            ];
        })->toArray();
    }

    /**
     * Get mahasiswa preview for dashboard
     */
    public function getMahasiswaPreview(int $limit = 10): array
    {
        $mahasiswa = \App\Models\Mahasiswa::with(['user', 'dokumen' => function($q) {
                $q->latest()->limit(1);
            }])
            ->limit($limit)
            ->get();
        
        return $mahasiswa->map(function($mhs) {
            $kitas = $mhs->dokumen->firstWhere('tipeDkmn', 'kitas');
            
            return [
                'init' => strtoupper(substr($mhs->nama, 0, 2)),
                'name' => $mhs->nama,
                'flag' => $this->getFlagEmoji($mhs->warNeg),
                'prodi' => $mhs->prodi ?? '-',
                'smt' => $mhs->semester ?? 1,
                'kitas' => $kitas ? $kitas->tglkdlwrs->format('d/m/Y') : '-',
                'attendance' => rand(75, 100), // Anda bisa ganti dengan data real
                'status' => $mhs->user?->status_profile ?? 'pending',
            ];
        })->toArray();
    }

    /**
     * Get monthly chart data (dokumen valid)
     */
    public function getMonthlyChartData(int $months = 6): array
    {
        $data = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            
            $data[] = [
                'label' => $month->format('M'),
                'b' => Dokumen::whereMonth('updated_at', $month->month)
                    ->whereYear('updated_at', $month->year)
                    ->whereIn('status', [status::APPROVED->value])
                    ->count(),
            ];
        }
        
        return $data;
    }

    /**
     * Get flag emoji helper (copy dari UserService atau buat trait)
     */
    private function getFlagEmoji(?string $negara): string
    {
        // sama dengan yang di UserService
        $flags = [
            'Uzbekistan' => '🇺🇿',
            'Vietnam' => '🇻🇳',
            // ... lengkapi sesuai kebutuhan
        ];
        
        return $flags[$negara] ?? '🌍';
    }
}