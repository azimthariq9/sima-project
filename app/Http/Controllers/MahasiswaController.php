<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Mahasiswa;


class MahasiswaController extends Controller
{
    
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    public function dashboard()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;

        return view('mahasiswa.dashboard', compact('mahasiswa'));
    }

    /*
    |--------------------------------------------------------------------------
    | COMPLETE PROFILE (FIRST LOGIN ONLY)
    |--------------------------------------------------------------------------
    */
    public function completeProfile()
    {
        // Jika sudah lengkap, jangan boleh balik lagi
        if (auth()->user()->profile_completed) {
            return redirect()->route('dashboard');
        }

        return view('mahasiswa.complete-profile');
    }

    public function storeCompleteProfile(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'npm'         => 'required|string|max:20|unique:mahasiswa,npm',
            'noWa'        => 'nullable|string|max:20',
            'tglLahir'    => 'nullable|date',
            'warNeg'      => 'nullable|string|max:100',
            'alamatAsal'  => 'nullable|string|max:255',
            'alamatIndo'  => 'nullable|string|max:255',
            'password'    => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        Mahasiswa::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nama'       => $request->nama,
                'npm'        => $request->npm,
                'noWa'       => $request->noWa,
                'tglLahir'   => $request->tglLahir,
                'warNeg'     => $request->warNeg,
                'alamatAsal' => $request->alamatAsal,
                'alamatIndo' => $request->alamatIndo,
            ]
        );

        $user->update([
            'password' => Hash::make($request->password),
            'profile_completed' => true,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Profil berhasil dilengkapi.');
    }

    /*
    |--------------------------------------------------------------------------
    | PROFILE (VIEW + EDIT NORMAL)
    |--------------------------------------------------------------------------
    */
    public function getProfile()
    {
        $mahasiswa = auth()->user()->mahasiswa;

        return view('mahasiswa.profile', compact('mahasiswa'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            abort(404);
        }

        $request->validate([
            'nama'       => 'required|string|max:255',
            'npm'        => 'required|string|max:20|unique:mahasiswa,npm,' . $mahasiswa->id,
            'noWa'       => 'nullable|string|max:20',
            'tglLahir'   => 'nullable|date',
            'warNeg'     => 'nullable|string|max:100',
            'alamatAsal' => 'nullable|string|max:255',
            'alamatIndo' => 'nullable|string|max:255',
            'password'   => 'nullable|string|min:8|confirmed',
        ]);

        $mahasiswa->update([
            'nama'       => $request->nama,
            'npm'        => $request->npm,
            'noWa'       => $request->noWa,
            'tglLahir'   => $request->tglLahir,
            'warNeg'     => $request->warNeg,
            'alamatAsal' => $request->alamatAsal,
            'alamatIndo' => $request->alamatIndo,
        ]);

        // Jika user ingin ganti password
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | STATIC PAGES
    |--------------------------------------------------------------------------
    */
    public function dokumen()
    {
        return view('mahasiswa.dokumen');
    }

    public function jadwal()
    {
        return view('mahasiswa.jadwal');
    }

    public function notifikasi()
    {
        return view('mahasiswa.notifikasi');
    }

    public function announcement()
    {
        return view('mahasiswa.announcement');
    }

    public function announcementShow($id)
    {
        return view('mahasiswa.announcement-show', compact('id'));
    }

    public function analytics()
    {
        return view('mahasiswa.analytics');
    }
}