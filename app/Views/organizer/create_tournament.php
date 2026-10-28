<?php
$page_title    = 'Create Tournament';
$page_subtitle = 'Fill in the details to create a new tournament';
?>
<?= $this->extend('organizer_base'); ?>

<?= $this->section('pageCss'); ?>
<link rel="stylesheet" href="<?= base_url() ?>/public/assets/plugins/minimal_rich_editor/richtext.min.css">
<style>
/* ── Form Cards ──────────────────────────────────────────── */
.form-card {
    background: #fff; border-radius: 12px; border: 1px solid #E5E7EB;
    padding: 24px 28px; margin-bottom: 20px;
}
.form-card-header {
    display: flex; align-items: flex-start; gap: 14px; margin-bottom: 22px;
}
.card-icon {
    width: 44px; height: 44px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 18px;
}
.card-icon.blue   { background: #DBEAFE; color: #2563EB; }
.card-icon.green  { background: #D1FAE5; color: #059669; }
.card-icon.purple { background: #EDE9FE; color: #7C3AED; }
.card-icon.orange { background: #FEF3C7; color: #D97706; }
.card-icon.indigo { background: #E0E7FF; color: #4338CA; }
.form-card-header .card-text h6 {
    font-size: 15px; font-weight: 600; color: #111827; margin: 0 0 3px;
}
.form-card-header .card-text p {
    font-size: 12px; color: #6B7280; margin: 0;
}

/* ── Input Groups ────────────────────────────────────────── */
.input-icon-wrap { position: relative; }
.input-icon-wrap .input-prefix-icon {
    position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
    color: #9CA3AF; font-size: 14px; pointer-events: none;
}
.input-icon-wrap .form-control,
.input-icon-wrap .custom-select { padding-left: 36px; }
.form-label-sm { font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 5px; }
.form-control, .custom-select {
    border: 1px solid #D1D5DB; border-radius: 8px;
    font-size: 14px; color: #111827; height: 40px;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.form-control:focus, .custom-select:focus {
    border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37,99,235,.12); outline: none;
}
textarea.form-control { height: auto; }
.field-error { font-size: 12px; color: #EF4444; margin-top: 3px; }

/* ── Events Card ─────────────────────────────────────────── */
.event-item {
    border: 1px solid #E5E7EB; border-radius: 10px;
    padding: 20px; margin-bottom: 16px; background: #F9FAFB;
}
.event-item .event-item-header {
    font-size: 13px; font-weight: 600; color: #374151;
    margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
}
.event-number {
    width: 24px; height: 24px; border-radius: 50%;
    background: #2563EB; color: #fff;
    font-size: 11px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
}
.team-settings { display: none; }
.event-add-remove { display: flex; justify-content: flex-end; gap: 8px; margin-top: 12px; }
.btn-icon-round {
    width: 34px; height: 34px; border-radius: 50%; display: flex;
    align-items: center; justify-content: center; border: none; cursor: pointer;
    font-size: 14px; transition: all 0.15s;
}
.btn-icon-round.add { background: #D1FAE5; color: #059669; }
.btn-icon-round.add:hover { background: #A7F3D0; }
.btn-icon-round.remove { background: #FEE2E2; color: #EF4444; }
.btn-icon-round.remove:hover { background: #FECACA; }

/* ── Bottom Action Bar ───────────────────────────────────── */
.org-action-bar {
    background: #fff; border-top: 1px solid #E5E7EB;
    padding: 16px 28px; display: flex; justify-content: flex-end; gap: 10px;
    position: sticky; bottom: 0; z-index: 40;
}
.btn-cancel {
    border: 1px solid #D1D5DB; background: #fff; color: #374151;
    border-radius: 8px; padding: 9px 20px; font-size: 14px; font-weight: 500;
    cursor: pointer; transition: all 0.15s;
}
.btn-cancel:hover { background: #F3F4F6; }
.btn-draft {
    border: 1px solid #2563EB; background: #fff; color: #2563EB;
    border-radius: 8px; padding: 9px 20px; font-size: 14px; font-weight: 500;
    cursor: pointer; transition: all 0.15s;
}
.btn-draft:hover { background: #EFF6FF; }
.btn-submit-main {
    border: none; background: #2563EB; color: #fff;
    border-radius: 8px; padding: 9px 22px; font-size: 14px; font-weight: 600;
    cursor: pointer; display: flex; align-items: center; gap: 8px;
    transition: background 0.15s;
}
.btn-submit-main:hover { background: #1D4ED8; }

/* ── Alerts ─────────────────────────────────────────────── */
.org-alert {
    border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;
    font-size: 14px; display: flex; align-items: flex-start; gap: 10px;
}
.org-alert.success { background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; }
.org-alert.danger  { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
.org-alert.info    { background: #EFF6FF; color: #1E3A8A; border: 1px solid #BFDBFE; }
.org-alert ul { margin: 6px 0 0 18px; padding: 0; }
</style>
<?= $this->endSection(); ?>

<?= $this->section('main_content'); ?>

<form id="ct-form" action="<?= current_url() ?>" method="post" enctype="multipart/form-data">

<!-- ── Alerts ──────────────────────────────────────────── -->
<?php if (session()->has('success_message')) : ?>
    <div class="org-alert success">
        <i class="fa-solid fa-circle-check"></i>
        <div><?= session('success_message') ?><?php session()->remove('success_message') ?></div>
    </div>
<?php endif ?>
<?php if (session()->has('error')) : ?>
    <div class="org-alert danger">
        <i class="fa-solid fa-circle-exclamation"></i>
        <div><?= session('error') ?><?php session()->remove('error') ?></div>
    </div>
<?php endif ?>
<?php if ($__errors = flash_errors()) : ?>
    <div class="org-alert danger">
        <i class="fa-solid fa-circle-exclamation"></i>
        <div>
            <strong>Please fix the following data:</strong>
            <ul>
                <?php foreach ($__errors as $err) : ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
<?php endif ?>

<!-- ── Package / Membership usage banner ──────────────── -->
<?php if (isset($package)) : ?>
    <div class="org-alert info">
        <i class="fa-solid fa-circle-info"></i>
        <div>
            You're on the <strong><?= esc($package->package_name) ?></strong> package —
            <?php if ($max_tournaments > 0) : ?>
                <?= (int) $tournament_used ?> of <?= (int) $max_tournaments ?> tournaments used
                (<?= (int) $remaining_tournaments ?> remaining).
            <?php else : ?>
                unlimited tournaments available.
            <?php endif ?>
        </div>
    </div>
<?php endif ?>

<!-- ① Tournament Information ──────────────────────────── -->
<div class="form-card">
    <div class="form-card-header">
        <div class="card-icon blue"><i class="fa-solid fa-trophy"></i></div>
        <div class="card-text">
            <h6>Tournament Information</h6>
            <p>Basic details about your tournament</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label-sm">Tournament Name <span class="text-danger">*</span></label>
            <input type="text" name="title" value="<?= set_value('title') ?>"
                   class="form-control" placeholder="e.g. City Badminton Open 2026">
            <div class="field-error"><?= session('errors.title') ?></div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label-sm">Sport <span class="text-danger">*</span></label>
            <div class="input-icon-wrap">
                <i class="fa-solid fa-key input-prefix-icon"></i>
                <select id="sports" name="sports" class="form-control custom-select">
                    <option value="">Select Sport</option>
                    <?php if (!empty($sports)) foreach ($sports as $vt) : ?>
                        <option value="<?= $vt->sports_id ?>"
                            data-sport-name="<?= esc(strtolower($vt->sports_name)) ?>"
                            <?= set_value('sports') == $vt->sports_id ? 'selected' : '' ?>>
                            <?= esc(ucfirst($vt->sports_name)) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="field-error"><?= session('errors.sports') ?></div>
        </div>
        <div class="col-md-12 mb-1">
            <label class="form-label-sm">Description</label>
            <textarea name="details" class="form-control" rows="3" placeholder="Describe your tournament..."><?= set_value('details') ?></textarea>
            <div class="field-error"><?= session('errors.details') ?></div>
        </div>
    </div>
</div>

<!-- ② Schedule ────────────────────────────────────────── -->
<div class="form-card">
    <div class="form-card-header">
        <div class="card-icon green"><i class="fa-regular fa-calendar-days"></i></div>
        <div class="card-text">
            <h6>Schedule</h6>
            <p>Set tournament start, end and last booking dates</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label-sm">Start Date <span class="text-danger">*</span></label>
            <div class="input-icon-wrap">
                <i class="fa-regular fa-calendar input-prefix-icon"></i>
                <input type="datetime-local" id="start_date" name="start_date"
                       value="<?= set_value('start_date') ?>"
                       onchange="updateEventStart(this)"
                       class="form-control start-date">
            </div>
            <div class="field-error"><?= session('errors.start_date') ?></div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label-sm">End Date <span class="text-danger">*</span></label>
            <div class="input-icon-wrap">
                <i class="fa-regular fa-calendar input-prefix-icon"></i>
                <input type="datetime-local" id="end_date" name="end_date"
                       value="<?= set_value('end_date') ?>"
                       onchange="updateEventEnd(this)"
                       class="form-control end-date">
            </div>
            <small class="text-muted" style="font-size:11px;">Must be on or after the tournament start date</small>
            <div class="field-error"><?= session('errors.end_date') ?></div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label-sm">Last Booking Date <span class="text-danger">*</span></label>
            <div class="input-icon-wrap">
                <i class="fa-regular fa-clock input-prefix-icon"></i>
                <input type="datetime-local" id="lastbook_date" name="lastbook_date"
                       value="<?= set_value('lastbook_date') ?>"
                       class="form-control lastbook-date">
            </div>
            <small class="text-muted" style="font-size:11px;">Must be on or before the tournament start date</small>
            <div class="field-error"><?= session('errors.lastbook_date') ?></div>
        </div>
    </div>
</div>

<!-- ③ Location ────────────────────────────────────────── -->
<div class="form-card">
    <div class="form-card-header">
        <div class="card-icon purple"><i class="fa-solid fa-location-dot"></i></div>
        <div class="card-text">
            <h6>Location</h6>
            <p>Where the tournament will be held</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label-sm">Country <span class="text-danger">*</span></label>
            <div class="input-icon-wrap">
                <i class="fa-solid fa-globe input-prefix-icon"></i>
                <select id="country" name="country" class="form-control custom-select">
                    <option value="">Select Country</option>
                    <?php if (!empty($countries)) foreach ($countries as $vt) : ?>
                        <option value="<?= $vt->country_id ?>"
                            <?= set_value('country') == $vt->country_id ? 'selected' : '' ?>>
                            <?= esc(ucfirst($vt->country_name)) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="field-error"><?= session('errors.country') ?></div>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label-sm">State <span class="text-danger">*</span></label>
            <div class="input-icon-wrap">
                <i class="fa-solid fa-map input-prefix-icon"></i>
                <select id="state" name="state" class="form-control custom-select">
                    <option value="">Select State</option>
                </select>
            </div>
            <div class="field-error"><?= session('errors.state') ?></div>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label-sm">District <span class="text-danger">*</span></label>
            <div class="input-icon-wrap">
                <i class="fa-solid fa-building input-prefix-icon"></i>
                <select id="district" name="district" class="form-control custom-select">
                    <option value="">Select District</option>
                </select>
            </div>
            <div class="field-error"><?= session('errors.district') ?></div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label-sm">City</label>
            <input type="text" id="city" name="city" value="<?= set_value('city') ?>"
                   class="form-control" placeholder="City">
            <div class="field-error"><?= session('errors.city') ?></div>
        </div>
        <div class="col-md-6 mb-1">
            <label class="form-label-sm">Venue / Address</label>
            <textarea name="address" class="form-control" rows="2" placeholder="Venue details..."><?= set_value('address') ?></textarea>
            <div class="field-error"><?= session('errors.address') ?></div>
        </div>
    </div>
</div>

<!-- ④ Additional Settings ─────────────────────────────── -->
<div class="form-card">
    <div class="form-card-header">
        <div class="card-icon orange"><i class="fa-solid fa-gear"></i></div>
        <div class="card-text">
            <h6>Additional Settings</h6>
            <p>Banner, rules and other options</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label-sm">Tournament Banner</label>
            <input type="file" name="listing_image" class="form-control" style="height:auto;padding:7px 12px;">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label-sm">Rules File (PDF/DOC)</label>
            <input type="file" name="listing_rules_file" class="form-control" style="height:auto;padding:7px 12px;">
        </div>
        <div class="col-md-12 mb-1">
            <label class="form-label-sm">Rules &amp; Regulations</label>
            <textarea class="form-control todolist" name="rules" rows="4" placeholder="Enter rules..."></textarea>
            <div class="field-error"><?= session('errors.rules') ?></div>
        </div>
    </div>
</div>

<!-- ⑤ Add Events ─────────────────────────────────────── -->
<div class="form-card">
    <div class="form-card-header">
        <div class="card-icon indigo"><i class="fa-regular fa-calendar-plus"></i></div>
        <div class="card-text">
            <h6>Events</h6>
            <p>Add one or more events to this tournament</p>
        </div>
    </div>

    <ul class="list-unstyled m-0" id="events-list">
        <li class="event-item">
            <div class="event-item-header">
                <span class="event-number">1</span> Event Details
            </div>
            <div class="row">
                <!-- Event name -->
                <div class="col-md-6 mb-3">
                    <label class="form-label-sm">Event <span class="text-danger">*</span></label>
                    <select class="form-control custom-select event-name" name="event_name[]">
                        <option value="">Select Event</option>
                        <?php if (!empty($event_list)) foreach ($event_list as $mfr) : ?>
                            <option value="<?= $mfr->event_id ?>"><?= esc(ucfirst($mfr->event_name)) ?></option>
                        <?php endforeach ?>
                    </select>
                    <div class="field-error"><?= session('errors.event_name[]') ?></div>
                </div>
                <!-- Event type -->
                <div class="col-md-6 mb-3">
                    <label class="form-label-sm">Event Type <span class="text-danger">*</span></label>
                    <select class="form-control custom-select event_type_select" name="event_type[]">
                        <option value="">Select Type</option>
                        <option value="individual">Individual</option>
                        <option value="doubles">Doubles</option>
                        <option value="team">Team</option>
                    </select>
                    <small class="text-muted event-type-lock-note" style="display:none;font-size:11px;">
                        Locked to Team for Cricket
                    </small>
                    <div class="field-error"><?= session('errors.type[]') ?></div>
                </div>
                <!-- Team settings (shown only for team type) -->
                <div class="col-md-6 mb-3 team-settings">
                    <label class="form-label-sm">Max Players Per Team</label>
                    <input type="number" class="form-control" name="max_players[]" min="1" value="1" placeholder="e.g. 11">
                </div>
                <div class="col-md-6 mb-3 team-settings">
                    <label class="form-label-sm">Max Teams <small class="text-muted">(0 = unlimited)</small></label>
                    <input type="number" class="form-control" name="max_teams[]" min="0" value="0" placeholder="0 = unlimited">
                </div>
                <!-- Match type -->
                <div class="col-md-6 mb-3">
                    <label class="form-label-sm">Match Format <span class="text-danger">*</span></label>
                    <select class="form-control custom-select match-type" name="type[]">
                        <option value="">Choose format</option>
                        <option value="knockout">Knockout</option>
                        <option value="knockout_round">Knockout Round</option>
                        <option value="round_robin_knockout">Round Robin Knockout</option>
                    </select>
                    <div class="field-error"><?= session('errors.type[]') ?></div>
                </div>
                <!-- Fee -->
                <div class="col-md-6 mb-3">
                    <label class="form-label-sm">Registration Fee (₹)</label>
                    <div class="input-icon-wrap">
                        <i class="fa-solid fa-indian-rupee-sign input-prefix-icon"></i>
                        <input type="text" class="form-control fee" name="fee[]"
                               value="<?= set_value('fee[]') ?>" placeholder="0.00">
                    </div>
                    <div class="field-error"><?= session('errors.fee[]') ?></div>
                </div>
                <!-- Event start -->
                <div class="col-md-6 mb-3">
                    <label class="form-label-sm">Event Start Date</label>
                    <div class="input-icon-wrap">
                        <i class="fa-regular fa-calendar input-prefix-icon"></i>
                        <input type="datetime-local" name="event_start[]"
                               value="<?= set_value('event_start[]') ?>"
                               class="form-control event-start">
                    </div>
                    <small class="text-muted" style="font-size:11px;">Must be on or after tournament start date</small>
                    <div class="field-error"><?= session('errors.event_start[]') ?></div>
                </div>
                <!-- Event end -->
                <div class="col-md-6 mb-3">
                    <label class="form-label-sm">Event End Date</label>
                    <div class="input-icon-wrap">
                        <i class="fa-regular fa-calendar input-prefix-icon"></i>
                        <input type="datetime-local" name="event_end[]"
                               value="<?= set_value('event_end[]') ?>"
                               class="form-control event-end">
                    </div>
                    <small class="text-muted" style="font-size:11px;">Must be on or before tournament end date</small>
                    <div class="field-error"><?= session('errors.event_end[]') ?></div>
                </div>
                <!-- Matchmaking date -->
                <div class="col-md-6 mb-3">
                    <label class="form-label-sm">Players Reporting Time</label>
                    <div class="input-icon-wrap">
                        <i class="fa-solid fa-shuffle input-prefix-icon"></i>
                        <input type="datetime-local" name="matchmaking_date[]"
                               value="<?= set_value('matchmaking_date[]') ?>"
                               class="form-control matchmaking-date">
                    </div>
                    <small class="text-muted" style="font-size:11px;">Must be between event start and end dates</small>
                    <div class="field-error"><?= session('errors.matchmaking_date[]') ?></div>
                </div>
                <!-- Required IDs -->
                <div class="col-md-12 mb-2">
                    <label class="form-label-sm">Required IDs</label>
                    <div style="display:flex;gap:16px;flex-wrap:wrap;">
                        <label style="font-size:13px;cursor:pointer;display:flex;align-items:center;gap:6px;">
                            <input type="checkbox" name="tnba_id[]" value="1"> TNBA-ID
                        </label>
                        <label style="font-size:13px;cursor:pointer;display:flex;align-items:center;gap:6px;">
                            <input type="checkbox" name="bai_id[]" value="1"> BAI-ID
                        </label>
                        <label style="font-size:13px;cursor:pointer;display:flex;align-items:center;gap:6px;">
                            <input type="checkbox" name="other_id[]" value="1"> Other-ID
                        </label>
                        <label style="font-size:13px;cursor:pointer;display:flex;align-items:center;gap:6px;">
                            <input type="checkbox" name="dob[]" value="1"> DOB
                        </label>
                    </div>
                </div>
            </div>
            <!-- Add / Remove buttons -->
            <div class="event-add-remove">
                <button type="button" class="btn-icon-round remove remove-event" title="Remove event">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
                <button type="button" class="btn-icon-round add add-event" title="Add another event">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>
        </li>
    </ul>
</div>

<!-- hidden draft flag -->
<input type="hidden" name="save_as_draft" id="draftFlag" value="0">

</form>

<!-- ── Bottom Action Bar (outside form for sticky, submits via JS) ── -->
<div class="org-action-bar">
    <button type="button" class="btn-cancel" onclick="history.back()">Cancel</button>
    <button type="button" class="btn-draft" onclick="submitDraft()">Save Draft</button>
    <button type="button" class="btn-submit-main" onclick="document.getElementById('ct-form').submit()">
        Create Tournament <i class="fa-solid fa-arrow-right"></i>
    </button>
</div>

<?= $this->endSection(); ?>

<?= $this->section('pageScript'); ?>
<script src="<?= base_url() ?>/public/assets/plugins/minimal_rich_editor/jquery.richtext.js"></script>
<script>
// Standard 3-option Event Type list, reused whenever a row is unlocked
var EVENT_TYPE_OPTIONS_HTML =
    '<option value="">Select Type</option>' +
    '<option value="individual">Individual</option>' +
    '<option value="doubles">Doubles</option>' +
    '<option value="team">Team</option>';

// Badminton-specific options (only Individual and Doubles)
var BADMINTON_EVENT_TYPE_OPTIONS_HTML =
    '<option value="">Select Type</option>' +
    '<option value="individual">Individual</option>' +
    '<option value="doubles">Doubles</option>';

// Cricket-specific options (only Team)
var CRICKET_EVENT_TYPE_OPTIONS_HTML =
    '<option value="team" selected>Team</option>';

function getSportName() {
    var $opt = $('#sports option:selected');
    var name = ($opt.data('sport-name') || $opt.text() || '').toString().toLowerCase();
    return name;
}

function isCricketSelected() {
    return getSportName().indexOf('cricket') !== -1;
}

function isBadmintonSelected() {
    return getSportName().indexOf('badminton') !== -1;
}

// Apply sport-specific event type restrictions
function applySportEventTypeLock() {
    var isCricket = isCricketSelected();
    var isBadminton = isBadmintonSelected();

    $('.event_type_select').each(function () {
        var $sel = $(this);
        var currentVal = $sel.val();
        
        if (isCricket) {
            // Cricket: Only Team allowed
            $sel.html(CRICKET_EVENT_TYPE_OPTIONS_HTML);
            $sel.prop('disabled', false);
            $sel.trigger('change');
        } else if (isBadminton) {
            // Badminton: Only Individual and Doubles (no Team)
            $sel.html(BADMINTON_EVENT_TYPE_OPTIONS_HTML);
            // If current value is 'team', reset it
            if (currentVal === 'team') {
                $sel.val('');
            }
            $sel.trigger('change');
        } else {
            // Other sports: All three options available
            $sel.html(EVENT_TYPE_OPTIONS_HTML);
            $sel.trigger('change');
        }

        // Show/hide lock note
        var $note = $sel.closest('.event-item').find('.event-type-lock-note');
        $note.toggle(isCricket);
        if (isBadminton) {
            $note.text('Team not available for Badminton').show();
        } else if (!isCricket) {
            $note.hide();
        }
    });
}

// Update date restrictions for all event date fields
function updateDateRestrictions() {
    var startDateVal = $('#start_date').val();
    var endDateVal = $('#end_date').val();
    
    // 1. Last Booking Date: Must be <= Tournament Start Date
    var $last = $('#lastbook_date');
    if (startDateVal) {
        $last.attr('max', startDateVal);
        if ($last.val() && $last.val() > startDateVal) {
            $last.val('');
        }
    } else {
        $last.removeAttr('max');
    }
    
    // 2. End Date: Must be >= Tournament Start Date
    var $endDate = $('#end_date');
    if (startDateVal) {
        $endDate.attr('min', startDateVal);
        if ($endDate.val() && $endDate.val() < startDateVal) {
            $endDate.val('');
        }
    } else {
        $endDate.removeAttr('min');
    }
    
    // 3. Event Start Date: Must be between Tournament Start Date and Tournament End Date
    $('.event-start').each(function() {
        var $this = $(this);
        if (startDateVal) {
            $this.attr('min', startDateVal);
            if ($this.val() && $this.val() < startDateVal) {
                $this.val('');
            }
        } else {
            $this.removeAttr('min');
        }
        if (endDateVal) {
            $this.attr('max', endDateVal);
            if ($this.val() && $this.val() > endDateVal) {
                $this.val('');
            }
        } else {
            $this.removeAttr('max');
        }
    });

    // 4. Event End Date: Must be <= Tournament End Date
    $('.event-end').each(function() {
        var $this = $(this);
        if (endDateVal) {
            $this.attr('max', endDateVal);
            if ($this.val() && $this.val() > endDateVal) {
                $this.val('');
            }
        } else {
            $this.removeAttr('max');
        }
    });

    // 5. Event End Date must be >= Event Start Date (per row), never before Tournament Start Date
    $('.event-item').each(function() {
        var $row = $(this);
        var $start = $row.find('.event-start');
        var $end = $row.find('.event-end');
        
        var rowMin = $start.val() || startDateVal;
        if (rowMin) {
            $end.attr('min', rowMin);
            if ($end.val() && $end.val() < rowMin) {
                $end.val('');
            }
        } else {
            $end.removeAttr('min');
        }
    });

    // 6. Matchmaking Date: Must be between Event Start and End Date (per row)
    $('.event-item').each(function() {
        var $row = $(this);
        var $start = $row.find('.event-start');
        var $end = $row.find('.event-end');
        var $matchmaking = $row.find('.matchmaking-date');
        
        if ($start.val()) {
            $matchmaking.attr('min', $start.val());
        } else {
            $matchmaking.removeAttr('min');
        }
        
        if ($end.val()) {
            $matchmaking.attr('max', $end.val());
        } else {
            $matchmaking.removeAttr('max');
        }
        
        if ($matchmaking.val()) {
            if ($start.val() && $matchmaking.val() < $start.val()) {
                $matchmaking.val('');
            }
            if ($end.val() && $matchmaking.val() > $end.val()) {
                $matchmaking.val('');
            }
        }
    });
}

$(function() {
    // Rich text editor
    $('.todolist').richText({
        height: 200, fontColor: false, imageUpload: false,
        fileUpload: false, videoEmbed: false, urls: false,
        table: false, removeStyles: false, code: false
    });

    // Min date for start_date AND lastbook_date (today) — lastbook_date
    // previously only got a "max" tied to start_date, so nothing stopped
    // a date in the past (before today) from being picked.
    var now = new Date();
    var pad = n => String(n).padStart(2,'0');
    var todayStr = now.getFullYear()+'-'+pad(now.getMonth()+1)+'-'+pad(now.getDate())+'T'+pad(now.getHours())+':'+pad(now.getMinutes());
    $('#start_date').attr('min', todayStr);
    $('#lastbook_date').attr('min', todayStr);

    // Apply sport-specific event type lock on load
    applySportEventTypeLock();
    
    // Update date restrictions on load
    setTimeout(updateDateRestrictions, 100);

    // When sport changes, update event type restrictions
    $(document).on('change', '#sports', function() {
        applySportEventTypeLock();
    });

    // When start date changes
    $(document).on('change', '#start_date', function() {
        updateEventStart(this);
        updateDateRestrictions();
    });

    // When end date changes
    $(document).on('change', '#end_date', function() {
        updateEventEnd(this);
        updateDateRestrictions();
    });

    // When event start date changes in any row
    $(document).on('change', '.event-start', function() {
        updateDateRestrictions();
    });

    // When event end date changes in any row
    $(document).on('change', '.event-end', function() {
        updateDateRestrictions();
    });

    // Country → State
    $(document).on('change','#country',function(){
        var id=$(this).val();
        if(!id){$('#state').html('<option value="">Select State</option>');$('#district').html('<option value="">Select District</option>');return;}
        $.post('<?= base_url('Ajax/get_states') ?>',{country:id},function(html){
            $('#state').html(html);
            $('#district').html('<option value="">Select District</option>');
            $('#city').val('');
        });
    });

    // State → District
    $(document).on('change','#state',function(){
        var id=$(this).val();
        if(!id){$('#district').html('<option value="">Select District</option>');return;}
        $.post('<?= base_url('Ajax/get_districts') ?>',{state:id},function(html){
            $('#district').html(html);$('#city').val('');
        });
    });

    // District → City
    $(document).on('change','#district',function(){
        var id=$(this).val();
        if(!id) return;
        $.post('<?= base_url('Ajax/get_city') ?>',{district_id:id},function(resp){
            $('#city').val(resp.trim().startsWith('<') ? $(resp).find('option:selected').text() : resp.trim());
        });
    });

    // Show/hide team fields
    $(document).on('change','.event_type_select',function(){
        var row=$(this).closest('.event-item');
        var val = $(this).val();
        if(val === 'team') {
            row.find('.team-settings').show();
        } else {
            row.find('.team-settings').hide();
        }
    });

    // Trigger initial team settings visibility
    $('.event_type_select').each(function() {
        $(this).trigger('change');
    });

    // Add event
    $(document).on('click','.add-event',function(){
        var $src=$(this).closest('.event-item');
        var $new=$src.clone();
        $new.find('input').val('');
        $new.find('select').each(function(){ 
            $(this).val($(this).find('option:first').val()); 
        });
        $new.find('.team-settings').hide();
        $new.find('.form-check-input').prop('checked',false);
        
        // Clear any date restrictions attributes that might be copied
        $new.find('.event-start, .event-end, .matchmaking-date').removeAttr('min max');
        
        // Renumber header
        var count=$('#events-list .event-item').length+1;
        $new.find('.event-number').text(count);
        $('#events-list').append($new);

        // Apply sport-specific event type lock to new row
        applySportEventTypeLock();
        
        // Update date restrictions for new row
        setTimeout(updateDateRestrictions, 50);
    });

    // Remove event
    $(document).on('click','.remove-event',function(){
        if($('#events-list .event-item').length>1){
            $(this).closest('.event-item').remove();
            // Renumber
            $('#events-list .event-item').each(function(i){ 
                $(this).find('.event-number').text(i+1); 
            });
        } else {
            alert('At least one event is required.');
        }
    });

    // Submit guard
    $('#ct-form').on('submit',function(e){
        //   alert('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
           console.log("111111111111");
        if(!$('#district').val()){
            alert('Please select a district before submitting.');
            e.preventDefault(); return false;
        }
        
        // Check Last Booking Date
        if ($('#start_date').val() && $('#lastbook_date').val() && $('#lastbook_date').val() > $('#start_date').val()) {
            alert('Last Booking Date must be on or before the Start Date.');
            e.preventDefault(); return false;
        }

        // Last Booking Date must not be in the past
        var nowGuard = new Date();
        var padGuard = n => String(n).padStart(2,'0');
        var todayGuardStr = nowGuard.getFullYear()+'-'+padGuard(nowGuard.getMonth()+1)+'-'+padGuard(nowGuard.getDate())+'T'+padGuard(nowGuard.getHours())+':'+padGuard(nowGuard.getMinutes());
        if ($('#lastbook_date').val() && $('#lastbook_date').val() < todayGuardStr) {
            alert('Last Booking Date cannot be in the past.');
            e.preventDefault(); return false;
        }
        
        // Check End Date
        if ($('#start_date').val() && $('#end_date').val() && $('#end_date').val() < $('#start_date').val()) {
            alert('End Date must be on or after the Start Date.');
            e.preventDefault(); return false;
        }
        
        // Validate event dates
        var valid = true;
        var tStart = $('#start_date').val();
        var tEnd = $('#end_date').val();
        $('.event-item').each(function() {
            var $start = $(this).find('.event-start');
            var $end = $(this).find('.event-end');
            var $match = $(this).find('.matchmaking-date');

            if (tStart && $start.val() && $start.val() < tStart) {
                alert('Event Start Date must be on or after the tournament Start Date.');
                valid = false;
                return false;
            }
            if (tEnd && $start.val() && $start.val() > tEnd) {
                alert('Event Start Date must be on or before the tournament End Date.');
                valid = false;
                return false;
            }
            if (tEnd && $end.val() && $end.val() > tEnd) {
                alert('Event End Date must be on or before the tournament End Date.');
                valid = false;
                return false;
            }
            if (tStart && $end.val() && $end.val() < tStart) {
                alert('Event End Date must be on or after the tournament Start Date.');
                valid = false;
                return false;
            }

            if ($start.val() && $end.val()) {
                if ($end.val() < $start.val()) {
                    alert('Event End Date must be after Event Start Date for all events.');
                    valid = false;
                    return false;
                }
            }
            
            if ($match.val() && $start.val() && $match.val() < $start.val()) {
                alert('Players Reporting Time must be on or after Event Start Date.');
                valid = false;
                return false;
            }
            
            if ($match.val() && $end.val() && $match.val() > $end.val()) {
                alert('Players Reporting Time must be on or before Event End Date.');
                valid = false;
                return false;
            }
        });
        
        if (!valid) {
            e.preventDefault();
            return false;
        }
    });
});

function submitDraft(){
    document.getElementById('draftFlag').value='1';
    document.getElementById('ct-form').submit();
}

function updateEventStart(el){
    if(!el.value) return;
    var v = new Date(el.value).toISOString().slice(0,16);
    $('.event-start').val(v);
    $('#end_date').val(v);
    $('#end_date').attr('min', el.value);
    updateDateRestrictions();
}

function updateEventEnd(el){
    if(!el.value) return;
    var v = new Date(el.value).toISOString().slice(0,16);
    $('.event-end').val(v);
    updateDateRestrictions();
}
</script>
<?= $this->endSection(); ?>