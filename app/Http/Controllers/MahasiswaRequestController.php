<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MahasiswaRequestController extends Controller
{
    public function index()
    {
        return view('mahasiswa.request.index');
    }

    public function create()
    {
        return view('mahasiswa.request.create');
    }

    public function store(Request $request)
    {
        // nanti logic simpan
        return redirect()->route('mahasiswa.request.index')
            ->with('success', 'Request berhasil dikirim.');
    }
}
