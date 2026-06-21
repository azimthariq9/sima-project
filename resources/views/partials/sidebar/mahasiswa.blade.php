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

    <a href="{{ route('mahasiswa.jadwal') }}"
       class="sima-nav__item {{ request()->routeIs('mahasiswa.jadwal') ? 'active' : '' }}"
       data-title="Jadwal">
        <i class="fas fa-calendar-days sima-nav__icon"></i>
        <span>Jadwal</span>
    </a>

    <a href="{{ route('mahasiswa.kehadiran') }}"
       class="sima-nav__item {{ request()->routeIs('mahasiswa.kehadiran*') ? 'active' : '' }}"
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

    <a href="{{ route('mahasiswa.dokumen.index') }}"
       class="sima-nav__item {{ request()->routeIs('mahasiswa.dokumen.*') || request()->routeIs('mahasiswa.request.*') ? 'active' : '' }}"
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

    @php
        $__mhsId = \Illuminate\Support\Facades\DB::table('mahasiswa')
            ->where('user_id', auth()->id())
            ->value('id');
        $__unread = $__mhsId
            ? \Illuminate\Support\Facades\DB::table('notification_mahasiswa')
                ->where('mahasiswa_id', $__mhsId)
                ->where('is_read', false)
                ->count()
            : 0;
    @endphp
    <a href="{{ route('mahasiswa.notifikasi') }}"
       class="sima-nav__item {{ request()->routeIs('mahasiswa.notifikasi') ? 'active' : '' }}"
       data-title="Notifikasi">
        <i class="fas fa-bell sima-nav__icon"></i>
        <span>Notifikasi</span>
        @if($__unread > 0)
            <span class="sima-nav__badge">{{ $__unread > 9 ? '9+' : $__unread }}</span>
        @endif
    </a>

</nav>

{{-- ── FOOTER ──────────────────────────────────── --}}
<div class="sima-sidebar__foot">
    <form method="POST" action="{{ route('logout') }}" style="width:100%">
        @csrf
        <button type="submit"
                style="width:100%;padding:9px 14px;border:1px solid rgba(220,38,38,.2);border-radius:10px;background:rgba(220,38,38,.06);color:#dc2626;display:flex;align-items:center;justify-content:center;gap:8px;cursor:pointer;font-size:13px;font-weight:600;transition:all .15s"
                onmouseover="this.style.background='rgba(220,38,38,.12)'"
                onmouseout="this.style.background='rgba(220,38,38,.06)'">
            <i class="fas fa-power-off"></i>
            <span class="sima-sidebar-label">Logout</span>
        </button>
    </form>
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

/* Collapse state — hide group labels */
.sima-sidebar.collapsed .sima-nav__group-label { display: none; }
.sima-sidebar.collapsed .sima-nav__badge {
    position: absolute;
    top: 4px; right: 4px;
    min-width: 14px; height: 14px;
    font-size: 8px;
}
.sima-sidebar.collapsed .sima-sidebar__foot { justify-content: center; }
.sima-sidebar.collapsed .sima-sidebar__foot .sima-sidebar-label { display: none; }
</style>
