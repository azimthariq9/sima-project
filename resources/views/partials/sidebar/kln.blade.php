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
    <a href="{{ route('kln.dashboard') }}"
       class="sima-nav__item {{ request()->routeIs('kln.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home sima-nav__icon"></i>
        Dashboard
    </a>


    {{-- List Users --}}
    <a href="{{ route('kln.users.page') }}"
       class="sima-nav__item {{ request()->routeIs('kln.users.page') ? 'active' : '' }}">
        <i class="fas fa-users sima-nav__icon"></i>
        List Users
    </a>


    {{-- Students & Lecturers --}}
    <a href="#"
       class="sima-nav__item">
        <i class="fas fa-user-tie sima-nav__icon"></i>
        Students & Lecturers
    </a>


    {{-- Request Documents --}}
    <a href="{{ route('kln.dokumen') }}"
       class="sima-nav__item {{ request()->routeIs('kln.dokumen.page') ? 'active' : '' }}">
        <i class="fas fa-file-alt sima-nav__icon"></i>
        Request Documents
    </a>


    {{-- SCHEDULE DROPDOWN --}}
    @php
        $scheduleActive = request()->routeIs('kln.schedule');
    @endphp

    <button class="sima-nav__item {{ $scheduleActive ? 'open active' : '' }}"
            onclick="toggleNav(this)">
        <i class="fas fa-clock sima-nav__icon"></i>
        Schedules
        <i class="fas fa-chevron-right sima-nav__chevron"></i>
    </button>

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
       class="sima-nav__item {{ request()->routeIs('kln.announcement') ? 'active' : '' }}">
        <i class="fas fa-envelope sima-nav__icon"></i>
        Announcement
    </a>


    {{-- Notification --}}
    <a href="#"
       class="sima-nav__item">
        <i class="fas fa-bell sima-nav__icon"></i>
        Notification
    </a>


    {{-- Details Presence --}}
    <a href="#"
       class="sima-nav__item">
        <i class="fas fa-check-square sima-nav__icon"></i>
        Details Presence
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