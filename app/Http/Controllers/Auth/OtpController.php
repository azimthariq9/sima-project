<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpLoginMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SEND OTP
    | Dipanggil dari halaman login jika user belum punya password.
    | Cek is_has_password → false → generate OTP → kirim email → redirect verify.
    |--------------------------------------------------------------------------
    */
    public function send(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak terdaftar.']);
        }

        if ($user->is_has_password) {
            return back()->withErrors(['email' => 'Akun ini menggunakan password. Silakan login dengan password.']);
        }

        // Rate limit: 1 kirim per 60 detik
        $existing = DB::table('password_reset_tokens')->where('email', $user->email)->first();
        if ($existing && Carbon::parse($existing->created_at)->diffInSeconds(now()) < 60) {
            session(['otp_email' => $user->email]);
            return redirect()->route('otp.verify')
                ->with('warning', 'OTP sudah dikirim. Tunggu 60 detik sebelum minta ulang.');
        }

        // Generate 6-digit OTP
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email'      => $user->email,
            'token'      => bcrypt($otp),
            'created_at' => now(),
        ]);

        // Kirim email OTP
        try {
            Mail::to($user->email)->send(new OtpLoginMail($otp, $user->email));
            $debugOtp = null;
        } catch (\Throwable $e) {
            $debugOtp = config('app.debug') ? $otp : null;
        }

        session(['otp_email' => $user->email]);

        return redirect()->route('otp.verify')
            ->with('success', 'Kode OTP telah dikirim ke email Anda.')
            ->with('otp_debug', $debugOtp);
    }

    /*
    |--------------------------------------------------------------------------
    | TAMPILKAN HALAMAN VERIFIKASI OTP
    |--------------------------------------------------------------------------
    */
    public function showVerify()
    {
        if (!session('otp_email')) {
            return redirect()->route('login');
        }
        return view('auth.otp-verify');
    }

    /*
    |--------------------------------------------------------------------------
    | VERIFIKASI OTP
    |--------------------------------------------------------------------------
    */
    public function verify(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:6']);

        $email = session('otp_email');
        if (!$email) {
            return redirect()->route('login')
                ->withErrors(['otp' => 'Sesi telah berakhir. Ulangi proses login.']);
        }

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'OTP tidak ditemukan. Minta kode baru.']);
        }

        // Expired setelah 10 menit
        if (Carbon::parse($record->created_at)->diffInMinutes(now()) > 10) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return back()->withErrors(['otp' => 'OTP sudah kadaluarsa. Klik "Kirim ulang" untuk kode baru.']);
        }

        if (!password_verify($request->otp, $record->token)) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid.']);
        }

        // Berhasil — hapus OTP, login user
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        $user = User::where('email', $email)->firstOrFail();
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->forget('otp_email');

        $role = strtolower((string) $user->role);
        return match ($role) {
            'mahasiswa' => $user->profile_completed
                ? redirect()->route('mahasiswa.dashboard')
                : redirect()->route('mahasiswa.complete-profile'),
            'dosen'   => redirect()->route('dosen.dashboard'),
            'kln'     => redirect()->route('kln.dashboard'),
            'jurusan' => redirect()->route('jurusan.dashboard'),
            'bipa'    => redirect()->route('bipa.dashboard'),
            default   => redirect('/dashboard'),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | RESEND OTP
    |--------------------------------------------------------------------------
    */
    public function resend(Request $request)
    {
        $email = session('otp_email');
        if (!$email) {
            return redirect()->route('login');
        }
        $request->merge(['email' => $email]);
        return $this->send($request);
    }
}
