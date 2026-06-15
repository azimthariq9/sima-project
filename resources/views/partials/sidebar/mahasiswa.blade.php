{{-- ═══════════════════════════════════════════════
   SIMA — Sidebar Mahasiswa  (partials/sidebar/mahasiswa.blade.php)
   ═══════════════════════════════════════════════ --}}

{{-- ── BRAND ──────────────────────────────────── --}}
<a href="{{ route('mahasiswa.dashboard') }}" class="sima-brand" style="text-decoration:none">
    <div class="sima-brand__logo">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
        </svg>
    </div>
    <div>
        <div class="sima-brand__name">SIMA</div>
        <div class="sima-brand__sub">Universitas Gunadarma</div>
    </div>
</a>

{{-- ── NAV ─────────────────────────────────────── --}}
<nav class="sima-nav" style="flex:1;overflow-y:auto">

    {{-- Group: Utama --}}
    <div class="sima-nav__group-label">Utama</div>

    <a href="{{ route('mahasiswa.dashboard') }}"
       class="sima-nav__item {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}"
       data-title="Dashboard">
        <i class="fas fa-house-chimney sima-nav__icon"></i>
        <span>Dashboard</span>
    </a>

    <a href="{{ route('mahasiswa.profile') }}"
       class="sima-nav__item {{ request()->routeIs('mahasiswa.profile') ? 'active' : '' }}"
       data-title="Biodata">
        <i class="fas fa-circle-user sima-nav__icon"></i>
        <span>Biodata &amp; Profil</span>
    </a>

    {{-- Group: Akademik --}}
    <div class="sima-nav__group-label" style="margin-top:8px">Akademik</div>

    {{-- Schedules dropdown --}}
    @php $scheduleActive = request()->routeIs('mahasiswa.jadwal*'); @endphp

    <button class="sima-nav__item {{ $scheduleActive ? 'active' : '' }}"
            onclick="toggleNav(this)"
            data-title="Jadwal">
        <i class="fas fa-calendar-days sima-nav__icon"></i>
        <span>Jadwal</span>
        <i class="fas fa-chevron-right sima-nav__chevron" style="transition:transform .2s;{{ $scheduleActive ? 'transform:rotate(90deg)' : '' }}"></i>
    </button>

    <div class="sima-nav__sub {{ $scheduleActive ? 'open' : '' }}">
        <a href="{{ route('mahasiswa.jadwal', ['tipe' => 'bipa']) }}"
           class="sima-nav__sub-item {{ request()->is('*jadwal*') && request()->get('tipe') === 'bipa' ? 'active' : '' }}">
            <i class="fas fa-language" style="width:14px;margin-right:6px;font-size:11px"></i> BIPA
        </a>
        <a href="{{ route('mahasiswa.jadwal', ['tipe' => 'kuliah']) }}"
           class="sima-nav__sub-item {{ request()->is('*jadwal*') && request()->get('tipe') === 'kuliah' ? 'active' : '' }}">
            <i class="fas fa-book-open" style="width:14px;margin-right:6px;font-size:11px"></i> Perkuliahan
        </a>
        <a href="{{ route('mahasiswa.jadwal', ['tipe' => 'kln']) }}"
           class="sima-nav__sub-item {{ request()->is('*jadwal*') && request()->get('tipe') === 'kln' ? 'active' : '' }}">
            <i class="fas fa-globe" style="width:14px;margin-right:6px;font-size:11px"></i> KLN
        </a>
    </div>

    <a href="#"
       class="sima-nav__item {{ request()->routeIs('mahasiswa.absensi*') ? 'active' : '' }}"
       data-title="Kehadiran">
        <i class="fas fa-clipboard-check sima-nav__icon"></i>
        <span>Detail Kehadiran</span>
    </a>

    <a href="{{ route('mahasiswa.analytics') }}"
       class="sima-nav__item {{ request()->routeIs('mahasiswa.analytics') ? 'active' : '' }}"
       data-title="Analitik">
        <i class="fas fa-chart-line sima-nav__icon"></i>
        <span>Analitik</span>
    </a>

    {{-- Group: Dokumen --}}
    <div class="sima-nav__group-label" style="margin-top:8px">Dokumen</div>

    <a href="{{ route('mahasiswa.request.create') }}"
       class="sima-nav__item {{ request()->routeIs('mahasiswa.request.*') ? 'active' : '' }}"
       data-title="Dokumen">
        <i class="fas fa-folder-open sima-nav__icon"></i>
        <span>Dokumen &amp; Request</span>
    </a>

    {{-- Group: Informasi --}}
    <div class="sima-nav__group-label" style="margin-top:8px">Informasi</div>

    <a href="{{ route('mahasiswa.announcement') }}"
       class="sima-nav__item {{ request()->routeIs('mahasiswa.announcement*') ? 'active' : '' }}"
       data-title="Pengumuman">
        <i class="fas fa-bullhorn sima-nav__icon"></i>
        <span>Pengumuman</span>
    </a>

    <a href="{{ route('mahasiswa.notifikasi') }}"
       class="sima-nav__item {{ request()->routeIs('mahasiswa.notifikasi') ? 'active' : '' }}"
       data-title="Notifikasi">
        <i class="fas fa-bell sima-nav__icon"></i>
        <span>Notifikasi</span>
        @if(isset($unreadNotifCount) && $unreadNotifCount > 0)
            <span class="sima-nav__badge">{{ $unreadNotifCount > 9 ? '9+' : $unreadNotifCount }}</span>
        @endif
    </a>

</nav>

{{-- ── FOOTER ──────────────────────────────────── --}}
<div class="sima-sidebar__foot">
    {{-- User mini info --}}
    <div class="sima-sidebar__user">
        <div class="sima-avatar" style="width:32px;height:32px;border-radius:8px;font-size:11px;flex-shrink:0">
            {{ strtoupper(substr($userIdentifier ?? auth()->user()->name ?? 'M', 0, 1)) }}
        </div>
        <div style="min-width:0;flex:1">
            <div style="font-size:12px;font-weight:600;color:#1f2937;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                {{ auth()->user()->name ?? 'Mahasiswa' }}
            </div>
            <div style="font-size:10.5px;color:#6b7280">
                {{ optional($mahasiswa ?? null, fn($m) => $m->npm) ?? auth()->user()->email }}
            </div>
        </div>
        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" title="Logout"
                style="width:30px;height:30px;border:1px solid rgba(0,0,0,.1);border-radius:7px;background:rgba(220,38,38,.06);color:#dc2626;display:grid;place-items:center;cursor:pointer;font-size:12px;flex-shrink:0;transition:all .15s">
                <i class="fas fa-power-off"></i>
            </button>
        </form>
    </div>
</div>

{{-- ── EXTRA STYLES FOR SIDEBAR ───────────────── --}}
<style>
.sima-nav__group-label {
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: rgba(31,41,55,.45);
    padding: 10px 14px 4px;
}

.sima-nav__badge {
    margin-left: auto;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 100px;
    background: #dc2626;
    color: white;
    font-size: 9px;
    font-weight: 700;
    display: grid;
    place-items: center;
    flex-shrink: 0;
}

.sima-nav__sub.open { display: block !important; }
.sima-nav__sub      { display: none; }

.sima-sidebar__user {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 8px 10px;
    background: rgba(108,143,255,.07);
    border-radius: 12px;
}

/* Collapse state — hide group labels */
.sima-sidebar.collapsed .sima-nav__group-label { display: none; }
.sima-sidebar.collapsed .sima-nav__badge {
    position: absolute;
    top: 4px; right: 4px;
    min-width: 14px; height: 14px;
    font-size: 8px;
}
.sima-sidebar.collapsed .sima-sidebar__user > *:not(.sima-avatar) { display: none; }
.sima-sidebar.collapsed .sima-sidebar__foot { justify-content: center; }
.sima-sidebar.collapsed .sima-sidebar__user { justify-content: center; padding: 8px; }
</style>
