<?php
// app/Http/Controllers/API/MahasiswaController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use App\Services\MahasiswaService;
use App\Services\DokumenService;
use App\Services\NotificationService;
use App\Services\AnnouncementService;
use App\Services\ReqDokumenService;
use App\Http\Requests\Mahasiswa\updateMahasiswaRequest;
use App\Http\Requests\Dokumen\createDokumenRequest;
use App\Http\Requests\ReqDokumen\createReqDokumenRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MahasiswaController extends Controller
{
    use ApiResponseTrait;

    protected $mahasiswaService;
    protected $dokumenService;
    protected $notificationService;
    protected $announcementService;
    protected $reqDokumenService;

    public function __construct(
        MahasiswaService $mahasiswaService,
        DokumenService $dokumenService,
        NotificationService $notificationService,
        AnnouncementService $announcementService,
        ReqDokumenService $reqDokumenService
    ) {
        $this->mahasiswaService = $mahasiswaService;
        $this->dokumenService = $dokumenService;
        $this->notificationService = $notificationService;
        $this->announcementService = $announcementService;
        $this->reqDokumenService = $reqDokumenService;
    }

    /**
     * Get profile mahasiswa yang sedang login
     */
    public function getProfile()
    {
        try {
            $user = Auth::user();
            
            $mahasiswa = $this->mahasiswaService->getProfile($user->id);
            
            if (!$mahasiswa) {
                return $this->errorResponse('Data mahasiswa tidak ditemukan', 404);
            }
            
            // Cek kelengkapan profile
            $completion = $this->mahasiswaService->checkProfileCompletion($user->id);
            
            return $this->successResponse([
                'mahasiswa' => $mahasiswa,
                'profile_completion' => $completion,
            ], 'Profile retrieved successfully');
            
        } catch (\Exception $e) {
            Log::error('Error in getProfile: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil profile', 500, $e->getMessage());
        }
    }

    /**
     * Update profile mahasiswa
     */
    public function updateProfile(updateMahasiswaRequest $request)
    {
        try {
            $user = Auth::user();
            
            // Validasi tambahan: pastikan user_id di request sesuai dengan user yang login
            if ($request->has('user_id') && $request->user_id != $user->id) {
                return $this->errorResponse('User ID tidak sesuai', 403);
            }
            
            // Hapus user_id dari data jika ada (karena sudah ditentukan dari login)
            $data = $request->validated();
            unset($data['user_id']);
            
            $mahasiswa = $this->mahasiswaService->updateProfile($user->id, $data);
            
            return $this->successResponse([
                'mahasiswa' => $mahasiswa,
                'message' => 'Profile berhasil diperbarui'
            ], 'Profile updated successfully');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Data mahasiswa tidak ditemukan', 404);
        } catch (\Exception $e) {
            Log::error('Error in updateProfile: ' . $e->getMessage());
            return $this->errorResponse('Gagal update profile', 500, $e->getMessage());
        }
    }

    /**
     * Get dashboard stats untuk mahasiswa
     */
    public function dashboard()
    {
        try {
            $user = Auth::user();
            
            $stats = $this->mahasiswaService->getDashboardStats($user->id);
            
            // Ambil pengumuman terbaru yang ditujukan untuk mahasiswa ini
            $mahasiswa = $this->mahasiswaService->getByUserIdOrFail($user->id);
            $announcements = $this->announcementService->getForMahasiswa($mahasiswa->id, 5);
            
            // Ambil notifikasi terbaru
            $notifications = $this->notificationService->getForUser($user->id, 5);
            
            return $this->successResponse([
                'stats' => $stats,
                'recent_announcements' => $announcements,
                'recent_notifications' => $notifications,
            ], 'Dashboard data retrieved successfully');
            
        } catch (\Exception $e) {
            Log::error('Error in dashboard: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data dashboard', 500, $e->getMessage());
        }
    }


    /**
     * Get semua dokumen milik mahasiswa
     */
    public function getDokumen(Request $request)
    {
        try {
            $user = Auth::user();
            $mahasiswa = $this->mahasiswaService->getByUserIdOrFail($user->id);
            
            $filters = $request->only(['tipeDkmn', 'status', 'per_page']);
            $dokumen = $this->dokumenService->getByMahasiswa($mahasiswa->id, $filters);
            
            return $this->paginatedResponse($dokumen, 'Dokumen retrieved successfully');
            
        } catch (\Exception $e) {
            Log::error('Error in getDokumen: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil dokumen', 500, $e->getMessage());
        }
    }

    /**
     * Download dokumen
     */
    public function downloadDokumen($id)
    {
        try {
            $user = Auth::user();
            $mahasiswa = $this->mahasiswaService->getByUserIdOrFail($user->id);
            
            // Verifikasi kepemilikan dokumen
            $dokumen = $this->dokumenService->findOrFail($id);
            
            if ($dokumen->mahasiswa_id !== $mahasiswa->id) {
                return $this->errorResponse('Anda tidak memiliki akses ke dokumen ini', 403);
            }
            
            return $this->dokumenService->downloadDokumen($id);
            
        } catch (\Exception $e) {
            Log::error('Error downloading dokumen: ' . $e->getMessage());
            return $this->errorResponse('Gagal download dokumen', 500, $e->getMessage());
        }
    }

    /**
     * Get history dokumen
     */
    public function getDokumenHistory(Request $request)
    {
        try {
            $user = Auth::user();
            $mahasiswa = $this->mahasiswaService->getByUserIdOrFail($user->id);
            
            $filters = $request->only(['dokumen_id', 'action', 'from_date', 'to_date', 'per_page']);
            
            $history = $this->dokumenService->getHistoryForMahasiswa(
                $mahasiswa->id,
                $filters
            );
            
            return $this->paginatedResponse($history, 'History retrieved successfully');
            
        } catch (\Exception $e) {
            Log::error('Error in getDokumenHistory: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil history', 500, $e->getMessage());
        }
    }

    /**
     * Request dokumen baru
     */
    public function requestDokumen(createReqDokumenRequest $request)
    {
        try {
            $user = Auth::user();
            $mahasiswa = $this->mahasiswaService->getByUserIdOrFail($user->id);
            
            $reqDokumen = $this->reqDokumenService->createRequest(
                $mahasiswa,
                $request->validated()
            );
            
            return $this->successResponse([
                'id' => $reqDokumen->id,
                'tipeDkmn' => $reqDokumen->tipeDkmn,
                'status' => $reqDokumen->status,
                'created_at' => $reqDokumen->created_at,
            ], 'Request dokumen berhasil dikirim', 201);
            
        } catch (\Exception $e) {
            Log::error('Error in requestDokumen: ' . $e->getMessage());
            return $this->errorResponse('Gagal membuat request', 500, $e->getMessage());
        }
    }
    
    /**
     * Lihat history request dokumen
     */
    public function getRequestDokumen(Request $request)
    {
        try {
            $user = Auth::user();
            $mahasiswa = $this->mahasiswaService->getByUserIdOrFail($user->id);
            
            $filters = $request->only(['status', 'per_page']);
            $requests = $this->reqDokumenService->getForMahasiswa($mahasiswa->id, $filters);
            
            return $this->paginatedResponse($requests, 'Requests retrieved successfully');
            
        } catch (\Exception $e) {
            Log::error('Error in getRequestDokumen: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data', 500, $e->getMessage());
        }
    }
}
