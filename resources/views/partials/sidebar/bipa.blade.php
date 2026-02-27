{{-- =========================
   SIDEBAR HEADER
========================= --}}
<div class="sima-sidebar__header">
    <div class="sima-sidebar__brand">

        <div class="sima-sidebar__logo-wrapper">
            <img src="{{ asset('img/logo.png') }}" 
                 class="sima-sidebar__logo"
                 alt="Logo Gunadarma">
        </div>

        <div>
            <div class="sima-sidebar__title">
                SIMA
            </div>
            <div class="sima-sidebar__subtitle">
                Universitas Gunadarma
            </div>
        </div>

    </div>
</div>
{{-- =========================
   NAVIGATION
========================= --}}
<div class="sima-nav">

    {{-- Dashboard --}}
    <a href="{{ route('bipa.dashboard') }}"
       class="sima-nav__item {{ request()->routeIs('bipa.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home sima-nav__icon"></i>
        Dashboard
    </a>

    {{-- Jadwal --}}
    <a href="{{ route('bipa.jadwal') }}"
       class="sima-nav__item {{ request()->routeIs('bipa.jadwal') ? 'active' : '' }}">
        <i class="fas fa-clock sima-nav__icon"></i>
        Jadwal
    </a>

    {{-- Pengumuman --}}
    <a href="{{ route('bipa.announcement') }}"
       class="sima-nav__item {{ request()->routeIs('bipa.announcement') ? 'active' : '' }}">
        <i class="fas fa-envelope sima-nav__icon"></i>
        Pengumuman
    </a>

    {{-- Notifikasi --}}
    <a href="{{ route('bipa.notifikasi') }}"
       class="sima-nav__item {{ request()->routeIs('bipa.notifikasi') ? 'active' : '' }}">
        <i class="fas fa-bell sima-nav__icon"></i>
        Notifikasi
    </a>

    {{-- Kehadiran & Nilai --}}
    <a href="{{ route('bipa.analytics') }}"
       class="sima-nav__item {{ request()->routeIs('bipa.analytics') ? 'active' : '' }}">
        <i class="fas fa-check-square sima-nav__icon"></i>
        Kehadiran & Nilai
    </a>


    {{-- SETTINGS --}}
    <div style="margin-top:20px;
                padding-top:15px;
                border-top:1px solid rgba(255,255,255,.25)">

        <select onchange="changeLang(this.value)"
                style="width:100%;
                       padding:7px;
                       border-radius:8px;
                       border:none;
                       font-size:12px;
                       margin-bottom:10px;">
            <option value="id">🇮🇩 Bahasa Indonesia</option>
            <option value="en">🇬🇧 English</option>
        </select>

        <button onclick="toggleTheme()"
                style="width:100%;
                       padding:7px;
                       border:none;
                       border-radius:8px;
                       background:white;
                       font-weight:600;
                       font-size:12px;">
            🌙 Toggle Dark Mode
        </button>
    </div>

    {{-- LOGOUT --}}
    <form method="POST" action="{{ route('logout') }}" style="margin-top:15px;">
        @csrf
        <button type="submit"
                class="sima-nav__item"
                style="border:none;background:none;width:100%;">
            <i class="fas fa-power-off sima-nav__icon"></i>
            Logout
        </button>
    </form>

</div>