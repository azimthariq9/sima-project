<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TypeController extends Controller
{
    /*
    |==========================================================================
    | TIPE MAHASISWA (Course Types)
    |==========================================================================
    */

    public function tipeMahasiswaIndex()
    {
        $items = DB::table('tipe_mahasiswa')->orderBy('nama')->get();
        return view('kln.types.mahasiswa', compact('items'));
    }

    public function storeTipeMahasiswa(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:tipe_mahasiswa,nama',
        ]);

        DB::table('tipe_mahasiswa')->insert([
            'nama'       => $request->nama,
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'flash'   => ['type' => 'success', 'message' => 'Course type added.'],
        ]);
    }

    public function updateTipeMahasiswa(Request $request, int $id)
    {
        $item = DB::table('tipe_mahasiswa')->where('id', $id)->first();
        if (!$item) return response()->json(['success' => false, 'message' => 'Not found.'], 404);

        $request->validate([
            'nama' => 'required|string|max:100|unique:tipe_mahasiswa,nama,' . $id,
        ]);

        DB::table('tipe_mahasiswa')->where('id', $id)->update([
            'nama'       => $request->nama,
            'is_active'  => $request->boolean('is_active', $item->is_active),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'flash'   => ['type' => 'success', 'message' => 'Course type updated.'],
        ]);
    }

    public function destroyTipeMahasiswa(int $id)
    {
        $item = DB::table('tipe_mahasiswa')->where('id', $id)->first();
        if (!$item) return response()->json(['success' => false, 'message' => 'Not found.'], 404);

        DB::table('tipe_mahasiswa')->where('id', $id)->update([
            'is_active'  => !$item->is_active,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'flash'   => ['type' => 'success', 'message' => $item->is_active ? 'Course type deactivated.' : 'Course type activated.'],
        ]);
    }

    /*
    |==========================================================================
    | TIPE DOKUMEN (Document Types)
    |==========================================================================
    */

    public function tipeDokumenIndex()
    {
        $items = DB::table('tipe_dokumen')->orderBy('kategori')->orderBy('nama')->get();
        return view('kln.types.dokumen', compact('items'));
    }

    public function storeTipeDokumen(Request $request)
    {
        $request->validate([
            'kode'             => 'required|string|max:50|unique:tipe_dokumen,kode',
            'nama'             => 'required|string|max:150',
            'kategori'         => 'required|in:external,internal',
            'penerbit_default' => 'nullable|string|max:50',
        ]);

        DB::table('tipe_dokumen')->insert([
            'kode'             => $request->kode,
            'nama'             => $request->nama,
            'kategori'         => $request->kategori,
            'penerbit_default' => $request->penerbit_default,
            'is_active'        => true,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        return response()->json([
            'success' => true,
            'flash'   => ['type' => 'success', 'message' => 'Document type added.'],
        ]);
    }

    public function updateTipeDokumen(Request $request, int $id)
    {
        $item = DB::table('tipe_dokumen')->where('id', $id)->first();
        if (!$item) return response()->json(['success' => false, 'message' => 'Not found.'], 404);

        $request->validate([
            'kode'             => 'required|string|max:50|unique:tipe_dokumen,kode,' . $id,
            'nama'             => 'required|string|max:150',
            'kategori'         => 'required|in:external,internal',
            'penerbit_default' => 'nullable|string|max:50',
        ]);

        DB::table('tipe_dokumen')->where('id', $id)->update([
            'kode'             => $request->kode,
            'nama'             => $request->nama,
            'kategori'         => $request->kategori,
            'penerbit_default' => $request->penerbit_default,
            'is_active'        => $request->boolean('is_active', $item->is_active),
            'updated_at'       => now(),
        ]);

        return response()->json([
            'success' => true,
            'flash'   => ['type' => 'success', 'message' => 'Document type updated.'],
        ]);
    }

    public function destroyTipeDokumen(int $id)
    {
        $item = DB::table('tipe_dokumen')->where('id', $id)->first();
        if (!$item) return response()->json(['success' => false, 'message' => 'Not found.'], 404);

        DB::table('tipe_dokumen')->where('id', $id)->update([
            'is_active'  => !$item->is_active,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'flash'   => ['type' => 'success', 'message' => $item->is_active ? 'Document type deactivated.' : 'Document type activated.'],
        ]);
    }
}
