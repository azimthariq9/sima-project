<?php
// app/Services/DashboardService.php

namespace App\Services;

use App\Models\User;
use App\Models\Dokumen;
use App\Models\Jadwal;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    protected $userService;
    protected $dokumenService;
    protected $jadwalService;
    
    public function __construct(
        UserService $userService,
        DokumenService $dokumenService,
        JadwalService $jadwalService
    ) {
        $this->userService = $userService;
        $this->dokumenService = $dokumenService;
        $this->jadwalService = $jadwalService;
    }
    
    /**
     * SATU METHOD untuk semua role
     */
    public function getStatsByRole(string $role): array
    {
        return match($role) {
            'kln' => $this->getKlnStats(),
            'jurusan' => $this->getJurusanStats(),
            'bipa' => $this->getBipaStats(),
            default => []
        };
    }
    
    /**
     * STATS UNTUK KLN
     */
    protected function getKlnStats(): array
    {
        return [
            'total_mahasiswa' => User::where('role', 'mahasiswa')->count(),
            'pending_docs' => Dokumen::where('status', 'pending')->count(),
            'critical_docs' => Dokumen::whereIn('status', ['expired', 'expiring'])
                ->whereDate('tanggal_berakhir', '<=', now()->addDays(30))
                ->count(),
            'validated_today' => Dokumen::whereDate('updated_at', today())
                ->where('status', 'approved')
                ->count(),
            'total_negara' => Mahasiswa::distinct('negara_asal')->count('negara_asal'),
            'weekly_schedule' => Jadwal::whereBetween('tanggal', [
                now()->startOfWeek(), 
                now()->endOfWeek()
            ])->count(),
            
            // Data kompleks
            'critical_documents' => $this->getCriticalDocuments(),
            'negara_distribution' => $this->getCountryDistribution(),
            'validation_queue' => $this->getValidationQueue(),
            'mahasiswa_preview' => $this->getMahasiswaPreview(),
            'activity_log' => $this->getRecentActivities(),
            'monthly_chart' => $this->getMonthlyChart(),
        ];
    }
    
    /**
     * STATS UNTUK JURUSAN (berbeda total!)
     */
    protected function getJurusanStats(): array
    {
        $jurusanId = auth()->user()->jurusan_id;
        
        return [
            'total_mahasiswa' => Mahasiswa::where('jurusan_id', $jurusanId)->count(),
            'total_dosen' => User::where('role', 'dosen')
                ->where('jurusan_id', $jurusanId)
                ->count(),
            'total_matakuliah' => Matakuliah::where('jurusan_id', $jurusanId)->count(),
            'kelas_aktif' => Kelas::where('jurusan_id', $jurusanId)
                ->where('status', 'active')
                ->count(),
            
            // Data spesifik jurusan
            'kehadiran_hari_ini' => $this->getKehadiranHariIni($jurusanId),
            'nilai_pending' => $this->getNilaiPending($jurusanId),
            'jadwal_semester' => $this->getJadwalSemester($jurusanId),
            'prestasi_mahasiswa' => $this->getPrestasiMahasiswa($jurusanId),
        ];
    }
    
    /**
     * STATS UNTUK BIPA (berbeda lagi!)
     */
    protected function getBipaStats(): array
    {
        return [
            'total_peserta' => PesertaBipa::where('tahun_ajaran', now()->year)->count(),
            'kelas_berjalan' => KelasBipa::where('status', 'active')->count(),
            'tingkat_kelas' => [
                'pemula' => KelasBipa::where('tingkat', 'pemula')->count(),
                'menengah' => KelasBipa::where('tingkat', 'menengah')->count(),
                'lanjutan' => KelasBipa::where('tingkat', 'lanjutan')->count(),
            ],
            'asesmen_minggu_ini' => AsesmenBipa::whereBetween('tanggal', [
                now()->startOfWeek(), 
                now()->endOfWeek()
            ])->count(),
            
            // Data spesifik BIPA
            'sertifikasi_terbaru' => $this->getSertifikasiTerbaru(),
            'pengajar_aktif' => $this->getPengajarAktif(),
        ];
    }
}
    
//     // Method helpers untuk KLN (sama seperti sebelumnya)
//     protected function getCriticalDocuments() { ... }
//     protected function getCountryDistribution() { ... }
//     protected function getValidationQueue() { ... }
//     protected function getMahasiswaPreview() { ... }
//     protected function getRecentActivities() { ... }
//     protected function getMonthlyChart() { ... }
    
//     // Method helpers untuk JURUSAN
//     protected function getKehadiranHariIni($jurusanId) { ... }
//     protected function getNilaiPending($jurusanId) { ... }
//     protected function getJadwalSemester($jurusanId) { ... }
//     protected function getPrestasiMahasiswa($jurusanId) { ... }
    
//     // Method helpers untuk BIPA
//     protected function getSertifikasiTerbaru() { ... }
//     protected function getPengajarAktif() { ... }
// }