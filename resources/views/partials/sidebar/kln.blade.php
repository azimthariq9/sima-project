{{-- =========================
   SIDEBAR HEADER
========================= --}}
<div @class(['sima-sidebar__header'])>
    <div class="sima-sidebar__brand">

        {{-- <div class="sima-sidebar__logo-wrapper" style="width:38px;height:38px;flex-shrink:0;">
            <img src="{{ asset('img/logo.png') }}"
                 class="sima-sidebar__logo"
                 alt="Logo Gunadarma"
                 style="width:100%;height:100%;object-fit:contain;">
        </div> --}}

        <div>
            <div class="sima-sidebar__title">SIMA</div>
            <div class="sima-sidebar__subtitle">KLN</div>
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

    {{-- Students & Lecturers --}}
    <a href="{{ route('kln.students.page') }}"
       class="sima-nav__item {{ request()->routeIs('kln.students.*') ? 'active' : '' }}"
       data-title="Students & Lecturers">
        <i class="fas fa-user-tie sima-nav__icon"></i>
        <span>Students & Lecturers</span>
    </a>

    {{-- Request Documents --}}
    <a href="{{ route('kln.dokumen.page') }}"
       class="sima-nav__item {{ request()->routeIs('kln.dokumen.page') ? 'active' : '' }}"
       data-title="Request Documents">
        <i class="fas fa-file-alt sima-nav__icon"></i>
        <span>Request Documents</span>
    </a>

    {{-- Untuk menu dengan dropdown
    <div class="sima-nav__item has-sub" onclick="toggleNav(this)" data-title="Dokumen">
        <i class="fas fa-file sima-nav__icon"></i>
        <span>Dokumen</span>
        <i class="fas fa-chevron-down sima-nav__chevron"></i>
    </div>
    <div class="sima-nav__sub">
        <a href="{{ route('kln.dokumen.page') }}" class="sima-nav__sub-item">Semua Dokumen</a>
        {{-- <a href="{{ route('kln.dokumen.pending') }}" class="sima-nav__sub-item">Pending</a> 
    </div> --}}

    {{-- SCHEDULE DROPDOWN --}}
    @php
        $scheduleActive = request()->routeIs('kln.jadwal.*');
    @endphp
    <button class="sima-nav__item has-sub {{ $scheduleActive ? 'open active' : '' }}" onclick="toggleNav(this)" data-title="Schedules"
            style="border:none;background:none;width:100%;text-align:left;">
        <i class="fas fa-clock sima-nav__icon"></i>
        <span>Schedules</span>
        <i class="fas fa-chevron-down sima-nav__chevron"></i>
    </button>

    <div class="sima-nav__sub {{ $scheduleActive ? 'open' : '' }}">
        <a href="{{ route('kln.jadwal.bipa') }}"
           class="sima-nav__sub-item {{ request()->routeIs('kln.jadwal.bipa') ? 'active' : '' }}">
            BIPA
        </a>
        <a href="{{ route('kln.jadwal.lecturers') }}"
           class="sima-nav__sub-item {{ request()->routeIs('kln.jadwal.lecturers') ? 'active' : '' }}">
            Lecturers
        </a>
        <a href="{{ route('kln.jadwal.kln') }}"
           class="sima-nav__sub-item {{ request()->routeIs('kln.jadwal.kln') ? 'active' : '' }}">
            KLN
        </a>
    </div>

    {{-- Announcement --}}
    <a href="{{ route('kln.announcement') }}"
       class="sima-nav__item {{ request()->routeIs('kln.announcement') ? 'active' : '' }}" data-title="Announcements">
        <i class="fas fa-envelope sima-nav__icon"></i>
        <span>Announcements</span>
    </a>


    {{-- Notification --}}
    <a href="{{ route('kln.notifikasi') }}"
       class="sima-nav__item {{ request()->routeIs('kln.notifikasi') ? 'active' : '' }}"
       data-title="Notifications">
        <i class="fas fa-bell sima-nav__icon"></i>
        <span>Notifications</span>
    </a>

    {{-- Broadcast --}}
    <a href="{{ route('kln.broadcast') }}"
       class="sima-nav__item {{ request()->routeIs('kln.broadcast*') ? 'active' : '' }}"
       data-title="Broadcast">
        <i class="fas fa-bullhorn sima-nav__icon"></i>
        <span>Broadcast</span>
    </a>


    {{-- Details Presence --}}
    <a href="{{ route('kln.attendance') }}"
       class="sima-nav__item {{ request()->routeIs('kln.attendance*') ? 'active' : '' }}"
       data-title="Attendance">
        <i class="fas fa-check-square sima-nav__icon"></i>
        <span>Attendance</span>
    </a>


    {{-- =========================
       TYPE MANAGEMENT
    ========================= --}}
    <div style="margin-top:20px;padding-top:15px;border-top:1px solid rgba(255,255,255,.25)">
        <div style="font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.85);padding:0 14px;margin-bottom:6px;">Type Management</div>
        <a href="{{ route('kln.types.mahasiswa') }}" class="sima-nav__item {{ request()->routeIs('kln.types.mahasiswa*') ? 'active' : '' }}" data-title="Course Types">
            <i class="fas fa-graduation-cap sima-nav__icon"></i>
            <span>Course Types</span>
        </a>
        <a href="{{ route('kln.types.dokumen') }}" class="sima-nav__item {{ request()->routeIs('kln.types.dokumen*') ? 'active' : '' }}" data-title="Document Types">
            <i class="fas fa-file-medical sima-nav__icon"></i>
            <span>Document Types</span>
        </a>
    </div>

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