{{-- resources/views/auth/login.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login — SIMA</title>
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
}

html, body { min-height:100%;background:var(--bg);font-family:'Plus Jakarta Sans',sans-serif;-webkit-font-smoothing:antialiased; }

.bg { position:fixed;inset:0;z-index:0;overflow:hidden; }
.bg::before { content:'';position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,0.07) 1px,transparent 1px);background-size:28px 28px;mask-image:radial-gradient(ellipse 80% 80% at 50% 50%,black 30%,transparent 100%);-webkit-mask-image:radial-gradient(ellipse 80% 80% at 50% 50%,black 30%,transparent 100%); }
.orb { position:absolute;border-radius:50%;filter:blur(80px);pointer-events:none; }
.o1 { width:450px;height:450px;background:radial-gradient(circle,rgba(108,143,255,.13),transparent 70%);top:-100px;left:-80px;animation:o1 14s ease-in-out infinite alternate; }
.o2 { width:380px;height:380px;background:radial-gradient(circle,rgba(167,139,250,.10),transparent 70%);bottom:-80px;right:-60px;animation:o2 18s ease-in-out infinite alternate; }
.o3 { width:250px;height:250px;background:radial-gradient(circle,rgba(52,211,153,.06),transparent 70%);top:55%;left:55%;animation:o3 22s ease-in-out infinite alternate; }
@keyframes o1{to{transform:translate(40px,60px)}} @keyframes o2{to{transform:translate(-35px,-50px)}} @keyframes o3{to{transform:translate(-40px,30px)}}

.wrap { position:relative;z-index:10;width:100%;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:32px 16px; }

/* ── LANGUAGE TOGGLE ── */
.lang-bar {
    position:fixed; top:20px; right:24px; z-index:100;
    display:flex; align-items:center; gap:4px;
    background:rgba(15,20,34,0.85);
    border:1px solid var(--card-b);
    border-radius:100px; padding:4px;
    backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px);
    box-shadow: 0 4px 20px rgba(0,0,0,.4);
}
.lang-btn {
    display:flex; align-items:center; gap:6px;
    border:none; background:none; border-radius:100px;
    padding:6px 14px;
    font-family:'Plus Jakarta Sans',sans-serif;
    font-size:12.5px; font-weight:600;
    cursor:pointer; transition:background .2s, color .2s, box-shadow .2s;
    color:var(--muted); letter-spacing:.02em;
}
.lang-btn .flag { font-size:14px; line-height:1; }
.lang-btn:hover { color:var(--text); }
.lang-btn.active {
    background:linear-gradient(135deg,var(--accent),var(--accent-2));
    color:#fff;
    box-shadow:0 2px 12px rgba(108,143,255,.4);
}

/* ── CARD ── */
.card { width:100%;max-width:420px;background:var(--card);border:1px solid var(--card-b);border-radius:20px;padding:40px 36px 36px;box-shadow:0 0 0 1px rgba(255,255,255,0.03) inset,0 24px 64px rgba(0,0,0,0.5),0 2px 0 rgba(255,255,255,0.04) inset;animation:cardIn .5s cubic-bezier(.22,1,.36,1) both; }
@keyframes cardIn{from{opacity:0;transform:translateY(16px) scale(.98)}to{opacity:1;transform:none}}

.header { display:flex;flex-direction:column;align-items:center;text-align:center;margin-bottom:28px; }
.logo-wrap { position:relative;width:56px;height:56px;margin-bottom:16px; }
.logo-bg { width:56px;height:56px;background:linear-gradient(135deg,rgba(108,143,255,.2),rgba(167,139,250,.2));border:1px solid rgba(108,143,255,.25);border-radius:14px;display:grid;place-items:center;position:relative; }
.logo-bg::before { content:'';position:absolute;inset:-1px;border-radius:15px;background:linear-gradient(135deg,rgba(108,143,255,.3),rgba(167,139,250,.15),transparent 60%);mask:linear-gradient(#fff,#fff) content-box,linear-gradient(#fff,#fff);-webkit-mask:linear-gradient(#fff,#fff) content-box,linear-gradient(#fff,#fff);mask-composite:exclude;-webkit-mask-composite:xor;padding:1px; }
.logo-bg svg { color:var(--accent); }
.logo-ping { position:absolute;top:-3px;right:-3px;width:12px;height:12px;border-radius:50%;background:var(--green);box-shadow:0 0 0 3px var(--card);animation:ping 2.5s ease-in-out infinite; }
@keyframes ping{0%,100%{box-shadow:0 0 0 3px var(--card),0 0 0 5px rgba(52,211,153,.3)}50%{box-shadow:0 0 0 3px var(--card),0 0 0 9px rgba(52,211,153,0)}}
.app-name { font-family:'Sora',sans-serif;font-size:22px;font-weight:700;color:#fff;letter-spacing:-.02em;margin-bottom:4px; }
.app-tagline { font-size:12px;color:var(--muted);font-weight:400;transition:opacity .15s; }

.sep { height:1px;background:linear-gradient(90deg,transparent,var(--card-b),var(--card-b),transparent);margin-bottom:24px; }

.status-pill { display:inline-flex;align-items:center;gap:6px;background:rgba(52,211,153,.07);border:1px solid rgba(52,211,153,.15);border-radius:100px;padding:4px 11px;font-size:11.5px;color:var(--green);font-weight:500;margin-bottom:20px; }
.sdot { width:5px;height:5px;border-radius:50%;background:var(--green);animation:sd 2s ease-in-out infinite; }
@keyframes sd{0%,100%{opacity:1}50%{opacity:.3}}

.form-title { font-family:'Sora',sans-serif;font-size:20px;font-weight:700;color:#fff;letter-spacing:-.02em;margin-bottom:4px;transition:opacity .15s; }
.form-sub { font-size:13.5px;color:var(--muted-lt);font-weight:300;margin-bottom:24px;line-height:1.5;transition:opacity .15s; }

.field { margin-bottom:16px; }
.field label { display:block;font-size:12.5px;font-weight:600;color:var(--muted-lt);margin-bottom:7px; }
.iw { position:relative; }
.iw .ico { position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none;transition:color .2s; }
.iw input { width:100%;background:var(--input-bg);border:1px solid var(--input-b);border-radius:10px;padding:12px 13px 12px 40px;color:var(--text);font-family:'Plus Jakarta Sans',sans-serif;font-size:14px;outline:none;transition:all .2s; }
.iw input::placeholder { color:var(--muted); }
.iw input:hover { border-color:var(--input-bh); }
.iw input:focus { border-color:rgba(108,143,255,.5);background:rgba(108,143,255,.06);box-shadow:0 0 0 3px rgba(108,143,255,.1); }
.iw:focus-within .ico { color:var(--accent); }
.pw-btn { position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted);padding:3px;display:grid;place-items:center;transition:color .2s; }
.pw-btn:hover { color:var(--text); }
.iw.with-toggle input { padding-right:40px; }

.opts { display:flex;align-items:center;justify-content:space-between;margin:16px 0 20px; }
.chk { display:flex;align-items:center;gap:8px;cursor:pointer;user-select:none;font-size:13px;color:var(--muted-lt);font-weight:400; }
.chk input[type=checkbox] { appearance:none;width:15px;height:15px;background:var(--input-bg);border:1px solid var(--input-b);border-radius:4px;cursor:pointer;display:grid;place-items:center;transition:all .18s; }
.chk input:checked { background:var(--accent);border-color:var(--accent);box-shadow:0 0 8px rgba(108,143,255,.35); }
.chk input:checked::after { content:'';display:block;width:8px;height:5px;border-left:1.8px solid #fff;border-bottom:1.8px solid #fff;transform:translateY(-1px) rotate(-45deg); }
.forgot { font-size:12.5px;color:var(--accent);text-decoration:none;font-weight:500;opacity:.75;transition:opacity .2s,color .15s; }
.forgot:hover { opacity:1; }

.btn { width:100%;padding:13px;border:none;border-radius:10px;background:linear-gradient(135deg,var(--accent),var(--accent-2));background-size:200% 200%;color:#fff;font-family:'Plus Jakarta Sans',sans-serif;font-size:14.5px;font-weight:600;cursor:pointer;position:relative;overflow:hidden;transition:transform .18s,box-shadow .18s,background-position .4s;box-shadow:0 4px 20px rgba(108,143,255,.3),0 1px 0 rgba(255,255,255,.12) inset; }
.btn::after { content:'';position:absolute;inset:0;background:linear-gradient(90deg,transparent 20%,rgba(255,255,255,.1) 50%,transparent 80%);transform:translateX(-120%);transition:transform .55s ease; }
.btn:hover { transform:translateY(-1px);background-position:100% 100%;box-shadow:0 8px 28px rgba(108,143,255,.4),0 1px 0 rgba(255,255,255,.12) inset; }
.btn:hover::after { transform:translateX(160%); }
.btn:active { transform:scale(.99); }

.err { margin-top:14px;background:rgba(248,113,113,.07);border:1px solid rgba(248,113,113,.2);border-radius:10px;padding:11px 13px;display:flex;align-items:center;gap:9px;font-size:13px;color:#fca5a5;animation:errin .25s ease both; }
@keyframes errin{from{opacity:0;transform:translateY(-4px)}}
.err svg { flex-shrink:0; }

.card-foot { margin-top:20px;padding-top:18px;border-top:1px solid var(--card-b);display:flex;align-items:center;justify-content:center;gap:6px;font-size:12px;color:var(--muted); }
.card-foot svg { color:var(--muted); }
.card-foot strong { color:var(--muted-lt);font-weight:500; }

[data-t] { transition: opacity .15s; }
</style>
</head>
<body>

<div class="bg">
    <div class="orb o1"></div>
    <div class="orb o2"></div>
    <div class="orb o3"></div>
</div>

<!-- Language Toggle -->
<div class="lang-bar">
    <button class="lang-btn active" id="btn-id" onclick="setLang('id')">
        <span class="flag">🇮🇩</span> ID
    </button>
    <button class="lang-btn" id="btn-en" onclick="setLang('en')">
        <span class="flag">🇬🇧</span> EN
    </button>
</div>

<div class="wrap">
    <div class="card">

        <div class="header">
            <div class="logo-wrap">
                <div class="logo-bg">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                </div>
                <span class="logo-ping"></span>
            </div>
            <div class="app-name">SIMA</div>
            <div class="app-tagline" data-t="tagline">Sistem Informasi Mahasiswa Asing</div>
        </div>

        <div class="sep"></div>

        <div class="status-pill">
            <span class="sdot"></span>
            <span data-t="status">Sistem aktif</span>
        </div>

        <div class="form-title" data-t="title">Masuk ke akun Anda</div>
        <div class="form-sub" data-t="subtitle">Selamat datang kembali di portal SIMA</div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="field">
                <label data-t="lbl-email">Email</label>
                <div class="iw">
                    <svg class="ico" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 7.5L22 7"/>
                    </svg>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           id="email-inp" placeholder="email@instansi.ac.id"
                           autocomplete="email" required>
                </div>
            </div>

            <div class="field">
                <label data-t="lbl-pw">Password</label>
                <div class="iw with-toggle">
                    <svg class="ico" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <input type="password" id="password" name="password"
                           placeholder="••••••••••" autocomplete="current-password" required>
                    <button class="pw-btn" type="button" onclick="togglePw()" tabindex="-1">
                        <svg id="eye-ic" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="opts">
                <label class="chk">
                    <input type="checkbox" name="remember">
                    <span data-t="remember">Ingat saya</span>
                </label>
                <a href="{{ route('password.request') }}" class="forgot" data-t="forgot">Lupa password?</a>
            </div>

            <button type="submit" class="btn" data-t="submit">Masuk</button>
        </form>

        @if ($errors->any())
            <div class="err">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                {{ $errors->first() }}
            </div>
        @endif

        <div class="card-foot">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <span data-t="secure">Koneksi <strong>terenkripsi & aman</strong></span>
        </div>

    </div>
</div>

<script>
const translations = {
    id: {
        tagline:    'Sistem Informasi Mahasiswa Asing',
        status:     'Sistem aktif',
        title:      'Masuk ke akun Anda',
        subtitle:   'Selamat datang kembali di portal SIMA',
        'lbl-email':'Email',
        'lbl-pw':   'Password',
        remember:   'Ingat saya',
        forgot:     'Lupa password?',
        submit:     'Masuk',
        secure:     'Koneksi <strong>terenkripsi & aman</strong>',
        'ph-email': 'email@instansi.ac.id',
    },
    en: {
        tagline:    'Foreign Student Information System',
        status:     'System online',
        title:      'Sign in to your account',
        subtitle:   'Welcome back to the SIMA portal',
        'lbl-email':'Email',
        'lbl-pw':   'Password',
        remember:   'Remember me',
        forgot:     'Forgot password?',
        submit:     'Sign In',
        secure:     'Connection is <strong>encrypted & secure</strong>',
        'ph-email': 'email@institution.ac.id',
    }
};

let currentLang = '{{ app()->getLocale() }}' === 'en' ? 'en' : 'id';

function setLang(lang) {
    if (lang === currentLang) return;
    currentLang = lang;

    document.getElementById('btn-id').classList.toggle('active', lang === 'id');
    document.getElementById('btn-en').classList.toggle('active', lang === 'en');
    document.documentElement.lang = lang;

    const t = translations[lang];
    document.querySelectorAll('[data-t]').forEach(el => {
        el.style.opacity = '0';
        setTimeout(() => {
            const key = el.getAttribute('data-t');
            if (t[key] !== undefined) el.innerHTML = t[key];
            el.style.opacity = '1';
        }, 130);
    });

    const emailInp = document.getElementById('email');
    setTimeout(() => { emailInp.placeholder = t['ph-email']; }, 130);
}

function togglePw() {
    const inp = document.getElementById('password');
    const ic  = document.getElementById('eye-ic');
    const show = inp.type === 'password';
    inp.type = show ? 'text' : 'password';
    ic.innerHTML = show
        ? `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>`
        : `<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/>`;
}
</script>

</body>
</html>
