<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Models\Kehadiran; // model untuk tabel jadwal_mahasiswa
use App\Traits\LogsActivityTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KehadiranService extends BaseService
{
    use LogsActivityTrait;

    public function __construct(Kehadiran $kehadiran)
    {
        parent::__construct($kehadiran);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE BULK KEHADIRAN
    | Satu submit untuk semua mahasiswa di satu sesi jadwal
    |--------------------------------------------------------------------------
    */
    public function storeBulk($maker, array $data): array
    {
        DB::beginTransaction();

        try {
            $jadwalId = $data['jadwal_id'];
            $sesi     = $data['sesi'];
            $tglSesi  = $data['tglSesi'];
            $jam      = $data['jam'] ?? null;

            // Update jam jika dosen ubah
            if ($jam) {
                Jadwal::where('id', $jadwalId)->update(['jam' => $jam]);
            }

            $created = [];
            foreach ($data['kehadiran'] as $row) {
                // Upsert: kalau sesi + jadwal + mahasiswa sudah ada, update statusnya
                $kehadiran = Kehadiran::updateOrCreate(
                    [
                        'jadwal_id'    => $jadwalId,
                        'mahasiswa_id' => $row['mahasiswa_id'],
                        'sesi'         => $sesi,
                    ],
                    [
                        'status'  => $row['status'],
                        'tglSesi' => $tglSesi,
                    ]
                );
                $created[] = $kehadiran;
            }

            // $this->logActivity(
            //     'CREATE',
            //     (object)['id' => $jadwalId, 'jadwal_id' => $jadwalId],
            //     "Dosen input kehadiran sesi {$sesi} untuk jadwal ID {$jadwalId}",
            //     $maker
            // );

            DB::commit();
            return $created;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing bulk kehadiran: ' . $e->getMessage(), [
                'data'  => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | GET KEHADIRAN BY JADWAL
    | Rekap semua sesi kehadiran di jadwal tertentu
    |--------------------------------------------------------------------------
    */
    public function getByJadwal(int $jadwalId): array
    {
        $rows = Kehadiran::with('mahasiswa')
            ->where('jadwal_id', $jadwalId)
            ->orderBy('sesi')
            ->orderBy('mahasiswa_id')
            ->get();

        // Group by sesi untuk tampilan rekap
        return $rows->groupBy('sesi')->map(function ($items, $sesi) {
            return [
                'sesi'      => $sesi,
                'kehadiran' => $items,
            ];
        })->values()->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE JAM JADWAL
    |--------------------------------------------------------------------------
    */
    public function updateJamJadwal($maker, int $jadwalId, string $jam): Jadwal
    {
        DB::beginTransaction();

        try {
            $jadwal = Jadwal::findOrFail($jadwalId);
            $jadwal->update(['jam' => $jam]);

            $this->logActivity(
                'UPDATE',
                $jadwal,
                "Dosen update jam jadwal ID {$jadwalId} menjadi {$jam}",
                $maker
            );

            DB::commit();
            return $jadwal->fresh(['kelas', 'matakuliah', 'dosen']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating jam jadwal: ' . $e->getMessage());
            throw $e;
        }
    }
}