<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Announcement\createAnnouncementRequest;
use App\Http\Requests\Announcement\updateAnnouncementRequest;
use App\Http\Requests\Dokumen\createDokumenRequest;
use App\Traits\ApiResponseTrait;
use App\Services\DashboardService;
use App\Services\UserService;
use App\Services\DokumenService;
use App\Services\NotificationService;
use App\Services\AnnouncementService;
use App\Services\HistoryDokumenService;
use App\Services\ReqDokumenService;
use App\Http\Requests\User\createUserRequest;
use App\Http\Requests\User\updateUserRequest;
use App\Http\Requests\dokumen\updateDokumenRequest;
use App\Http\Requests\HistoryDok\createHistoryDokRequest;
use App\Http\Requests\Jadwal\createJadwalRequest;
use App\Http\Requests\Jadwal\updateJadwalRequest;
use App\Http\Requests\ReqDokumen\createReqDokumenRequest;
use App\Http\Requests\ReqDokumen\updateReqDokumenRequest;
use App\Http\Requests\updateStatusRequest;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Enums\Status;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;



class KlnController extends Controller
{
    use ApiResponseTrait; // ← TETAP DIPAKAI

    protected $dashboardService;
    protected $UserService;
    protected $dokumenService;
    protected $NotificationService;
    protected $AnnouncementService;
    protected $HistoryDokumenService;
    protected $reqDokumenService;
    

    public function __construct(
        DashboardService $dashboardService,
        UserService $userService,
        DokumenService $dokumenService,
        NotificationService $NotificationService,
        AnnouncementService $AnnouncementService,
        HistoryDokumenService $HistoryDokumenService,
        ReqDokumenService $reqDokumenService,
    ) {
        $this->dashboardService = $dashboardService;
        $this->UserService = $userService;
        $this->dokumenService = $dokumenService;
        $this->NotificationService = $NotificationService;
        $this->AnnouncementService = $AnnouncementService;
        $this->HistoryDokumenService = $HistoryDokumenService;
        $this->reqDokumenService = $reqDokumenService;
    }

    /**
     * ========== HALAMAN (return VIEW) ==========
     */
    public function index()
    {
        return view('kln.dashboard');
    }

    public function usersPage()
    {
        return view('kln.users.index');
    }

    public function dokumenPage()
    {
        return view('kln.dokumen.index');
    }

    public function announcementPage()
    {
        return view('kln.announcements.index');
    }

    public function jadwalPage()
    {
        return view('kln.jadwal.index');
    }

    public function monitoringPage()
    {
        return view('kln.monitoring.index');
    }

    public function profile()
    {
        return view('kln.profile');
    }

    /**
     * ========== API ENDPOINTS (return JSON) ==========
     * METHOD INI SAMA PERSIS DENGAN YANG SEBELUMNYA
     */
    // public function getDashboardStats()
    // {
    //     try {
    //         $data = [
    //             'statistics' => [
    //                 'total_mahasiswa' => $this->UserService->countByRole('mahasiswa'),
    //                 'pending_docs' => $this->dokumenService->countPending(),
    //                 'critical_docs' => $this->dokumenService->countCritical(),
    //                 'validated_today' => $this->dokumenService->countValidatedToday(),
    //             ],
    //             'critical_documents' => $this->dokumenService->getCriticalDocuments(5),
    //             'validation_queue' => $this->dokumenService->getValidationQueue(6),
    //             'negara_distribution' => $this->UserService->getCountryDistribution(),
    //             'monthly_chart' => $this->UserService->getMonthlyStats(),
    //         ];
    //         return $this->successResponse($data, 'Dashboard stats retrieved');
            
    //     } catch (\Exception $e) {
    //         return $this->errorResponse('Gagal ambil data', 500, $e->getMessage());
    //     }
    // }

    public function getUsers(Request $request)
    {
        try {
            $filters = $request->only(['role', 'email', 'status_profile']);
            $users = $this->UserService->getAll($filters, $request->get('per_page', 15));
            
            return $this->paginatedResponse($users, 'Users retrieved');
            
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal ambil users', 500, $e->getMessage());
        }
    }

    public function storeUser(createUserRequest $request)
    {
        try {
            $maker = $request->user();
            $user = $this->UserService->create($maker,$request->validated());
            return $this->successResponse($user, 'User created', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal create user'. $maker->email, 500, $e->getMessage());
        }
    }

    public function updateStatusMahasiswa($id, updateStatusRequest $request)
    {
        try {
            $maker = $request->user();
            $mahasiswa = $this->UserService->updateProfileStatus(
                $maker, 
                $id,
                $request->status,
                $request->notification_id
            );

            return $this->successResponse($mahasiswa, 'Status updated');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal update status', 500, $e->getMessage());
        }
    }

    public function showUser($id)
    {
        try {
            $user = $this->UserService->findOrFail($id);
            return $this->successResponse($user, 'User details retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal ambil user', 404, $e->getMessage());
        }
    }

//Dokumen


    public function updateDokumenStatus(updateDokumenRequest $request, $id)
    {
        try {
            $dokumen = $this->dokumenService->updateStatus(
                $id, 
                $request->status, 
                $request->catatan_revisi
            );
            return $this->successResponse($dokumen, 'Status updated');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal update status', 500, $e->getMessage());
        }
    }

    public function verifyDokumen(createHistoryDokRequest $request, $id)
    {
        try {
            $admin = auth()->user();
            
            $dokumen = $this->dokumenService->verifyDokumen(
                $id,
                $admin,
                $request->action,
                $request->message
            );
            
            return $this->successResponse([
                'id' => $dokumen->id,
                'status' => $dokumen->status,
                'history' => $dokumen->histories
            ], "Dokumen berhasil di{$request->action}");
            
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal verifikasi dokumen', 500, $e->getMessage());
        }
    }

    // Untuk lihat history dokumen
    public function getHistory($id)
    {
        try {
            $history = $this->HistoryDokumenService->getHistoryForDokumen($id);
            return $this->paginatedResponse($history, 'History retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal ambil history', 500);
        }
    }

// request Dokumen
    public function indexReqDocument(Request $request)
    {
        try {
            $filters = $request->only(['status', 'tipeDkmn', 'search', 'per_page']);
            $requests = $this->reqDokumenService->getForAdmin($filters);
            
            return $this->paginatedResponse($requests, 'Requests retrieved successfully');
            
        } catch (\Exception $e) {
            Log::error('Error in getRequests: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data', 500, $e->getMessage());
        }
    }

     /**
     * Get detail request
     */
    public function showReqDocument($id)
    {
        try {
            $request = $this->reqDokumenService->findOrFail($id);
            $request->load(['mahasiswa', 'user', 'fileDetail']);
            
            return $this->successResponse($request, 'Request detail retrieved');
            
        } catch (\Exception $e) {
            return $this->errorResponse('Request tidak ditemukan', 404);
        }
    }

    public function updateReqDokumen(updateReqDokumenRequest $request, $id)
    {
        try {
            $admin = Auth::user();
            
            $reqDokumen = $this->reqDokumenService->updateStatus(
                $id,
                $request->validated(),
                $admin
            );
            
            $message = $this->getStatusMessage($request->status);
            
            return $this->successResponse([
                'id' => $reqDokumen->id,
                'status' => $reqDokumen->status,
                'catatan' => $reqDokumen->catatan,
            ], $message);
            
        } catch (\Exception $e) {
            Log::error('Error updating request status: ' . $e->getMessage());
            return $this->errorResponse('Gagal update status', 500, $e->getMessage());
        }
    }

    /**
     * Upload dokumen untuk request (complete request)
     */
        /**
         * Upload dokumen untuk request (complete request)
         */
        public function uploadReqDokumen(Request $request, $id)
    {
        try {
            $admin = Auth::user();
            
            // Validasi manual karena ini upload file
            $request->validate([
                'file' => 'required|file|mimes:pdf|max:2048',
                'notification_id' => 'nullable|exists:notification,id',
                'tipeDkmn' => 'required|string|max:50'
            ]);
            
            // Upload file dan complete request
            $reqDokumen = $this->reqDokumenService->completeRequestWithFile(
                $id,
                $request->file('file'),
                [], // data tambahan jika perlu
                $admin
            );
            
            // Kirim notifikasi ke mahasiswa
            if ($request->notification_id) {
                $this->NotificationService->sendToUsers(
                    $request->notification_id,
                    [$reqDokumen->user_id]
                );
            }
            
            return $this->successResponse([
                'request' => [
                    'id' => $reqDokumen->id,
                    'tipeDkmn' => $reqDokumen->tipeDkmn,
                    'namaDkmn' => $reqDokumen->namaDkmn,
                    'status' => $reqDokumen->status,
                    'file' => $reqDokumen->fileDetail->first(),
                ]
            ], 'Request dokumen berhasil diselesaikan', 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse('Validasi gagal', 422, $e->errors());
        } catch (\Exception $e) {
            Log::error('Error uploading dokumen for request: ' . $e->getMessage());
            return $this->errorResponse('Gagal upload dokumen: ' . $e->getMessage(), 500);
        }
    }

// announcement

    public function storeAnnouncement(createAnnouncementRequest $request)
    {
        try{
            $maker = $request->user();
            $announcement = $this->AnnouncementService->create(
                $maker,
                $request->validated()
            );
            return $this->successResponse($announcement, 'Announcement Created');
        } catch (\Exception $e){
            return $this->errorResponse('Gagal membuat announcement', 500, $e->getMessage());
        }
    }

    public function updateAnnouncement(updateAnnouncementRequest $request, $id)
    {
        try{
            
        } catch (\Exception $e){
            return $this->errorResponse('Gagal update status', 500, $e->getMessage());
        }
    }

    public function destroyAnnouncement($id)
    {
        try{
            
        } catch (\Exception $e){
            return $this->errorResponse('Gagal update status', 500, $e->getMessage());
        }
    }


// Jadwal
    public function storeJadwal(createJadwalRequest $request)
    {
        // Implementasi create jadwal
    }

    public function updateJadwal(updateJadwalRequest $request, $id)
    {
        // Implementasi update jadwal
    }

    public function destroyJadwal($id)
    {
        // Implementasi delete jadwal
    }



//private function
private function getStatusMessage($status)
    {
        $messages = [
            'processing' => 'Request sedang diproses',
            'ready' => 'Dokumen siap, silakan upload file',
            'rejected' => 'Request ditolak',
            'completed' => 'Request selesai',
        ];
        
        return $messages[$status] ?? 'Status berhasil diupdate';
    }




    // ... method lainnya SAMA PERSIS dengan sebelumnya
}