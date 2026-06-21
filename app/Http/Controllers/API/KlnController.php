<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ReqDokumen;
use App\Models\FileDetail;
use App\Models\User;
use App\Models\jurusan;
use App\Services\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KlnController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $today = now()->toDateString();

        // ── Stat cards ────────────────────────────────────────────
        $totalMahasiswa    = DB::table('mahasiswa')->count();
        $dokumenPending    = DB::table('reqDokumen')->where('status', 'pending')->count();
        $dokumenExpired    = DB::table('dokumen')
            ->whereDate('tglKdlwrs', '<', $today)->count();
        $absensiHariIni    = DB::table('jadwal_mahasiswa')
            ->whereDate('tglSesi', $today)->where('status', 'present')->count();
        $negaraDistinct    = DB::table('mahasiswa')
            ->whereNotNull('warNeg')->where('warNeg', '<>', '')
            ->distinct()->count('warNeg');
        $divalidasiHariIni = DB::table('dokumen')
            ->whereDate('updated_at', $today)->where('status', 'approved')->count();
        $jadwalAktif       = DB::table('jadwal')->count();

        // ── Dokumen kritis (expired + ≤30 hari) ──────────────────
        $dokumenKritis = DB::table('dokumen')
            ->join('mahasiswa', 'dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->whereDate('dokumen.tglKdlwrs', '<=', now()->addDays(30)->toDateString())
            ->select(
                'mahasiswa.nama',
                'mahasiswa.warNeg',
                'dokumen.tipeDkmn',
                'dokumen.tglKdlwrs',
                DB::raw("CASE WHEN dokumen.\"tglKdlwrs\" < CURRENT_DATE THEN 'expired' ELSE 'expiring' END as doc_status")
            )
            ->orderBy('dokumen.tglKdlwrs')
            ->limit(10)
            ->get();

        // ── Sebaran negara ────────────────────────────────────────
        $sebaranNegara = DB::table('mahasiswa')
            ->whereNotNull('warNeg')->where('warNeg', '<>', '')
            ->select('warNeg', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('warNeg')
            ->orderByDesc('jumlah')
            ->limit(7)
            ->get();

        // ── Antrian validasi (dokumen uploaded, belum divalidasi) ────
        $antrianValidasi = DB::table('dokumen')
            ->join('mahasiswa', 'dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->whereIn('dokumen.status', ['pending', 'active'])
            ->whereNull('dokumen.deleted_at')
            ->select('dokumen.id', 'mahasiswa.nama', 'mahasiswa.id as mahasiswa_id',
                     'dokumen.tipeDkmn', 'dokumen.namaDkmn', 'dokumen.created_at')
            ->orderBy('dokumen.created_at')
            ->limit(5)
            ->get();

        // ── Request dokumen (mahasiswa minta diterbitkan) ─────────
        $reqDokumenPending = DB::table('reqDokumen')->where('status', 'pending')->count();

        return view('kln.dashboard', compact(
            'totalMahasiswa', 'dokumenPending', 'dokumenExpired',
            'absensiHariIni', 'negaraDistinct', 'divalidasiHariIni', 'jadwalAktif',
            'dokumenKritis', 'sebaranNegara', 'antrianValidasi', 'reqDokumenPending'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | DOKUMEN LIST
    |--------------------------------------------------------------------------
    */

    public function dokumen(Request $request)
    {
        $total    = ReqDokumen::count();
        $pending  = ReqDokumen::where('status', 'pending')->count();
        $approved = ReqDokumen::where('status', 'approved')->count();
        $rejected = ReqDokumen::where('status', 'rejected')->count();

        $query = ReqDokumen::with('mahasiswa')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->whereHas('mahasiswa', fn($mq) => $mq->where('nama', 'ilike', "%{$s}%")
                    ->orWhere('npm', 'ilike', "%{$s}%"));
            });
        }

        $requests = $query->paginate(10)->withQueryString();

        return view('kln.dokumen', compact('requests', 'total', 'pending', 'approved', 'rejected'));
    }



    /*
    |--------------------------------------------------------------------------
    | SHOW DETAIL (AJAX)
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $req = DB::table('reqDokumen')
            ->join('mahasiswa', 'reqDokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('reqDokumen.id', $id)
            ->select('reqDokumen.*', 'mahasiswa.nama as nama_mahasiswa', 'mahasiswa.npm')
            ->first();

        if (!$req) {
            return response()->json(['error' => 'Request tidak ditemukan.'], 404);
        }

        $file = DB::table('fileDetail')
            ->where('reqDokumen_id', $id)
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'id'         => $req->id,
            'mahasiswa'  => $req->nama_mahasiswa ?? '-',
            'npm'        => $req->npm ?? '-',
            'tipe'       => $req->tipeDkmn ?? '-',
            'status'     => $req->status ?? '-',
            'message'    => $req->message ?? '-',
            'keterangan' => $req->keterangan ?? null,
            'file'       => $file ? [
                'path'     => $file->path,
                'mimeType' => $file->mimeType,
                'fileSize' => $file->fileSize,
                'url'      => route('kln.dokumen.file', $req->id),
            ] : null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT REQUEST
    |--------------------------------------------------------------------------
    */

    public function rejectDokumen(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|string|max:1000',
        ]);

        $req = DB::table('reqDokumen')->where('id', $id)->first();
        if (!$req) {
            return response()->json(['success' => false, 'message' => 'Request tidak ditemukan.'], 404);
        }

        DB::table('reqDokumen')->where('id', $id)->update([
            'status'     => 'rejected',
            'keterangan' => $request->input('keterangan'),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE REQUEST
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $req = ReqDokumen::findOrFail($id);
        $req->delete();

        return response()->json(['success' => true]);
    }


    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD FILE (secure — lewat middleware auth)
    |--------------------------------------------------------------------------
    */

    public function downloadFile($id)
    {
        $file = DB::table('fileDetail')
            ->where('reqDokumen_id', $id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$file) {
            abort(404, 'File tidak ditemukan.');
        }

        $fileName = basename($file->path);

        // Coba disk public dulu, lalu local (private)
        foreach (['public', 'local'] as $disk) {
            if (Storage::disk($disk)->exists($file->path)) {
                return Storage::disk($disk)->download($file->path, $fileName);
            }
        }

        abort(404, 'File tidak ada di storage. Path: ' . $file->path);
    }


    /*
    |--------------------------------------------------------------------------
    | UPLOAD & APPROVE
    |--------------------------------------------------------------------------
    */

    public function uploadFile(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $req = DB::table('reqDokumen')->where('id', $id)->first();
        if (!$req) {
            return response()->json(['success' => false, 'message' => 'Request tidak ditemukan.'], 404);
        }

        $uploadedFile = $request->file('file');
        $newPath      = $uploadedFile->store('req_dokumen', 'local');

        // Jika sudah ada file sebelumnya, hapus dari storage dan update record
        $existing = DB::table('fileDetail')->where('reqDokumen_id', $id)->orderBy('created_at', 'desc')->first();

        if ($existing) {
            foreach (['local', 'public'] as $disk) {
                if (Storage::disk($disk)->exists($existing->path)) {
                    Storage::disk($disk)->delete($existing->path);
                    break;
                }
            }

            DB::table('fileDetail')->where('id', $existing->id)->update([
                'path'       => $newPath,
                'mimeType'   => $uploadedFile->getClientMimeType(),
                'fileSize'   => $uploadedFile->getSize(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('fileDetail')->insert([
                'reqDokumen_id' => $id,
                'path'          => $newPath,
                'mimeType'      => $uploadedFile->getClientMimeType(),
                'fileSize'      => $uploadedFile->getSize(),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        DB::table('reqDokumen')->where('id', $id)->update([
            'status'     => 'approved',
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'file'    => [
                'path'     => $newPath,
                'mimeType' => $uploadedFile->getClientMimeType(),
                'fileSize' => $uploadedFile->getSize(),
                'url'      => route('kln.dokumen.file', $id),
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | STUDENTS & LECTURERS PAGE
    |--------------------------------------------------------------------------
    */

    public function studentsPage(Request $request)
    {
        $totalMahasiswa = DB::table('mahasiswa')->count();
        $totalDosen     = DB::table('dosen')->count();
        $totalJurusan   = DB::table('users')
            ->join('mahasiswa', 'users.id', '=', 'mahasiswa.user_id')
            ->whereNotNull('users.jurusan_id')
            ->distinct()
            ->count('users.jurusan_id');

        $searchM   = $request->input('search_m', '');
        $jurusanM  = $request->input('jurusan_m', '');
        $dokStatus = $request->input('dok_status', '');

        $mhsQuery = DB::table('mahasiswa')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->leftJoin('jurusan', 'users.jurusan_id', '=', 'jurusan.id')
            ->leftJoin('dokumen', function ($join) {
                $join->on('dokumen.mahasiswa_id', '=', 'mahasiswa.id')
                     ->whereNull('dokumen.deleted_at')
                     ->whereNotNull('dokumen.tglKdlwrs');
            })
            ->select(
                'mahasiswa.id',
                'mahasiswa.nama',
                'mahasiswa.npm as identifier',
                'jurusan.namaJurusan',
                'users.jurusan_id',
                'users.status',
                DB::raw("MIN(CASE
                    WHEN dokumen.\"tglKdlwrs\" IS NULL THEN NULL
                    WHEN dokumen.\"tglKdlwrs\" < CURRENT_DATE THEN 1
                    WHEN dokumen.\"tglKdlwrs\" <= CURRENT_DATE + INTERVAL '30 days' THEN 2
                    ELSE 3
                END) as doc_expiry_level")
            )
            ->groupBy('mahasiswa.id', 'mahasiswa.nama', 'mahasiswa.npm',
                      'jurusan.namaJurusan', 'users.jurusan_id', 'users.status')
            ->orderBy('mahasiswa.nama');

        if ($searchM) {
            $mhsQuery->where(function ($q) use ($searchM) {
                $q->where('mahasiswa.nama', 'ilike', "%{$searchM}%")
                  ->orWhere('mahasiswa.npm',  'ilike', "%{$searchM}%");
            });
        }
        if ($jurusanM) {
            $mhsQuery->where('users.jurusan_id', $jurusanM);
        }

        $mahasiswaList = $mhsQuery->paginate(10, ['*'], 'page_m')->withQueryString();

        if ($dokStatus) {
            $caseExpr = 'MIN(CASE
                    WHEN dokumen."tglKdlwrs" IS NULL THEN NULL
                    WHEN dokumen."tglKdlwrs" < CURRENT_DATE THEN 1
                    WHEN dokumen."tglKdlwrs" <= CURRENT_DATE + INTERVAL \'30 days\' THEN 2
                    ELSE 3
                END)';
            if ($dokStatus === 'belum_ada') {
                $mahasiswaList = $mhsQuery->havingRaw("{$caseExpr} IS NULL")
                    ->paginate(10, ['*'], 'page_m')->withQueryString();
            } else {
                $levelMap = ['expired' => 1, 'warning' => 2, 'aman' => 3];
                $level = $levelMap[$dokStatus] ?? null;
                if ($level) {
                    $mahasiswaList = $mhsQuery->havingRaw("{$caseExpr} = ?", [$level])
                        ->paginate(10, ['*'], 'page_m')->withQueryString();
                }
            }
        }

        $searchD  = $request->input('search_d', '');
        $jurusanD = $request->input('jurusan_d', '');

        $dosQuery = DB::table('dosen')
            ->join('users', 'dosen.user_id', '=', 'users.id')
            ->leftJoin('jurusan', 'users.jurusan_id', '=', 'jurusan.id')
            ->select(
                'dosen.id',
                'dosen.nama',
                'dosen.nidn as identifier',
                'jurusan.namaJurusan',
                'users.jurusan_id',
                'users.status'
            )
            ->orderBy('dosen.nama');

        if ($searchD) {
            $dosQuery->where('dosen.nama', 'ilike', "%{$searchD}%");
        }
        if ($jurusanD) {
            $dosQuery->where('users.jurusan_id', $jurusanD);
        }

        $dosenList = $dosQuery->paginate(10, ['*'], 'page_d')->withQueryString();

        $jurusan = DB::table('jurusan')->orderBy('namaJurusan')->get();

        return view('kln.students.index', compact(
            'mahasiswaList', 'dosenList', 'jurusan',
            'totalMahasiswa', 'totalDosen', 'totalJurusan',
            'searchM', 'jurusanM', 'dokStatus', 'searchD', 'jurusanD'
        ));
    }

    public function mahasiswaDetail($id)
    {
        $mahasiswa = DB::table('mahasiswa')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->leftJoin('jurusan', 'users.jurusan_id', '=', 'jurusan.id')
            ->where('mahasiswa.id', $id)
            ->select(
                'mahasiswa.*',
                'users.email',
                'users.status',
                'jurusan.namaJurusan'
            )
            ->first();

        if (!$mahasiswa) abort(404);

        $dokumen = DB::table('dokumen')
            ->leftJoin('fileDetail', 'dokumen.id', '=', 'fileDetail.dokumen_id')
            ->where('dokumen.mahasiswa_id', $id)
            ->whereNull('dokumen.deleted_at')
            ->select(
                'dokumen.id',
                'dokumen.tipeDkmn',
                'dokumen.namaDkmn',
                'dokumen.penerbit',
                'dokumen.noDkmn',
                'dokumen.tglTerbit',
                'dokumen.tglKdlwrs',
                'dokumen.status',
                'dokumen.created_at',
                'fileDetail.id as fileDetail_id',
                'fileDetail.path as file_path',
                'fileDetail.mimeType',
                'fileDetail.fileSize'
            )
            ->orderBy('dokumen.created_at', 'desc')
            ->get();

        return view('kln.students.mahasiswa', compact('mahasiswa', 'dokumen'));
    }

    public function dosenDetail($id)
    {
        $dosen = DB::table('dosen')
            ->join('users', 'dosen.user_id', '=', 'users.id')
            ->leftJoin('jurusan', 'users.jurusan_id', '=', 'jurusan.id')
            ->where('dosen.id', $id)
            ->select(
                'dosen.*',
                'users.email',
                'users.status',
                'jurusan.namaJurusan'
            )
            ->first();

        if (!$dosen) abort(404);

        return view('kln.students.dosen', compact('dosen'));
    }

    public function downloadDokumen($mahasiswaId, $dokumenId)
    {
        $file = $this->getDokumenFile($mahasiswaId, $dokumenId);
        if (!$file) abort(404);

        $fileName = basename($file->path);
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($file->path)) {
                return Storage::disk($disk)->download($file->path, $fileName);
            }
        }
        abort(404, 'File tidak ada di storage.');
    }

    public function previewDokumen($mahasiswaId, $dokumenId)
    {
        $file = $this->getDokumenFile($mahasiswaId, $dokumenId);
        if (!$file) abort(404);

        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($file->path)) {
                return Storage::disk($disk)->response($file->path, basename($file->path), [
                    'Content-Disposition' => 'inline; filename="' . basename($file->path) . '"',
                ]);
            }
        }
        abort(404, 'File tidak ada di storage.');
    }

    public function updateDokumenStatus(Request $request, $mahasiswaId, $dokumenId)
    {
        $request->validate(['status' => 'required|in:pending,rejected,approved']);

        $exists = DB::table('dokumen')
            ->where('id', $dokumenId)
            ->where('mahasiswa_id', $mahasiswaId)
            ->whereNull('deleted_at')
            ->exists();

        if (!$exists) return response()->json(['success' => false, 'message' => 'Dokumen tidak ditemukan.'], 404);

        $dokumen = DB::table('dokumen')->where('id', $dokumenId)->first();

        DB::table('dokumen')->where('id', $dokumenId)->update([
            'status'     => $request->status,
            'updated_at' => now(),
        ]);

        ActivityLog::record(
            "Memperbarui status dokumen #{$dokumenId} menjadi {$request->status}",
            'dokumen',
            (int) $dokumenId
        );

        // Auto-trigger notifikasi ke mahasiswa
        if (in_array($request->status, ['approved', 'rejected'])) {
            $tipeDkm = $dokumen->tipeDkmn ?? 'Dokumen';
            if ($request->status === 'approved') {
                $subj = "Dokumen {$tipeDkm} Diverifikasi";
                $msg  = "Dokumen {$tipeDkm} Anda telah diverifikasi oleh KLN.";
            } else {
                $subj = "Dokumen {$tipeDkm} Ditolak";
                $msg  = "Dokumen {$tipeDkm} Anda ditolak oleh KLN. Silakan hubungi KLN untuk informasi lebih lanjut.";
            }
            try {
                $this->createNotificationForMahasiswa($subj, $msg, 'document', [(int) $mahasiswaId]);
            } catch (\Throwable) {}
        }

        return response()->json(['success' => true, 'status' => $request->status]);
    }

    private function getDokumenFile($mahasiswaId, $dokumenId)
    {
        return DB::table('fileDetail')
            ->join('dokumen', 'fileDetail.dokumen_id', '=', 'dokumen.id')
            ->where('dokumen.id', $dokumenId)
            ->where('dokumen.mahasiswa_id', $mahasiswaId)
            ->whereNull('dokumen.deleted_at')
            ->select('fileDetail.*')
            ->first();
    }


    /*
    |--------------------------------------------------------------------------
    | USERS PAGE
    |--------------------------------------------------------------------------
    */

    public function usersPage()
    {
        $jurusan = DB::table('jurusan')->orderBy('namaJurusan')->get();
        return view('kln.users.index', compact('jurusan'));
    }
    /*
    |--------------------------------------------------------------------------
    | GET USERS DATA
    |--------------------------------------------------------------------------
    */

    public function getUsers()
    {
        $email = request('email');
        $sort  = request('sort');

        $query = DB::table('users')->select('id', 'role', 'email', 'status', 'jurusan_id');

        if ($email) {
            $query->where('email', 'like', "%{$email}%");
        }

        if ($sort) {
            [$field, $dir] = explode('_', $sort, 2);
            $allowed = ['id', 'email', 'role', 'status'];
            if (in_array($field, $allowed)) {
                $query->orderBy($field, $dir === 'desc' ? 'desc' : 'asc');
            }
        } else {
            $query->orderBy('id', 'asc');
        }

        $paginated = $query->paginate(10);

        return response()->json([
            'success'    => true,
            'data'       => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE USER
    |--------------------------------------------------------------------------
    */

    public function storeUser(Request $request)
    {
        return response()->json(['success' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW USER
    |--------------------------------------------------------------------------
    */

    public function showUser($id)
    {
        $user = \App\Models\User::findOrFail($id);

        return response()->json($user);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE USER
    |--------------------------------------------------------------------------
    */

    public function updateUser(Request $request, $id)
    {
        $rules = [
            'email'  => 'required|email|unique:users,email,' . $id,
            'role'   => 'required|in:kln,jurusan,dosen,mahasiswa,bipa',
            'status' => 'required|in:active,inactive',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6';
        }

        $request->validate($rules);

        $update = [
            'email'      => $request->email,
            'role'       => $request->role,
            'status'     => $request->status,
            'jurusan_id' => $request->jurusan_id ?: null,
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $update['password'] = bcrypt($request->password);
        }

        $userBefore = DB::table('users')->where('id', $id)->first();
        DB::table('users')->where('id', $id)->update($update);

        ActivityLog::record("Memperbarui user #{$id} ({$request->email})", 'users', (int) $id);

        // Auto-trigger: akun mahasiswa baru diaktifkan
        if ($request->status === 'active'
            && ($userBefore->status ?? '') !== 'active'
            && $request->role === 'mahasiswa'
        ) {
            $mhs = DB::table('mahasiswa')->where('user_id', $id)->first();
            if ($mhs) {
                try {
                    $this->createNotificationForMahasiswa(
                        'Akun Anda Telah Diaktifkan',
                        'Selamat! Akun SIMA Anda telah diaktifkan. Anda sekarang dapat mengakses semua fitur.',
                        'account',
                        [$mhs->id]
                    );
                } catch (\Throwable) {}
            }
        }

        return response()->json([
            'success' => true,
            'data'    => DB::table('users')->select('id', 'role', 'email', 'status', 'jurusan_id')->get(),
            'flash'   => ['type' => 'success', 'message' => 'User berhasil diperbarui.'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE USER
    |--------------------------------------------------------------------------
    */

    public function destroyUser($id)
    {
        return response()->json(['success' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS MAHASISWA
    |--------------------------------------------------------------------------
    */

    public function updateStatusMahasiswa($id)
    {
        return response()->json(['success' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | Announcemet PAGE
    |--------------------------------------------------------------------------
    */

    public function announcementPage()
    {
        $totalAll     = DB::table('announcement')->where('sumber', 'kln')->count();
        $totalActive  = DB::table('announcement')->where('sumber', 'kln')->where('status', 'active')->count();
        $totalDraft   = DB::table('announcement')->where('sumber', 'kln')->where('status', 'draft')->count();
        $totalPenting = DB::table('announcement')->where('sumber', 'kln')->where('is_penting', true)->count();

        $announcements = DB::table('announcement')
            ->where('sumber', 'kln')
            ->selectRaw("announcement.*, (SELECT COUNT(*) FROM announcement_files WHERE announcement_files.announcement_id = announcement.id) as file_count")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('kln.announcement', compact(
            'announcements', 'totalAll', 'totalActive', 'totalDraft', 'totalPenting'
        ));
    }

    public function createAnnouncementPage()
    {
        return view('kln.announcement.create');
    }

    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'subject'    => 'required|string|max:255',
            'message'    => 'required|string',
            'status'     => 'required|in:draft,active,inactive',
            'is_penting' => 'nullable|boolean',
            'files.*'    => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx|max:5120',
        ]);

        $id = DB::table('announcement')->insertGetId([
            'user_id'    => Auth::id(),
            'subject'    => $request->subject,
            'message'    => $request->message,
            'sumber'     => 'kln',
            'status'     => $request->status,
            'is_penting' => $request->boolean('is_penting'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store("announcements/{$id}", 'local');
                DB::table('announcement_files')->insert([
                    'announcement_id' => $id,
                    'path'            => $path,
                    'originalName'    => $file->getClientOriginalName(),
                    'mimeType'        => $file->getMimeType(),
                    'size'            => $file->getSize(),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }

        ActivityLog::record("Membuat pengumuman: {$request->subject}", 'announcement', $id);

        // Auto-trigger: pengumuman langsung aktif → notifikasi ke semua mahasiswa
        if ($request->status === 'active') {
            try {
                $this->createNotificationForMahasiswa(
                    'Pengumuman: ' . $request->subject,
                    \Illuminate\Support\Str::limit(strip_tags($request->message), 200),
                    'announcement',
                    []
                );
            } catch (\Throwable) {}
        }

        return redirect()->route('kln.announcement')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function editAnnouncementPage(int $id)
    {
        $ann = DB::table('announcement')->where('id', $id)->first();
        if (!$ann) abort(404);

        $files = DB::table('announcement_files')
            ->where('announcement_id', $id)
            ->get();

        return view('kln.announcement.edit', compact('ann', 'files'));
    }

    public function updateAnnouncement(Request $request, int $id)
    {
        $request->validate([
            'subject'    => 'required|string|max:255',
            'message'    => 'required|string',
            'status'     => 'required|in:draft,active,inactive',
            'is_penting' => 'nullable|boolean',
            'files.*'    => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx|max:5120',
        ]);

        DB::table('announcement')->where('id', $id)->update([
            'subject'    => $request->subject,
            'message'    => $request->message,
            'status'     => $request->status,
            'is_penting' => $request->boolean('is_penting'),
            'updated_at' => now(),
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store("announcements/{$id}", 'local');
                DB::table('announcement_files')->insert([
                    'announcement_id' => $id,
                    'path'            => $path,
                    'originalName'    => $file->getClientOriginalName(),
                    'mimeType'        => $file->getMimeType(),
                    'size'            => $file->getSize(),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }

        ActivityLog::record("Memperbarui pengumuman: {$request->subject}", 'announcement', $id);

        return redirect()->route('kln.announcement')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroyAnnouncement(int $id)
    {
        $files = DB::table('announcement_files')->where('announcement_id', $id)->get();
        foreach ($files as $file) {
            Storage::disk('local')->delete($file->path);
        }
        DB::table('announcement_files')->where('announcement_id', $id)->delete();
        DB::table('announcement')->where('id', $id)->delete();

        ActivityLog::record("Menghapus pengumuman #{$id}", 'announcement', $id);

        return response()->json([
            'success' => true,
            'flash'   => ['type' => 'success', 'message' => 'Pengumuman berhasil dihapus.'],
        ]);
    }

    public function serveAnnouncementFile(int $annId, int $fileId)
    {
        $file = DB::table('announcement_files')
            ->where('id', $fileId)
            ->where('announcement_id', $annId)
            ->first();

        if (!$file || !Storage::disk('local')->exists($file->path)) abort(404);

        return Storage::disk('local')->response($file->path, $file->originalName);
    }

    public function destroyAnnouncementFile(int $fileId)
    {
        $file = DB::table('announcement_files')->where('id', $fileId)->first();
        if (!$file) abort(404);

        Storage::disk('local')->delete($file->path);
        DB::table('announcement_files')->where('id', $fileId)->delete();

        return response()->json(['success' => true]);
    }


    /*
    |--------------------------------------------------------------------------
    | ATTENDANCE — LEVEL 1 (ringkasan per mahasiswa)
    |--------------------------------------------------------------------------
    */
    public function attendancePage(Request $request)
    {
        $search = $request->input('q', '');

        $query = DB::table('mahasiswa')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->leftJoin('jadwal_mahasiswa', 'jadwal_mahasiswa.mahasiswa_id', '=', 'mahasiswa.id')
            ->select(
                'mahasiswa.id',
                'mahasiswa.nama',
                'mahasiswa.npm',
                'users.status as user_status',
                DB::raw("COUNT(DISTINCT jadwal_mahasiswa.jadwal_id) as total_jadwal"),
                DB::raw("SUM(CASE WHEN jadwal_mahasiswa.status = 'present'     THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN jadwal_mahasiswa.status = 'absent'      THEN 1 ELSE 0 END) as absen"),
                DB::raw("SUM(CASE WHEN jadwal_mahasiswa.status = 'excused'     THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN jadwal_mahasiswa.status = 'belum hadir' THEN 1 ELSE 0 END) as belum")
            )
            ->groupBy('mahasiswa.id', 'mahasiswa.nama', 'mahasiswa.npm', 'users.status')
            ->orderBy('mahasiswa.nama');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('mahasiswa.nama', 'ilike', "%{$search}%")
                  ->orWhere('mahasiswa.npm',  'ilike', "%{$search}%");
            });
        }

        // hitung stats dari full data (tanpa paginate)
        $allRows = $query->get()->map(function ($row) {
            $berlangsung = $row->hadir + $row->absen + $row->izin;
            $row->pct    = $berlangsung > 0 ? round($row->hadir / $berlangsung * 100, 1) : null;
            return $row;
        });
        $stats = [
            'total'   => $allRows->count(),
            'below75' => $allRows->filter(fn ($r) => $r->pct !== null && $r->pct < 75)->count(),
            'noData'  => $allRows->filter(fn ($r) => $r->pct === null)->count(),
        ];

        // paginate untuk display
        $attendanceList = $query->paginate(10)->withQueryString();
        $attendanceList->through(function ($row) {
            $berlangsung = $row->hadir + $row->absen + $row->izin;
            $row->pct    = $berlangsung > 0 ? round($row->hadir / $berlangsung * 100, 1) : null;
            return $row;
        });

        return view('kln.attendance', compact('attendanceList', 'stats', 'search'));
    }

    /*
    |--------------------------------------------------------------------------
    | ATTENDANCE — LEVEL 2 (detail per mahasiswa)
    |--------------------------------------------------------------------------
    */
    public function attendanceDetail(int $id)
    {
        $mahasiswa = DB::table('mahasiswa')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->leftJoin('jurusan', 'users.jurusan_id', '=', 'jurusan.id')
            ->where('mahasiswa.id', $id)
            ->select('mahasiswa.*', 'users.email', 'users.status as user_status', 'jurusan.namaJurusan')
            ->first();

        if (!$mahasiswa) abort(404);

        $jadwalList = DB::table('jadwal_mahasiswa')
            ->join('jadwal',     'jadwal.id',     '=', 'jadwal_mahasiswa.jadwal_id')
            ->join('matakuliah', 'matakuliah.id', '=', 'jadwal.matakuliah_id')
            ->join('kelas',      'kelas.id',      '=', 'jadwal.kelas_id')
            ->leftJoin('dosen',  'dosen.id',      '=', 'jadwal.dosen_id')
            ->where('jadwal_mahasiswa.mahasiswa_id', $id)
            ->select(
                'jadwal.id as jadwal_id',
                'jadwal.hari',
                'jadwal.jam',
                'jadwal.ruangan',
                'jadwal.totalSesi',
                'jadwal.tahunAjar',
                'matakuliah.namaMk',
                'matakuliah.kodeMk',
                'kelas.kodeKelas',
                'dosen.nama as namaDosen',
                DB::raw("SUM(CASE WHEN jadwal_mahasiswa.status = 'present'     THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN jadwal_mahasiswa.status = 'absent'      THEN 1 ELSE 0 END) as absen"),
                DB::raw("SUM(CASE WHEN jadwal_mahasiswa.status = 'excused'     THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN jadwal_mahasiswa.status = 'belum hadir' THEN 1 ELSE 0 END) as belum")
            )
            ->groupBy(
                'jadwal.id', 'jadwal.hari', 'jadwal.jam', 'jadwal.ruangan',
                'jadwal.totalSesi', 'jadwal.tahunAjar',
                'matakuliah.namaMk', 'matakuliah.kodeMk',
                'kelas.kodeKelas', 'dosen.nama'
            )
            ->orderBy('matakuliah.namaMk')
            ->get()
            ->map(function ($j) {
                $berlangsung = $j->hadir + $j->absen + $j->izin;
                $j->pct      = $berlangsung > 0 ? round($j->hadir / $berlangsung * 100, 1) : null;
                return $j;
            });

        // Per-sesi records grouped by jadwal_id for accordion
        $sesiByJadwal = DB::table('jadwal_mahasiswa')
            ->where('mahasiswa_id', $id)
            ->select('jadwal_id', 'sesi', 'status', 'tglSesi')
            ->orderBy('jadwal_id')
            ->orderBy('sesi')
            ->get()
            ->groupBy('jadwal_id');

        $totalHadir       = $jadwalList->sum('hadir');
        $totalAbsen       = $jadwalList->sum('absen');
        $totalIzin        = $jadwalList->sum('izin');
        $totalBelum       = $jadwalList->sum('belum');
        $totalBerlangsung = $totalHadir + $totalAbsen + $totalIzin;
        $totalPct         = $totalBerlangsung > 0
            ? round($totalHadir / $totalBerlangsung * 100, 1)
            : null;

        return view('kln.attendance.detail', compact(
            'mahasiswa', 'jadwalList', 'sesiByJadwal',
            'totalHadir', 'totalAbsen', 'totalIzin', 'totalBelum', 'totalPct'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | NOTIFICATION — HELPER
    | Creates one notification record and distributes to mahasiswa target list.
    |--------------------------------------------------------------------------
    */
    private function createNotificationForMahasiswa(
        string $subject,
        string $message,
        string $type,
        array $mahasiswaIds   // empty = all mahasiswa
    ): void {
        $notifId = DB::table('notification')->insertGetId([
            'subject'    => $subject,
            'message'    => $message,
            'status'     => 'active',
            'type'       => $type,
            'sender_id'  => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (empty($mahasiswaIds)) {
            $mahasiswaIds = DB::table('mahasiswa')->pluck('id')->toArray();
        }

        $rows = array_map(fn ($mId) => [
            'notification_id' => $notifId,
            'mahasiswa_id'    => $mId,
            'is_read'         => false,
            'created_at'      => now(),
            'updated_at'      => now(),
        ], $mahasiswaIds);

        DB::table('notification_mahasiswa')->insert($rows);
    }

    /*
    |--------------------------------------------------------------------------
    | BROADCAST PAGE — KLN kirim notifikasi ke mahasiswa
    |--------------------------------------------------------------------------
    */
    public function broadcastPage()
    {
        $mahasiswaList = DB::table('mahasiswa')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->leftJoin('jurusan', 'users.jurusan_id', '=', 'jurusan.id')
            ->where('users.status', 'active')
            ->select(
                'mahasiswa.id',
                'mahasiswa.nama',
                'mahasiswa.npm',
                'users.jurusan_id',
                'jurusan.namaJurusan'
            )
            ->orderBy('mahasiswa.nama')
            ->get();

        // Map mahasiswa_id → kelas_ids (comma-separated) untuk JS filter
        $mahasiswaKelasMap = DB::table('mahasiswa_kelas')
            ->select('mahasiswa_id', 'kelas_id')
            ->get()
            ->groupBy('mahasiswa_id')
            ->map(fn($items) => $items->pluck('kelas_id')->join(','));

        $jurusanList = DB::table('jurusan')->orderBy('namaJurusan')->get();

        $kelasList = DB::table('kelas')
            ->join('mahasiswa_kelas', 'kelas.id', '=', 'mahasiswa_kelas.kelas_id')
            ->select('kelas.id', 'kelas.kodeKelas', 'kelas.tahunAjar')
            ->distinct()
            ->orderBy('kelas.kodeKelas')
            ->get();

        $riwayat = DB::table('notification')
            ->select(
                'notification.*',
                DB::raw('(SELECT COUNT(*) FROM notification_mahasiswa WHERE notification_mahasiswa.notification_id = notification.id) as total_penerima'),
                DB::raw('(SELECT COUNT(*) FROM notification_mahasiswa WHERE notification_mahasiswa.notification_id = notification.id AND is_read = true) as total_dibaca')
            )
            ->orderBy('notification.created_at', 'desc')
            ->paginate(10);

        return view('kln.broadcast', compact(
            'mahasiswaList', 'mahasiswaKelasMap', 'jurusanList', 'kelasList', 'riwayat'
        ));
    }

    public function storeBroadcast(Request $request)
    {
        $request->validate([
            'subject'       => 'required|string|max:255',
            'message'       => 'required|string',
            'target'        => 'required|in:all,selected',
            'mahasiswa_ids' => 'required_if:target,selected|array',
            'mahasiswa_ids.*' => 'integer|exists:mahasiswa,id',
        ]);

        $ids = $request->target === 'all' ? [] : $request->mahasiswa_ids;
        $this->createNotificationForMahasiswa(
            $request->subject,
            $request->message,
            'broadcast',
            $ids
        );

        ActivityLog::record('Mengirim broadcast notifikasi: ' . $request->subject, 'notification', null);

        return redirect()->route('kln.broadcast')->with('success', 'Notifikasi berhasil dikirim.');
    }

    /*
    |--------------------------------------------------------------------------
    | NOTIFIKASI — KLN ALERT BOARD (Opsi B: real-time monitoring)
    |--------------------------------------------------------------------------
    */
    public function notifikasiPage(Request $request)
    {
        // Dokumen sudah kadaluwarsa
        $expiredDokumen = DB::table('dokumen')
            ->join('mahasiswa', 'dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->join('users',     'mahasiswa.user_id',    '=', 'users.id')
            ->leftJoin('jurusan', 'users.jurusan_id',   '=', 'jurusan.id')
            ->whereNull('dokumen.deleted_at')
            ->whereNotNull('dokumen.tglKdlwrs')
            ->whereRaw("dokumen.\"tglKdlwrs\" < CURRENT_DATE")
            ->select(
                'dokumen.id as dokumen_id',
                'dokumen.tipeDkmn',
                'dokumen.namaDkmn',
                'dokumen.tglKdlwrs',
                'mahasiswa.id as mahasiswa_id',
                'mahasiswa.nama',
                'mahasiswa.npm',
                'jurusan.namaJurusan'
            )
            ->orderBy('dokumen.tglKdlwrs')
            ->paginate(10, ['*'], 'page_exp')->withQueryString();

        // Dokumen hampir kadaluwarsa (≤ 30 hari ke depan)
        $nearExpiredDokumen = DB::table('dokumen')
            ->join('mahasiswa', 'dokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->join('users',     'mahasiswa.user_id',    '=', 'users.id')
            ->leftJoin('jurusan', 'users.jurusan_id',   '=', 'jurusan.id')
            ->whereNull('dokumen.deleted_at')
            ->whereNotNull('dokumen.tglKdlwrs')
            ->whereRaw("dokumen.\"tglKdlwrs\" >= CURRENT_DATE AND dokumen.\"tglKdlwrs\" <= CURRENT_DATE + INTERVAL '30 days'")
            ->select(
                'dokumen.id as dokumen_id',
                'dokumen.tipeDkmn',
                'dokumen.namaDkmn',
                'dokumen.tglKdlwrs',
                'mahasiswa.id as mahasiswa_id',
                'mahasiswa.nama',
                'mahasiswa.npm',
                'jurusan.namaJurusan'
            )
            ->orderBy('dokumen.tglKdlwrs')
            ->paginate(10, ['*'], 'page_near')->withQueryString();

        $nearExpiredDokumen->through(function ($row) {
            $row->sisa_hari = now()->diffInDays(\Carbon\Carbon::parse($row->tglKdlwrs), false);
            return $row;
        });

        // Akun mahasiswa nonaktif
        $inactiveMahasiswa = DB::table('mahasiswa')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->leftJoin('jurusan', 'users.jurusan_id', '=', 'jurusan.id')
            ->where('users.status', 'inactive')
            ->where('users.role', 'mahasiswa')
            ->select(
                'mahasiswa.id',
                'mahasiswa.nama',
                'mahasiswa.npm',
                'users.email',
                'users.id as user_id',
                'jurusan.namaJurusan'
            )
            ->orderBy('mahasiswa.nama')
            ->paginate(10, ['*'], 'page_inact')->withQueryString();

        // Request dokumen pending
        $pendingRequests = DB::table('reqDokumen')
            ->join('mahasiswa', 'reqDokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->join('users',     'mahasiswa.user_id',       '=', 'users.id')
            ->leftJoin('jurusan', 'users.jurusan_id',      '=', 'jurusan.id')
            ->where('reqDokumen.status', 'pending')
            ->select(
                'reqDokumen.id',
                'reqDokumen.tipeDkmn',
                'reqDokumen.created_at',
                'mahasiswa.id as mahasiswa_id',
                'mahasiswa.nama',
                'mahasiswa.npm',
                'jurusan.namaJurusan'
            )
            ->orderBy('reqDokumen.created_at')
            ->paginate(10, ['*'], 'page_req')->withQueryString();

        $stats = [
            'expired'     => $expiredDokumen->total(),
            'nearExpired' => $nearExpiredDokumen->total(),
            'inactive'    => $inactiveMahasiswa->total(),
            'pending'     => $pendingRequests->total(),
        ];

        return view('kln.notifikasi', compact(
            'expiredDokumen', 'nearExpiredDokumen', 'inactiveMahasiswa',
            'pendingRequests', 'stats'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | JADWAL — BIPA PAGE (view-only)
    |--------------------------------------------------------------------------
    */
    public function jadwalBipa(Request $request)
    {
        $bipaJurusanIds = DB::table('users')
            ->where('role', 'bipa')
            ->whereNotNull('jurusan_id')
            ->pluck('jurusan_id')->unique()->values();

        $jadwalList = $bipaJurusanIds->isNotEmpty()
            ? $this->getJadwalByJurusanIds($bipaJurusanIds->toArray(), $request)
            : $this->emptyPaginator();

        return view('kln.jadwal.bipa', compact('jadwalList'));
    }

    /*
    |--------------------------------------------------------------------------
    | JADWAL — LECTURERS PAGE (view-only)
    |--------------------------------------------------------------------------
    */
    public function jadwalLecturers(Request $request)
    {
        $jurusanJurusanIds = DB::table('users')
            ->where('role', 'jurusan')
            ->whereNotNull('jurusan_id')
            ->pluck('jurusan_id')->unique()->values();

        $jadwalList = $jurusanJurusanIds->isNotEmpty()
            ? $this->getJadwalByJurusanIds($jurusanJurusanIds->toArray(), $request)
            : $this->emptyPaginator();

        return view('kln.jadwal.lecturers', compact('jadwalList'));
    }

    /*
    |--------------------------------------------------------------------------
    | JADWAL — KLN PAGE (view + tambah)
    |--------------------------------------------------------------------------
    */
    public function jadwalKln(Request $request)
    {
        $klnJurusanIds = DB::table('users')
            ->where('role', 'kln')
            ->whereNotNull('jurusan_id')
            ->pluck('jurusan_id')->unique()->values();

        $jadwalList = $klnJurusanIds->isNotEmpty()
            ? $this->getJadwalByJurusanIds($klnJurusanIds->toArray(), $request)
            : $this->emptyPaginator();

        $kelas = DB::table('kelas')
            ->select('id', 'kodeKelas', 'tahunAjar')
            ->orderBy('kodeKelas')
            ->get();

        return view('kln.jadwal.kln', compact('jadwalList', 'kelas'));
    }

    /*
    |--------------------------------------------------------------------------
    | JADWAL — DETAIL PAGE (shared for BIPA / Lecturers / KLN)
    |--------------------------------------------------------------------------
    */
    public function jadwalDetail($id)
    {
        $jadwal = DB::table('jadwal')
            ->join('matakuliah',  'jadwal.matakuliah_id', '=', 'matakuliah.id')
            ->join('kelas',       'jadwal.kelas_id',      '=', 'kelas.id')
            ->leftJoin('dosen',   'jadwal.dosen_id',      '=', 'dosen.id')
            ->leftJoin('jurusan', 'matakuliah.jurusan_id', '=', 'jurusan.id')
            ->where('jadwal.id', $id)
            ->select(
                'jadwal.id',
                'jadwal.hari',
                'jadwal.jam',
                'jadwal.ruangan',
                'jadwal.totalSesi',
                'jadwal.tahunAjar',
                'matakuliah.namaMk',
                'matakuliah.kodeMk',
                'matakuliah.sks',
                'kelas.kodeKelas',
                'dosen.nama as namaDosen',
                'dosen.nidn',
                'jurusan.namaJurusan'
            )
            ->first();

        if (!$jadwal) abort(404);

        $kehadiran = DB::table('jadwal_mahasiswa')
            ->join('mahasiswa', 'jadwal_mahasiswa.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('jadwal_mahasiswa.jadwal_id', $id)
            ->select(
                'mahasiswa.id as mahasiswa_id',
                'mahasiswa.nama',
                'mahasiswa.npm',
                DB::raw("SUM(CASE WHEN jadwal_mahasiswa.status = 'present'     THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN jadwal_mahasiswa.status = 'absent'      THEN 1 ELSE 0 END) as absen"),
                DB::raw("SUM(CASE WHEN jadwal_mahasiswa.status = 'excused'     THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN jadwal_mahasiswa.status = 'belum hadir' THEN 1 ELSE 0 END) as belum_hadir")
            )
            ->groupBy('mahasiswa.id', 'mahasiswa.nama', 'mahasiswa.npm')
            ->orderBy('mahasiswa.nama')
            ->get();

        return view('kln.jadwal.detail', compact('jadwal', 'kehadiran'));
    }

    /*
    |--------------------------------------------------------------------------
    | JADWAL — SEARCH KEGIATAN (autocomplete for KLN form)
    |--------------------------------------------------------------------------
    */
    public function searchKegiatan(\Illuminate\Http\Request $request)
    {
        $q = $request->input('q', '');

        $results = DB::table('matakuliah')
            ->where('jurusan_id', 1)
            ->where(function ($query) use ($q) {
                $query->where('namaMk', 'ilike', "%{$q}%")
                      ->orWhere('kodeMk', 'ilike', "%{$q}%");
            })
            ->select('id', 'namaMk', 'kodeMk')
            ->orderBy('namaMk')
            ->limit(10)
            ->get();

        return response()->json($results);
    }

    /*
    |--------------------------------------------------------------------------
    | JADWAL — STORE (KLN only)
    |--------------------------------------------------------------------------
    */
    public function jadwalStore(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'kelas_id'      => 'required|integer|exists:kelas,id',
            'matakuliah_id' => 'nullable|integer|exists:matakuliah,id',
            'kegiatan_nama' => 'nullable|string|max:100',
            'hari'          => 'required|string|max:10',
            'jam'           => 'required|string|max:25',
            'ruangan'       => 'required|string|max:100',
            'totalSesi'     => 'required|integer|min:1',
            'tahunAjar'     => 'nullable|string|max:9',
        ]);

        if ($request->filled('matakuliah_id')) {
            $matakuliahId = (int) $request->matakuliah_id;
        } elseif ($request->filled('kegiatan_nama')) {
            do {
                $kodeMk = 'KLN-' . rand(1000, 9999);
            } while (DB::table('matakuliah')->where('kodeMk', $kodeMk)->exists());

            $matakuliahId = DB::table('matakuliah')->insertGetId([
                'namaMk'     => trim($request->kegiatan_nama),
                'kodeMk'     => $kodeMk,
                'sks'        => 0,
                'keterangan' => 'kegiatan',
                'jurusan_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'Pilih atau buat kegiatan terlebih dahulu.'], 422);
        }

        $jadwalId = DB::table('jadwal')->insertGetId([
            'kelas_id'      => $request->kelas_id,
            'matakuliah_id' => $matakuliahId,
            'dosen_id'      => null,
            'hari'          => $request->hari,
            'jam'           => $request->jam,
            'ruangan'       => $request->ruangan,
            'totalSesi'     => $request->totalSesi,
            'tahunAjar'     => $request->tahunAjar,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $mkNama = DB::table('matakuliah')->where('id', $matakuliahId)->value('namaMk');
        ActivityLog::record("Membuat jadwal: {$mkNama}", 'jadwal', $jadwalId);

        return response()->json([
            'success' => true,
            'id'      => $jadwalId,
            'flash'   => ['type' => 'success', 'message' => 'Jadwal berhasil dibuat.'],
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | JADWAL — PRIVATE HELPER
    |--------------------------------------------------------------------------
    */
    private function getJadwalByJurusanIds(array $jurusanIds, Request $request)
    {
        $query = DB::table('jadwal')
            ->join('matakuliah',  'jadwal.matakuliah_id', '=', 'matakuliah.id')
            ->join('kelas',       'jadwal.kelas_id',      '=', 'kelas.id')
            ->leftJoin('dosen',   'jadwal.dosen_id',      '=', 'dosen.id')
            ->leftJoin('jurusan', 'matakuliah.jurusan_id', '=', 'jurusan.id')
            ->whereIn('matakuliah.jurusan_id', $jurusanIds)
            ->select(
                'jadwal.id',
                'jadwal.hari',
                'jadwal.jam',
                'jadwal.ruangan',
                'jadwal.totalSesi',
                'jadwal.tahunAjar',
                'matakuliah.namaMk',
                'matakuliah.kodeMk',
                'kelas.kodeKelas',
                'dosen.nama as namaDosen',
                'jurusan.namaJurusan'
            )
            ->orderByRaw("CASE jadwal.hari
                WHEN 'Senin'  THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu'   THEN 3
                WHEN 'Kamis'  THEN 4 WHEN 'Jumat'  THEN 5 WHEN 'Sabtu'  THEN 6
                ELSE 7 END")
            ->orderBy('jadwal.jam');

        if ($request->filled('hari')) {
            $query->where('jadwal.hari', $request->input('hari'));
        }
        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('matakuliah.namaMk', 'ilike', "%{$s}%")
                  ->orWhere('dosen.nama',       'ilike', "%{$s}%");
            });
        }

        return $query->paginate(10)->withQueryString();
    }

    private function emptyPaginator()
    {
        return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
    }

}