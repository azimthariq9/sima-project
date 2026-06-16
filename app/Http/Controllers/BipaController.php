<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BipaController extends Controller
{
    /*
    |==========================================================================
    | HELPER PRIVATE
    |==========================================================================
    */

    private function unreadNotif(): int
    {
        return DB::table('notification_users')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
    }

    private function kirimNotifMahasiswa(int $userId, string $judul, string $pesan): void
    {
        $notifId = DB::table('notification')->insertGetId([
            'subject'    => $judul,
            'message'    => $pesan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('notification_users')->insert([
            'notification_id' => $notifId,
            'user_id'         => $userId,
            'is_read'         => false,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }


    /*
    |==========================================================================
    | DASHBOARD
    | Route: GET bipa/dashboard -> bipa.dashboard
    |==========================================================================
    */

    public function dashboard()
    {
        $unreadNotifCount = $this->unreadNotif();
        return view('bipa.dashboard', compact('unreadNotifCount'));
    }


    /*
    |==========================================================================
    | PROFIL
    | Route: GET bipa/profil -> bipa.profil
    |==========================================================================
    */

    public function profil()
    {
        $user = Auth::user();
        $unreadNotifCount = $this->unreadNotif();

        return view('bipa.profil', compact('user', 'unreadNotifCount'));
    }


    /*
    |==========================================================================
    | NOTIFIKASI
    | Route: GET bipa/notifikasi -> bipa.notifikasi
    |==========================================================================
    */

    public function notifikasi()
    {
        DB::table('notification_users')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'updated_at' => now()]);

        $notifikasi = DB::table('notification_users')
            ->join('notification', 'notification_users.notification_id', '=', 'notification.id')
            ->where('notification_users.user_id', Auth::id())
            ->select('notification.*', 'notification_users.is_read', 'notification_users.created_at as received_at')
            ->orderBy('notification_users.created_at', 'desc')
            ->paginate(15);

        return view('bipa.notifikasi', compact('notifikasi'));
    }


    /*
    |==========================================================================
    | JADWAL
    | Route: GET bipa/jadwal -> bipa.jadwal
    |==========================================================================
    */

    public function jadwal()
    {
        $unreadNotifCount = $this->unreadNotif();
        return view('bipa.jadwal', compact('unreadNotifCount'));
    }


    /*
    |==========================================================================
    | ANALYTICS
    | Route: GET bipa/analytics -> bipa.analytics
    |==========================================================================
    */

    public function analytics()
    {
        $unreadNotifCount = $this->unreadNotif();
        return view('bipa.analytics', compact('unreadNotifCount'));
    }


    /*
    |==========================================================================
    | ANNOUNCEMENT
    | Route: GET bipa/announcement -> bipa.announcement
    |==========================================================================
    */

    public function announcement()
    {
        $announcements = DB::table('announcement')
            ->where('sumber', 'bipa')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $unreadNotifCount = $this->unreadNotif();

        return view('bipa.announcement', compact('announcements', 'unreadNotifCount'));
    }


    /*
    |==========================================================================
    | DOKUMEN
    | Tabel: "fileDetail" (path, mimeType, fileSize, reqDokumen_id)
    |==========================================================================
    */

    public function dokumen(Request $request)
    {
        // View belum dibuat — redirect sementara
        return redirect()->route('bipa.dashboard')
            ->with('info', 'Halaman Dokumen sedang dalam pengembangan.');
    }

    public function showDokumen(int $id)
    {
        $file = DB::table('fileDetail')
            ->join('reqDokumen', 'fileDetail.reqDokumen_id', '=', 'reqDokumen.id')
            ->join('mahasiswa', 'reqDokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('fileDetail.id', $id)
            ->select(
                'fileDetail.*',
                'reqDokumen.tipeDkmn',
                'reqDokumen.namaDkmn',
                'mahasiswa.nama as nama_mahasiswa',
                'mahasiswa.npm'
            )
            ->first();

        if (!$file) {
            return response()->json(['error' => 'File tidak ditemukan.'], 404);
        }

        return response()->json($file);
    }

    public function uploadDokumen(Request $request, int $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $req = DB::table('reqDokumen')
            ->join('mahasiswa', 'reqDokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->where('reqDokumen.id', $id)
            ->select('reqDokumen.*', 'mahasiswa.nama as nama_mahasiswa', 'users.id as user_id')
            ->first();

        if (!$req) {
            return back()->with('error', 'Request dokumen tidak ditemukan.');
        }

        $file     = $request->file('file');
        $path     = $file->store('dokumen/bipa/' . $req->mahasiswa_id, 'public');

        DB::table('fileDetail')->insert([
            'reqDokumen_id' => $id,
            'path'          => $path,
            'mimeType'      => $file->getClientMimeType(),
            'fileSize'      => $file->getSize(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('reqDokumen')
            ->where('id', $id)
            ->update(['status' => 'approved', 'updated_at' => now()]);

        $this->kirimNotifMahasiswa(
            $req->user_id,
            'Dokumen BIPA Siap',
            "Dokumen '{$req->namaDkmn}' dari BIPA sudah siap. Silakan unduh di menu Dokumen."
        );

        return back()->with('success', 'Dokumen berhasil diupload dan mahasiswa telah diberitahu.');
    }


    /*
    |==========================================================================
    | REQUEST DOKUMEN
    | Tabel: "reqDokumen" (tipeDkmn, namaDkmn, status, message)
    |==========================================================================
    */

    public function indexReqDocument(Request $request)
    {
        // View belum dibuat — redirect sementara
        return redirect()->route('bipa.dashboard')
            ->with('info', 'Halaman Request Dokumen sedang dalam pengembangan.');
    }

    public function showReqDocument(int $id)
    {
        $req = DB::table('reqDokumen')
            ->join('mahasiswa', 'reqDokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->where('reqDokumen.id', $id)
            ->select('reqDokumen.*', 'mahasiswa.nama as nama_mahasiswa', 'mahasiswa.npm')
            ->first();

        if (!$req) {
            return response()->json(['error' => 'Request tidak ditemukan.'], 404);
        }

        return response()->json($req);
    }

    public function updateReqDokumen(Request $request, int $id)
    {
        $request->validate([
            'status'  => 'required|in:pending,approved,rejected',
            'message' => 'nullable|string|max:500',
        ]);

        $req = DB::table('reqDokumen')
            ->join('mahasiswa', 'reqDokumen.mahasiswa_id', '=', 'mahasiswa.id')
            ->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->where('reqDokumen.id', $id)
            ->select('reqDokumen.*', 'users.id as user_id', 'mahasiswa.nama as nama_mahasiswa')
            ->first();

        if (!$req) {
            return back()->with('error', 'Request tidak ditemukan.');
        }

        $updateData = ['status' => $request->status, 'updated_at' => now()];
        if ($request->filled('message')) {
            $updateData['message'] = $request->message;
        }

        DB::table('reqDokumen')->where('id', $id)->update($updateData);

        $pesanNotif = match($request->status) {
            'approved' => "Permintaan dokumen '{$req->namaDkmn}' Anda telah disetujui oleh BIPA.",
            'rejected' => "Permintaan dokumen '{$req->namaDkmn}' tidak dapat diproses." . ($request->message ? " Alasan: {$request->message}" : ''),
            default    => "Status permintaan dokumen Anda telah diperbarui.",
        };

        $this->kirimNotifMahasiswa($req->user_id, 'Update Request Dokumen BIPA', $pesanNotif);

        return back()->with('success', 'Status request berhasil diperbarui.');
    }

    public function uploadReqDokumen(Request $request, int $id)
    {
        return $this->uploadDokumen($request, $id);
    }
}
