<?= $this->extend('organizer_base'); ?>
<?= $this->section('pageCss'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
.match-vs { display:flex; align-items:center; gap:8px; font-weight:600; white-space:nowrap; }
.match-vs .vs-label { font-size:10px; font-weight:700; color:#94A3B8;
    background:#F1F5F9; border-radius:4px; padding:2px 6px; }
.winner-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px;
    border-radius:20px; font-size:11px; font-weight:600; background:#DCFCE7; color:#166534; }
.winner-badge i { font-size:10px; }
.no-result { font-size:12px; color:#94A3B8; font-style:italic; }
#resultsTable_wrapper .dataTables_length label,
#resultsTable_wrapper .dataTables_filter label { font-size:13px; font-weight:500; color:#374151; }
#resultsTable_wrapper .dataTables_length select,
#resultsTable_wrapper .dataTables_filter input {
    border:1px solid #E2E8F0; border-radius:8px; padding:5px 10px;
    font-size:13px; font-family:'Montserrat',sans-serif;
}
#resultsTable_wrapper .dataTables_filter input:focus { outline:none; border-color:#E53E3E; box-shadow:0 0 0 3px rgba(229,62,62,.1); }
#resultsTable_wrapper .dataTables_info { font-size:13px; color:#6B7280; }
#resultsTable_wrapper .dataTables_paginate .paginate_button { border-radius:6px !important; font-size:13px; }
#resultsTable_wrapper .dataTables_paginate .paginate_button.current,
#resultsTable_wrapper .dataTables_paginate .paginate_button.current:hover {
    background:#E53E3E !important; border-color:#E53E3E !important; color:#fff !important;
}
#resultsTable_wrapper .dataTables_paginate .paginate_button:hover {
    background:#FEE2E2 !important; border-color:#FECACA !important; color:#E53E3E !important;
}
</style>
<?= $this->endSection(); ?>

<?= $this->section('main_content'); ?>

<!-- Filter bar -->
<form method="get" action="<?= current_url() ?>">
    <div class="filter-bar" style="margin-bottom:20px;">
        <select name="tournament" class="custom-select" style="min-width:200px;">
            <option value="">All Tournaments</option>
            <?php if (!empty($tournaments)): foreach ($tournaments as $t): ?>
                <option value="<?= $t->listing_id ?>"
                    <?= (isset($_GET['tournament']) && $_GET['tournament'] == $t->listing_id) ? 'selected' : '' ?>>
                    <?= esc($t->listing_title) ?>
                </option>
            <?php endforeach; endif; ?>
        </select>
        <select name="event" class="custom-select" style="min-width:180px;">
            <option value="">All Events</option>
            <?php if (!empty($events)): foreach ($events as $e): ?>
                <option value="<?= $e->listing_event_id ?>"
                    <?= (isset($_GET['event']) && $_GET['event'] == $e->listing_event_id) ? 'selected' : '' ?>>
                    <?= esc($e->event_name ?? '') ?>
                </option>
            <?php endforeach; endif; ?>
        </select>
        <button type="submit" class="btn-filter">
            <i class="fa-solid fa-filter"></i> Filter
        </button>
        <?php if (!empty($_GET['tournament']) || !empty($_GET['event'])): ?>
            <a href="<?= current_url() ?>" class="btn-cancel" style="height:40px;display:flex;align-items:center;">
                Clear
            </a>
        <?php endif ?>
    </div>
</form>

<div class="page-card">
    <div class="page-card-header">
        <span class="page-card-title">Match Results</span>
        <span style="font-size:12px;color:#94A3B8;">
            <?= count($match_list ?? []) ?> match<?= count($match_list ?? []) !== 1 ? 'es' : '' ?>
        </span>
    </div>

    <div class="org-table-wrap">
        <table class="org-table" id="resultsTable" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tournament</th>
                    <th>Event</th>
                    <th>Round</th>
                    <th>Match</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Umpire</th>
                    <th>Winner</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($match_list)): ?>
                    <?php foreach ($match_list as $row): ?>
                        <tr>
                            <td><?= $row->match_id ?></td>
                            <td style="font-weight:500;color:#0F172A;"><?= esc($row->listing_title ?? '') ?></td>
                            <td><?= esc($row->event_name ?? '') ?></td>
                            <td>
                                <span class="badge-status badge-upcoming" style="background:#EDE9FE;color:#7C3AED;">
                                    <?= esc($row->round_name ?? '') ?>
                                </span>
                            </td>
                            <td>
                                <div class="match-vs">
                                    <span><?= esc($row->team1 ?? '—') ?></span>
                                    <span class="vs-label">VS</span>
                                    <span><?= esc($row->team2 ?? '—') ?></span>
                                </div>
                            </td>
                            <td style="white-space:nowrap;color:#6B7280;">
                                <?= !empty($row->match_date) ? date('d M Y, h:i A', strtotime($row->match_date)) : '—' ?>
                            </td>
                            <td><?= esc($row->match_location ?? '—') ?></td>
                            <td><?= esc($row->umpire_name ?? '—') ?></td>
                            <td>
                                <?php if (!empty($row->winner_name)): ?>
                                    <span class="winner-badge">
                                        <i class="fa-solid fa-trophy"></i>
                                        <?= esc($row->winner_name) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="no-result">Pending</span>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <i class="fa-regular fa-star"></i>
                                <p>No match results found. Results will appear once matches are completed.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('pageScript'); ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function () {
    $('#resultsTable').DataTable({
        paging: true,
        ordering: true,
        info: true,
        order: [[0, 'desc']],
        language: {
            search: '',
            searchPlaceholder: 'Search results...',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ matches',
            paginate: { previous: 'Previous', next: 'Next' }
        }
    });
});
</script>
<?= $this->endSection(); ?>
