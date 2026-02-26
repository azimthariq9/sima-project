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
use App\Http\Requests\User\createUserRequest;
use App\Http\Requests\User\updateUserRequest;
use App\Http\Requests\dokumen\updateDokumenRequest;
use App\Http\Requests\Jadwal\createJadwalRequest;
use App\Http\Requests\Jadwal\updateJadwalRequest;
use App\Http\Requests\updateStatusRequest;

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

    public function __construct(
        DashboardService $dashboardService,
        UserService $userService,
        DokumenService $dokumenService,
        NotificationService $NotificationService,
        AnnouncementService $AnnouncementService
    ) {
        $this->dashboardService = $dashboardService;
        $this->UserService = $userService;
        $this->dokumenService = $dokumenService;
        $this->NotificationService = $NotificationService;
        $this->AnnouncementService = $AnnouncementService;
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

    /**
     * ========== API ENDPOINTS (return JSON) ==========
     * METHOD INI SAMA PERSIS DENGAN YANG SEBELUMNYA
     */
    public function getDashboardStats()
    {
        try {
            $data = [
                'statistics' => [
                    'total_mahasiswa' => $this->UserService->countByRole('mahasiswa'),
                    'pending_docs' => $this->dokumenService->countPending(),
                    'critical_docs' => $this->dokumenService->countCritical(),
                    'validated_today' => $this->dokumenService->countValidatedToday(),
                ],
                'critical_documents' => $this->dokumenService->getCriticalDocuments(5),
                'validation_queue' => $this->dokumenService->getValidationQueue(6),
                'negara_distribution' => $this->UserService->getCountryDistribution(),
                'monthly_chart' => $this->UserService->getMonthlyStats(),
            ];
            return $this->successResponse($data, 'Dashboard stats retrieved');
            
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal ambil data', 500, $e->getMessage());
        }
    }

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
    public function storeDokumen(createDokumenRequest $request)
    {
        try{
            
        } catch (\Exception $e){
            return $this->errorResponse('Gagal update status', 500, $e->getMessage());
        }
    }

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
            return $this->errorResponse('Gagal update status', 500, $e->getMessage());
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







    // ... method lainnya SAMA PERSIS dengan sebelumnya
}