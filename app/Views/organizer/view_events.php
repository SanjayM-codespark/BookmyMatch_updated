<?php 
$role_id = session()->get('role_id');
$layout = (session()->get('role_id') == 1) ? 'admin_base' : 'organizer_base'; ?>
<?= $this->extend($layout); ?>
<?= $this->section('pageCss'); ?>
<?= $this->endSection(); ?>

<?= $this->section('main_content'); ?>

<?php if (session()->has('success_message')) : ?>
    <div style="display:flex;align-items:center;gap:10px;background:#D1FAE5;color:#065F46;border:1px solid #A7F3D0;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:14px;">
        <i class="fa-solid fa-circle-check"></i>
        <?= session('success_message') ?><?php session()->remove('success_message') ?>
    </div>
<?php endif ?>

<div class="page-card">
    <div class="page-card-header">
        <span class="page-card-title">List of Events</span>
        <?php if (!empty($tourney_id) && session()->get('role_id') != 1) : ?>
            <a href="<?= base_url('create_event/' . $tourney_id) ?>"
               style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;background:#2563EB;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                <i class="fa-solid fa-plus"></i> Add Event
            </a>
        <?php endif ?>
    </div>

    <?php if (!empty($events)) : ?>
    <div class="org-table-wrap">
        <table class="org-table" id="eventsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Event Title</th>
                    <th>Event Type</th>
                    <th>Match Format</th>
                    <th>Fee</th>
                    <th>Teams</th>
                    <th>Tournament</th>
                    <th>Participants</th>
                    <?php if ($role_id != 1) : ?><th>Action</th><?php endif ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $row) : ?>
                <tr>
                    <td><?= $row->event_id ?></td>
                    <td><?= esc($row->event_name) ?></td>
                    <td><?= esc($row->listing_event_type) ?></td>
                    <td><?= esc($row->listing_match_type) ?></td>
                    <td><?= $row->listing_event_fee ? '₹' . $row->listing_event_fee : '—' ?></td>
                    <td>
                        <?php if (!empty($row->listing_event_type) && $row->listing_event_type === 'team') : ?>
                            <span class="badge-status badge-upcoming">Team</span><br>
                            <small style="color:#6B7280;">Max: <?= !empty($row->listing_event_max_teams) ? $row->listing_event_max_teams : '∞' ?> teams</small><br>
                            <?php if (!empty($row->listing_event_max_players)) : ?>
                                <small style="color:#9CA3AF;"><?= $row->listing_event_max_players ?> players/team</small>
                            <?php endif ?>
                        <?php else : ?>
                            <span style="color:#D1D5DB;">—</span>
                        <?php endif ?>
                    </td>
                    <td><?= esc($row->listing_title) ?></td>
                    <td>
                        <a class="btn-tbl btn-tbl-info" href="<?= base_url('organizer/participants/' . $row->listing_id . '/' . $row->listing_event_id) ?>">
                            <i class="fa-regular fa-eye"></i> View
                        </a>
                    </td>
                    <?php if ($role_id != 1) : ?>
                    <td>
                        <div style="display:flex;gap:5px;flex-wrap:wrap;">
                            <a class="btn-tbl btn-tbl-edit" href="<?= base_url('edit_tourney_event/' . $row->listing_event_id . '/' . $row->listing_event_listing_id) ?>">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </a>
                            <a class="btn-tbl btn-tbl-delete" href="javascript:void(0)"
                               onclick="deleteTourneyEvent('<?= $row->listing_event_id ?>')">
                                <i class="fa-solid fa-trash-can"></i> Delete
                            </a>
                            <button type="button" class="btn-tbl btn-match-schedule"
                               data-event-id="<?= $row->listing_event_id ?>"
                               style="background:#1E3A5F;color:#fff;border:none;cursor:pointer;">
                                <i class="fa-solid fa-calendar-check"></i> Match Schedule
                            </button>
                        </div>
                    </td>
                    <?php endif ?>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <?php else : ?>
    <div class="empty-state">
        <i class="fa-regular fa-calendar-days"></i>
        <p>No events found for this tournament.</p>
    </div>
    <?php endif ?>
</div>

<!-- MATCH SCHEDULE GROUPS POPUP MODAL (Exact match for Image 1) -->
<div id="bmmScheduleModalBackdrop" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.6);backdrop-filter:blur(3px);z-index:9999;align-items:center;justify-content:center;padding:16px;">
    <div id="bmmScheduleModalCard" style="background:#FFFFFF;border-radius:16px;max-width:880px;width:100%;box-shadow:0 25px 50px -12px rgba(0,0,0,0.35);padding:28px 32px;position:relative;animation:modalFadeIn .25s ease-out;max-height:90vh;overflow-y:auto;">
        
        <!-- Modal Header -->
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:22px;">
            <div>
                <h2 id="bmmModalTourneyTitle" style="font-size:22px;font-weight:800;color:#0F172A;margin:0 0 6px 0;letter-spacing:-0.3px;">
                    Loading...
                </h2>
                <p id="bmmModalEventSubtitle" style="font-size:14px;font-weight:500;color:#475569;margin:0;">
                    Please wait...
                </p>
            </div>
            <div style="display:flex;align-items:center;gap:12px;">
                <span id="bmmModalActiveBadge" style="background:#DBEAFE;color:#1E40AF;font-size:13px;font-weight:700;padding:6px 18px;border-radius:20px;white-space:nowrap;display:inline-block;">
                    5 Active
                </span>
                <button type="button" id="bmmModalCloseBtn" style="background:none;border:none;font-size:26px;color:#94A3B8;cursor:pointer;line-height:1;padding:2px 8px;border-radius:6px;transition:color .15s;" onmouseover="this.style.color='#0F172A'" onmouseout="this.style.color='#94A3B8'">
                    &times;
                </button>
            </div>
        </div>

        <!-- Groups Table -->
        <div style="border:1px solid #F1F5F9;border-radius:12px;overflow:hidden;">
            <table style="width:100%;border-collapse:separate;border-spacing:0;" id="bmmModalGroupsTable">
                <thead>
                    <tr style="background:#FCE8E6;">
                        <th style="padding:15px 22px;font-size:13px;font-weight:700;color:#1F2937;text-align:left;border:none;letter-spacing:.3px;">Groups</th>
                        <th style="padding:15px 22px;font-size:13px;font-weight:700;color:#1F2937;text-align:left;border:none;letter-spacing:.3px;">Type</th>
                        <th style="padding:15px 22px;font-size:13px;font-weight:700;color:#1F2937;text-align:left;border:none;letter-spacing:.3px;">Players</th>
                        <th style="padding:15px 22px;font-size:13px;font-weight:700;color:#1F2937;text-align:left;border:none;letter-spacing:.3px;">Rounds</th>
                        <th style="padding:15px 22px;font-size:13px;font-weight:700;color:#1F2937;text-align:right;border:none;letter-spacing:.3px;">ACTION</th>
                    </tr>
                </thead>
                <tbody id="bmmModalGroupsBody">
                    <tr>
                        <td colspan="5" style="text-align:center;padding:30px;color:#64748B;">
                            <i class="fa-solid fa-spinner fa-spin"></i> Loading schedule groups...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>

<style>
@keyframes modalFadeIn {
    from { opacity: 0; transform: scale(0.96) translateY(-10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
.bmm-group-row td {
    padding: 18px 22px;
    border-bottom: 1px solid #F1F5F9;
    vertical-align: middle;
}
.bmm-group-row:last-child td {
    border-bottom: none;
}
.btn-group-view {
    background: #0F172A;
    color: #FFFFFF !important;
    padding: 8px 24px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 13px;
    text-decoration: none !important;
    display: inline-block;
    transition: background .15s ease;
}
.btn-group-view:hover {
    background: #1E293B;
}
</style>

<?= $this->endSection(); ?>

<?= $this->section('pageScript'); ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(function(){
    $('#eventsTable').DataTable({ paging:true, ordering:true, info:true,
        language:{ search:'', searchPlaceholder:'Search...' }
    });

    // Match Schedule button clicked -> check groups, show Popup only when a group is available
    $(document).on('click', '.btn-match-schedule', function(e){
        e.preventDefault();
        var $btn = $(this);
        if ($btn.data('bmm-loading')) return;
        var eventId = $btn.data('event-id');
        var btnHtml = $btn.html();
        $btn.data('bmm-loading', 1).html('<i class="fa-solid fa-spinner fa-spin"></i> Checking...');
        openScheduleModal(eventId, function(){
            $btn.data('bmm-loading', 0).html(btnHtml);
        });
    });

    // Close Modal
    $('#bmmModalCloseBtn, #bmmScheduleModalBackdrop').on('click', function(e){
        if (e.target === this) {
            closeScheduleModal();
        }
    });

    $(document).on('keydown', function(e){
        if (e.key === 'Escape') closeScheduleModal();
    });
});

function openScheduleModal(eventId, done) {
    var $backdrop = $('#bmmScheduleModalBackdrop');

    $.getJSON('<?= base_url('organizer/get_event_schedule_groups') ?>/' + eventId, function(res){
        if (done) done();

        if (!res.status) {
            alert(res.message || 'Error loading schedule groups');
            return;
        }

        // Popup allowed only when at least one group exists for this tournament + event
        if (!res.has_group || !res.groups || res.groups.length === 0) {
            alert(res.message || 'No group available for this event. Please create a group first.');
            return;
        }

        $backdrop.css('display', 'flex');
        $('#bmmModalTourneyTitle').text(res.tournament_title || 'Tiruvallur District Badminton Championship');
        var subtitle = (res.event_name || 'Under 15 Boys Singles') + ' — ' + (res.sports_name || 'Badminton') + ' — ' + (res.venue || 'Indoor Stadium');
        $('#bmmModalEventSubtitle').text(subtitle);
        $('#bmmModalActiveBadge').text(res.active_badge || '5 Active');

        var rowsHtml = '';
        $.each(res.groups, function(i, g){
            rowsHtml += '<tr class="bmm-group-row">' +
                '<td style="font-weight:800;color:#0F172A;font-size:15px;">' + g.group_name + '</td>' +
                '<td style="font-weight:600;color:#1E293B;font-size:14px;">' + g.type + '</td>' +
                '<td style="font-weight:700;color:#0F172A;font-size:15px;">' + g.players + '</td>' +
                '<td style="font-weight:700;color:#0F172A;font-size:15px;">' + g.rounds + '</td>' +
                '<td style="text-align:right;">' +
                    (g.has_players ? '<a href="' + g.view_url + '" class="btn-group-view">View</a>'
                                   : '<span style="color:#94A3B8;font-size:13px;font-weight:600;">No match scheduled</span>') +
                '</td>' +
            '</tr>';
        });
        $('#bmmModalGroupsBody').html(rowsHtml);
    }).fail(function(){
        if (done) done();
        alert('Failed to fetch schedule data. Please check connection.');
    });
}

function closeScheduleModal() {
    $('#bmmScheduleModalBackdrop').fadeOut(150);
}

function deleteTourneyEvent(id) {
    if (!confirm('Are you sure you want to delete this event?')) return;
    $.post('<?= base_url('Ajax/delete_tourney_event') ?>', {event_id: id}, function(r) {
        var o = $.parseJSON(r);
        alert(o.message);
        if (o.success === 'success') location.reload();
    }).fail(function(){ alert('Something went wrong.'); });
}
</script>
<style>
.pagination .page-link { border-radius:6px; color:#2563EB; border:1px solid #E5E7EB; font-size:13px; }
.pagination .page-item.active .page-link { background:#2563EB; border-color:#2563EB; color:#fff; }
</style>
<?= $this->endSection(); ?>
