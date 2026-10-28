<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $page_title ?? 'Admin' ?> — BookMyMatch</title>

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
.adm-shell { display: flex; min-height: 100vh; }

/* ══════════════════════════════════════════════════════════
   SIDEBAR
══════════════════════════════════════════════════════════ */
.adm-sidebar {
    width: var(--sb-width); min-width: var(--sb-width);
    background: var(--sb-bg);
    position: fixed; top: 0; left: 0; height: 100vh;
    display: flex; flex-direction: column;
    z-index: 100; overflow-y: auto; overflow-x: hidden;
}

/* Brand */
.adm-brand {
    padding: 22px 16px 18px;
    display: flex; align-items: center; gap: 11px;
    border-bottom: 1px solid var(--divider); flex-shrink: 0;
}
.adm-brand-icon {
    width: 42px; height: 42px; border-radius: 10px;
    background: var(--accent);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 20px; flex-shrink: 0;
}
.adm-brand-text .bt1 { font-size: 19px; font-weight: 800; color: #fff; line-height: 1.1; letter-spacing: -0.3px; }
.adm-brand-text .bt2 { font-size: 11px; font-weight: 500; color: #64748B; margin-top: 1px; }

/* Nav wrapper */
.adm-nav { padding: 10px 10px; flex: 1; }

.nav-section-label {
    font-size: 10px; font-weight: 700; color: var(--nav-muted);
    letter-spacing: 0.12em; text-transform: uppercase;
    padding: 14px 10px 5px; display: block;
}

/* Nav item */
.adm-nav-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px; border-radius: 10px;
    color: var(--nav-text); font-size: 13.5px; font-weight: 500;
    cursor: pointer; transition: background 0.15s, color 0.15s;
    margin-bottom: 2px; white-space: nowrap;
}
.adm-nav-item:hover { background: var(--nav-hover); color: #fff; text-decoration: none; }
.adm-nav-item.active { background: var(--accent); color: #fff; }
.adm-nav-item.active:hover { background: var(--accent-dark); color: #fff; text-decoration: none; }
.adm-nav-item .nv-icon { width: 20px; text-align: center; font-size: 15px; flex-shrink: 0; color: var(--nav-muted); }
.adm-nav-item:hover .nv-icon, .adm-nav-item.active .nv-icon { color: #fff; }
.adm-nav-item .nv-arrow { margin-left: auto; font-size: 11px; color: var(--nav-muted);
    transition: transform 0.2s; flex-shrink: 0; }
.adm-nav-item[aria-expanded="true"] .nv-arrow { transform: rotate(90deg); }
.adm-nav-item[aria-expanded="true"] { color: #fff; }

/* Sub-items */
.adm-nav-sub { padding: 8px 12px 8px 38px; font-size: 12.5px; font-weight: 400;
    color: #94A3B8; border-radius: 8px; display: flex; align-items: center; gap: 8px;
    transition: background 0.15s, color 0.15s; margin-bottom: 1px; }
.adm-nav-sub:hover { background: var(--nav-hover); color: #E2E8F0; text-decoration: none; }
.adm-nav-sub.active { color: var(--accent); background: rgba(229,62,62,0.12); font-weight: 600; }
.adm-nav-sub::before { content: ''; width: 5px; height: 5px; border-radius: 50%;
    background: #334155; flex-shrink: 0; }
.adm-nav-sub.active::before { background: var(--accent); }

.adm-nav-divider { border: none; border-top: 1px solid var(--divider); margin: 8px 8px; }

/* Sidebar footer — user card */
.adm-sb-footer { padding: 12px; border-top: 1px solid var(--divider); flex-shrink: 0; }
.adm-sb-user {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px; border-radius: 10px;
    background: rgba(255,255,255,0.04); cursor: pointer; transition: background 0.15s;
}
.adm-sb-user:hover { background: rgba(255,255,255,0.08); }
.adm-sb-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,0.1);
    display: flex; align-items: center; justify-content: center;
    color: #94A3B8; font-size: 16px; flex-shrink: 0; overflow: hidden;
}
.adm-sb-avatar img { width: 100%; height: 100%; object-fit: cover; }
.adm-sb-uname { font-size: 13px; font-weight: 600; color: #E2E8F0;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 110px; }
.adm-sb-urole { font-size: 11px; color: #64748B; }

/* ══════════════════════════════════════════════════════════
   MAIN AREA
══════════════════════════════════════════════════════════ */
.adm-main { margin-left: var(--sb-width); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

/* Page Header */
.adm-page-header {
    background: #fff; border-bottom: 1px solid #E5E7EB;
    padding: 16px 28px;
    display: flex; align-items: center; justify-content: space-between;
    position: sticky; top: 0; z-index: 50;
}
.adm-page-header-left { display: flex; align-items: center; gap: 14px; }
.menu-btn { background: none; border: none; cursor: pointer; color: #6B7280; font-size: 18px; padding: 4px; }
.adm-ph-titles h1 { font-size: 22px; font-weight: 700; margin: 0; color: #0F172A; line-height: 1.2; }
.adm-ph-titles p  { font-size: 12px; color: #94A3B8; margin: 2px 0 0; font-weight: 400; }
.adm-page-header-right { display: flex; align-items: center; gap: 12px; }

/* Search */
.adm-search-wrap { position: relative; min-width: 240px; }
.adm-search-bar {
    display: flex; align-items: center; gap: 8px;
    background: #F8FAFC; border: 1px solid #E2E8F0;
    border-radius: 24px; padding: 7px 16px;
    font-size: 13px; color: #94A3B8; transition: border-color 0.15s;
}
.adm-search-bar:focus-within { border-color: #E53E3E; background: #fff; box-shadow: 0 0 0 3px rgba(229,62,62,.08); }
.adm-search-bar i { color: #94A3B8; font-size: 13px; flex-shrink: 0; }
.adm-search-bar input {
    border: none; background: transparent; outline: none;
    font-size: 13px; color: #374151; width: 100%;
    font-family: 'Montserrat', sans-serif;
}
.adm-search-bar input::placeholder { color: #94A3B8; }
.adm-search-dropdown {
    position: absolute; top: calc(100% + 8px); left: 0; right: 0;
    background: #fff; border: 1px solid #E5E7EB; border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.10); z-index: 200;
    overflow: hidden; display: none;
}
.adm-search-dropdown.open { display: block; }
.search-result-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 16px; cursor: pointer; text-decoration: none;
    color: #374151; font-size: 13px; font-weight: 500; transition: background 0.12s;
}
.search-result-item:hover { background: #FFF5F5; color: #E53E3E; text-decoration: none; }
.search-result-item .sri-icon {
    width: 30px; height: 30px; border-radius: 8px;
    background: #FEE2E2; color: #E53E3E;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; flex-shrink: 0;
}
.search-result-item .sri-section { font-size: 11px; color: #9CA3AF; font-weight: 400; }
.search-no-results { padding: 20px 16px; text-align: center; color: #94A3B8; font-size: 13px; }

/* Content */
.adm-content { padding: 24px 28px; flex: 1; }

/* ══════════════════════════════════════════════════════════
   SHARED COMPONENTS (same as organizer_base)
══════════════════════════════════════════════════════════ */
.page-card {
    background: #fff; border-radius: 12px; border: 1px solid #E5E7EB;
    padding: 20px 24px; margin-bottom: 20px; overflow-x: auto;
}
.page-card-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 18px; flex-wrap: wrap; gap: 10px;
}
.page-card-title { font-size: 15px; font-weight: 600; color: #0F172A; margin: 0; }

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

.badge-status { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-upcoming  { background: #FEF9C3; color: #854D0E; }
.badge-live      { background: #DCFCE7; color: #166534; }
.badge-completed { background: #F1F5F9; color: #64748B; }
.badge-cancelled { background: #FEE2E2; color: #991B1B; }
.badge-active    { background: #DCFCE7; color: #166534; }
.badge-inactive  { background: #FEE2E2; color: #991B1B; }
.badge-pending   { background: #FEF9C3; color: #854D0E; }
.badge-upi       { background: #DBEAFE; color: #1D4ED8; }

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
.btn-tbl-approve{ background:#DCFCE7; color:#166534; border-color:#86EFAC; }
.btn-tbl-approve:hover{ background:#BBF7D0; color:#166534; text-decoration:none; }
.btn-tbl-reject { background:#FEF2F2; color:#EF4444; border-color:#FECACA; }
.btn-tbl-reject:hover { background:#FEE2E2; color:#EF4444; text-decoration:none; }
.btn-tbl-action { background: var(--accent); color: #fff; border-color: var(--accent); }
.btn-tbl-action:hover{ background: var(--accent-dark); color:#fff; text-decoration:none; }

.filter-bar { display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end; margin-bottom:20px; }
.filter-bar .form-control, .filter-bar .custom-select {
    height:40px; font-size:13px; border-radius:8px; border:1px solid #E2E8F0; background:#F8FAFC;
}
.filter-bar .btn-filter {
    height:40px; padding:0 22px; border-radius:8px; border:none;
    background: var(--accent); color:#fff; font-size:13px; font-weight:600;
    cursor:pointer; display:flex; align-items:center; gap:6px; font-family:'Montserrat',sans-serif;
}
.filter-bar .btn-filter:hover { background: var(--accent-dark); }

.empty-state { text-align:center; padding:56px 0; color:#94A3B8; }
.empty-state i { font-size:44px; margin-bottom:14px; display:block; color:#E2E8F0; }
.empty-state p { font-size:14px; margin:0; }

/* Stat cards */
.stat-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; margin-bottom:24px; }
.stat-card {
    background:#fff; border-radius:12px; border:1px solid #E5E7EB;
    padding:20px 24px;
}
.stat-card .sc-label { font-size:11px; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:8px; }
.stat-card .sc-value { font-size:32px; font-weight:700; color:#0F172A; line-height:1; }

/* Normalize legacy admin content */
.adm-content .container-fluid { padding-left:0; padding-right:0; }
.adm-content .card { border-radius:12px !important; border:1px solid #E5E7EB !important; box-shadow:none !important; margin-bottom:20px; }
.adm-content .card-body { padding:20px 24px; }
.adm-content .form-control { border-radius:8px; font-size:13px; border-color:#E2E8F0; font-family:'Montserrat',sans-serif; }
.adm-content .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(229,62,62,.1); outline:none; }
.adm-content .btn-primary { background:var(--accent); border-color:var(--accent); font-family:'Montserrat',sans-serif; border-radius:8px; }
.adm-content .btn-primary:hover { background:var(--accent-dark); border-color:var(--accent-dark); }
.adm-content .btn-danger  { border-radius:8px; font-family:'Montserrat',sans-serif; }
.adm-content .btn-success { border-radius:8px; font-family:'Montserrat',sans-serif; }
.adm-content .table thead th { background:#FFF0EE; border-bottom:1px solid #FECACA; font-size:11.5px; text-transform:uppercase; letter-spacing:.05em; font-weight:700; color:#374151; }
.adm-content .table tbody tr { border-bottom:1px solid #F1F5F9; }
.adm-content .table tbody tr:hover { background:#FFFBFB; }
.adm-content .alert { border-radius:8px; font-size:13px; }
.adm-content section[style*="background-color"] { display:none !important; }
.adm-content .dash-head1, .adm-content .dash-link1 { display:none; }
.adm-content .main { margin:0 !important; padding:0 !important; }

.pagination .page-link { border-radius:6px; color:var(--accent); border:1px solid #E5E7EB; font-size:13px; font-family:'Montserrat',sans-serif; }
.pagination .page-item.active .page-link { background:var(--accent); border-color:var(--accent); color:#fff; }
.pagination .page-item.disabled .page-link { color:#CBD5E1; }

@media (max-width: 768px) {
    .adm-sidebar { transform:translateX(-100%); transition:transform 0.22s; }
    .adm-sidebar.open { transform:translateX(0); box-shadow:4px 0 20px rgba(0,0,0,0.4); }
    .adm-main { margin-left:0; }
    .adm-page-header { padding:12px 16px; }
    .adm-content { padding:16px; }
}
</style>
</head>
<body>
<div class="adm-shell">

<!-- ════════════════════════════════
     DARK SIDEBAR
════════════════════════════════ -->
<aside class="adm-sidebar" id="admSidebar">

    <!-- Brand -->
    <div class="adm-brand">
        <div class="adm-brand-icon"><i class="fa-solid fa-person-running"></i></div>
        <div class="adm-brand-text">
            <div class="bt1">Book</div>
            <div class="bt2">My Match</div>
        </div>
    </div>

    <!-- Nav -->
    <nav class="adm-nav">
        <?php
        $uri  = service('uri');
        $path = '/' . implode('/', $uri->getSegments());

        $inMaster     = preg_match('#/(admin/(participant|user|umpire|posts|events|sports|country|states|districts|cities)|organizer/umpire)#', $path);
        $inTournament = str_contains($path, '/tournament_list') || str_contains($path, '/admin/tournament');
        $inOrganizer  = str_contains($path, '/organizer_list');
        $inPackages   = preg_match('#/admin/(packages|memberships|edit_package)#', $path);
        $inSystems    = preg_match('#/admin/(general_settings|notifications|home_banners|privacy_policy|about_us|contact_us|faqs|terms_conditions)#', $path);
        ?>

        <span class="nav-section-label">Management</span>

        <!-- Dashboard -->
        <a href="<?= base_url('admin') ?>"
           class="adm-nav-item<?= ($path === '/admin' || $path === '/admin/') ? ' active' : '' ?>">
            <span class="nv-icon"><i class="fa-solid fa-border-all"></i></span>
            Dashboard
        </a>

        <!-- Booking History -->
        <a href="<?= base_url('admin/booking_history') ?>"
           class="adm-nav-item<?= str_contains($path, '/admin/booking_history') ? ' active' : '' ?>">
            <span class="nv-icon"><i class="fa-solid fa-calendar-check"></i></span>
            Booking History
        </a>

        <hr class="adm-nav-divider">
        <span class="nav-section-label">Admin</span>

        <!-- Master Entries -->
        <a class="adm-nav-item" data-toggle="collapse" href="#navMaster" role="button"
           aria-expanded="<?= $inMaster ? 'true' : 'false' ?>">
            <span class="nv-icon"><i class="fa-solid fa-table-cells-large"></i></span>
            Master Entries
            <i class="fa-solid fa-chevron-right nv-arrow"></i>
        </a>
        <div class="collapse <?= $inMaster ? 'show' : '' ?>" id="navMaster">
            <a href="<?= base_url('admin/participant') ?>" class="adm-nav-sub<?= str_contains($path,'/admin/participant') ? ' active' : '' ?>">Participants</a>
            <a href="<?= base_url('admin/user') ?>"        class="adm-nav-sub<?= str_contains($path,'/admin/user') ? ' active' : '' ?>">Users</a>
            <a href="<?= base_url('admin/umpire') ?>" class="adm-nav-sub<?= str_contains($path,'/admin/umpire') ? ' active' : '' ?>">Umpires</a>
            <a href="<?= base_url('admin/posts') ?>"       class="adm-nav-sub<?= str_contains($path,'/admin/posts') ? ' active' : '' ?>">Posts</a>
            <a href="<?= base_url('admin/events') ?>"      class="adm-nav-sub<?= str_contains($path,'/admin/events') ? ' active' : '' ?>">Events</a>
            <a href="<?= base_url('admin/sports') ?>"      class="adm-nav-sub<?= str_contains($path,'/admin/sports') ? ' active' : '' ?>">Sports</a>
            <a href="<?= base_url('admin/country') ?>"     class="adm-nav-sub<?= str_contains($path,'/admin/country') ? ' active' : '' ?>">Countries</a>
            <a href="<?= base_url('admin/states') ?>"      class="adm-nav-sub<?= str_contains($path,'/admin/states') ? ' active' : '' ?>">States</a>
            <a href="<?= base_url('admin/districts') ?>"   class="adm-nav-sub<?= str_contains($path,'/admin/districts') ? ' active' : '' ?>">Districts</a>
            <a href="<?= base_url('admin/cities') ?>"      class="adm-nav-sub<?= str_contains($path,'/admin/cities') ? ' active' : '' ?>">Cities</a>
        </div>

        <!-- Tournaments -->
        <a class="adm-nav-item" data-toggle="collapse" href="#navTournaments" role="button"
           aria-expanded="<?= $inTournament ? 'true' : 'false' ?>">
            <span class="nv-icon"><i class="fa-solid fa-trophy"></i></span>
            Tournaments
            <i class="fa-solid fa-chevron-right nv-arrow"></i>
        </a>
        <div class="collapse <?= $inTournament ? 'show' : '' ?>" id="navTournaments">
            <a href="<?= base_url('tournament_list') ?>"           class="adm-nav-sub<?= ($path === '/tournament_list') ? ' active' : '' ?>">All</a>
            <a href="<?= base_url('tournament_list/upcoming') ?>"  class="adm-nav-sub<?= str_contains($path,'upcoming') ? ' active' : '' ?>">Upcoming</a>
            <a href="<?= base_url('tournament_list/ongoing') ?>"   class="adm-nav-sub<?= str_contains($path,'ongoing') ? ' active' : '' ?>">Ongoing</a>
            <a href="<?= base_url('tournament_list/completed') ?>" class="adm-nav-sub<?= str_contains($path,'completed') ? ' active' : '' ?>">Completed</a>
        </div>

        <!-- Organizer List -->
        <a class="adm-nav-item" data-toggle="collapse" href="#navOrganizer" role="button"
           aria-expanded="<?= $inOrganizer ? 'true' : 'false' ?>">
            <span class="nv-icon"><i class="fa-regular fa-user"></i></span>
            Organizer List
            <i class="fa-solid fa-chevron-right nv-arrow"></i>
        </a>
        <div class="collapse <?= $inOrganizer ? 'show' : '' ?>" id="navOrganizer">
            <a href="<?= base_url('organizer_list/new') ?>"
               class="adm-nav-sub<?= str_contains($path,'/new') ? ' active' : '' ?>">
                New
                <?php if (!empty($data1)): ?>
                    <span style="margin-left:auto;background:#E53E3E;color:#fff;border-radius:10px;padding:1px 7px;font-size:10px;"><?= $data1 ?></span>
                <?php endif ?>
            </a>
            <a href="<?= base_url('organizer_list/verified') ?>"
               class="adm-nav-sub<?= str_contains($path,'/verified') ? ' active' : '' ?>">
                Verified
                <?php if (!empty($data2)): ?>
                    <span style="margin-left:auto;background:#059669;color:#fff;border-radius:10px;padding:1px 7px;font-size:10px;"><?= $data2 ?></span>
                <?php endif ?>
            </a>
        </div>

        <!-- Packages -->
        <a class="adm-nav-item" data-toggle="collapse" href="#navPackages" role="button"
           aria-expanded="<?= $inPackages ? 'true' : 'false' ?>">
            <span class="nv-icon"><i class="fa-solid fa-box-open"></i></span>
            Packages
            <i class="fa-solid fa-chevron-right nv-arrow"></i>
        </a>
        <div class="collapse <?= $inPackages ? 'show' : '' ?>" id="navPackages">
            <a href="<?= base_url('admin/packages') ?>"    class="adm-nav-sub<?= str_contains($path,'/admin/packages') || str_contains($path,'/admin/edit_package') ? ' active' : '' ?>">Manage Packages</a>
            <a href="<?= base_url('admin/memberships') ?>" class="adm-nav-sub<?= str_contains($path,'/admin/memberships') ? ' active' : '' ?>">Memberships</a>
        </div>

        <!-- Systems -->
        <a class="adm-nav-item" data-toggle="collapse" href="#navSystems" role="button"
           aria-expanded="<?= $inSystems ? 'true' : 'false' ?>">
            <span class="nv-icon"><i class="fa-solid fa-gear"></i></span>
            Systems
            <i class="fa-solid fa-chevron-right nv-arrow"></i>
        </a>
        <div class="collapse <?= $inSystems ? 'show' : '' ?>" id="navSystems">
            <a href="<?= base_url('admin/general_settings') ?>"  class="adm-nav-sub<?= str_contains($path,'general_settings') ? ' active' : '' ?>">General Settings</a>
            <a href="<?= base_url('admin/notifications') ?>"     class="adm-nav-sub<?= str_contains($path,'notifications') ? ' active' : '' ?>">Notifications</a>
            <a href="<?= base_url('admin/home_banners') ?>"      class="adm-nav-sub<?= str_contains($path,'home_banners') ? ' active' : '' ?>">Home Banners</a>
            <a href="<?= base_url('admin/privacy_policy') ?>"    class="adm-nav-sub<?= str_contains($path,'privacy_policy') ? ' active' : '' ?>">Privacy Policy</a>
            <a href="<?= base_url('admin/about_us') ?>"          class="adm-nav-sub<?= str_contains($path,'about_us') ? ' active' : '' ?>">About Us</a>
            <a href="<?= base_url('admin/contact_us') ?>"        class="adm-nav-sub<?= str_contains($path,'contact_us') ? ' active' : '' ?>">Contact Us</a>
            <a href="<?= base_url('admin/faqs') ?>"              class="adm-nav-sub<?= str_contains($path,'faqs') ? ' active' : '' ?>">FAQ's</a>
            <a href="<?= base_url('admin/terms_conditions') ?>"  class="adm-nav-sub<?= str_contains($path,'terms_conditions') ? ' active' : '' ?>">Terms &amp; Conditions</a>
        </div>

        <hr class="adm-nav-divider">

        <a href="<?= base_url('logout') ?>" class="adm-nav-item">
            <span class="nv-icon"><i class="fa-solid fa-arrow-right-from-bracket"></i></span>
            Logout
        </a>
    </nav>

    <!-- User card at bottom -->
    <div class="adm-sb-footer">
        <?php
        $sbAvatar = !empty($users->user_profile_img)
            ? base_url() . '/public/uploads/images/profile/' . ($users->user_id ?? '') . '/' . $users->user_profile_img
            : null;
        ?>
        <div class="adm-sb-user">
            <div class="adm-sb-avatar">
                <?php if ($sbAvatar): ?>
                    <img src="<?= $sbAvatar ?>" alt="">
                <?php else: ?>
                    <i class="fa-regular fa-user"></i>
                <?php endif ?>
            </div>
            <div>
                <div class="adm-sb-uname"><?= esc($users->user_name ?? 'Admin') ?></div>
                <div class="adm-sb-urole">Super Admin</div>
            </div>
        </div>
    </div>

</aside>

<!-- ════════════════════════════════
     MAIN
════════════════════════════════ -->
<div class="adm-main">

    <!-- Page Header -->
    <?php
    if (empty($page_title)) {
        $seg = service('uri')->getSegment(2) ?: service('uri')->getSegment(1) ?: 'dashboard';
        $page_title = ucwords(str_replace(['_', '-'], ' ', $seg));
    }
    ?>
    <header class="adm-page-header">
        <div class="adm-page-header-left">
            <button class="menu-btn" id="admSidebarToggle"><i class="fa-solid fa-bars"></i></button>
            <div class="adm-ph-titles">
                <h1><?= esc($page_title) ?></h1>
                <?php if (!empty($page_subtitle)): ?>
                    <p><?= esc($page_subtitle) ?></p>
                <?php endif ?>
            </div>
        </div>
        <div class="adm-page-header-right">
            <div class="adm-search-wrap" id="admSearchWrap">
                <div class="adm-search-bar">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="admSearchInput" placeholder="Search pages..." autocomplete="off">
                </div>
                <div class="adm-search-dropdown" id="admSearchDropdown"></div>
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="adm-content">
        <?= $this->renderSection('main_content') ?>
    </main>

</div>
</div><!-- /.adm-shell -->

<script src="<?= base_url() ?>/public/assets/js/jquery-3.6.0.min.js"></script>
<script src="<?= base_url() ?>/public/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url() ?>/public/assets/icons/fontawesome-free-6.0.0-web/js/fontawesome.min.js"></script>
<script>
document.getElementById('admSidebarToggle').addEventListener('click', function() {
    document.getElementById('admSidebar').classList.toggle('open');
});

// Quick-nav search
(function () {
    var BASE = '<?= base_url() ?>';
    var NAV = [
        { label: 'Dashboard',          section: 'Management',    icon: 'fa-border-all',               url: BASE + '/admin' },
        { label: 'Booking History',    section: 'Management',    icon: 'fa-calendar-check',           url: BASE + '/admin/booking_history' },
        { label: 'Participants',       section: 'Master Entries', icon: 'fa-users',                   url: BASE + '/admin/participant' },
        { label: 'Users',              section: 'Master Entries', icon: 'fa-user',                    url: BASE + '/admin/user' },
        { label: 'Umpires',            section: 'Master Entries', icon: 'fa-user-tie',                url: BASE + '/admin/umpire' },
        { label: 'Posts',              section: 'Master Entries', icon: 'fa-newspaper',               url: BASE + '/admin/posts' },
        { label: 'Events',             section: 'Master Entries', icon: 'fa-calendar-days',           url: BASE + '/admin/events' },
        { label: 'Sports',             section: 'Master Entries', icon: 'fa-baseball-bat-ball',       url: BASE + '/admin/sports' },
        { label: 'Countries',          section: 'Master Entries', icon: 'fa-earth-asia',              url: BASE + '/admin/country' },
        { label: 'States',             section: 'Master Entries', icon: 'fa-map',                     url: BASE + '/admin/states' },
        { label: 'Districts',          section: 'Master Entries', icon: 'fa-location-dot',            url: BASE + '/admin/districts' },
        { label: 'Cities',             section: 'Master Entries', icon: 'fa-city',                    url: BASE + '/admin/cities' },
        { label: 'All Tournaments',    section: 'Tournaments',   icon: 'fa-trophy',                   url: BASE + '/tournament_list' },
        { label: 'Upcoming Tournaments', section: 'Tournaments', icon: 'fa-trophy',                  url: BASE + '/tournament_list/upcoming' },
        { label: 'Organizer List - New', section: 'Organizer',   icon: 'fa-user-plus',               url: BASE + '/organizer_list/new' },
        { label: 'Organizer List - Verified', section: 'Organizer', icon: 'fa-user-check',           url: BASE + '/organizer_list/verified' },
        { label: 'Manage Packages',    section: 'Packages',      icon: 'fa-box-open',                 url: BASE + '/admin/packages' },
        { label: 'Memberships',        section: 'Packages',      icon: 'fa-id-card',                  url: BASE + '/admin/memberships' },
        { label: 'General Settings',   section: 'Systems',       icon: 'fa-gear',                     url: BASE + '/admin/general_settings' },
        { label: 'Notifications',      section: 'Systems',       icon: 'fa-bell',                     url: BASE + '/admin/notifications' },
        { label: 'Home Banners',       section: 'Systems',       icon: 'fa-image',                    url: BASE + '/admin/home_banners' },
        { label: 'Privacy Policy',     section: 'Systems',       icon: 'fa-shield',                   url: BASE + '/admin/privacy_policy' },
        { label: 'About Us',           section: 'Systems',       icon: 'fa-circle-info',              url: BASE + '/admin/about_us' },
        { label: 'Contact Us',         section: 'Systems',       icon: 'fa-envelope',                 url: BASE + '/admin/contact_us' },
        { label: "FAQ's",              section: 'Systems',       icon: 'fa-question-circle',          url: BASE + '/admin/faqs' },
        { label: 'Terms & Conditions', section: 'Systems',       icon: 'fa-file-contract',            url: BASE + '/admin/terms_conditions' },
        { label: 'Logout',             section: '',              icon: 'fa-arrow-right-from-bracket',  url: BASE + '/logout' },
    ];

    var input    = document.getElementById('admSearchInput');
    var dropdown = document.getElementById('admSearchDropdown');
    var wrap     = document.getElementById('admSearchWrap');

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
                    + '<span><span style="display:block;">' + escHtml(n.label) + '</span>'
                    + (n.section ? '<span class="sri-section">' + escHtml(n.section) + '</span>' : '')
                    + '</span></a>';
            }).join('');
        }
        dropdown.classList.add('open');
    }
    function close() { dropdown.classList.remove('open'); dropdown.innerHTML = ''; }
    function escHtml(s) { return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    input.addEventListener('input', function() { render(this.value); });
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { close(); input.blur(); }
        if (e.key === 'Enter') { var f = dropdown.querySelector('.search-result-item'); if (f) window.location.href = f.href; }
    });
    document.addEventListener('click', function(e) { if (!wrap.contains(e.target)) close(); });
})();
</script>
<?= $this->renderSection('pageScript') ?>
</body>
</html>
