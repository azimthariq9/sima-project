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
            <div class="sima-sidebar__subtitle">Admin Jurusan</div>
        </div>
    </div>
</div>

{{-- =========================
   NAVIGATION
========================= --}}
<div class="sima-nav">

    <a href="{{ route('jurusan.dashboard') }}"
       class="sima-nav__item {{ request()->routeIs('jurusan.dashboard') ? 'active' : '' }}"
       data-title="Dashboard">
        <i class="fas fa-home sima-nav__icon"></i>
        <span>Dashboard</span>
    </a>

    {{-- DOSEN --}}
    <a href="{{ route('jurusan.dosen.page') }}"
       class="sima-nav__item {{ request()->routeIs('jurusan.dosen.*') ? 'active' : '' }}"
       data-title="Dosen">
        <i class="fas fa-chalkboard-teacher sima-nav__icon"></i>
        <span>Dosen</span>
    </a>

    {{-- MAHASISWA --}}
    <a href="{{ route('jurusan.mahasiswa.page') }}"
       class="sima-nav__item {{ request()->routeIs('jurusan.mahasiswa.*') ? 'active' : '' }}"
       data-title="Mahasiswa">
        <i class="fas fa-user-graduate sima-nav__icon"></i>
        <span>Mahasiswa</span>
    </a>

    {{-- MATAKULIAH --}}
    <a href="{{ route('jurusan.matakuliah.page') }}"
       class="sima-nav__item {{ request()->routeIs('jurusan.matakuliah.*') ? 'active' : '' }}"
       data-title="Matakuliah">
        <i class="fas fa-book sima-nav__icon"></i>
        <span>Mata Kuliah</span>
    </a>

    {{-- KELAS --}}
    <a href="{{ route('jurusan.kelas.page') }}"
       class="sima-nav__item {{ request()->routeIs('jurusan.kelas.*') ? 'active' : '' }}"
       data-title="Kelas">
        <i class="fas fa-door-open sima-nav__icon"></i>
        <span>Kelas</span>
    </a>

    {{-- JADWAL --}}
    <a href="{{ route('jurusan.jadwal.page') }}"
       class="sima-nav__item {{ request()->routeIs('jurusan.jadwal.*') ? 'active' : '' }}"
       data-title="Jadwal">
        <i class="fas fa-calendar-alt sima-nav__icon"></i>
        <span>Jadwal</span>
    </a>

    {{-- PENGUMUMAN --}}
    <a href="#"
       class="sima-nav__item"
       data-title="Pengumuman">
        <i class="fas fa-megaphone sima-nav__icon"></i>
        <span>Pengumuman</span>
    </a>

    {{-- NOTIFIKASI --}}
    <a href="{{ route('jurusan.notifikasi') }}"
       class="sima-nav__item {{ request()->routeIs('jurusan.notifikasi') ? 'active' : '' }}"
       data-title="Notifikasi">
        <i class="fas fa-bell sima-nav__icon"></i>
        <span>Notifikasi</span>
    </a>

    {{-- PROFIL --}}
    <a href="{{ route('jurusan.profil') }}"
       class="sima-nav__item {{ request()->routeIs('jurusan.profil') ? 'active' : '' }}"
       data-title="Profil">
        <i class="fas fa-user-circle sima-nav__icon"></i>
        <span>Profil</span>
    </a>

    {{-- ========================= SETTINGS ========================= --}}
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