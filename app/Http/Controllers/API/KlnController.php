<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ReqDokumen;
use App\Models\FileDetail;
use App\Models\User;
use App\Models\jurusan;
use Illuminate\Http\Request;

class KlnController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function index()
    {   
        
        return view('kln.dashboard');
       
    }


    /*
    |--------------------------------------------------------------------------
    | DOKUMEN LIST
    |--------------------------------------------------------------------------
    */

    public function dokumen()
    {
        $requests = ReqDokumen::with('mahasiswa')
            ->latest()
            ->get();

        return view('kln.dokumen', compact('requests'));
    }



    /*
    |--------------------------------------------------------------------------
    | SHOW DETAIL (AJAX)
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $req = ReqDokumen::with('mahasiswa')
            ->findOrFail($id);

        return response()->json([
            'id' => $req->id,
            'mahasiswa' => $req->mahasiswa->nama ?? '-',
            'tipe' => $req->tipeDkmn->value,
            'status' => $req->status->value,
            'message' => $req->message,
        ]);
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
    | UPLOAD & APPROVE
    |--------------------------------------------------------------------------
    */

    public function uploadFile(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:2048'
        ]);

        $req = ReqDokumen::findOrFail($id);

        $file = $request->file('file');
        $path = $file->store('req_dokumen', 'public');

        FileDetail::create([
            'dokumen_id' => null,
            'reqDokumen_id' => $req->id,
            'path' => $path,
            'mimeType' => $file->getClientMimeType(),
            'fileSize' => $file->getSize(),
        ]);

        $req->update([
            'status' => 'approved'
        ]);

        return response()->json(['success' => true]);
    }


    /*
    |--------------------------------------------------------------------------
    | USERS PAGE
    |--------------------------------------------------------------------------
    */

    public function usersPage()
    {
        return view('kln.users.index');
    }
    /*
    |--------------------------------------------------------------------------
    | GET USERS DATA
    |--------------------------------------------------------------------------
    */

    public function getUsers()
    {
        $users = \App\Models\User::all();

        return response()->json($users);
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
        return response()->json(['success' => true]);
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

    public function announcementPage(){
        return response()->view('kln.announcement');
    }

}