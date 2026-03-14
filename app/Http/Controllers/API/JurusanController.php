<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Support\Facades\Auth;

class JurusanController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        return view('jurusan.dashboard');
    }

    /*
    |--------------------------------------------------------------------------
    | DOSEN PAGE
    |--------------------------------------------------------------------------
    */
    public function dosenPage()
    {
        return view('jurusan.dosen.index');
    }

    /*
    |--------------------------------------------------------------------------
    | MATAKULIAH PAGE
    |--------------------------------------------------------------------------
    */
    public function matakuliahPage()
    {
        return view('jurusan.matakuliah.index');
    }

    /*
    |--------------------------------------------------------------------------
    | KELAS PAGE
    |--------------------------------------------------------------------------
    */
    public function kelasPage()
    {
        return view('jurusan.kelas.index');
    }

    /*
    |--------------------------------------------------------------------------
    | JADWAL PAGE
    |--------------------------------------------------------------------------
    */
    public function jadwalPage()
    {
        return view('jurusan.jadwal.index');
    }
}