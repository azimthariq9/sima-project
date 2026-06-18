<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\kelas;
use App\Models\Matakuliah;
use App\Models\dosen;
use App\Services\JadwalService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Jadwal\createJadwalRequest;
use App\Http\Requests\Jadwal\updateJadwalRequest;
use App\Services\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class JadwalController extends Controller
{
    protected JadwalService $jadwalService;

    public function __construct(JadwalService $jadwalService)
    {
        $this->jadwalService = $jadwalService;
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $jadwal = $this->jadwalService->getAll();
        return response()->json([
            'success' => true,
            'data'    => $jadwal->items(),
            'pagination' => [
                'current_page' => $jadwal->currentPage(),
                'last_page'    => $jadwal->lastPage(),
                'total'        => $jadwal->total(),
            ],
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | GET DATA — AJAX table
    |--------------------------------------------------------------------------
    */
    public function getData(Request $request)
    {
        $filters = [];
        if ($request->filled('hari'))     $filters['hari']     = $request->hari;
        if ($request->filled('kelas_id')) $filters['kelas_id'] = $request->kelas_id;
        if ($request->filled('dosen_id')) $filters['dosen_id'] = $request->dosen_id;

        $jadwal = $this->jadwalService->getAll($filters);

        return response()->json([
            'success'    => true,
            'data'       => $jadwal->items(),
            'pagination' => [
                'current_page' => $jadwal->currentPage(),
                'last_page'    => $jadwal->lastPage(),
                'per_page'     => $jadwal->perPage(),
                'total'        => $jadwal->total(),
            ],
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        try {
            $jadwal = Jadwal::with(['kelas', 'dosen.user', 'matakuliah'])->findOrFail($id);
            return response()->json(['success' => true, 'data' => $jadwal], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    | Input dari admin: kodeKelas + tahunAjar, kodeMk, kodeDos/nidn
    | Controller yang resolve ke ID masing-masing
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'kodeKelas'    => ['required', 'string'],
            'tahunAjar'    => ['required', 'string'],
            'kodeMk'       => ['required', 'string'],
            'kodeDos'      => ['required', 'string'], // bisa kodeDos atau nidn
            'hari'         => ['required', 'string', 'max:10'],
            'jam'          => ['required', 'string', 'max:20'],
            'ruangan'      => ['required', 'string', 'max:50'],
            'totalSesi'    => ['required', 'integer', 'min:1'],
        ]);

        try {
            // Resolve kelas via kodeKelas + tahunAjar
            $kelas = kelas::where('kodeKelas', $request->kodeKelas)
                ->where('tahunAjar', $request->tahunAjar)
                ->firstOrFail();

            // Resolve matakuliah via kodeMk (unique)
            $matakuliah = Matakuliah::where('kodeMk', $request->kodeMk)
                ->firstOrFail();

            // Resolve dosen via kodeDos ATAU nidn
            $dosen = dosen::where('kodeDos', $request->kodeDos)
                ->orWhere('nidn', $request->kodeDos)
                ->firstOrFail();

            $maker  = Auth::user();
            $jadwal = $this->jadwalService->create($maker, [
                'kelas_id'      => $kelas->id,
                'matakuliah_id' => $matakuliah->id,
                'dosen_id'      => $dosen->id,
                'hari'          => $request->hari,
                'jam'           => $request->jam,
                'ruangan'       => $request->ruangan,
                'totalSesi'     => $request->totalSesi,
            ]);

            ActivityLog::record(
                "Membuat jadwal: {$request->kodeMk} - {$request->kodeKelas}",
                'jadwal',
                $jadwal->id
            );

            return response()->json([
                'success' => true,
                'data'    => $jadwal,
                'flash'   => ['type' => 'success', 'message' => 'Jadwal berhasil dibuat', 'theme' => 'amazon', 'timeout' => 5000],
            ], 201);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Berikan pesan yang informatif berdasarkan yang tidak ditemukan
            $pesan = $this->resolveNotFoundMessage($request);
            return response()->json([
                'success' => false,
                'message' => $pesan,
                'flash'   => ['type' => 'error', 'message' => $pesan, 'theme' => 'amazon', 'timeout' => 5000],
            ], 404);

        } catch (\Exception $e) {
            Log::error('Create jadwal failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'flash'   => ['type' => 'error', 'message' => 'Gagal membuat jadwal', 'theme' => 'amazon', 'timeout' => 5000],
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    | Sama seperti store — input kode, resolve ke ID
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $request->validate([
            'kodeKelas'    => ['sometimes', 'string'],
            'tahunAjar'    => ['sometimes', 'string'],
            'kodeMk'       => ['sometimes', 'string'],
            'kodeDos'      => ['sometimes', 'string'],
            'hari'         => ['sometimes', 'string', 'max:10'],
            'jam'          => ['sometimes', 'string', 'max:20'],
            'ruangan'      => ['sometimes', 'string', 'max:50'],
            'totalSesi'    => ['sometimes', 'integer', 'min:1'],
        ]);

        try {
            $jadwal = Jadwal::findOrFail($id);
            $data   = [];

            // Resolve kelas jika kodeKelas dan tahunAjar dikirim
            if ($request->filled('kodeKelas') && $request->filled('tahunAjar')) {
                $kelas = kelas::where('kodeKelas', $request->kodeKelas)
                    ->where('tahunAjar', $request->tahunAjar)
                    ->firstOrFail();
                $data['kelas_id'] = $kelas->id;
            }

            // Resolve matakuliah
            if ($request->filled('kodeMk')) {
                $matakuliah = Matakuliah::where('kodeMk', $request->kodeMk)->firstOrFail();
                $data['matakuliah_id'] = $matakuliah->id;
            }

            // Resolve dosen
            if ($request->filled('kodeDos')) {
                $dosen = dosen::where('kodeDos', $request->kodeDos)
                    ->orWhere('nidn', $request->kodeDos)
                    ->firstOrFail();
                $data['dosen_id'] = $dosen->id;
            }

            // Field langsung
            foreach (['hari', 'jam', 'ruangan', 'totalSesi'] as $field) {
                if ($request->filled($field)) {
                    $data[$field] = $request->$field;
                }
            }

            $maker   = Auth::user();
            $updated = $this->jadwalService->update($maker, $id, $data);

            ActivityLog::record("Memperbarui jadwal #{$id}", 'jadwal', (int) $id);

            return response()->json([
                'success' => true,
                'data'    => $updated,
                'flash'   => ['type' => 'success', 'message' => 'Jadwal berhasil diupdate', 'theme' => 'amazon', 'timeout' => 5000],
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $pesan = $this->resolveNotFoundMessage($request);
            return response()->json(['success' => false, 'message' => $pesan], 404);

        } catch (\Exception $e) {
            Log::error('Update jadwal failed', ['jadwal_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        try {
            $maker = Auth::user();
            $this->jadwalService->delete($maker, $id);

            ActivityLog::record("Menghapus jadwal #{$id}", 'jadwal', (int) $id);

            return response()->json([
                'success' => true,
                'data'    => [],
                'flash'   => ['type' => 'success', 'message' => 'Jadwal berhasil dihapus', 'theme' => 'amazon', 'timeout' => 5000],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Delete jadwal failed', ['jadwal_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER — Pesan error yang informatif saat resolve gagal
    |--------------------------------------------------------------------------
    */
    private function resolveNotFoundMessage(Request $request): string
    {
        // Coba cari satu per satu untuk tahu yang mana yang tidak ditemukan
        if ($request->filled('kodeKelas') && $request->filled('tahunAjar')) {
            $kelasAda = kelas::where('kodeKelas', $request->kodeKelas)
                ->where('tahunAjar', $request->tahunAjar)
                ->exists();
            if (!$kelasAda) {
                return "Kelas '{$request->kodeKelas}' tahun ajaran '{$request->tahunAjar}' tidak ditemukan";
            }
        }

        if ($request->filled('kodeMk')) {
            $mkAda = Matakuliah::where('kodeMk', $request->kodeMk)->exists();
            if (!$mkAda) {
                return "Mata kuliah dengan kode '{$request->kodeMk}' tidak ditemukan";
            }
        }

        if ($request->filled('kodeDos')) {
            $dosenAda = dosen::where('kodeDos', $request->kodeDos)
                ->orWhere('nidn', $request->kodeDos)
                ->exists();
            if (!$dosenAda) {
                return "Dosen dengan kode/NIDN '{$request->kodeDos}' tidak ditemukan";
            }
        }

        return 'Data tidak ditemukan, periksa kembali input Anda';
    }
}