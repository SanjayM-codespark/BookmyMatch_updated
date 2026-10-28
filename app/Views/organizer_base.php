<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $page_title ?? 'Organizer' ?> — BookMyMatch</title>

    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/icons/fontawesome-free-6.0.0-web/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <?= $this->renderSection('pageCss') ?>

<style>
/* ── Base ─────────────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; }
body { margin: 0; font-family: 'Montserrat', sans-serif; background: #F1F5F9; color: #111827; }
a { text-decoration: none; }

:root {
    --sb-bg:       #0D0C1D;
    --sb-width:    220px;
    --accent:      #E53E3E;
    --accent-dark: #C53030;
    --nav-text:    #CBD5E1;
    --nav-muted:   #64748B;
    --nav-hover:   rgba(255,255,255,0.07);
    --divider:     rgba(255,255,255,0.08);
}

/* ── Shell ────────────────────────────────────────────────── */
.org-shell { display: flex; min-height: 100vh; }

/* ══════════════════════════════════════════════════════════
   SIDEBAR  (dark)
══════════════════════════════════════════════════════════ */
.org-sidebar {
    width: var(--sb-width); min-width: var(--sb-width);
    background: var(--sb-bg);
    position: fixed; top: 0; left: 0; height: 100vh;
    display: flex; flex-direction: column;
    z-index: 100; overflow-y: auto; overflow-x: hidden;
}

/* Brand */
.org-brand {
    padding: 22px 16px 18px;
    display: flex; align-items: center; gap: 11px;
    border-bottom: 1px solid var(--divider);
    flex-shrink: 0;
    text-decoration: none; color: inherit;
}
.org-brand-icon {
    width: 42px; height: 42px; border-radius: 10px;
    background: var(--accent);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 20px; flex-shrink: 0;
}
.org-brand-text .bt1 { font-size: 19px; font-weight: 800; color: #fff; line-height: 1.1; letter-spacing: -0.3px; }
.org-brand-text .bt2 { font-size: 11px; font-weight: 500; color: #64748B; margin-top: 1px; }

/* Nav wrapper */
.org-nav { padding: 10px 10px; flex: 1; }

.nav-section-label {
    font-size: 10px; font-weight: 700; color: var(--nav-muted);
    letter-spacing: 0.12em; text-transform: uppercase;
    padding: 14px 10px 5px; display: block;
}

/* Nav item */
.org-nav-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px; border-radius: 10px;
    color: var(--nav-text); font-size: 13.5px; font-weight: 500;
    cursor: pointer; transition: background 0.15s, color 0.15s;
    margin-bottom: 2px; position: relative; white-space: nowrap;
}
.org-nav-item:hover { background: var(--nav-hover); color: #fff; text-decoration: none; }
.org-nav-item.active { background: var(--accent); color: #fff; }
.org-nav-item.active:hover { background: var(--accent-dark); color: #fff; text-decoration: none; }
.org-nav-item .nv-icon { width: 20px; text-align: center; font-size: 15px; flex-shrink: 0;
    color: var(--nav-muted); }
.org-nav-item:hover .nv-icon,
.org-nav-item.active .nv-icon { color: #fff; }
.org-nav-item .nv-arrow { margin-left: auto; font-size: 11px; color: var(--nav-muted);
    transition: transform 0.2s; flex-shrink: 0; }
.org-nav-item[aria-expanded="true"] .nv-arrow { transform: rotate(90deg); }
.org-nav-item[aria-expanded="true"] { color: #fff; }

/* Sub-items */
.org-nav-sub { padding: 8px 12px 8px 40px; font-size: 13px; font-weight: 400;
    color: #94A3B8; border-radius: 8px; display: flex; align-items: center; gap: 8px;
    transition: background 0.15s, color 0.15s; margin-bottom: 1px; }
.org-nav-sub:hover { background: var(--nav-hover); color: #E2E8F0; text-decoration: none; }
.org-nav-sub.active { color: var(--accent); background: rgba(229,62,62,0.12); font-weight: 600; }
.org-nav-sub::before { content: ''; width: 5px; height: 5px; border-radius: 50%;
    background: #334155; flex-shrink: 0; }
.org-nav-sub.active::before { background: var(--accent); }

.org-nav-divider { border: none; border-top: 1px solid var(--divider); margin: 8px 8px; }

/* Sidebar footer — user card */
.org-sb-footer {
    padding: 12px;
    border-top: 1px solid var(--divider);
    flex-shrink: 0;
}
.org-sb-user {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px; border-radius: 10px;
    background: rgba(255,255,255,0.04);
    cursor: pointer; transition: background 0.15s;
    text-decoration: none; color: inherit;
}
.org-sb-user:hover { background: rgba(255,255,255,0.08); }
.org-sb-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,0.1);
    display: flex; align-items: center; justify-content: center;
    color: #94A3B8; font-size: 16px; flex-shrink: 0; overflow: hidden;
}
.org-sb-avatar img { width: 100%; height: 100%; object-fit: cover; }
.org-sb-uname { font-size: 13px; font-weight: 600; color: #E2E8F0; white-space: nowrap;
    overflow: hidden; text-overflow: ellipsis; max-width: 110px; }
.org-sb-urole { font-size: 11px; color: #64748B; }

/* ══════════════════════════════════════════════════════════
   MAIN AREA
══════════════════════════════════════════════════════════ */
.org-main { margin-left: var(--sb-width); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

/* ── Page Header (replaces topbar) ──────────────────────── */
.org-page-header {
    background: #fff;
    border-bottom: 1px solid #E5E7EB;
    padding: 16px 28px;
    display: flex; align-items: center; justify-content: space-between;
    position: sticky; top: 0; z-index: 50;
}
.org-page-header-left { display: flex; align-items: center; gap: 14px; }
.menu-btn { background: none; border: none; cursor: pointer; color: #6B7280; font-size: 18px; padding: 4px; }
.org-ph-titles h1 { font-size: 22px; font-weight: 700; margin: 0; color: #0F172A; line-height: 1.2; }
.org-ph-titles p  { font-size: 12px; color: #94A3B8; margin: 2px 0 0; font-weight: 400; }
.org-page-header-right { display: flex; align-items: center; gap: 12px; }
.org-search-wrap { position: relative; min-width: 240px; }
.org-search-bar {
    display: flex; align-items: center; gap: 8px;
    background: #F8FAFC; border: 1px solid #E2E8F0;
    border-radius: 24px; padding: 7px 16px;
    font-size: 13px; color: #94A3B8; transition: border-color 0.15s;
}
.org-search-bar:focus-within { border-color: #E53E3E; background: #fff; box-shadow: 0 0 0 3px rgba(229,62,62,.08); }
.org-search-bar i { color: #94A3B8; font-size: 13px; flex-shrink: 0; }
.org-search-bar input {
    border: none; background: transparent; outline: none;
    font-size: 13px; color: #374151; width: 100%;
    font-family: 'Montserrat', sans-serif;
}
.org-search-bar input::placeholder { color: #94A3B8; }

/* Search dropdown - FIXED positioning */
.org-search-dropdown {
    position: absolute; top: calc(100% + 8px); left: 0; right: 0;
    background: #fff; border: 1px solid #E5E7EB; border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.10); z-index: 200;
    overflow: hidden; display: none;
    max-height: 400px; overflow-y: auto;
}
.org-search-dropdown.open { display: block; }
.search-result-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 16px; cursor: pointer; text-decoration: none;
    color: #374151; font-size: 13px; font-weight: 500;
    transition: background 0.12s;
}
.search-result-item:hover { background: #FFF5F5; color: #E53E3E; text-decoration: none; }
.search-result-item .sri-icon {
    width: 30px; height: 30px; border-radius: 8px;
    background: #FEE2E2; color: #E53E3E;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; flex-shrink: 0;
}
.search-result-item .sri-label { flex: 1; }
.search-result-item .sri-section { font-size: 11px; color: #9CA3AF; font-weight: 400; }
.search-no-results { padding: 20px 16px; text-align: center; color: #94A3B8; font-size: 13px; }

/* ── Content ──────────────────────────────────────────────── */
.org-content { padding: 24px 28px; flex: 1; }

/* ══════════════════════════════════════════════════════════
   SHARED COMPONENTS
══════════════════════════════════════════════════════════ */

/* Cards */
.page-card {
    background: #fff; border-radius: 12px; border: 1px solid #E5E7EB;
    padding: 20px 24px; margin-bottom: 20px; overflow-x: auto;
}
.page-card-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 18px; flex-wrap: wrap; gap: 10px;
}
.page-card-title { font-size: 15px; font-weight: 600; color: #0F172A; margin: 0; }

/* Table */
.org-table-wrap { overflow-x: auto; }
.org-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.org-table thead tr { background: #FFF0EE; }
.org-table thead th {
    padding: 11px 14px; text-align: left; font-weight: 700;
    color: #374151; white-space: nowrap; font-size: 11.5px;
    text-transform: uppercase; letter-spacing: 0.05em;
    border-bottom: 1px solid #FECACA;
}
.org-table tbody tr { border-bottom: 1px solid #F1F5F9; transition: background 0.1s; }
.org-table tbody tr:hover { background: #FFFBFB; }
.org-table tbody td { padding: 12px 14px; color: #374151; vertical-align: middle; }
.org-table tbody td:first-child { color: #94A3B8; font-size: 12px; font-weight: 600; }

/* Status badges */
.badge-status { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-upcoming  { background: #FEF9C3; color: #854D0E; }
.badge-live      { background: #DCFCE7; color: #166534; }
.badge-completed { background: #F1F5F9; color: #64748B; }
.badge-cancelled { background: #FEE2E2; color: #991B1B; }
.badge-paid      { background: #DCFCE7; color: #166534; }
.badge-upi       { background: #DBEAFE; color: #1D4ED8; }

/* Table action buttons */
.btn-tbl {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 5px 13px; border-radius: 7px; font-size: 12px; font-weight: 500;
    cursor: pointer; border: 1px solid transparent; text-decoration: none;
    transition: all 0.15s; white-space: nowrap; font-family: 'Montserrat', sans-serif;
}
.btn-tbl-view   { background:#ECFDF5; color:#059669; border-color:#A7F3D0; }
.btn-tbl-view:hover  { background:#D1FAE5; color:#059669; text-decoration:none; }
.btn-tbl-edit   { background:#EFF6FF; color:#2563EB; border-color:#BFDBFE; }
.btn-tbl-edit:hover  { background:#DBEAFE; color:#2563EB; text-decoration:none; }
.btn-tbl-delete { background:#FEF2F2; color:#EF4444; border-color:#FECACA; }
.btn-tbl-delete:hover{ background:#FEE2E2; color:#EF4444; text-decoration:none; }
.btn-tbl-cancel { background:#FFFBEB; color:#D97706; border-color:#FDE68A; }
.btn-tbl-cancel:hover{ background:#FEF3C7; color:#D97706; text-decoration:none; }
.btn-tbl-info   { background:#EFF6FF; color:#2563EB; border-color:#BFDBFE; }
.btn-tbl-info:hover  { background:#DBEAFE; color:#2563EB; text-decoration:none; }
.btn-tbl-action { background: var(--accent); color: #fff; border-color: var(--accent); }
.btn-tbl-action:hover{ background: var(--accent-dark); color:#fff; text-decoration:none; }

/* Filter bar */
.filter-bar { display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end; margin-bottom:20px; }
.filter-bar .form-control, .filter-bar .custom-select {
    height:40px; font-size:13px; border-radius:8px; border:1px solid #E2E8F0;
    background:#F8FAFC;
}
.filter-bar .btn-filter {
    height:40px; padding:0 22px; border-radius:8px; border:none;
    background: var(--accent); color:#fff; font-size:13px; font-weight:600;
    cursor:pointer; display:flex; align-items:center; gap:6px;
    font-family:'Montserrat',sans-serif;
}
.filter-bar .btn-filter:hover { background: var(--accent-dark); }

/* Empty state */
.empty-state { text-align:center; padding:56px 0; color:#94A3B8; }
.empty-state i { font-size:44px; margin-bottom:14px; display:block; color:#E2E8F0; }
.empty-state p { font-size:14px; margin:0; }

/* Form cards (create/edit pages) */
.form-card {
    background: #fff; border-radius: 12px; border: 1px solid #E5E7EB;
    padding: 24px 28px; margin-bottom: 20px;
}
.form-card-header { display:flex; align-items:flex-start; gap:14px; margin-bottom:22px; }
.card-icon { width:44px; height:44px; border-radius:50%; display:flex; align-items:center;
    justify-content:center; flex-shrink:0; font-size:18px; }
.card-icon.blue   { background:#DBEAFE; color:#2563EB; }
.card-icon.green  { background:#D1FAE5; color:#059669; }
.card-icon.purple { background:#EDE9FE; color:#7C3AED; }
.card-icon.orange { background:#FEF3C7; color:#D97706; }
.card-icon.red    { background:#FEE2E2; color:#E53E3E; }
.card-icon.indigo { background:#E0E7FF; color:#4338CA; }
.form-card-header .card-text h6 { font-size:15px; font-weight:600; color:#0F172A; margin:0 0 3px; }
.form-card-header .card-text p  { font-size:12px; color:#6B7280; margin:0; }
.form-label-sm { font-size:13px; font-weight:500; color:#374151; margin-bottom:5px; display:block; }
.input-icon-wrap { position:relative; }
.input-icon-wrap .input-prefix-icon {
    position:absolute; left:12px; top:50%; transform:translateY(-50%);
    color:#9CA3AF; font-size:14px; pointer-events:none;
}
.input-icon-wrap .form-control,
.input-icon-wrap .custom-select { padding-left:36px; }
.field-error { font-size:12px; color:#EF4444; margin-top:3px; }

/* Bottom action bar */
.org-action-bar {
    background:#fff; border-top:1px solid #E5E7EB;
    padding:16px 28px; display:flex; justify-content:flex-end; gap:10px;
    position:sticky; bottom:0; z-index:40;
}
.btn-cancel { border:1px solid #D1D5DB; background:#fff; color:#374151; border-radius:8px;
    padding:9px 20px; font-size:14px; font-weight:500; cursor:pointer; font-family:'Montserrat',sans-serif; }
.btn-cancel:hover { background:#F3F4F6; }
.btn-draft { border:1px solid var(--accent); background:#fff; color:var(--accent); border-radius:8px;
    padding:9px 20px; font-size:14px; font-weight:500; cursor:pointer; font-family:'Montserrat',sans-serif; }
.btn-draft:hover { background:#FEF2F2; }
.btn-submit-main { border:none; background:var(--accent); color:#fff; border-radius:8px;
    padding:9px 22px; font-size:14px; font-weight:600; cursor:pointer;
    display:flex; align-items:center; gap:8px; font-family:'Montserrat',sans-serif; }
.btn-submit-main:hover { background:var(--accent-dark); }

/* ── Normalize legacy content ────────────────────────────── */
.org-content .container-fluid { padding-left:0; padding-right:0; }
.org-content .container { max-width:100%; padding:0; }
.org-content .card,
.org-content .card.p-4 { border-radius:12px !important; border:1px solid #E5E7EB !important;
    box-shadow:none !important; margin-bottom:20px; }
.org-content .col-form-label { font-size:13px; font-weight:500; color:#374151; }
.org-content .inputgroups { border:1px solid #E2E8F0; border-radius:8px; font-size:14px; height:40px; }
.org-content .inputgroups:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(229,62,62,.1); outline:none; }
.org-content textarea.inputgroups, .org-content textarea.form-control { height:auto; }
.org-content .text-danger { font-size:12px; margin-top:3px; }
.org-content .alert { border-radius:8px; font-size:14px; }
.org-content .jumbotron { background:#F8FAFC; border-radius:12px; color:#94A3B8; }
.org-content section[style*="background-color"] { display:none !important; }
.org-content .dash-head1 { display:none; }
.org-content .dash-link1 { display:none; }

/* ── Pagination ───────────────────────────────────────────── */
.pagination .page-link { border-radius:6px; color:var(--accent); border:1px solid #E5E7EB; font-size:13px; font-family:'Montserrat',sans-serif; }
.pagination .page-item.active .page-link { background:var(--accent); border-color:var(--accent); color:#fff; }
.pagination .page-item.disabled .page-link { color:#CBD5E1; }

/* ── Responsive ───────────────────────────────────────────── */
@media (max-width: 768px) {
    .org-sidebar { transform:translateX(-100%); transition:transform 0.22s; }
    .org-sidebar.open { transform:translateX(0); box-shadow:4px 0 20px rgba(0,0,0,0.4); }
    .org-main { margin-left:0; }
    .org-page-header { padding:12px 16px; flex-wrap: wrap; gap: 10px; }
    .org-content { padding:16px; }
    .org-search-wrap { min-width: 160px; }
    .org-page-header-right { flex: 1; justify-content: flex-end; }
}

/* ── Responsive table improvements ──────────────────────── */
@media (max-width: 576px) {
    .org-table thead { display: none; }
    .org-table tbody tr { 
        display: block; 
        margin-bottom: 16px;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 12px;
    }
    .org-table tbody td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        border-bottom: 1px solid #F1F5F9;
    }
    .org-table tbody td:last-child { border-bottom: none; }
    .org-table tbody td:before {
        content: attr(data-label);
        font-weight: 600;
        color: #6B7280;
        font-size: 11px;
        text-transform: uppercase;
    }
}
</style>
</head>
<body>
<div class="org-shell">

<!-- ════════════════════════════════
     DARK SIDEBAR
════════════════════════════════ -->
<aside class="org-sidebar" id="orgSidebar">

    <!-- Brand -->
    <a class="org-brand" href="<?= base_url(); ?>">
        <div class="org-brand-icon">
            <i class="fa-solid fa-person-running"></i>
        </div>
        <div class="org-brand-text">
            <div class="bt1">Book</div>
            <div class="bt2">My Match</div>
        </div>
    </a>

    <!-- Nav -->
    <nav class="org-nav">
        <?php
        $uri  = service('uri');
        $path = '/' . implode('/', $uri->getSegments());
        if (! function_exists('org_nav_active')) {
            function org_nav_active(string $path, array $matches): string {
                foreach ($matches as $m) {
                    if (str_contains($path, $m)) return ' active';
                }
                return '';
            }
        }
        $inTournament = str_contains($path, '/create_tournament') || str_contains($path, '/edit_tournament')
                     || str_contains($path, '/organizr') || str_contains($path, '/organizer1');
        $inEvents     = str_contains($path, '/view_events') || str_contains($path, '/create_event')
                     || str_contains($path, '/edit_tourney_event') || str_contains($path, '/add_tourney_event')
                     || str_contains($path, '/events');
        $inSchedule   = str_contains($path, '/organizer/match_list') || str_contains($path, '/organizer/group_list')
                     || str_contains($path, '/organizer/team_list') || str_contains($path, '/organizer/round_list');
        ?>

        <span class="nav-section-label">Management</span>

        <!-- Dashboard -->
        <a href="<?= base_url('organizer/dashboard') ?>"
           class="org-nav-item<?= (!$inTournament && !$inEvents && $path === '/organizer/dashboard') ? ' active' : '' ?>">
            <span class="nv-icon"><i class="fa-solid fa-border-all"></i></span>
            Dashboard
        </a>

        <!-- Tournament (expandable) -->
        <a class="org-nav-item" data-toggle="collapse" href="#navTournament" role="button"
           aria-expanded="<?= $inTournament ? 'true' : 'false' ?>">
            <span class="nv-icon"><i class="fa-solid fa-trophy"></i></span>
            Tournament
            <i class="fa-solid fa-chevron-right nv-arrow"></i>
        </a>
        <div class="collapse <?= $inTournament ? 'show' : '' ?>" id="navTournament">
            <a href="<?= base_url('organizr') ?>"
               class="org-nav-sub<?= (str_contains($path,'/organizr')||str_contains($path,'/organizer1')) ? ' active' : '' ?>">
                All Tournaments
            </a>
            <a href="<?= base_url('create_tournament') ?>"
               class="org-nav-sub<?= str_contains($path,'/create_tournament') ? ' active' : '' ?>">
                Create Tournament
            </a>
        </div>

        <!-- Events (expandable) -->
        <a class="org-nav-item" data-toggle="collapse" href="#navEvents" role="button"
           aria-expanded="<?= $inEvents ? 'true' : 'false' ?>">
            <span class="nv-icon"><i class="fa-regular fa-calendar-days"></i></span>
            Events
            <i class="fa-solid fa-chevron-right nv-arrow"></i>
        </a>
        <div class="collapse <?= $inEvents ? 'show' : '' ?>" id="navEvents">
            <a href="<?= base_url('events') ?>"
               class="org-nav-sub<?= str_contains($path,'/events') ? ' active' : '' ?>">
                 Events
            </a>
            <a href="<?= base_url('view_events') ?>"
               class="org-nav-sub<?= str_contains($path,'/view_events') ? ' active' : '' ?>">
                View Events
            </a>
        </div>

        <!-- Schedule (expandable) -->
        <a class="org-nav-item" data-toggle="collapse" href="#navSchedule" role="button"
           aria-expanded="<?= $inSchedule ? 'true' : 'false' ?>">
            <span class="nv-icon"><i class="fa-regular fa-chess-rook"></i></span>
            Schedule
            <i class="fa-solid fa-chevron-right nv-arrow"></i>
        </a>
        <div class="collapse <?= $inSchedule ? 'show' : '' ?>" id="navSchedule">
            <a href="<?= base_url('organizer/match_list') ?>"
               class="org-nav-sub<?= str_contains($path,'/organizer/match_list') ? ' active' : '' ?>">
                Schedule Match
            </a>
            <a href="<?= base_url('organizer/group_list') ?>"
               class="org-nav-sub<?= str_contains($path,'/organizer/group_list') ? ' active' : '' ?>">
                Groups
            </a>
            <a href="<?= base_url('organizer/team_list') ?>"
               class="org-nav-sub<?= str_contains($path,'/organizer/team_list') ? ' active' : '' ?>">
                Teams
            </a>
            <a href="<?= base_url('organizer/round_list') ?>"
               class="org-nav-sub<?= str_contains($path,'/organizer/round_list') ? ' active' : '' ?>">
                Rounds
            </a>
        </div>
        
        <!-- Umpires -->
        <a href="<?= base_url('organizer/umpire_list') ?>"
           class="org-nav-item<?= str_contains($path,'/umpire') ? ' active' : '' ?>">
            <span class="nv-icon"><i class="fa-regular fa-user"></i></span>
            Umpires
        </a>

        <hr class="org-nav-divider">

        <!-- Participants / Bookings -->
        <a href="<?= base_url('admin/participants') ?>"
           class="org-nav-item<?= (str_contains($path,'/admin/participants')||str_contains($path,'/booking')) ? ' active' : '' ?>">
            <span class="nv-icon"><i class="fa-solid fa-users"></i></span>
            Participants
        </a>

        <!-- Results -->
        <a href="<?= base_url('organizer/results') ?>"
           class="org-nav-item<?= str_contains($path,'/results') ? ' active' : '' ?>">
            <span class="nv-icon"><i class="fa-regular fa-star"></i></span>
            Results
        </a>

        <hr class="org-nav-divider">

        <!-- Logout -->
        <a href="<?= base_url('logout') ?>" class="org-nav-item">
            <span class="nv-icon"><i class="fa-solid fa-arrow-right-from-bracket"></i></span>
            Logout
        </a>
    </nav>

    <!-- User card at bottom - FIXED: Handle undefined $users -->
    <div class="org-sb-footer">
        <?php
        // Check if $users exists and is an object
        $userName = 'Organizer';
        $userRole = 'Organizer';
        $avatarUrl = null;
        
        if (isset($users) && is_object($users)) {
            $userName = !empty($users->user_name) ? $users->user_name : 'Organizer';
            $userRole = !empty($users->role) ? $users->role : 'Organizer';
            if (!empty($users->user_profile_img) && !empty($users->user_id)) {
                $avatarUrl = base_url() . '/public/uploads/images/profile/' . $users->user_id . '/' . $users->user_profile_img;
            }
        }
        ?>
        <a class="org-sb-user" href="<?= base_url('profile') ?>">
            <div class="org-sb-avatar">
                <?php if ($avatarUrl): ?>
                    <img src="<?= $avatarUrl ?>" alt="<?= esc($userName) ?>">
                <?php else: ?>
                    <i class="fa-regular fa-user"></i>
                <?php endif ?>
            </div>
            <div>
                <div class="org-sb-uname"><?= esc($userName) ?></div>
                <div class="org-sb-urole"><?= esc($userRole) ?></div>
            </div>
        </a>
    </div>

</aside>

<!-- ════════════════════════════════
     MAIN
════════════════════════════════ -->
<div class="org-main">

    <!-- Page Header -->
    <header class="org-page-header">
        <div class="org-page-header-left">
            <button class="menu-btn" id="sidebarToggle" aria-label="Toggle sidebar">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="org-ph-titles">
                <h1><?= esc($page_title ?? 'Organizer Dashboard') ?></h1>
                <?php if (!empty($page_subtitle)): ?>
                    <p><?= esc($page_subtitle) ?></p>
                <?php endif ?>
            </div>
        </div>
        <div class="org-page-header-right">
            <div class="org-search-wrap" id="orgSearchWrap">
                <div class="org-search-bar">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="orgSearchInput" placeholder="Search pages..." autocomplete="off">
                </div>
                <div class="org-search-dropdown" id="orgSearchDropdown"></div>
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="org-content">
        <?= $this->renderSection('main_content') ?>
    </main>

</div>
</div><!-- /.org-shell -->

<!-- ════════════════════════════════
     SCRIPTS
════════════════════════════════ -->
<script src="<?= base_url() ?>/public/assets/js/jquery-3.6.0.min.js"></script>
<script src="<?= base_url() ?>/public/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url() ?>/public/assets/icons/fontawesome-free-6.0.0-web/js/fontawesome.min.js"></script>

<script>
(function() {
    'use strict';

    // ── Sidebar mobile toggle ──────────────────────────────
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('orgSidebar').classList.toggle('open');
    });

    // ── Close sidebar when clicking outside on mobile ──────
    document.addEventListener('click', function(e) {
        var sidebar = document.getElementById('orgSidebar');
        var toggle = document.getElementById('sidebarToggle');
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        }
    });

    // ── Rotate chevron when Bootstrap collapse opens/closes ─
    $(document).on('show.bs.collapse hide.bs.collapse', '.collapse', function() {
        var btn = $('[href="#' + this.id + '"]');
        btn.toggleClass('text-white');
        // Also toggle aria-expanded
        var isExpanded = $(this).hasClass('show');
        btn.attr('aria-expanded', !isExpanded);
    });

    // ── Quick-nav search ──────────────────────────────────────
    var BASE = '<?= base_url() ?>';
    var NAV = [
        { label: 'Dashboard',          section: 'Management', icon: 'fa-border-all',               url: BASE + '/organizer/dashboard' },
        { label: 'All Tournaments',    section: 'Tournament',  icon: 'fa-trophy',                   url: BASE + '/organizr' },
        { label: 'Create Tournament',  section: 'Tournament',  icon: 'fa-plus',                     url: BASE + '/create_tournament' },
        { label: 'All Events',         section: 'Events',      icon: 'fa-calendar-days',            url: BASE + '/events' },
        { label: 'View Events',        section: 'Events',      icon: 'fa-calendar-days',            url: BASE + '/view_events' },
        { label: 'Umpires',            section: 'Management',  icon: 'fa-user',                     url: BASE + '/organizer/umpire_list' },
        { label: 'Add Umpire',         section: 'Umpires',     icon: 'fa-user-plus',                url: BASE + '/organizer/add_umpire' },
        { label: 'Schedule Match',     section: 'Schedule',    icon: 'fa-chess-rook',               url: BASE + '/organizer/match_list' },
        { label: 'Groups',             section: 'Schedule',    icon: 'fa-layer-group',              url: BASE + '/organizer/group_list' },
        { label: 'Teams',              section: 'Schedule',    icon: 'fa-people-group',             url: BASE + '/organizer/team_list' },
        { label: 'Rounds',             section: 'Schedule',    icon: 'fa-rotate',                   url: BASE + '/organizer/round_list' },
        { label: 'Participants',       section: 'Management',  icon: 'fa-users',                    url: BASE + '/admin/participants' },
        { label: 'Results',            section: 'Management',  icon: 'fa-star',                     url: BASE + '/organizer/results' },
        { label: 'Logout',             section: '',            icon: 'fa-arrow-right-from-bracket', url: BASE + '/logout' },
    ];

    var input    = document.getElementById('orgSearchInput');
    var dropdown = document.getElementById('orgSearchDropdown');
    var wrap     = document.getElementById('orgSearchWrap');

    function render(query) {
        var q = query.trim().toLowerCase();
        if (!q) { close(); return; }

        var hits = NAV.filter(function(n) {
            return n.label.toLowerCase().includes(q) || n.section.toLowerCase().includes(q);
        });

        if (!hits.length) {
            dropdown.innerHTML = '<div class="search-no-results">No pages found for "<strong>' + escHtml(query) + '</strong>"</div>';
        } else {
            dropdown.innerHTML = hits.map(function(n) {
                return '<a class="search-result-item" href="' + n.url + '">'
                    + '<span class="sri-icon"><i class="fa-solid ' + n.icon + '"></i></span>'
                    + '<span class="sri-label">' + escHtml(n.label)
                    + (n.section ? '<br><span class="sri-section">' + escHtml(n.section) + '</span>' : '')
                    + '</span>'
                    + '</a>';
            }).join('');
        }
        dropdown.classList.add('open');
    }

    function close() {
        dropdown.classList.remove('open');
        dropdown.innerHTML = '';
    }

    function escHtml(s) {
        if (!s) return '';
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    // Input events
    input.addEventListener('input', function() { render(this.value); });
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { close(); input.blur(); }
        if (e.key === 'Enter') {
            var first = dropdown.querySelector('.search-result-item');
            if (first) { window.location.href = first.href; }
        }
        // Arrow key navigation
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            var items = dropdown.querySelectorAll('.search-result-item');
            if (!items.length) return;
            var currentIndex = -1;
            for (var i = 0; i < items.length; i++) {
                if (items[i].classList.contains('active')) {
                    currentIndex = i;
                    items[i].classList.remove('active');
                }
            }
            if (e.key === 'ArrowDown') {
                currentIndex = (currentIndex + 1) % items.length;
            } else {
                currentIndex = (currentIndex - 1 + items.length) % items.length;
            }
            items[currentIndex].classList.add('active');
            items[currentIndex].scrollIntoView({ block: 'nearest' });
        }
    });

    // Click on result item
    dropdown.addEventListener('click', function(e) {
        var item = e.target.closest('.search-result-item');
        if (item) {
            window.location.href = item.href;
        }
    });

    // Close when clicking outside
    document.addEventListener('click', function(e) {
        if (!wrap.contains(e.target)) close();
    });

    // ── Keyboard shortcut: Ctrl+K or Cmd+K ──────────────────
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            input.focus();
            input.select();
        }
    });

    // ── Initialize tooltips/popovers if needed ──────────────
    if (typeof $ !== 'undefined' && $.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }

})();
</script>

<?= $this->renderSection('pageScript') ?>
</body>
</html>