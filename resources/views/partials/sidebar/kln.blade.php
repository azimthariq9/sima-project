{{-- =========================
   SIDEBAR HEADER
========================= --}}
<div @class(['sima-sidebar__header'])>
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

    <a href="{{ route('kln.dashboard') }}" class="sima-nav__item {{ request()->routeIs('kln.dashboard') ? 'active' : '' }}" data-title="Dashboard">
        <i class="fas fa-home sima-nav__icon"></i>
                 <span>Dashboard</span>
    </a>

    <a href="{{ route('kln.users.page') }}" class="sima-nav__item {{ request()->routeIs('kln.users.*') ? 'active' : '' }}" data-title="Users">
        <i class="fas fa-users sima-nav__icon"></i>
        <span>Users</span>
    </a>

    {{-- Untuk menu dengan dropdown --}}
    <div class="sima-nav__item has-sub" onclick="toggleNav(this)" data-title="Dokumen">
        <i class="fas fa-file sima-nav__icon"></i>
        <span>Dokumen</span>
        <i class="fas fa-chevron-down sima-nav__chevron"></i>
    </div>
    <div class="sima-nav__sub">
        <a href="{{ route('kln.dokumen.page') }}" class="sima-nav__sub-item">Semua Dokumen</a>
        {{-- <a href="{{ route('kln.dokumen.pending') }}" class="sima-nav__sub-item">Pending</a> --}}
    </div>

    {{-- SCHEDULE DROPDOWN --}}
    @php
        $scheduleActive = request()->routeIs('kln.schedule');
    @endphp
    <div class="sima-nav__item has-sub {{ $scheduleActive ? 'open active' : '' }}" onclick="toggleNav(this)" data-title="Jadwal">
        <i class="fas fa-clock sima-nav__icon"></i>
        <span>Jadwal</span>
        <i class="fas fa-chevron-down sima-nav__chevron"></i>
    </div>

    {{-- <div class="sima-nav__item has-sub" onclick="toggleNav(this)" data-title="Jadwal">
        <a href="" class="sima-nav__item {{ $scheduleActive ? 'open active' : '' }}"
                onclick="toggleNav(this)">
            <i class="fas fa-clock sima-nav__icon"></i>
            Schedules
            <i class="fas fa-chevron-right sima-nav__chevron"></i>
        </a>
    </div> --}}
    

    <div class="sima-nav__sub {{ $scheduleActive ? 'open' : '' }}">
        <a href="{{ route('kln.schedule') }}"
           class="sima-nav__sub-item {{ $scheduleActive ? 'active' : '' }}">
            BIPA
        </a>
        <a href="{{ route('kln.schedule') }}"
           class="sima-nav__sub-item">
            Lectures
        </a>
        <a href="{{ route('kln.schedule') }}"
           class="sima-nav__sub-item">
            KLN
        </a>
    </div>

    {{-- Announcement --}}
    <a href="{{ route('kln.announcement') }}"
       class="sima-nav__item {{ request()->routeIs('kln.announcement') ? 'active' : '' }}" data-title="announcement">
        <i class="fas fa-envelope sima-nav__icon"></i>
        <span>Pengumuman</span>
    </a>


    {{-- Notification --}}
    <a href="#"
       class="sima-nav__item">
        <i class="fas fa-bell sima-nav__icon"></i>
        <span>Notifikasi</span>
    </a>


    {{-- Details Presence --}}
    <a href="#"
       class="sima-nav__item">
        <i class="fas fa-check-square sima-nav__icon"></i>
        <span>Daftar Kehadiran</span>
    </a>


    {{-- =========================
       SETTINGS SECTION
    ========================= --}}
    <div style="margin-top:20px;
                padding-top:15px;
                border-top:1px solid rgba(255,255,255,.25)">

        {{-- Language Toggle --}}
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

        {{-- Dark Mode Toggle --}}
        <button onclick="toggleTheme()"
                style="width:100%;
                       padding:7px;
                       border:none;
                       border-radius:8px;
                       background:white;
                       font-weight:600;
                       font-size:12px;">
            🌙 
        </button>

    </div>


    {{-- =========================
       LOGOUT
    ========================= --}}
    <form method="POST" action="{{ route('logout') }}" style="margin-top:15px;">
    @csrf
    <button type="submit"
                class="sima-nav__item"
                style="border:none;background:none;width:100%;">
            <i class="fas fa-power-off sima-nav__icon"></i>
            <span>Logout</span>
        </button>
    </form>



</div>