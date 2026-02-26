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
use App\Http\Requests\Mahasiswa\updateMahasiswaRequest;
use App\Http\Requests\ReqDokumen\createReqDokumenRequest;
use App\Http\Requests\ReqDokumen\updateReqDokumenRequest;
use App\Http\Requests\updateStatusRequest;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class MahasiswaController extends Controller
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

    public function updateProfile(updateMahasiswaRequest $request){
        $mahasiswa = 
    }



}
