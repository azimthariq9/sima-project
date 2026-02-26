<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('page_title', 'SIMA') — SIMA</title>

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Sora:wght@400;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<!-- Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- Bootstrap grid only -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

<style>
/* ═══════════════════════════════════════════════════════
   SIMA DESIGN SYSTEM — v2.0
   ═══════════════════════════════════════════════════════ */

:root {
    /* Colors */
    --c-bg:          #f4f5f9;
    --c-surface:     #ffffff;
    --c-surface-2:   #f8f9fc;
    --c-border:      #e5e7ef;
    --c-border-soft: #eef0f6;

    /* Text */
    --c-text-1:  #111827;
    --c-text-2:  #374151;
    --c-text-3:  #9ca3af;
    --c-text-4:  #d1d5db;

    /* Brand / Accent */
    --c-accent:      #6c8fff;
    --c-accent-2:    #a78bfa;
    --c-accent-light:#eff3ff;

    /* Semantic */
    --c-blue:    #2563EB;
    --c-blue-lt: #EFF6FF;
    --c-green:   #059669;
    --c-green-lt:#ECFDF5;
    --c-red:     #DC2626;
    --c-red-lt:  #FEF2F2;
    --c-amber:   #D97706;
    --c-amber-lt:#FFFBEB;
    --c-purple:  #7C3AED;
    --c-purple-lt:#F5F3FF;
    --c-teal:    #0D9488;
    --c-teal-lt: #F0FDFA;
    --c-navy:    #0D1B2A;
    --c-navy-lt: #EFF4FF;
    --c-gold:    #D97706;

    /* Sidebar */
    --sidebar-w:    240px;
    --sidebar-bg:   #ffffff;
    --sidebar-border:#eef0f6;

    /* Typography */
    --f-body:    'Plus Jakarta Sans', sans-serif;
    --f-display: 'Sora', sans-serif;
    --f-mono:    'JetBrains Mono', monospace;

    /* Misc */
    --radius:    12px;
    --radius-sm: 8px;
    --radius-lg: 16px;
    --shadow-sm: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
    --shadow:    0 4px 16px rgba(0,0,0,.07), 0 1px 3px rgba(0,0,0,.04);
    --shadow-md: 0 8px 32px rgba(0,0,0,.09), 0 2px 8px rgba(0,0,0,.04);
    --transition: .18s ease;
}

/* ── RESET & BASE ─────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html, body {
    height: 100%;
    font-family: var(--f-body);
    font-size: 14px;
    color: var(--c-text-1);
    background: var(--c-bg);
    -webkit-font-smoothing: antialiased;
    line-height: 1.5;
}

/* ── LAYOUT SHELL ─────────────────────────────────── */
.sima-shell {
    display: flex;
    min-height: 100vh;
}

/* COLLAPSE MODE */
.sima-sidebar.collapsed {
    width: 78px;
}

.sima-sidebar.collapsed 
.sima-nav__item {
    position: relative;
    overflow: hidden;
}

.sima-nav__item::after {
    content: '';
    position: absolute;
    left: -100%;
    top: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        120deg,
        transparent,
        rgba(255,255,255,.2),
        transparent
    );
    transition: .6s;
}

.sima-nav__item:hover::after {
    left: 100%;
}

.sima-nav__item.active {
    box-shadow: 0 6px 18px rgba(108,143,255,.25);
}

body.dark .sima-card {
    background: #1e293b;
    border: 1px solid #334155;
}

body.dark .sima-topbar {
    background: #0f172a;
    border-bottom: 1px solid #334155;
}
.sima-sidebar.collapsed .sima-sidebar__subtitle {
    display: none;
}

.sima-sidebar.collapsed .sima-sidebar__logo-wrapper {
    margin: 0 auto;
}

.sima-sidebar.collapsed .sima-nav__item {
    justify-content: center;
}
/* ═════════ SIDEBAR BASE ═════════ */
/* ===============================
   SIDEBAR — PREMIUM MODE
================================ */

.sima-sidebar {
    width: var(--sidebar-w);
    background: linear-gradient(180deg, #6c5ce7 0%, #5a4bd6 40%, #ffffff 100%);
    border-right: 1px solid var(--sidebar-border);
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    display: flex;
    flex-direction: column;
    transition: width .25s ease, transform .25s ease;
    z-index: 200;
}

/* TEXT FIX */
.sima-sidebar .sima-nav__item {
    color: #1f2937;
}

body.dark .sima-sidebar {
    background: linear-gradient(180deg, #1e1b4b 0%, #0f172a 100%);
}

body.dark .sima-sidebar .sima-nav__item {
    color: #e2e8f0;
}

body.dark .sima-sidebar .sima-nav__item:hover {
    background: rgba(255,255,255,.08);
}

body.dark .sima-sidebar .sima-nav__item.active {
    background: rgba(167,139,250,.25);
    color: #fff;
}
/* Avatar fix */
.sima-avatar {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, #6c8fff, #a78bfa);
    font-size: 13px;
}
.sima-sidebar__header {
    padding: 22px 18px 18px;
    border-bottom: 1px solid #e5e7ef;
}

.sima-sidebar__brand {
    display: flex;
    align-items: center;
    gap: 12px;
}

.sima-sidebar__logo {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    object-fit: contain;
    background: white;
    padding: 4px;
    box-shadow: 0 2px 6px rgba(0,0,0,.08);
}

.sima-sidebar__title {
    font-size: 17px;
    font-weight: 700;
    color: #111827;
    line-height: 1.2;
}

.sima-sidebar__subtitle {
    font-size: 12px;
    color: #6b7280;
}

.sima-sidebar__logo-wrapper {
    background: #f3f4f6;
    padding: 6px;
    border-radius: 14px;
}
/* Dark Mode */
body.dark {
    --c-bg: #0f172a;
    --c-surface: #1e293b;
    --c-text-1: #f1f5f9;
    --c-text-2: #cbd5e1;
    --c-border-soft: #334155;
}

body.dark .sima-sidebar {
    background: linear-gradient(180deg, #2d1b69 0%, #1e293b 60%, #0f172a 100%);
}
/* Brand */
.sima-brand {
    padding: 22px 20px 18px;
    display: flex;
    align-items: center;
    gap: 11px;
    border-bottom: 1px solid var(--c-border-soft);
    text-decoration: none;
    flex-shrink: 0;
}
.sima-brand__logo {
    width: 38px; height: 38px;
    background: linear-gradient(135deg, var(--c-accent), var(--c-accent-2));
    border-radius: 10px;
    display: grid; place-items: center;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(108,143,255,.3);
}
.sima-brand__logo svg { color: white; }
.sima-brand__text {}
.sima-brand__name {
    font-family: var(--f-display);
    font-size: 16px;
    font-weight: 700;
    color: var(--c-text-1);
    letter-spacing: -.02em;
    line-height: 1.1;
}
.sima-brand__sub {
    font-size: 10.5px;
    color: var(--c-text-3);
    font-weight: 400;
    letter-spacing: .01em;
}

/* Nav */
.sima-nav {
    padding: 18px 14px;
}

.sima-nav__item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border-radius: 14px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    color: #1f2937;
    margin-bottom: 6px;
    transition: all .2s ease;
}

.sima-nav__item:hover {
    background: #ececf4;
}

.sima-nav__item.active {
    background: #dcd3f5;
    color: #4c3bbf;
    font-weight: 600;
}

.sima-nav__icon {
    width: 20px;
    font-size: 16px;
    color: #111827;
}

.sima-nav__sub {
    padding-left: 36px;
    margin-top: 6px;
}

.sima-nav__sub-item {
    display: block;
    padding: 6px 0;
    font-size: 13px;
    color: #6b7280;
    text-decoration: none;
}

.sima-nav__sub-item:hover {
    color: #4c3bbf;
}

.sima-nav__chevron {
    margin-left: auto;
    font-size: 12px;
}
.sima-nav__sub-item.active { color: var(--c-accent); font-weight: 500; }
.sima-nav__sub-item.active::before { background: var(--c-accent); }

/* Sidebar footer */
.sima-sidebar__foot {
    padding: 12px 10px;
    border-top: 1px solid var(--c-border-soft);
    flex-shrink: 0;
}

/* ═══════════════════════════════════════════════════
   MAIN CONTENT
   ═══════════════════════════════════════════════════ */
.sima-main {
    flex: 1;
    margin-left: var(--sidebar-w);
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* Topbar */
.sima-topbar {
    height: 60px;
    background: var(--c-surface);
    border-bottom: 1px solid var(--c-border-soft);
    display: flex;
    align-items: center;
    padding: 0 28px;
    gap: 16px;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: var(--shadow-sm);
}
.sima-topbar__hamburger {
    display: none;
    background: none;
    border: none;
    cursor: pointer;
    color: var(--c-text-2);
    font-size: 18px;
    padding: 4px;
}
.sima-topbar__breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--c-text-3);
    font-weight: 400;
}
.sima-topbar__breadcrumb span { color: var(--c-text-2); font-weight: 500; }
.sima-topbar__section {
    font-size: 13px;
    color: var(--c-text-3);
}
.sima-topbar__right {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 8px;
}
.sima-topbar__icon-btn {
    width: 36px; height: 36px;
    border-radius: 9px;
    background: none;
    border: 1px solid var(--c-border);
    display: grid; place-items: center;
    cursor: pointer;
    color: var(--c-text-2);
    font-size: 14px;
    transition: background var(--transition), color var(--transition);
    position: relative;
    text-decoration: none;
}
.sima-topbar__icon-btn:hover { background: var(--c-bg); color: var(--c-text-1); }
.sima-notif-badge {
    position: absolute;
    top: -3px; right: -3px;
    width: 16px; height: 16px;
    border-radius: 50%;
    background: var(--c-red);
    color: white;
    font-size: 9px;
    font-weight: 700;
    display: grid; place-items: center;
    border: 2px solid var(--c-surface);
}
.sima-user-btn {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 5px 10px 5px 5px;
    border-radius: 10px;
    border: 1px solid var(--c-border);
    background: none;
    cursor: pointer;
    transition: background var(--transition);
    text-decoration: none;
    color: var(--c-text-1);
}
.sima-user-btn:hover { background: var(--c-bg); }

/* Page content */
.sima-content {
    flex: 1;
    padding: 28px;
}

/* Page header */
.sima-page-header {
    margin-bottom: 24px;
}
.sima-page-title {
    font-family: var(--f-display);
    font-size: 24px;
    font-weight: 700;
    color: var(--c-text-1);
    letter-spacing: -.025em;
    line-height: 1.15;
}
.sima-page-subtitle {
    font-size: 14px;
    color: var(--c-text-3);
    margin-top: 4px;
}

/* ═══════════════════════════════════════════════════
   CARD COMPONENT
   ═══════════════════════════════════════════════════ */
.sima-card {
    background: var(--c-surface);
    border: 1px solid var(--c-border-soft);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.sima-card__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 20px 0;
    gap: 12px;
}
.sima-card__title {
    font-family: var(--f-display);
    font-size: 15px;
    font-weight: 700;
    color: var(--c-text-1);
    letter-spacing: -.02em;
    margin: 0;
}
.sima-card__subtitle {
    font-size: 12px;
    color: var(--c-text-3);
    margin-top: 2px;
    font-weight: 400;
}
.sima-card__action {
    font-size: 12.5px;
    color: var(--c-accent);
    text-decoration: none;
    font-weight: 600;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: opacity var(--transition);
    flex-shrink: 0;
}
.sima-card__action:hover { opacity: .75; }
.sima-card__body {
    padding: 18px 20px;
}

/* ═══════════════════════════════════════════════════
   STAT CARD
   ═══════════════════════════════════════════════════ */
.sima-stat {
    background: var(--c-surface);
    border: 1px solid var(--c-border-soft);
    border-radius: var(--radius-lg);
    padding: 18px;
    box-shadow: var(--shadow-sm);
    transition: box-shadow var(--transition), transform var(--transition);
    position: relative;
    overflow: hidden;
}
.sima-stat:hover { box-shadow: var(--shadow); transform: translateY(-1px); }
.sima-stat::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 3px 3px 0 0;
}
.sima-stat--blue::before  { background: linear-gradient(90deg, var(--c-blue), #60a5fa); }
.sima-stat--red::before   { background: linear-gradient(90deg, var(--c-red), #f87171); }
.sima-stat--green::before { background: linear-gradient(90deg, var(--c-green), #34d399); }
.sima-stat--amber::before  { background: linear-gradient(90deg, var(--c-amber), #fbbf24); }
.sima-stat--navy::before   { background: linear-gradient(90deg, #0D1B2A, #1B3256); }
.sima-stat--teal::before   { background: linear-gradient(90deg, var(--c-teal), #2dd4bf); }
.sima-stat--purple::before { background: linear-gradient(90deg, var(--c-purple), #a78bfa); }

.sima-stat__icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: grid; place-items: center;
    font-size: 15px;
    margin-bottom: 12px;
}
.sima-stat__icon--blue   { background: var(--c-blue-lt);   color: var(--c-blue); }
.sima-stat__icon--red    { background: var(--c-red-lt);    color: var(--c-red); }
.sima-stat__icon--green  { background: var(--c-green-lt);  color: var(--c-green); }
.sima-stat__icon--amber  { background: var(--c-amber-lt);  color: var(--c-amber); }
.sima-stat__icon--navy   { background: #EFF4FF;             color: #0D1B2A; }
.sima-stat__icon--teal   { background: var(--c-teal-lt);   color: var(--c-teal); }
.sima-stat__icon--purple { background: var(--c-purple-lt);  color: var(--c-purple); }

.sima-stat__label {
    font-size: 11.5px;
    color: var(--c-text-3);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: 4px;
}
.sima-stat__value {
    display: block;
    font-family: var(--f-display);
    font-size: 32px;
    font-weight: 700;
    color: var(--c-text-1);
    letter-spacing: -.03em;
    line-height: 1;
    margin-bottom: 8px;
}
.sima-stat__delta {
    font-size: 11.5px;
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: 500;
}
.sima-stat__delta--flat  { color: var(--c-text-3); }
.sima-stat__delta--up    { color: var(--c-green); }
.sima-stat__delta--down  { color: var(--c-red); }

/* ═══════════════════════════════════════════════════
   BADGES
   ═══════════════════════════════════════════════════ */
.sima-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    border-radius: 100px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .02em;
    white-space: nowrap;
}
.sima-badge--blue   { background: var(--c-blue-lt);   color: var(--c-blue); }
.sima-badge--green  { background: var(--c-green-lt);  color: var(--c-green); }
.sima-badge--red    { background: var(--c-red-lt);    color: var(--c-red); }
.sima-badge--amber  { background: var(--c-amber-lt);  color: var(--c-amber); }
.sima-badge--purple { background: var(--c-purple-lt); color: var(--c-purple); }
.sima-badge--teal   { background: var(--c-teal-lt);   color: var(--c-teal); }
.sima-badge--grey   { background: var(--c-bg);        color: var(--c-text-3); border: 1px solid var(--c-border); }

/* ═══════════════════════════════════════════════════
   ALERT
   ═══════════════════════════════════════════════════ */
.sima-alert {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    border-radius: var(--radius);
    font-size: 13.5px;
    font-weight: 400;
    line-height: 1.5;
    border: 1px solid transparent;
}
.sima-alert--blue   { background: var(--c-blue-lt);   color: #1e40af; border-color: rgba(37,99,235,.15); }
.sima-alert--green  { background: var(--c-green-lt);  color: #065f46; border-color: rgba(5,150,105,.15); }
.sima-alert--amber  { background: var(--c-amber-lt);  color: #92400e; border-color: rgba(217,119,6,.2); }
.sima-alert--red    { background: var(--c-red-lt);    color: #991b1b; border-color: rgba(220,38,38,.15); }
.sima-alert__icon   { flex-shrink: 0; font-size: 15px; }
.sima-alert__text   { flex: 1; }
.sima-alert__action {
    font-weight: 600;
    font-size: 12.5px;
    text-decoration: none;
    color: inherit;
    opacity: .85;
    border: 1px solid currentColor;
    padding: 4px 10px;
    border-radius: 6px;
    white-space: nowrap;
    transition: opacity var(--transition);
    flex-shrink: 0;
}
.sima-alert__action:hover { opacity: 1; }

/* ═══════════════════════════════════════════════════
   SCHEDULE ITEM
   ═══════════════════════════════════════════════════ */
.sima-sch {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 12px;
    border-radius: var(--radius-sm);
    margin-bottom: 6px;
    cursor: pointer;
    transition: background var(--transition);
    border: 1px solid transparent;
}
.sima-sch:hover { background: var(--c-bg); border-color: var(--c-border-soft); }
.sima-sch__time {
    font-family: var(--f-mono);
    font-size: 11px;
    color: var(--c-text-3);
    font-weight: 500;
    flex-shrink: 0;
    width: 86px;
}
.sima-sch__dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    border: 2px solid;
    flex-shrink: 0;
}
.sima-sch__info { flex: 1; min-width: 0; }
.sima-sch__title {
    font-size: 13px;
    font-weight: 600;
    color: var(--c-text-1);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sima-sch__meta {
    font-size: 11.5px;
    color: var(--c-text-3);
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ═══════════════════════════════════════════════════
   PROGRESS BAR
   ═══════════════════════════════════════════════════ */
.sima-prog {
    height: 6px;
    background: var(--c-bg);
    border-radius: 100px;
    overflow: hidden;
    border: 1px solid var(--c-border-soft);
}
.sima-prog__bar {
    height: 100%;
    border-radius: 100px;
    transition: width .6s cubic-bezier(.22,1,.36,1);
    width: 0;
}

/* ═══════════════════════════════════════════════════
   TIMELINE
   ═══════════════════════════════════════════════════ */
.sima-timeline {
    list-style: none;
    padding: 0; margin: 0;
}
.sima-tl-item {
    display: flex;
    gap: 12px;
    padding-bottom: 16px;
    position: relative;
}
.sima-tl-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 30px;
    bottom: 0;
    width: 1px;
    background: var(--c-border-soft);
}
.sima-tl-dot {
    width: 30px; height: 30px;
    border-radius: 9px;
    display: grid; place-items: center;
    font-size: 12px;
    flex-shrink: 0;
}
.sima-tl-content { flex: 1; padding-top: 4px; }
.sima-tl-title {
    font-size: 13px;
    font-weight: 500;
    color: var(--c-text-1);
    line-height: 1.4;
}
.sima-tl-time {
    font-size: 11.5px;
    color: var(--c-text-3);
    margin-top: 3px;
}

/* ═══════════════════════════════════════════════════
   QUICK ACTIONS
   ═══════════════════════════════════════════════════ */
.sima-quick {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 14px 8px;
    border-radius: var(--radius);
    background: var(--c-bg);
    border: 1px solid var(--c-border-soft);
    text-decoration: none;
    transition: all var(--transition);
    text-align: center;
    cursor: pointer;
}
.sima-quick:hover {
    background: var(--c-surface);
    box-shadow: var(--shadow);
    border-color: var(--c-border);
    transform: translateY(-1px);
}
.sima-quick__icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: grid; place-items: center;
    font-size: 15px;
}
.sima-quick__label {
    font-size: 11px;
    font-weight: 600;
    color: var(--c-text-2);
    line-height: 1.3;
}

/* ═══════════════════════════════════════════════════
   AVATAR
   ═══════════════════════════════════════════════════ */
.sima-avatar {
    display: grid;
    place-items: center;
    font-family: var(--f-display);
    font-weight: 700;
    color: white;
    flex-shrink: 0;
    user-select: none;
}

.sima-avatar-wrapper {
    position: relative;
}

.sima-role-badge {
    position: absolute;
    bottom: -4px;
    right: -6px;
    background: linear-gradient(135deg,#6c8fff,#a78bfa);
    color: white;
    font-size: 8px;
    padding: 2px 6px;
    border-radius: 20px;
    font-weight: 700;
    letter-spacing: .5px;
}

/* ═══════════════════════════════════════════════════
   ANNOUNCE ITEM
   ═══════════════════════════════════════════════════ */
.sima-announce {
    padding: 14px;
    border-radius: var(--radius);
    border: 1px solid var(--c-border-soft);
    margin-bottom: 10px;
    cursor: pointer;
    transition: all var(--transition);
    background: var(--c-surface);
}
.sima-announce:hover { border-color: var(--c-border); box-shadow: var(--shadow-sm); }
.sima-announce__title {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--c-text-1);
    line-height: 1.4;
}
.sima-announce__body {
    font-size: 12.5px;
    color: var(--c-text-3);
    line-height: 1.55;
    margin-top: 5px;
}
.sima-announce__meta {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 9px;
    font-size: 11.5px;
    color: var(--c-text-3);
    flex-wrap: wrap;
}

/* ═══════════════════════════════════════════════════
   TABLE
   ═══════════════════════════════════════════════════ */
.sima-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.sima-table thead tr {
    border-bottom: 1px solid var(--c-border-soft);
}
.sima-table thead th {
    padding: 10px 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--c-text-3);
    text-align: left;
    white-space: nowrap;
}
.sima-table tbody td {
    padding: 12px 20px;
    border-bottom: 1px solid var(--c-border-soft);
    color: var(--c-text-2);
    vertical-align: middle;
}
.sima-table tbody tr:last-child td { border-bottom: none; }
.sima-table tbody tr:hover td { background: var(--c-bg); }

/* ═══════════════════════════════════════════════════
   BUTTONS
   ═══════════════════════════════════════════════════ */
.sima-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 16px;
    border-radius: var(--radius-sm);
    font-family: var(--f-body);
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    border: none;
    transition: all var(--transition);
    white-space: nowrap;
    background: linear-gradient(135deg, var(--c-accent), var(--c-accent-2));
    color: white;
    box-shadow: 0 2px 10px rgba(108,143,255,.25);
}
.sima-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 16px rgba(108,143,255,.35); color: white; }
.sima-btn:active { transform: scale(.98); }

.sima-btn--outline {
    background: transparent;
    color: var(--c-text-2);
    box-shadow: none;
    border: 1px solid var(--c-border);
}
.sima-btn--outline:hover { background: var(--c-bg); color: var(--c-text-1); box-shadow: none; transform: none; }

.sima-btn--gold {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    box-shadow: 0 2px 10px rgba(217,119,6,.25);
}
.sima-btn--gold:hover { box-shadow: 0 4px 16px rgba(217,119,6,.35); color: white; }

.sima-btn--blue {
    background: linear-gradient(135deg, #2563EB, #3b82f6);
    color: white;
    box-shadow: 0 2px 10px rgba(37,99,235,.25);
}
.sima-btn--blue:hover { box-shadow: 0 4px 16px rgba(37,99,235,.35); color: white; }

.sima-btn--danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    box-shadow: 0 2px 10px rgba(220,38,38,.2);
}

.sima-btn--sm {
    padding: 6px 12px;
    font-size: 12px;
    border-radius: 7px;
}
.sima-btn--full { width: 100%; justify-content: center; }

/* ═══════════════════════════════════════════════════
   FORM INPUTS
   ═══════════════════════════════════════════════════ */
.sima-input {
    width: 100%;
    background: var(--c-surface);
    border: 1px solid var(--c-border);
    border-radius: var(--radius-sm);
    padding: 10px 14px;
    color: var(--c-text-1);
    font-family: var(--f-body);
    font-size: 14px;
    outline: none;
    transition: border-color var(--transition), box-shadow var(--transition);
}
.sima-input:hover { border-color: var(--c-text-4); }
.sima-input:focus { border-color: var(--c-accent); box-shadow: 0 0 0 3px rgba(108,143,255,.1); }
.sima-input::placeholder { color: var(--c-text-4); }

.sima-label {
    display: block;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--c-text-2);
    margin-bottom: 6px;
}

/* ═══════════════════════════════════════════════════
   ANIMATIONS
   ═══════════════════════════════════════════════════ */
.sima-fade { opacity: 0; animation: fadeUp .45s ease forwards; }
.sima-fade--1 { animation-delay: .05s; }
.sima-fade--2 { animation-delay: .10s; }
.sima-fade--3 { animation-delay: .15s; }
.sima-fade--4 { animation-delay: .20s; }
.sima-fade--5 { animation-delay: .25s; }
.sima-fade--6 { animation-delay: .30s; }
.sima-fade--7 { animation-delay: .35s; }

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: none; }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50%       { opacity: .4; }
}

/* ═══════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════ */
@media (max-width: 768px) {
    .sima-sidebar {
        transform: translateX(-100%);
    }
    .sima-sidebar.open {
        transform: translateX(0);
        box-shadow: var(--shadow-md);
    }
    .sima-main {
        margin-left: 0;
    }
    .sima-topbar__hamburger { display: block; }
    .sima-content { padding: 16px; }
    .sima-overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,.35);
        z-index: 150;
    }
    .sima-overlay.open { display: block; }
}

/* ═══════════════════════════════════════════════════
   SCROLLBAR
   ═══════════════════════════════════════════════════ */
::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--c-border); border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: var(--c-text-4); }
</style>
@stack('head_styles')
</head>
@php
    $role = strtolower(auth()->user()->role instanceof \App\Enums\Role
        ? auth()->user()->role->value
        : auth()->user()->role);

    $prefix = match($role) {
        'kln' => 'kln',
        'dosen' => 'dosen',
        'bipa' => 'bipa',
        'jurusan' => 'jurusan',
        default => 'mahasiswa'
    };
@endphp
<body>
<div class="sima-shell">

    <!-- ── SIDEBAR ─────────────────────────────── -->
    <aside class="sima-sidebar" id="sidebar">
        @include('partials.sidebar.'.$prefix)
    </aside>
    <!-- Mobile overlay -->
    <div class="sima-overlay" id="overlay" onclick="closeSidebar()"></div>

    <!-- ── MAIN ───────────────────────────────── -->
    <div class="sima-main">

        <!-- Topbar -->
        <header class="sima-topbar">
            <button class="sima-topbar__hamburger" onclick="openSidebar()">
                <i class="fas fa-bars"></i>
            </button>

            <div class="sima-topbar__breadcrumb">
                SIMA <i class="fas fa-slash" style="font-size:9px;opacity:.3;margin:0 2px"></i>
                <span>@yield('page_section', ucfirst($prefix))</span>
                <button onclick="toggleCollapse()" 
                        style="background:none;border:none;margin-right:10px;cursor:pointer;">
                    <i class="fas fa-angle-double-left"></i>
                </button>
            </div>

            <div class="sima-topbar__right">
                <!-- Notif -->
                <a href="{{ route($prefix.'.notifikasi') }}" 
                class="sima-topbar__icon-btn" 
                title="Notifikasi">
                    <i class="fas fa-bell"></i>
                    @if(isset($unreadNotifCount) && $unreadNotifCount > 0)
                        <span class="sima-notif-badge">{{ $unreadNotifCount }}</span>
                    @endif
                </a>
                <!-- User -->
            <div class="dropdown">
                <button class="sima-user-btn" data-bs-toggle="dropdown">
                <div class="sima-avatar-wrapper">
                    <div class="sima-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'M', 0, 2)) }}
                    </div>
                    <div class="sima-role-badge">
                        {{ strtoupper($prefix) }}
                    </div>
                </div>
                    <span>{{ auth()->user()->nim ?? auth()->user()->id }}</span>
                    <i class="fas fa-chevron-down" style="font-size:11px;"></i>
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:12px;">
                    <li>
                        <a class="dropdown-item" href="{{ route($prefix.'.profil') }}">
                            <i class="fas fa-user me-2"></i> Profile
                        </a>
                    </li>

                    <li>
                        <button class="dropdown-item" onclick="toggleTheme()">
                            <i class="fas fa-moon me-2"></i> Dark / Light Mode
                        </button>
                    </li>

                    <li>
                        <button class="dropdown-item" onclick="toggleLang()">
                            <i class="fas fa-language me-2"></i> ID / EN
                        </button>
                    </li>

                    <li><hr class="dropdown-divider"></li>

                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-power-off me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>            
        </div>
        </header>

        <!-- Content -->
        <main class="sima-content">

            <!-- Page Header -->
            <div class="sima-page-header">
                <div class="sima-page-title">@yield('page_title', 'Dashboard')</div>
                <div class="sima-page-subtitle">@yield('page_subtitle', '')</div>
            </div>

            <!-- Flash messages -->
            @if(session('success'))
            <div class="sima-alert sima-alert--green sima-fade" style="margin-bottom:20px">
                <i class="fas fa-circle-check sima-alert__icon"></i>
                <div class="sima-alert__text">{{ session('success') }}</div>
            </div>
            @endif
            @if(session('error'))
            <div class="sima-alert sima-alert--red sima-fade" style="margin-bottom:20px">
                <i class="fas fa-circle-xmark sima-alert__icon"></i>
                <div class="sima-alert__text">{{ session('error') }}</div>
            </div>
            @endif

            @yield('main_content')
        </main>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* ── Sidebar toggle ─────────────────────────── */
function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('overlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('overlay').classList.remove('open');
    document.body.style.overflow = '';
}

/* ── Nav dropdown ───────────────────────────── */
function toggleNav(btn) {
    const isOpen = btn.classList.contains('open');
    btn.classList.toggle('open', !isOpen);
    const sub = btn.nextElementSibling;
    if (sub && sub.classList.contains('sima-nav__sub')) {
        sub.classList.toggle('open', !isOpen);
    }
}

/* ── Progress bar animation ─────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.sima-prog__bar[data-w]').forEach(function (bar) {
        const pct = bar.getAttribute('data-w');
        setTimeout(() => { bar.style.width = pct + '%'; }, 200);
    });

    /* ── Count-up animation ─────────────────── */
    document.querySelectorAll('[data-count]').forEach(function (el) {
        const target = parseInt(el.getAttribute('data-count'), 10);
        if (isNaN(target)) return;
        let cur = 0;
        const step = Math.ceil(target / 30);
        const timer = setInterval(() => {
            cur = Math.min(cur + step, target);
            el.textContent = cur;
            if (cur >= target) clearInterval(timer);
        }, 30);
    });
});

/* ── Dark Mode ───────────────────────── */
function toggleTheme() {
    const body = document.body;
    body.classList.toggle('dark');

    localStorage.setItem('theme', body.classList.contains('dark') ? 'dark' : 'light');
}

document.addEventListener('DOMContentLoaded', function () {
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark');
    }
});

function toggleLang() {
    const current = localStorage.getItem('lang') || 'id';
    const newLang = current === 'id' ? 'en' : 'id';
    localStorage.setItem('lang', newLang);
    alert('Language switched to: ' + newLang.toUpperCase());
}

function toggleCollapse() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('collapsed');

    localStorage.setItem(
        'sidebar',
        sidebar.classList.contains('collapsed') ? 'mini' : 'full'
    );
}

document.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('sidebar') === 'mini') {
        document.getElementById('sidebar').classList.add('collapsed');
    }
});
</script>

@stack('page_js')
@yield('page_js')
</body>
</html>
