{{-- =========================
   SIDEBAR HEADER
========================= --}}
<div class="sima-sidebar__header">
    <div class="sima-sidebar__brand">
        <div class="sima-sidebar__logo-wrapper">
            <img src="{{ asset('img/logo.png') }}"
                 class="sima-sidebar__logo"
                 alt="Logo">
        </div>
        <div>
            <div class="sima-sidebar__title">SIMA</div>
            <div class="sima-sidebar__subtitle">Dosen</div>
        </div>
    </div>
</div>

{{-- =========================
   NAVIGATION
========================= --}}
<div class="sima-nav">

    <a href="{{ route('dosen.dashboard') }}"
       class="sima-nav__item {{ request()->routeIs('dosen.dashboard') ? 'active' : '' }}"
       data-title="Dashboard">
        <i class="fas fa-home sima-nav__icon"></i>
        <span>Dashboard</span>
    </a>

    {{-- JADWAL & KELAS --}}
    <a href="{{ route('dosen.jadwal.index') }}"
       class="sima-nav__item {{ request()->routeIs('dosen.jadwal.*') ? 'active' : '' }}"
       data-title="Jadwal & Kelas">
        <i class="fas fa-calendar-alt sima-nav__icon"></i>
        <span>Jadwal & Kelas</span>
    </a>

    {{-- NOTIFIKASI --}}
    <a href="{{ route('dosen.notifikasi') }}"
       class="sima-nav__item {{ request()->routeIs('dosen.notifikasi') ? 'active' : '' }}"
       data-title="Notifikasi">
        <i class="fas fa-bell sima-nav__icon"></i>
        <span>Notifikasi</span>
    </a>

    {{-- PROFIL --}}
    <a href="{{ route('dosen.profil') }}"
       class="sima-nav__item {{ request()->routeIs('dosen.profil') ? 'active' : '' }}"
       data-title="Profil">
        <i class="fas fa-user-circle sima-nav__icon"></i>
        <span>Profil</span>
    </a>

    {{-- SETTINGS --}}
    <div style="margin-top:20px; padding-top:15px; border-top:1px solid rgba(255,255,255,.25)">
        <button onclick="toggleTheme()"
                style="width:100%; padding:7px; border:none; border-radius:8px;
                       background:white; font-weight:600; font-size:12px;">
            🌙 Dark / Light Mode
        </button>
    </div>

    {{-- LOGOUT --}}
    <form method="POST" action="{{ route('logout') }}" style="margin-top:15px;">
        @csrf
        <button type="submit" class="sima-nav__item" style="border:none; background:none; width:100%;">
            <i class="fas fa-power-off sima-nav__icon"></i>
            <span>Logout</span>
        </button>
    </form>

</div>