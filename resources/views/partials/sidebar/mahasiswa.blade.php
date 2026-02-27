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
    <a href="{{ route('mahasiswa.dashboard') }}"
       class="sima-nav__item {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home sima-nav__icon"></i>
        Dashboard
    </a>


    <a href="{{ route('mahasiswa.profile') }}"
    class="sima-nav__item {{ request()->routeIs('mahasiswa.profile') ? 'active' : '' }}">
        <i class="fas fa-user sima-nav__icon"></i>
        Biodata / Profile
    </a>
    {{-- Complimentary Docs --}}
    <a href="{{ route('mahasiswa.request.create') }}"
    class="sima-nav__item {{ request()->routeIs('mahasiswa.request.create') ? 'active' : '' }}">
        <i class="fas fa-folder-open sima-nav__icon"></i>
        Complimentary Docs
    </a>

    {{-- SCHEDULES DROPDOWN --}}
    @php
        $scheduleActive = request()->routeIs('mahasiswa.jadwal');
    @endphp

    <button class="sima-nav__item {{ $scheduleActive ? 'open active' : '' }}"
            onclick="toggleNav(this)">
        <i class="fas fa-clock sima-nav__icon"></i>
        Schedules
        <i class="fas fa-chevron-right sima-nav__chevron"></i>
    </button>

    <div class="sima-nav__sub {{ $scheduleActive ? 'open' : '' }}">
        <a href="{{ route('mahasiswa.jadwal') }}"
           class="sima-nav__sub-item {{ $scheduleActive ? 'active' : '' }}">
            BIPA
        </a>
        <a href="{{ route('mahasiswa.jadwal') }}"
           class="sima-nav__sub-item">
            Lectures
        </a>
        <a href="{{ route('mahasiswa.jadwal') }}"
           class="sima-nav__sub-item">
            KLN
        </a>
    </div>


    {{-- Announcement --}}
    <a href="{{ route('mahasiswa.announcement') }}"
       class="sima-nav__item {{ request()->routeIs('mahasiswa.announcement') ? 'active' : '' }}">
        <i class="fas fa-envelope sima-nav__icon"></i>
        Announcement
    </a>


    {{-- Notification --}}
    <a href="{{ route('mahasiswa.notifikasi') }}"
       class="sima-nav__item {{ request()->routeIs('mahasiswa.notifikasi') ? 'active' : '' }}">
        <i class="fas fa-bell sima-nav__icon"></i>
        Notification
    </a>


    {{-- Presence --}}
    <a href="#" class="sima-nav__item">
        <i class="fas fa-check-square sima-nav__icon"></i>
        Details Presence
    </a>


    {{-- Change Password --}}
    <a href="{{ route('profile.edit') }}" class="sima-nav__item">
        <i class="fas fa-key sima-nav__icon"></i>
        Change Password
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
            🌙 Toggle Dark Mode
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
            Logout
        </button>
    </form>

</div>