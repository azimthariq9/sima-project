{{-- resources/views/auth/otp-verify.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Verifikasi OTP — SIMA</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Sora:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --bg:        #080b12;
    --card:      #0f1422;
    --card-b:    rgba(255,255,255,0.06);
    --input-bg:  rgba(255,255,255,0.04);
    --input-b:   rgba(255,255,255,0.08);
    --input-bh:  rgba(255,255,255,0.15);
    --text:      #e8eaf6;
    --muted:     #5c6080;
    --muted-lt:  #8b90b0;
    --accent:    #6c8fff;
    --accent-2:  #a78bfa;
    --green:     #34d399;
    --red:       #f87171;
}
html, body { min-height:100%;background:var(--bg);font-family:'Plus Jakarta Sans',sans-serif;-webkit-font-smoothing:antialiased; }
.bg { position:fixed;inset:0;z-index:0;overflow:hidden; }
.bg::before { content:'';position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,0.07) 1px,transparent 1px);background-size:28px 28px;mask-image:radial-gradient(ellipse 80% 80% at 50% 50%,black 30%,transparent 100%);-webkit-mask-image:radial-gradient(ellipse 80% 80% at 50% 50%,black 30%,transparent 100%); }
.orb { position:absolute;border-radius:50%;filter:blur(80px);pointer-events:none; }
.o1 { width:450px;height:450px;background:radial-gradient(circle,rgba(108,143,255,.13),transparent 70%);top:-100px;left:-80px;animation:o1 14s ease-in-out infinite alternate; }
.o2 { width:380px;height:380px;background:radial-gradient(circle,rgba(167,139,250,.10),transparent 70%);bottom:-80px;right:-60px;animation:o2 18s ease-in-out infinite alternate; }
@keyframes o1{to{transform:translate(40px,60px)}} @keyframes o2{to{transform:translate(-35px,-50px)}}

.wrap { position:relative;z-index:10;width:100%;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:32px 16px; }
.card { width:100%;max-width:400px;background:var(--card);border:1px solid var(--card-b);border-radius:20px;padding:40px 36px 36px;box-shadow:0 0 0 1px rgba(255,255,255,0.03) inset,0 24px 64px rgba(0,0,0,0.5);animation:cardIn .5s cubic-bezier(.22,1,.36,1) both; }
@keyframes cardIn{from{opacity:0;transform:translateY(16px) scale(.98)}to{opacity:1;transform:none}}

.header { text-align:center;margin-bottom:28px; }
.icon-wrap { width:56px;height:56px;margin:0 auto 16px;background:linear-gradient(135deg,rgba(108,143,255,.2),rgba(167,139,250,.2));border:1px solid rgba(108,143,255,.25);border-radius:14px;display:grid;place-items:center; }
.icon-wrap svg { color:var(--accent); }
.title { font-family:'Sora',sans-serif;font-size:20px;font-weight:700;color:#fff;margin-bottom:6px; }
.sub { font-size:13px;color:var(--muted-lt);line-height:1.5; }
.email-chip { display:inline-block;margin-top:8px;padding:3px 10px;background:rgba(108,143,255,.1);border:1px solid rgba(108,143,255,.2);border-radius:100px;font-size:12.5px;color:var(--accent);font-weight:500; }

.sep { height:1px;background:linear-gradient(90deg,transparent,var(--card-b),var(--card-b),transparent);margin-bottom:24px; }

.field { margin-bottom:20px; }
.field label { display:block;font-size:12.5px;font-weight:600;color:var(--muted-lt);margin-bottom:7px; }

/* OTP input boxes */
.otp-wrap { display:flex;gap:10px;justify-content:center; }
.otp-wrap input {
    width:48px;height:56px;text-align:center;
    background:var(--input-bg);border:1px solid var(--input-b);
    border-radius:12px;color:var(--text);
    font-family:'Sora',sans-serif;font-size:22px;font-weight:700;
    outline:none;transition:all .2s;
}
.otp-wrap input:focus { border-color:rgba(108,143,255,.5);background:rgba(108,143,255,.06);box-shadow:0 0 0 3px rgba(108,143,255,.1); }
.otp-wrap input.err { border-color:rgba(248,113,113,.5);background:rgba(248,113,113,.06); }

.btn-submit {
    width:100%;padding:13px;border:none;border-radius:12px;
    background:linear-gradient(135deg,var(--accent),var(--accent-2));
    color:#fff;font-family:'Plus Jakarta Sans',sans-serif;font-size:14.5px;font-weight:700;
    cursor:pointer;transition:opacity .2s,transform .1s;letter-spacing:.01em;
    box-shadow:0 4px 20px rgba(108,143,255,.35);
}
.btn-submit:hover { opacity:.9; }
.btn-submit:active { transform:scale(.98); }
.btn-submit:disabled { opacity:.5;cursor:not-allowed; }

.alert { display:none;padding:10px 14px;border-radius:10px;font-size:12.5px;font-weight:500;margin-bottom:16px; }
.alert-err { background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.2);color:var(--red); }
.alert-ok  { background:rgba(52,211,153,.1);border:1px solid rgba(52,211,153,.2);color:var(--green); }

.resend-row { text-align:center;margin-top:20px;font-size:13px;color:var(--muted-lt); }
.resend-btn { background:none;border:none;cursor:pointer;color:var(--accent);font-size:13px;font-weight:600;padding:0;transition:opacity .2s; }
.resend-btn:disabled { opacity:.4;cursor:not-allowed; }

.back-link { display:flex;align-items:center;justify-content:center;gap:6px;margin-top:16px;font-size:12.5px;color:var(--muted);text-decoration:none;transition:color .2s; }
.back-link:hover { color:var(--text); }
</style>
</head>
<body>
<div class="bg"><div class="orb o1"></div><div class="orb o2"></div></div>

<div class="wrap">
    <div class="card">
        <div class="header">
            <div class="icon-wrap">
                <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <div class="title">Masukkan Kode OTP</div>
            <div class="sub">
                Kode 6-digit telah dikirim ke<br>
                <span class="email-chip">{{ session('otp_email') }}</span>
            </div>
        </div>
        <div class="sep"></div>

        @if(session('success'))
            <div class="alert alert-ok" style="display:flex;gap:8px;align-items:center">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-err" style="display:flex;gap:8px;align-items:center">
                ⚠ {{ session('warning') }}
            </div>
        @endif
        @if(config('app.debug') && session('otp_debug'))
            <div class="alert alert-ok" style="display:flex;gap:8px;align-items:center">
                [DEBUG] OTP: <strong>{{ session('otp_debug') }}</strong>
            </div>
        @endif

        <div id="alertErr" class="alert alert-err">
            @if($errors->first('otp')){{ $errors->first('otp') }}@endif
        </div>

        <form method="POST" action="{{ route('otp.verify.submit') }}" id="otpForm">
            @csrf
            <div class="field">
                <label>Kode OTP</label>
                <div class="otp-wrap">
                    @for($i = 0; $i < 6; $i++)
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                               class="otp-digit" autocomplete="one-time-code" {{ $i === 0 ? 'autofocus' : '' }}>
                    @endfor
                </div>
                <input type="hidden" name="otp" id="otpHidden">
            </div>

            <button type="submit" class="btn-submit" id="btnSubmit">
                Verifikasi & Masuk
            </button>
        </form>

        <div class="resend-row">
            Tidak menerima kode?
            <form method="POST" action="{{ route('otp.resend') }}" style="display:inline">
                @csrf
                <button type="submit" class="resend-btn" id="resendBtn">Kirim ulang</button>
            </form>
            <span id="countdown" style="display:none;color:var(--muted)"></span>
        </div>

        <a href="{{ route('login') }}" class="back-link">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
            Kembali ke halaman login
        </a>
    </div>
</div>

<script>
const digits   = document.querySelectorAll('.otp-digit');
const hidden   = document.getElementById('otpHidden');
const alertErr = document.getElementById('alertErr');

// Auto show error if exists
if (alertErr.textContent.trim()) alertErr.style.display = 'flex';

// OTP input navigation
digits.forEach((inp, i) => {
    inp.addEventListener('input', e => {
        const v = e.target.value.replace(/\D/g, '');
        inp.value = v.slice(-1);
        if (v && i < digits.length - 1) digits[i + 1].focus();
        syncHidden();
    });
    inp.addEventListener('keydown', e => {
        if (e.key === 'Backspace' && !inp.value && i > 0) {
            digits[i - 1].focus();
            digits[i - 1].value = '';
            syncHidden();
        }
    });
    inp.addEventListener('paste', e => {
        e.preventDefault();
        const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
        paste.split('').forEach((c, j) => { if (digits[j]) digits[j].value = c; });
        if (digits[Math.min(paste.length, 5)]) digits[Math.min(paste.length, 5)].focus();
        syncHidden();
    });
});

function syncHidden() {
    hidden.value = [...digits].map(d => d.value).join('');
}

document.getElementById('otpForm').addEventListener('submit', function(e) {
    syncHidden();
    if (hidden.value.length !== 6) {
        e.preventDefault();
        alertErr.textContent = 'Masukkan semua 6 digit kode OTP.';
        alertErr.style.display = 'flex';
        digits.forEach(d => d.classList.add('err'));
        return;
    }
    alertErr.style.display = 'none';
    document.getElementById('btnSubmit').disabled = true;
    document.getElementById('btnSubmit').textContent = 'Memverifikasi…';
});

// Countdown resend (60 detik)
let seconds = 60;
const countdown = document.getElementById('countdown');
const resendBtn = document.getElementById('resendBtn');
resendBtn.disabled = true;
countdown.style.display = 'inline';
countdown.textContent = `(${seconds}s)`;

const timer = setInterval(() => {
    seconds--;
    if (seconds <= 0) {
        clearInterval(timer);
        countdown.style.display = 'none';
        resendBtn.disabled = false;
    } else {
        countdown.textContent = `(${seconds}s)`;
    }
}, 1000);
</script>
</body>
</html>
