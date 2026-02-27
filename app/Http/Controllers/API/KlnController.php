<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ReqDokumen;
use App\Models\FileDetail;
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

}