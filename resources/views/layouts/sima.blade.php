@extends('adminlte::page')

@section('title', config('adminlte.title', 'SIMA') . ' | ' . trim($__env->yieldContent('page_title')))


@section('content_header')
<div class="sima-page-header">
    <div class="sima-page-header__left">
        <div class="sima-breadcrumb">
            <span class="sima-breadcrumb__root">SIMA</span>
            <i class="fas fa-chevron-right sima-breadcrumb__sep"></i>
            <span class="sima-breadcrumb__current">@yield('page_title')</span>
        </div>
        <h1 class="sima-page-header__title">@yield('page_title')</h1>
        <p class="sima-page-header__sub">@yield('page_subtitle')</p>
    </div>
    <div class="sima-page-header__right">
        @php $role = strtoupper(auth()->user()->role ?? 'USER'); @endphp
        <div class="sima-role-badge sima-role-badge--{{ strtolower(auth()->user()->role ?? 'user') }}">
            <i class="fas fa-shield-alt"></i>
            <span>{{ $role }}</span>
        </div>
        <div class="sima-datetime" id="simaDatetime"></div>
    </div>
</div>
@endsection

@section('content')
<div class="sima-content">
    @yield('main_content')
</div>
@endsection

@section('css')
{{-- Google Fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,600;1,9..144,300&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
/* =============================================
   SIMA DESIGN SYSTEM — PREMIUM CORPORATE ELITE
   ============================================= */

:root {
    /* Core Palette */
    --sima-navy:        #080F1E;
    --sima-navy-mid:    #0D1B2A;
    --sima-navy-light:  #132236;
    --sima-slate:       #1B3256;
    --sima-gold:        #C4973A;
    --sima-gold-light:  #E0B86A;
    --sima-gold-pale:   #F5E6C8;

    /* Semantic */
    --sima-blue:        #2563EB;
    --sima-blue-soft:   #EFF6FF;
    --sima-teal:        #0D9488;
    --sima-teal-soft:   #F0FDFA;
    --sima-red:         #DC2626;
    --sima-red-soft:    #FEF2F2;
    --sima-amber:       #D97706;
    --sima-amber-soft:  #FFFBEB;
    --sima-emerald:     #059669;
    --sima-emerald-soft:#ECFDF5;
    --sima-purple:      #7C3AED;
    --sima-purple-soft: #F5F3FF;

    /* Surface */
    --sima-bg:          #EEF2F7;
    --sima-surface:     #FFFFFF;
    --sima-surface-2:   #F8FAFC;
    --sima-border:      #E2E8F0;
    --sima-border-soft: #F1F5F9;

    /* Text */
    --sima-text-primary:   #0B1628;
    --sima-text-secondary: #475569;
    --sima-text-muted:     #94A3B8;
    --sima-text-inverse:   #FFFFFF;

    /* Typography */
    --font-display: 'Fraunces', Georgia, serif;
    --font-body:    'DM Sans', system-ui, sans-serif;
    --font-mono:    'DM Mono', monospace;

    /* Shadows */
    --shadow-xs:  0 1px 2px rgba(8,15,30,0.04);
    --shadow-sm:  0 2px 8px rgba(8,15,30,0.06), 0 1px 3px rgba(8,15,30,0.04);
    --shadow-md:  0 8px 24px rgba(8,15,30,0.08), 0 2px 8px rgba(8,15,30,0.04);
    --shadow-lg:  0 20px 48px rgba(8,15,30,0.12), 0 8px 16px rgba(8,15,30,0.06);
    --shadow-gold:0 8px 32px rgba(196,151,58,0.20);

    /* Transitions */
    --ease-out-expo: cubic-bezier(0.16, 1, 0.3, 1);
    --ease-in-out:   cubic-bezier(0.4, 0, 0.2, 1);
}

/* ─── GLOBAL RESET ─────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; }

body {
    font-family: var(--font-body);
    background: var(--sima-bg);
    color: var(--sima-text-primary);
    -webkit-font-smoothing: antialiased;
}

/* ─── SIDEBAR ─────────────────────────────────── */
.main-sidebar {
    background: var(--sima-navy) !important;
    border-right: none !important;
    box-shadow: 4px 0 32px rgba(0,0,0,0.20);
}

.main-sidebar::before {
    content: '';
    position: absolute;
    top: 0; right: 0;
    width: 1px; height: 100%;
    background: linear-gradient(180deg, var(--sima-gold) 0%, transparent 60%);
    opacity: 0.4;
}

/* Brand/Logo */
.brand-link {
    background: var(--sima-navy) !important;
    border-bottom: 1px solid rgba(196,151,58,0.20) !important;
    padding: 18px 20px !important;
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    text-decoration: none !important;
}

.brand-link .brand-text {
    font-family: var(--font-display) !important;
    font-size: 22px !important;
    font-weight: 600 !important;
    color: white !important;
    letter-spacing: 0.02em;
}

.brand-link .brand-text b { color: var(--sima-gold) !important; }

.brand-link::after {
    content: 'GUNADARMA';
    font-family: var(--font-body);
    font-size: 9px;
    font-weight: 500;
    letter-spacing: 0.18em;
    color: var(--sima-gold);
    opacity: 0.7;
    margin-left: auto;
}

/* Sidebar User Panel */
.user-panel {
    background: rgba(255,255,255,0.03) !important;
    border-bottom: 1px solid rgba(255,255,255,0.06) !important;
    padding: 16px 20px !important;
}

.user-panel .info a {
    font-family: var(--font-body) !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: rgba(255,255,255,0.85) !important;
}

/* Sidebar Nav Headers */
.nav-sidebar .nav-header {
    font-family: var(--font-body) !important;
    font-size: 9px !important;
    font-weight: 600 !important;
    letter-spacing: 0.20em !important;
    color: var(--sima-gold) !important;
    opacity: 0.8 !important;
    padding: 18px 20px 6px !important;
    text-transform: uppercase;
}

/* Sidebar Nav Items */
.nav-sidebar .nav-item .nav-link {
    font-family: var(--font-body) !important;
    font-size: 13px !important;
    font-weight: 400 !important;
    color: rgba(255,255,255,0.60) !important;
    border-radius: 8px !important;
    margin: 1px 10px !important;
    padding: 10px 14px !important;
    transition: all 0.20s var(--ease-in-out) !important;
    display: flex !important;
    align-items: center !important;
    gap: 10px;
    position: relative;
    overflow: hidden;
}

.nav-sidebar .nav-item .nav-link .nav-icon {
    width: 16px;
    text-align: center;
    font-size: 13px !important;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.nav-sidebar .nav-item .nav-link:hover {
    background: rgba(255,255,255,0.07) !important;
    color: rgba(255,255,255,0.90) !important;
}

.nav-sidebar .nav-item .nav-link:hover .nav-icon { opacity: 1; }

.nav-sidebar .nav-item .nav-link.active {
    background: linear-gradient(135deg, var(--sima-gold), #A67C30) !important;
    color: white !important;
    font-weight: 500 !important;
    box-shadow: 0 4px 14px rgba(196,151,58,0.30) !important;
}

.nav-sidebar .nav-item .nav-link.active .nav-icon { opacity: 1; }

/* ─── TOPBAR / NAVBAR ─────────────────────────── */
.main-header.navbar {
    background: var(--sima-surface) !important;
    border-bottom: 1px solid var(--sima-border) !important;
    box-shadow: var(--shadow-sm) !important;
    padding: 0 24px !important;
    height: 60px;
}

.navbar-nav .nav-link {
    color: var(--sima-text-secondary) !important;
    font-family: var(--font-body);
    font-size: 13px;
    transition: color 0.15s;
}

.navbar-nav .nav-link:hover { color: var(--sima-text-primary) !important; }

/* Sidebar Toggle */
[data-widget="pushmenu"] {
    color: var(--sima-text-muted) !important;
    font-size: 16px !important;
}

/* ─── CONTENT WRAPPER ──────────────────────────── */
.content-wrapper {
    background: var(--sima-bg) !important;
    padding-top: 0 !important;
}

/* ─── PAGE HEADER ─────────────────────────────── */
.content-header {
    padding: 20px 28px 0 !important;
    background: transparent !important;
}

.sima-page-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    padding-bottom: 4px;
}

.sima-breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 6px;
}

.sima-breadcrumb__root {
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 0.10em;
    color: var(--sima-gold);
    text-transform: uppercase;
}

.sima-breadcrumb__sep {
    font-size: 8px;
    color: var(--sima-text-muted);
}

.sima-breadcrumb__current {
    font-size: 11px;
    color: var(--sima-text-muted);
}

.sima-page-header__title {
    font-family: var(--font-display);
    font-size: 28px;
    font-weight: 600;
    color: var(--sima-text-primary);
    margin: 0 0 4px;
    line-height: 1.2;
    letter-spacing: -0.02em;
}

.sima-page-header__sub {
    font-size: 13px;
    color: var(--sima-text-muted);
    margin: 0;
    font-weight: 400;
}

.sima-page-header__right {
    display: flex;
    align-items: center;
    gap: 16px;
}

.sima-role-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 7px 14px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.10em;
    text-transform: uppercase;
}

.sima-role-badge--mahasiswa { background: var(--sima-blue-soft); color: var(--sima-blue); }
.sima-role-badge--kln       { background: var(--sima-teal-soft); color: var(--sima-teal); }
.sima-role-badge--jurusan   { background: var(--sima-purple-soft); color: var(--sima-purple); }
.sima-role-badge--bipa      { background: var(--sima-amber-soft); color: var(--sima-amber); }
.sima-role-badge--admin     { background: linear-gradient(135deg,#FEF9EC,#FEF3CD); color: var(--sima-gold); border: 1px solid rgba(196,151,58,0.25); }
.sima-role-badge--user      { background: var(--sima-surface-2); color: var(--sima-text-muted); }

.sima-datetime {
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--sima-text-muted);
    text-align: right;
    line-height: 1.5;
}

/* ─── CONTENT AREA ────────────────────────────── */
.content, .sima-content {
    padding: 20px 28px 32px !important;
}

/* ─── STAT CARDS ─────────────────────────────── */
.sima-stat {
    background: var(--sima-surface);
    border-radius: 16px;
    padding: 24px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--sima-border-soft);
    position: relative;
    overflow: hidden;
    transition: transform 0.25s var(--ease-out-expo), box-shadow 0.25s var(--ease-out-expo);
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.sima-stat:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
}

.sima-stat__accent {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 3px;
    border-radius: 16px 16px 0 0;
}

.sima-stat__accent--blue     { background: linear-gradient(90deg, var(--sima-blue), #60A5FA); }
.sima-stat__accent--teal     { background: linear-gradient(90deg, var(--sima-teal), #2DD4BF); }
.sima-stat__accent--red      { background: linear-gradient(90deg, var(--sima-red), #F87171); }
.sima-stat__accent--amber    { background: linear-gradient(90deg, var(--sima-amber), #FCD34D); }
.sima-stat__accent--emerald  { background: linear-gradient(90deg, var(--sima-emerald), #34D399); }
.sima-stat__accent--purple   { background: linear-gradient(90deg, var(--sima-purple), #A78BFA); }
.sima-stat__accent--gold     { background: linear-gradient(90deg, var(--sima-gold), var(--sima-gold-light)); }
.sima-stat__accent--navy     { background: linear-gradient(90deg, var(--sima-slate), #2563EB); }

.sima-stat__icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    margin-bottom: 12px;
    flex-shrink: 0;
}

.sima-stat__icon--blue    { background: var(--sima-blue-soft);   color: var(--sima-blue); }
.sima-stat__icon--teal    { background: var(--sima-teal-soft);   color: var(--sima-teal); }
.sima-stat__icon--red     { background: var(--sima-red-soft);    color: var(--sima-red); }
.sima-stat__icon--amber   { background: var(--sima-amber-soft);  color: var(--sima-amber); }
.sima-stat__icon--emerald { background: var(--sima-emerald-soft);color: var(--sima-emerald); }
.sima-stat__icon--purple  { background: var(--sima-purple-soft); color: var(--sima-purple); }
.sima-stat__icon--gold    { background: #FEF9EC;                 color: var(--sima-gold); }
.sima-stat__icon--navy    { background: #EFF6FF;                 color: var(--sima-slate); }

.sima-stat__label {
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--sima-text-muted);
}

.sima-stat__value {
    font-family: var(--font-display);
    font-size: 36px;
    font-weight: 600;
    color: var(--sima-text-primary);
    line-height: 1;
    letter-spacing: -0.03em;
    margin: 4px 0;
}

.sima-stat__delta {
    font-size: 12px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 4px;
}

.sima-stat__delta--up   { color: var(--sima-emerald); }
.sima-stat__delta--down { color: var(--sima-red); }
.sima-stat__delta--flat { color: var(--sima-text-muted); }

/* ─── CARDS ─────────────────────────────────────── */
.sima-card {
    background: var(--sima-surface);
    border-radius: 16px;
    border: 1px solid var(--sima-border-soft);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

.sima-card__header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--sima-border-soft);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.sima-card__title {
    font-family: var(--font-display);
    font-size: 17px;
    font-weight: 600;
    color: var(--sima-text-primary);
    margin: 0;
    letter-spacing: -0.01em;
}

.sima-card__subtitle {
    font-size: 12px;
    color: var(--sima-text-muted);
    margin-top: 2px;
}

.sima-card__action {
    font-size: 12px;
    font-weight: 500;
    color: var(--sima-blue);
    text-decoration: none;
    padding: 6px 12px;
    border-radius: 8px;
    background: var(--sima-blue-soft);
    transition: all 0.15s;
}

.sima-card__action:hover {
    background: var(--sima-blue);
    color: white;
    text-decoration: none;
}

.sima-card__body { padding: 24px; }

/* ─── TABLE ─────────────────────────────────────── */
.sima-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.sima-table thead th {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.10em;
    text-transform: uppercase;
    color: var(--sima-text-muted);
    padding: 12px 16px;
    background: var(--sima-surface-2);
    border-bottom: 1px solid var(--sima-border);
    white-space: nowrap;
}

.sima-table thead th:first-child { border-radius: 10px 0 0 0; }
.sima-table thead th:last-child  { border-radius: 0 10px 0 0; }

.sima-table tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid var(--sima-border-soft);
    color: var(--sima-text-primary);
    vertical-align: middle;
}

.sima-table tbody tr:last-child td { border-bottom: none; }

.sima-table tbody tr {
    transition: background 0.15s;
}

.sima-table tbody tr:hover td {
    background: var(--sima-surface-2);
}

/* ─── BADGES & PILLS ─────────────────────────── */
.sima-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.sima-badge--blue    { background: var(--sima-blue-soft);   color: var(--sima-blue); }
.sima-badge--teal    { background: var(--sima-teal-soft);   color: var(--sima-teal); }
.sima-badge--red     { background: var(--sima-red-soft);    color: var(--sima-red); }
.sima-badge--amber   { background: var(--sima-amber-soft);  color: var(--sima-amber); }
.sima-badge--emerald { background: var(--sima-emerald-soft);color: var(--sima-emerald); }
.sima-badge--purple  { background: var(--sima-purple-soft); color: var(--sima-purple); }
.sima-badge--grey    { background: #F1F5F9; color: var(--sima-text-secondary); }

/* ─── TIMELINE ───────────────────────────────── */
.sima-timeline { list-style: none; padding: 0; margin: 0; }

.sima-timeline__item {
    display: flex;
    gap: 16px;
    padding: 0 0 20px;
    position: relative;
}

.sima-timeline__item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 17px;
    top: 36px;
    bottom: 0;
    width: 1px;
    background: var(--sima-border);
}

.sima-timeline__dot {
    width: 36px; height: 36px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}

.sima-timeline__content { flex: 1; padding-top: 4px; }

.sima-timeline__title {
    font-size: 13px;
    font-weight: 500;
    color: var(--sima-text-primary);
    margin-bottom: 2px;
}

.sima-timeline__time {
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--sima-text-muted);
}

/* ─── AVATAR ─────────────────────────────────── */
.sima-avatar {
    width: 34px; height: 34px;
    border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700;
    flex-shrink: 0;
}

/* ─── PROGRESS ───────────────────────────────── */
.sima-progress {
    height: 6px;
    border-radius: 99px;
    background: var(--sima-border);
    overflow: hidden;
}

.sima-progress__bar {
    height: 100%;
    border-radius: 99px;
    transition: width 1.2s var(--ease-out-expo);
}

/* ─── DIVIDER ────────────────────────────────── */
.sima-divider {
    height: 1px;
    background: var(--sima-border-soft);
    margin: 20px 0;
}

/* ─── ANNOUNCEMENT CARD ──────────────────────── */
.sima-announce {
    padding: 16px;
    border-radius: 12px;
    background: var(--sima-surface-2);
    border: 1px solid var(--sima-border-soft);
    margin-bottom: 10px;
    transition: border-color 0.15s;
}

.sima-announce:hover { border-color: var(--sima-border); }
.sima-announce:last-child { margin-bottom: 0; }

.sima-announce__title {
    font-size: 13px; font-weight: 600;
    color: var(--sima-text-primary);
    margin-bottom: 4px;
}

.sima-announce__body {
    font-size: 12px;
    color: var(--sima-text-secondary);
    line-height: 1.6;
    margin-bottom: 8px;
}

.sima-announce__meta {
    display: flex; align-items: center; gap: 10px;
    font-size: 11px; color: var(--sima-text-muted);
}

/* ─── SCHEDULE ITEM ─────────────────────────── */
.sima-schedule {
    display: flex;
    gap: 16px;
    align-items: flex-start;
    padding: 14px 0;
    border-bottom: 1px solid var(--sima-border-soft);
}

.sima-schedule:last-child { border-bottom: none; padding-bottom: 0; }

.sima-schedule__time {
    font-family: var(--font-mono);
    font-size: 12px;
    color: var(--sima-text-muted);
    min-width: 70px;
    padding-top: 2px;
}

.sima-schedule__dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    border: 2px solid;
    margin-top: 5px;
    flex-shrink: 0;
}

.sima-schedule__info { flex: 1; }

.sima-schedule__title {
    font-size: 13px; font-weight: 500;
    color: var(--sima-text-primary);
    margin-bottom: 2px;
}

.sima-schedule__meta {
    font-size: 12px; color: var(--sima-text-muted);
}

/* ─── CHART PLACEHOLDER ─────────────────────── */
.sima-chart-wrap {
    position: relative;
    width: 100%;
}

/* ─── DONUT STAT ─────────────────────────────── */
.sima-donut-wrap {
    display: flex;
    align-items: center;
    gap: 24px;
}

/* ─── QUICK ACTIONS ──────────────────────────── */
.sima-quick {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px 12px;
    border-radius: 14px;
    background: var(--sima-surface-2);
    border: 1px solid var(--sima-border-soft);
    text-decoration: none;
    transition: all 0.20s var(--ease-out-expo);
    text-align: center;
}

.sima-quick:hover {
    background: var(--sima-surface);
    border-color: var(--sima-border);
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
    text-decoration: none;
}

.sima-quick__icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    margin-bottom: 10px;
}

.sima-quick__label {
    font-size: 12px; font-weight: 500;
    color: var(--sima-text-secondary);
}

/* ─── ALERT STRIPE ───────────────────────────── */
.sima-alert {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 18px;
    border-radius: 12px;
    margin-bottom: 10px;
    font-size: 13px;
    border: 1px solid transparent;
}

.sima-alert--red    { background: var(--sima-red-soft);    color: var(--sima-red);    border-color: #FED7D7; }
.sima-alert--amber  { background: var(--sima-amber-soft);  color: var(--sima-amber);  border-color: #FDE68A; }
.sima-alert--blue   { background: var(--sima-blue-soft);   color: var(--sima-blue);   border-color: #BFDBFE; }
.sima-alert--teal   { background: var(--sima-teal-soft);   color: var(--sima-teal);   border-color: #99F6E4; }
.sima-alert--emerald{ background: var(--sima-emerald-soft);color: var(--sima-emerald);border-color: #A7F3D0; }

/* ─── COUNTER ANIMATION ──────────────────────── */
.sima-stat__value[data-count] { display: block; }

/* ─── FOOTER ─────────────────────────────────── */
.main-footer {
    background: var(--sima-surface) !important;
    border-top: 1px solid var(--sima-border) !important;
    font-family: var(--font-body);
    font-size: 12px;
    color: var(--sima-text-muted) !important;
    padding: 14px 28px !important;
}

/* ─── RESPONSIVE ─────────────────────────────── */
@media (max-width: 768px) {
    .sima-page-header { flex-direction: column; align-items: flex-start; gap: 12px; }
    .sima-page-header__right { align-self: flex-start; }
    .content, .sima-content { padding: 16px !important; }
    .content-header { padding: 16px 16px 0 !important; }
}

/* ─── SCROLLBAR ──────────────────────────────── */
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--sima-border); border-radius: 99px; }
::-webkit-scrollbar-thumb:hover { background: var(--sima-text-muted); }

/* ─── LOAD ANIMATION ────────────────────────── */
@keyframes simaFadeUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}

.sima-fade { animation: simaFadeUp 0.5s var(--ease-out-expo) both; }
.sima-fade--1 { animation-delay: 0.05s; }
.sima-fade--2 { animation-delay: 0.10s; }
.sima-fade--3 { animation-delay: 0.15s; }
.sima-fade--4 { animation-delay: 0.20s; }
.sima-fade--5 { animation-delay: 0.25s; }
.sima-fade--6 { animation-delay: 0.30s; }
.sima-fade--7 { animation-delay: 0.35s; }
.sima-fade--8 { animation-delay: 0.40s; }

/* ─── OVERRIDE ADMINLTE DEFAULTS ─────────────── */
.small-box { border-radius: 16px !important; box-shadow: var(--shadow-sm) !important; }
.card { border-radius: 16px !important; border: 1px solid var(--sima-border-soft) !important; box-shadow: var(--shadow-sm) !important; }
.card-header { background: var(--sima-surface) !important; border-bottom-color: var(--sima-border-soft) !important; }
a { color: var(--sima-blue); }
a:hover { color: var(--sima-text-primary); }
</style>
@endsection

@section('js')
<script>
/* ─── DATETIME CLOCK ─────────────────────────── */
(function() {
    const el = document.getElementById('simaDatetime');
    if (!el) return;

    function pad(n) { return String(n).padStart(2, '0'); }

    function tick() {
        const now = new Date();
        const days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        el.innerHTML =
            `<span style="display:block">${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}</span>` +
            `<span style="display:block;text-align:right">${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())} WIB</span>`;
    }
    tick();
    setInterval(tick, 1000);
})();

/* ─── COUNTER ANIMATION ─────────────────────── */
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('[data-count]');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseInt(el.getAttribute('data-count'));
                const duration = 1000;
                const step = Math.ceil(target / (duration / 16));
                let current = 0;
                const timer = setInterval(() => {
                    current = Math.min(current + step, target);
                    el.textContent = current.toLocaleString();
                    if (current >= target) clearInterval(timer);
                }, 16);
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.3 });

    counters.forEach(c => observer.observe(c));
});
</script>
@yield('page_js')
@endsection