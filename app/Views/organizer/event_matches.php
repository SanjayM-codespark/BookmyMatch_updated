<?= $this->extend('organizer_base'); ?>
<?= $this->section('pageCss'); ?>
<style>
/* Page Header matching Image 2 */
.bracket-header-card {
    background: #FFFFFF;
    border-radius: 14px;
    padding: 20px 28px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    border: 1px solid #E2E8F0;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}
.bracket-header-left h1 {
    font-size: 22px;
    font-weight: 800;
    color: #0F172A;
    margin: 0 0 6px 0;
    letter-spacing: -0.3px;
}
.bracket-header-left p {
    font-size: 14px;
    font-weight: 500;
    color: #475569;
    margin: 0;
}
.bracket-header-right {
    display: flex;
    align-items: center;
    gap: 12px;
}
.badge-active-pill {
    background: #DBEAFE;
    color: #1E40AF;
    font-size: 13px;
    font-weight: 700;
    padding: 6px 18px;
    border-radius: 20px;
    display: inline-block;
}
.btn-header-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    background: #F1F5F9;
    color: #1E293B;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: background .15s;
}
.btn-header-back:hover {
    background: #E2E8F0;
    color: #0F172A;
}

/* Round Navigation Pills - doubles as the straight header row for each column */
.round-pills-bar {
    display: flex;
    gap: 75px;
    min-width: fit-content;
    position: relative;
    z-index: 2;
    padding-bottom: 12px;
    margin-bottom: 24px;
    border-bottom: 1px solid #E2E8F0;
}
.round-pill-btn {
    background: #0F172A;
    color: #FFFFFF;
    border-radius: 24px;
    padding: 8px 18px;
    font-size: 13px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    white-space: nowrap;
    cursor: pointer;
    transition: transform .15s, background .15s;
    user-select: none;
    border: none;
    width: 290px;
    min-width: 290px;
    box-sizing: border-box;
}
.round-pill-spacer {
    width: 270px;
    min-width: 270px;
    flex-shrink: 0;
}
.round-pill-btn:hover {
    background: #1E293B;
    transform: translateY(-1px);
}
.round-pill-btn.active {
    background: #2563EB;
}
.round-pill-badge {
    background: #1E293B;
    color: #FFFFFF;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 12px;
}
.round-pill-btn.active .round-pill-badge {
    background: #1D4ED8;
}

/* Bracket Viewport & Columns */
.bracket-viewport {
    background: #F8FAFC;
    border-radius: 16px;
    border: 1px solid #E2E8F0;
    padding: 32px 24px;
    overflow-x: auto;
    position: relative;
    min-height: 600px;
}
#bracketSvgLayer {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}
.bracket-columns-container {
    display: flex;
    gap: 75px;
    align-items: stretch;
    position: relative;
    z-index: 2;
    min-width: fit-content;
}
.bracket-round-col {
    display: flex;
    flex-direction: column;
    justify-content: space-around;
    width: 290px;
    min-width: 290px;
}

/* Match Card matching Image 2 */
.match-bracket-card {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 12px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.04);
    margin: 16px 0;
    position: relative;
    transition: box-shadow .2s, transform .2s;
    overflow: hidden;
}
.match-bracket-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}
.match-bracket-card.has-winner {
    border-color: #86EFAC;
}

/* Card Top Header */
.card-header-bar {
    background: #0F172A;
    color: #FFFFFF;
    padding: 8px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 12px;
}
.card-match-title {
    font-weight: 700;
    color: #FFFFFF;
    letter-spacing: .2px;
}
.card-match-date {
    color: #CBD5E1;
    font-weight: 500;
    font-size: 11px;
}

/* Player Rows */
.card-player-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 14px;
    transition: background .15s;
}
.card-player-row.is-winner {
    background: #F0FDF4;
}
.player-info-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 0;
}
.player-avatar-box {
    width: 34px;
    height: 34px;
    background: #0F172A;
    color: #FFFFFF;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 800;
    flex-shrink: 0;
}
.player-name-wrap {
    overflow: hidden;
}
.player-name-txt {
    font-size: 14px;
    font-weight: 700;
    color: #0F172A;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    line-height: 1.2;
}
.player-sub-txt {
    font-size: 11px;
    color: #94A3B8;
    font-weight: 500;
    line-height: 1.2;
}

/* Round 1 player-swap dropdown */
.player-swap-select {
    margin-top: 3px;
    font-size: 11px;
    font-weight: 600;
    color: #2563EB;
    background: #EFF6FF;
    border: 1px solid #BFDBFE;
    border-radius: 5px;
    padding: 2px 4px;
    max-width: 160px;
}
.player-swap-select:focus {
    outline: none;
    border-color: #2563EB;
}

/* Scores */
.player-score-txt {
    font-size: 14px;
    font-weight: 700;
    color: #0F172A;
    letter-spacing: 5px;
    font-family: system-ui, -apple-system, sans-serif;
    cursor: pointer;
    padding: 2px 6px;
    border-radius: 4px;
    transition: background .15s;
    white-space: nowrap;
}
.player-score-txt:hover {
    background: #F1F5F9;
    color: #2563EB;
}

/* VS Divider */
.card-vs-divider {
    border-top: 1px dashed #E2E8F0;
    position: relative;
    margin: 2px 14px;
    text-align: center;
}
.card-vs-divider span {
    position: absolute;
    top: -8px;
    left: 50%;
    transform: translateX(-50%);
    background: #FFFFFF;
    padding: 0 8px;
    font-size: 10px;
    font-weight: 700;
    color: #94A3B8;
}

/* Winner Action Buttons */
.card-winner-actions {
    display: flex;
    gap: 8px;
    padding: 10px 14px 12px;
    border-top: 1px solid #F8FAFC;
    background: #FAFAFA;
}
.btn-win-action {
    flex: 1;
    background: #E6F9F0;
    color: #059669;
    border: 1px solid transparent;
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 11px;
    font-weight: 700;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.btn-win-action:hover:not(:disabled) {
    background: #D1FAE5;
    border-color: #A7F3D0;
}
.btn-win-action.is-selected {
    background: #059669 !important;
    color: #FFFFFF !important;
    box-shadow: 0 2px 6px rgba(5,150,105,0.3);
}
.btn-win-action.is-subdued {
    opacity: 0.45;
    background: #F1F5F9;
    color: #64748B;
}

/* Walkover / Bye */
.card-special-actions {
    display: flex;
    gap: 8px;
    padding: 0 14px 12px;
    background: #FAFAFA;
}
.btn-special-action {
    flex: 1;
    background: #FFFFFF;
    color: #475569;
    border: 1px dashed #CBD5E1;
    border-radius: 6px;
    padding: 5px 8px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
}
.btn-special-action:hover {
    background: #FFF7ED;
    border-color: #FDBA74;
    color: #C2410C;
}
.result-tag {
    display: none;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .4px;
    text-transform: uppercase;
    padding: 2px 7px;
    margin-left: 6px;
    border-radius: 10px;
    background: #FEF3C7;
    color: #B45309;
}
.result-tag.is-bye { background: #E0E7FF; color: #4338CA; }
.result-tag.show { display: inline-block; }

/* Score Edit Modal */
#scoreEditModalBackdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,0.6);
    backdrop-filter: blur(3px);
    z-index: 10000;
    align-items: center;
    justify-content: center;
    padding: 16px;
}
.score-edit-box {
    background: #FFFFFF;
    border-radius: 14px;
    max-width: 440px;
    width: 100%;
    padding: 24px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.25);
    animation: modalFadeIn .2s ease-out;
}

/* Champion Column & Card matching uploaded screenshot */
.bracket-champion-col {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 270px;
    min-width: 270px;
}
.champion-stage-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    width: 100%;
}
.champion-trophy-box {
    display: flex;
    justify-content: center;
    align-items: flex-end;
    margin-bottom: -10px;
    z-index: 2;
    pointer-events: none;
}
.champion-trophy-img {
    width: 200px;
    max-width: 200px;
    height: auto;
    object-fit: contain;
    filter: drop-shadow(0 10px 22px rgba(0,0,0,0.16));
    animation: trophyFloat 3s ease-in-out infinite alternate;
}
@keyframes trophyFloat {
    0% { transform: translateY(0); }
    100% { transform: translateY(-4px); }
}
.champion-bracket-card {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 12px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.06);
    width: 260px;
    overflow: hidden;
    position: relative;
    z-index: 1;
    transition: transform .2s, box-shadow .2s;
}
.champion-bracket-card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}
.champion-card-topbar {
    background: #0F172A;
    color: #FFFFFF;
    font-size: 13px;
    font-weight: 700;
    text-align: center;
    padding: 8px 14px;
    letter-spacing: .3px;
}
.champion-card-main {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 12px 14px;
    background: #FFFFFF;
}
.champion-avatar-box {
    width: 36px;
    height: 36px;
    background: #0F172A;
    color: #FFFFFF;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    font-weight: 800;
    flex-shrink: 0;
}
.champion-name-box {
    font-size: 15px;
    font-weight: 700;
    color: #0F172A;
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.2;
}
.champion-winner-pill {
    background: #00A859;
    color: #FFFFFF;
    font-size: 12px;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: 6px;
    white-space: nowrap;
    display: inline-block;
    box-shadow: 0 2px 6px rgba(0,168,89,0.25);
}
</style>
<?= $this->endSection(); ?>

<?= $this->section('main_content'); ?>

<?php
$tourney_title = !empty($event->listing_title) ? $event->listing_title : 'Tiruvallur District Badminton Championship';
$event_name    = !empty($event->event_name) ? $event->event_name : 'Under 15 Boys Singles';
$sport_name    = !empty($event->sports_name) ? $event->sports_name : 'Badminton';
$venue_name    = !empty($venue) ? $venue : 'Indoor Stadium';
$is_team       = ($event->listing_event_type === 'team');
$unit_label    = $is_team ? 'teams' : 'players';
?>

<!-- Header matching Image 2 -->
<div class="bracket-header-card">
    <div class="bracket-header-left">
        <h1><?= esc($tourney_title) ?></h1>
        <p><?= esc($event_name) ?> &mdash; <?= esc($sport_name) ?> &mdash; <?= esc($venue_name) ?></p>
    </div>
    <div class="bracket-header-right">
        <span class="badge-active-pill" id="activeBadge"><?= (int) ($active_count ?? 0) ?> Active</span>
        <a href="<?= base_url('view_events/' . $event->listing_event_listing_id) ?>" class="btn-header-back">
            <i class="fa-solid fa-arrow-left"></i> Events
        </a>
    </div>
</div>

<!-- Round Navigation Pills matching Image 2 -->
<?php if (!empty($missing_players)): ?>
<div style="margin:12px 0;padding:12px 16px;border-radius:10px;background:#FEF3C7;border:1px solid #FDE68A;color:#92400E;font-size:13px;">
    <strong><?= count($missing_players) ?> player(s) registered after the draw was created</strong>
    and are not in this bracket: <?= esc(implode(', ', $missing_players)) ?>.
    Matches already have winners, so the draw was left unchanged.
</div>
<?php endif ?>

<!-- Elimination Tournament Bracket Board -->
<div class="bracket-viewport" id="bracketViewport">
    <!-- SVG overlay for connectors -->
    <svg id="bracketSvgLayer" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <marker id="arrowHead" markerWidth="6" markerHeight="6" refX="5" refY="3" orient="auto">
                <polygon points="0 0, 6 3, 0 6" fill="#94A3B8" />
            </marker>
            <marker id="arrowHeadDark" markerWidth="6" markerHeight="6" refX="5" refY="3" orient="auto">
                <polygon points="0 0, 6 3, 0 6" fill="#0F172A" />
            </marker>
        </defs>
    </svg>

    <!-- Round titles - same widths/gaps as the columns, so each sits straight above its column -->
    <div class="round-pills-bar">
        <?php foreach ($rounds as $r_idx => $rnd): ?>
            <button type="button" class="round-pill-btn <?= $r_idx === 0 ? 'active' : '' ?>"
                    data-round-idx="<?= $r_idx ?>"
                    onclick="scrollToRoundCol(<?= $r_idx ?>)">
                <span><?= esc($rnd->round_name) ?> &mdash; <?= (int) (count($rnd->matches) * 2) ?> <?= $unit_label ?></span>
                <span class="round-pill-badge" id="round-badge-<?= $rnd->round_id ?>">
                    <?= (int)$rnd->completed_count ?>/<?= (int)$rnd->total_count ?>
                </span>
            </button>
        <?php endforeach ?>
        <div class="round-pill-spacer"></div>
    </div>

    <div class="bracket-columns-container" id="bracketColumnsContainer">
        <?php foreach ($rounds as $r_idx => $rnd): ?>
        <div class="bracket-round-col" id="round-col-<?= $r_idx ?>">
            <?php foreach ($rnd->matches as $m_idx => $m):
                $p1_id = $is_team ? $m->match_team1_id : $m->match_individual1_id;
                $p2_id = $is_team ? $m->match_team2_id : $m->match_individual2_id;
                $p1_name = !empty($m->p1_display_name) ? $m->p1_display_name : 'TBD';
                $p2_name = !empty($m->p2_display_name) ? $m->p2_display_name : 'TBD';
                $p1_avatar = !empty($m->p1_avatar) ? $m->p1_avatar : '?';
                $p2_avatar = !empty($m->p2_avatar) ? $m->p2_avatar : '?';

                // Format scores: set 1, 2, 3
                $s1_pts = [];
                $s2_pts = [];
                if (!empty($m->sets_scores)) {
                    for ($s = 1; $s <= 3; $s++) {
                        $s1_pts[] = isset($m->sets_scores[$p1_id][$s]) ? $m->sets_scores[$p1_id][$s] : '-';
                        $s2_pts[] = isset($m->sets_scores[$p2_id][$s]) ? $m->sets_scores[$p2_id][$s] : '-';
                    }
                } else {
                    $s1_pts = ['-', '-', '-'];
                    $s2_pts = ['-', '-', '-'];
                }
                $s1_str = implode('  ', $s1_pts);
                $s2_str = implode('  ', $s2_pts);

                $winner_id = (int)$m->winner_id;
                $p1_is_winner = ($winner_id > 0 && $winner_id === (int)$p1_id);
                $p2_is_winner = ($winner_id > 0 && $winner_id === (int)$p2_id);

                // Walkover / bye results are tagged in match_winning_desc
                $res_type = ($winner_id > 0) ? ($m->score_data['type'] ?? '') : '';
                if (!in_array($res_type, ['walkover', 'bye'], true)) $res_type = '';
                if ($res_type !== '') {
                    $s1_str = $p1_is_winner ? ($res_type === 'bye' ? 'BYE' : 'W/O') : '-';
                    $s2_str = $p2_is_winner ? ($res_type === 'bye' ? 'BYE' : 'W/O') : '-';
                }

                $date_str = !empty($m->match_date) ? date('d M, h:i A', strtotime($m->match_date)) : '07 May, 08:00 AM';
            ?>
            <div class="match-bracket-card <?= $winner_id > 0 ? 'has-winner' : '' ?>"
                 id="bracket-card-<?= $m->match_id ?>"
                 data-match-id="<?= $m->match_id ?>"
                 data-round-idx="<?= $r_idx ?>"
                 data-match-idx="<?= $m_idx ?>"
                 data-round-id="<?= $rnd->round_id ?>"
                 data-p1-id="<?= $p1_id ?>"
                 data-p2-id="<?= $p2_id ?>"
                 data-p1-name="<?= esc($p1_name) ?>"
                 data-p2-name="<?= esc($p2_name) ?>"
                 data-winner-id="<?= $winner_id ?>">

                <!-- Top bar -->
                <div class="card-header-bar">
                    <span class="card-match-title">Match <?= ($m_idx + 1) ?><span class="result-tag <?= $res_type === 'bye' ? 'is-bye' : '' ?> <?= $res_type !== '' ? 'show' : '' ?>" id="result-tag-<?= $m->match_id ?>"><?= $res_type === 'bye' ? 'Bye' : ($res_type === 'walkover' ? 'Walkover' : '') ?></span></span>
                    <span class="card-match-date"><i class="fa-regular fa-clock" style="font-size:10px;margin-right:3px;"></i> <?= $date_str ?></span>
                </div>

                <!-- Player 1 Row -->
                <div class="card-player-row <?= $p1_is_winner ? 'is-winner' : '' ?>" id="p1-row-<?= $m->match_id ?>">
                    <div class="player-info-wrap">
                        <div class="player-avatar-box" id="p1-avatar-<?= $m->match_id ?>">
                            <?= esc($p1_avatar) ?>
                        </div>
                        <div class="player-name-wrap">
                            <span class="player-name-txt" id="p1-name-<?= $m->match_id ?>"><?= esc($p1_name) ?></span>
                            <span class="player-sub-txt"><?= $is_team ? 'Team 1' : 'Player 1' ?></span>
                            <?php if ($r_idx === 0): ?>
                            <select class="player-swap-select" id="p1-swap-<?= $m->match_id ?>"
                                    data-match-id="<?= $m->match_id ?>" data-slot="1"
                                    onchange="swapBracketPlayer(this)">
                                <option value="0" <?= $p1_id <= 0 ? 'selected' : '' ?>>-- TBD --</option>
                                <?php foreach (($round1_units ?? []) as $u_id => $u_name): ?>
                                <option value="<?= $u_id ?>" <?= (int)$p1_id === (int)$u_id ? 'selected' : '' ?>><?= esc($u_name) ?></option>
                                <?php endforeach ?>
                            </select>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="player-score-txt"
                         title="Click to edit scores"
                         onclick="openScoreModal(<?= $m->match_id ?>, '<?= esc($p1_name) ?>', '<?= esc($p2_name) ?>', <?= (int)$p1_id ?>, <?= (int)$p2_id ?>)"
                         id="p1-score-<?= $m->match_id ?>">
                        <?= $s1_str ?>
                    </div>
                </div>

                <!-- Divider -->
                <div class="card-vs-divider">
                    <span>VS</span>
                </div>

                <!-- Player 2 Row -->
                <div class="card-player-row <?= $p2_is_winner ? 'is-winner' : '' ?>" id="p2-row-<?= $m->match_id ?>">
                    <div class="player-info-wrap">
                        <div class="player-avatar-box" id="p2-avatar-<?= $m->match_id ?>">
                            <?= esc($p2_avatar) ?>
                        </div>
                        <div class="player-name-wrap">
                            <span class="player-name-txt" id="p2-name-<?= $m->match_id ?>"><?= esc($p2_name) ?></span>
                            <span class="player-sub-txt"><?= $is_team ? 'Team 2' : 'Player 2' ?></span>
                            <?php if ($r_idx === 0): ?>
                            <select class="player-swap-select" id="p2-swap-<?= $m->match_id ?>"
                                    data-match-id="<?= $m->match_id ?>" data-slot="2"
                                    onchange="swapBracketPlayer(this)">
                                <option value="0" <?= $p2_id <= 0 ? 'selected' : '' ?>>-- TBD --</option>
                                <?php foreach (($round1_units ?? []) as $u_id => $u_name): ?>
                                <option value="<?= $u_id ?>" <?= (int)$p2_id === (int)$u_id ? 'selected' : '' ?>><?= esc($u_name) ?></option>
                                <?php endforeach ?>
                            </select>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="player-score-txt"
                         title="Click to edit scores"
                         onclick="openScoreModal(<?= $m->match_id ?>, '<?= esc($p1_name) ?>', '<?= esc($p2_name) ?>', <?= (int)$p1_id ?>, <?= (int)$p2_id ?>)"
                         id="p2-score-<?= $m->match_id ?>">
                        <?= $s2_str ?>
                    </div>
                </div>

                <!-- Winner Selection Buttons matching Image 2 -->
                <div class="card-winner-actions">
                    <button type="button"
                            class="btn-win-action <?= $p1_is_winner ? 'is-selected' : ($p2_is_winner ? 'is-subdued' : '') ?>"
                            id="btn-win1-<?= $m->match_id ?>"
                            onclick="setBracketWinner(<?= $m->match_id ?>, <?= (int)$p1_id ?>, <?= (int)$p2_id ?>, 1)">
                        <?php if ($p1_is_winner): ?><i class="fa-solid fa-trophy" style="font-size:10px;margin-right:3px;"></i><?php endif ?>
                        <span id="btn-win1-txt-<?= $m->match_id ?>"><?= esc($p1_name) ?> Win's</span>
                    </button>
                    <button type="button"
                            class="btn-win-action <?= $p2_is_winner ? 'is-selected' : ($p1_is_winner ? 'is-subdued' : '') ?>"
                            id="btn-win2-<?= $m->match_id ?>"
                            onclick="setBracketWinner(<?= $m->match_id ?>, <?= (int)$p2_id ?>, <?= (int)$p1_id ?>, 2)">
                        <?php if ($p2_is_winner): ?><i class="fa-solid fa-trophy" style="font-size:10px;margin-right:3px;"></i><?php endif ?>
                        <span id="btn-win2-txt-<?= $m->match_id ?>"><?= esc($p2_name) ?> Win's</span>
                    </button>
                </div>

                <!-- Special results: walkover (opponent absent) / bye (no opponent in draw) -->
                <div class="card-special-actions">
                    <button type="button" class="btn-special-action"
                            title="Opponent absent / withdrew / disqualified - the other player advances"
                            onclick="openWalkoverModal(<?= $m->match_id ?>)">
                        <i class="fa-solid fa-person-walking-arrow-right"></i> Walkover
                    </button>
                    <button type="button" class="btn-special-action"
                            title="No opponent in the draw - the player advances without playing"
                            onclick="giveBye(<?= $m->match_id ?>)">
                        <i class="fa-solid fa-forward"></i> Bye
                    </button>
                </div>

            </div>
            <?php endforeach ?>
        </div>
        <?php endforeach ?>

        <!-- CHAMPION COLUMN NEXT TO FINAL MATCH (Matching Uploaded Screenshot) -->
        <div class="bracket-round-col bracket-champion-col" id="championCol">
            <div class="champion-stage-wrap" id="championStageWrap">
                <!-- Top Trophy Icon matching screenshot -->
                <div class="champion-trophy-box">
                    <img src="<?= base_url('public/assets/img/winnericon.png') ?>"
                         alt="Champion Trophy"
                         class="champion-trophy-img">
                </div>

                <!-- Champion Card matching screenshot -->
                <div class="champion-bracket-card<?= !empty($champion['id']) ? ' has-champion' : '' ?>" id="championCard">
                    <div class="champion-card-topbar">
                        Champion
                    </div>
                    <div class="champion-card-main">
                        <div class="champion-avatar-box" id="champAvatar">
                            <?= !empty($champion['name']) ? strtoupper(mb_substr($champion['name'], 0, 1)) : '?' ?>
                        </div>
                        <div class="champion-name-box" id="champName">
                            <?= !empty($champion['name']) ? esc($champion['name']) : 'TBD' ?>
                        </div>
                        <span class="champion-winner-pill">
                            Winner
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Score Edit Modal Popover -->
<div id="scoreEditModalBackdrop">
    <div class="score-edit-box">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="margin:0;font-size:16px;font-weight:700;color:#0F172A;" id="scoreModalTitle">Update Match Scores</h3>
            <button type="button" onclick="closeScoreModal()" style="background:none;border:none;font-size:22px;color:#94A3B8;cursor:pointer;">&times;</button>
        </div>

        <form id="scoreModalForm">
            <input type="hidden" id="modalMatchId" name="match_id">
            <input type="hidden" id="modalSportType" name="sport_type" value="sets">
            <input type="hidden" id="modalTeam1Id" name="team1_id">
            <input type="hidden" id="modalTeam2Id" name="team2_id">
            <input type="hidden" name="num_sets" value="3">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;font-weight:700;font-size:13px;color:#334155;">
                <div id="modalP1Header">Player 1</div>
                <div id="modalP2Header">Player 2</div>
            </div>

            <!-- Set 1 -->
            <div style="margin-bottom:10px;">
                <label style="font-size:11px;font-weight:600;color:#64748B;display:block;margin-bottom:4px;">Set 1 Points</label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <input type="number" id="m_s1_p1" name="sets[1][p1]" class="form-control" placeholder="P1 pts" min="0" max="35" style="border-radius:6px;font-size:13px;">
                    <input type="number" id="m_s1_p2" name="sets[1][p2]" class="form-control" placeholder="P2 pts" min="0" max="35" style="border-radius:6px;font-size:13px;">
                </div>
            </div>

            <!-- Set 2 -->
            <div style="margin-bottom:10px;">
                <label style="font-size:11px;font-weight:600;color:#64748B;display:block;margin-bottom:4px;">Set 2 Points</label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <input type="number" id="m_s2_p1" name="sets[2][p1]" class="form-control" placeholder="P1 pts" min="0" max="35" style="border-radius:6px;font-size:13px;">
                    <input type="number" id="m_s2_p2" name="sets[2][p2]" class="form-control" placeholder="P2 pts" min="0" max="35" style="border-radius:6px;font-size:13px;">
                </div>
            </div>

            <!-- Set 3 -->
            <div style="margin-bottom:18px;">
                <label style="font-size:11px;font-weight:600;color:#64748B;display:block;margin-bottom:4px;">Set 3 Points</label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <input type="number" id="m_s3_p1" name="sets[3][p1]" class="form-control" placeholder="P1 pts" min="0" max="35" style="border-radius:6px;font-size:13px;">
                    <input type="number" id="m_s3_p2" name="sets[3][p2]" class="form-control" placeholder="P2 pts" min="0" max="35" style="border-radius:6px;font-size:13px;">
                </div>
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeScoreModal()" class="btn btn-light btn-sm" style="font-weight:600;">Cancel</button>
                <button type="submit" id="btnSaveScoreModal" class="btn btn-primary btn-sm" style="font-weight:700;background:#2563EB;padding:7px 18px;">
                    <i class="fa-solid fa-floppy-disk"></i> Save Scores
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Walkover Modal -->
<div id="walkoverModalBackdrop" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.6);z-index:10000;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:12px;padding:22px;width:100%;max-width:400px;">
        <h5 style="font-weight:800;margin-bottom:4px;">Walkover</h5>
        <p style="font-size:12px;color:#64748B;margin-bottom:14px;">
            The match is not played. Select the player/team that is <b>present</b> &mdash; they win and advance to the next round.
        </p>
        <input type="hidden" id="woMatchId">
        <label style="display:flex;align-items:center;gap:8px;padding:9px 12px;border:1px solid #E2E8F0;border-radius:8px;margin-bottom:8px;cursor:pointer;">
            <input type="radio" name="woWinner" value="1"> <span id="woName1" style="font-weight:600;"></span>
        </label>
        <label style="display:flex;align-items:center;gap:8px;padding:9px 12px;border:1px solid #E2E8F0;border-radius:8px;margin-bottom:14px;cursor:pointer;">
            <input type="radio" name="woWinner" value="2"> <span id="woName2" style="font-weight:600;"></span>
        </label>
        <label style="font-size:11px;font-weight:600;color:#64748B;display:block;margin-bottom:4px;">Reason for the walkover</label>
        <select id="woReason" class="form-control" style="border-radius:6px;font-size:13px;margin-bottom:18px;">
            <option value="No show">Opponent did not show up</option>
            <option value="Injury">Opponent injured</option>
            <option value="Withdrawn">Opponent withdrew</option>
            <option value="Disqualified">Opponent disqualified</option>
        </select>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button type="button" onclick="closeWalkoverModal()" class="btn btn-light btn-sm" style="font-weight:600;">Cancel</button>
            <button type="button" onclick="submitWalkover()" class="btn btn-sm" style="font-weight:700;background:#EA580C;color:#fff;padding:7px 18px;">Confirm Walkover</button>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('pageScript'); ?>
<script>
const BASE = '<?= base_url() ?>';

$(document).ready(function(){
    // Initial draw of bracket tree connectors
    drawConnectors();

    // Redraw on window resize
    $(window).on('resize', function(){
        drawConnectors();
    });

    // Handle score modal save
    $('#scoreModalForm').on('submit', function(e){
        e.preventDefault();
        saveScoreModalSubmit();
    });

    // Remember each Round 1 swap-dropdown's current value so a failed save can revert to it
    $('.player-swap-select').each(function(){
        $(this).data('prev-value', $(this).val());
    });
});

// Scroll to round column on clicking pill
function scrollToRoundCol(idx) {
    $('.round-pill-btn').removeClass('active');
    $('.round-pill-btn[data-round-idx="' + idx + '"]').addClass('active');

    const col = document.getElementById('round-col-' + idx);
    const viewport = document.getElementById('bracketViewport');
    if (col && viewport) {
        viewport.scrollTo({
            left: col.offsetLeft - 30,
            behavior: 'smooth'
        });
    }
}

// Draw crisp SVG tree connector lines between rounds
function drawConnectors() {
    const svg = document.getElementById('bracketSvgLayer');
    const container = document.getElementById('bracketColumnsContainer');
    if (!svg || !container) return;

    // Remove existing paths
    const oldPaths = svg.querySelectorAll('.bracket-line');
    oldPaths.forEach(p => p.remove());

    const numCols = $('.bracket-round-col').length;
    const vpRect = svg.getBoundingClientRect();

    for (let r = 0; r < numCols - 1; r++) {
        const currCards = $('#round-col-' + r + ' .match-bracket-card');
        const nextCards = $('#round-col-' + (r + 1) + ' .match-bracket-card');

        currCards.each(function(i){
            const targetIdx = Math.floor(i / 2);
            const targetCard = nextCards.eq(targetIdx);

            if (targetCard.length) {
                const cRect = this.getBoundingClientRect();
                const tRect = targetCard[0].getBoundingClientRect();

                // Coordinates relative to SVG layer
                const x1 = cRect.right - vpRect.left;
                const y1 = cRect.top + (cRect.height / 2) - vpRect.top;

                const x2 = tRect.left - vpRect.left;
                const y2 = tRect.top + (tRect.height / 2) - vpRect.top;

                const xMid = x1 + (x2 - x1) * 0.55;

                // Create branch path
                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                const d = `M ${x1} ${y1} H ${xMid} V ${y2} H ${x2 - 6}`;
                path.setAttribute('d', d);
                path.setAttribute('fill', 'none');
                path.setAttribute('stroke', '#94A3B8');
                path.setAttribute('stroke-width', '1.5');
                path.setAttribute('class', 'bracket-line');
                path.setAttribute('marker-end', 'url(#arrowHead)');

                svg.appendChild(path);
            }
        });
    }
}

// Pick and save winner
function setBracketWinner(matchId, winnerId, loserId, playerSlot, resultType, reason) {
    resultType = resultType || 'normal';
    if (!winnerId || winnerId === 0) {
        alert('This player is not yet assigned. Please wait for previous round winner or assign players.');
        return;
    }

    const $card = $('#bracket-card-' + matchId);
    const $btn1 = $('#btn-win1-' + matchId);
    const $btn2 = $('#btn-win2-' + matchId);

    // Call backend to update match winner and advance to next round
    $.post(BASE + '/organizer/save_bracket_winner', {
        match_id: matchId,
        winner_id: winnerId,
        loser_id: loserId,
        result_type: resultType,
        reason: reason || ''
    }, function(res){
        if (res.status) {
            applyResultTag(matchId, res.result_type, playerSlot);
            // Update button styles
            if (playerSlot === 1) {
                $btn1.addClass('is-selected').removeClass('is-subdued');
                $btn2.removeClass('is-selected').addClass('is-subdued');
                $('#p1-row-' + matchId).addClass('is-winner');
                $('#p2-row-' + matchId).removeClass('is-winner');
            } else {
                $btn2.addClass('is-selected').removeClass('is-subdued');
                $btn1.removeClass('is-selected').addClass('is-subdued');
                $('#p2-row-' + matchId).addClass('is-winner');
                $('#p1-row-' + matchId).removeClass('is-winner');
            }
            $card.addClass('has-winner');

            // If advanced to next round, update target card immediately
            if (res.advanced_to) {
                const targetMatchId = res.advanced_to.match_id;
                const slot = res.advanced_to.slot; // 1 or 2
                const $targetCard = $('#bracket-card-' + targetMatchId);

                if ($targetCard.length) {
                    const name = res.winner_name || 'Winner';
                    const avatar = res.avatar || name.charAt(0).toUpperCase();

                    if (slot === 1) {
                        $('#p1-name-' + targetMatchId).text(name);
                        $('#p1-avatar-' + targetMatchId).text(avatar);
                        $('#btn-win1-txt-' + targetMatchId).text(name + " Win's");
                        $targetCard.attr('data-p1-id', winnerId);
                        $targetCard.attr('data-p1-name', name);
                        $('#btn-win1-' + targetMatchId).attr('onclick', 'setBracketWinner(' + targetMatchId + ', ' + winnerId + ', ' + ($targetCard.attr('data-p2-id') || 0) + ', 1)');
                    } else {
                        $('#p2-name-' + targetMatchId).text(name);
                        $('#p2-avatar-' + targetMatchId).text(avatar);
                        $('#btn-win2-txt-' + targetMatchId).text(name + " Win's");
                        $targetCard.attr('data-p2-id', winnerId);
                        $targetCard.attr('data-p2-name', name);
                        $('#btn-win2-' + targetMatchId).attr('onclick', 'setBracketWinner(' + targetMatchId + ', ' + winnerId + ', ' + ($targetCard.attr('data-p1-id') || 0) + ', 2)');
                    }
                }
            }

            // Redraw bracket lines
            drawConnectors();

            // Live "X Active" badge (units still in contention)
            if (typeof res.active_count !== 'undefined') {
                $('#activeBadge').text(res.active_count + ' Active');
            }

            // Final decided (or changed) - refresh the Champion column
            if (res.is_final) {
                const champName = res.winner_name || 'Winner';
                const champAvatar = res.avatar || champName.charAt(0).toUpperCase();
                $('#champName').text(champName);
                $('#champAvatar').text(champAvatar);
                $('#championCard').addClass('has-champion');
            }
        } else {
            alert(res.message || 'Error updating winner.');
        }
    }, 'json').fail(function(){
        alert('Server connection error while saving winner.');
    });
}

// Show/clear the Walkover / Bye tag and the W/O | BYE score text on a card
function applyResultTag(matchId, resultType, winnerSlot) {
    const $tag = $('#result-tag-' + matchId);
    $tag.removeClass('show is-bye');
    if (resultType === 'walkover' || resultType === 'bye') {
        $tag.text(resultType === 'bye' ? 'Bye' : 'Walkover').addClass('show');
        if (resultType === 'bye') $tag.addClass('is-bye');
        const label = (resultType === 'bye') ? 'BYE' : 'W/O';
        $('#p' + winnerSlot + '-score-' + matchId).text(label);
        $('#p' + (winnerSlot === 1 ? 2 : 1) + '-score-' + matchId).text('-');
    } else {
        // A normal result replaces an earlier walkover/bye label
        $('#p1-score-' + matchId + ', #p2-score-' + matchId).each(function(){
            if (['W/O', 'BYE'].indexOf($(this).text().trim()) !== -1) $(this).text('-  -  -');
        });
    }
}

function walkoverCardInfo(matchId) {
    const $c = $('#bracket-card-' + matchId);
    return {
        p1: parseInt($c.attr('data-p1-id'), 10) || 0,
        p2: parseInt($c.attr('data-p2-id'), 10) || 0,
        n1: $c.attr('data-p1-name') || 'TBD',
        n2: $c.attr('data-p2-name') || 'TBD'
    };
}

function openWalkoverModal(matchId) {
    const c = walkoverCardInfo(matchId);
    if (!c.p1 || !c.p2) {
        alert('A walkover needs both players assigned. If there is no opponent, use Bye instead.');
        return;
    }
    $('#woMatchId').val(matchId);
    $('#woName1').text(c.n1);
    $('#woName2').text(c.n2);
    $('input[name="woWinner"]').prop('checked', false);
    $('#walkoverModalBackdrop').css('display', 'flex');
}

function closeWalkoverModal() {
    $('#walkoverModalBackdrop').hide();
}

function submitWalkover() {
    const matchId = parseInt($('#woMatchId').val(), 10);
    const slot = parseInt($('input[name="woWinner"]:checked').val(), 10);
    if (!slot) { alert('Select the player/team that advances.'); return; }
    const c = walkoverCardInfo(matchId);
    const winnerId = (slot === 1) ? c.p1 : c.p2;
    const loserId  = (slot === 1) ? c.p2 : c.p1;
    closeWalkoverModal();
    setBracketWinner(matchId, winnerId, loserId, slot, 'walkover', $('#woReason').val());
}

// Bye: only valid when exactly one side of the match is assigned
function giveBye(matchId) {
    const c = walkoverCardInfo(matchId);
    if ((c.p1 && c.p2) || (!c.p1 && !c.p2)) {
        alert(c.p1 && c.p2
            ? 'Both players are assigned, so this is not a bye. Use Walkover if the opponent is absent.'
            : 'No player is assigned to this match yet.');
        return;
    }
    const slot = c.p1 ? 1 : 2;
    const name = c.p1 ? c.n1 : c.n2;
    if (!confirm(name + ' has no opponent. Give a bye and advance to the next round?')) return;
    setBracketWinner(matchId, c.p1 || c.p2, 0, slot, 'bye', '');
}

// Round 1 only: pick a different player/team for a match slot from the dropdown.
// If that unit already sits in another Round 1 slot, the backend swaps the two
// so nobody ends up duplicated or missing from the bracket.
function swapBracketPlayer(selectEl) {
    const $select  = $(selectEl);
    const matchId  = $select.data('match-id');
    const slot     = parseInt($select.data('slot'), 10);
    const newId    = parseInt($select.val(), 10) || 0;
    const prevId   = parseInt($select.data('prev-value'), 10) || 0;

    if (newId === prevId) return;

    $select.prop('disabled', true);

    $.post(BASE + '/organizer/swap_bracket_player', {
        match_id: matchId,
        slot: slot,
        unit_id: newId
    }, function(res){
        $select.prop('disabled', false);

        if (!res.status) {
            alert(res.message || 'Error updating player.');
            $select.val(prevId);
            return;
        }

        applyBracketSlot(matchId, slot, res.new_id, res.new_name);

        if (res.swapped_to) {
            applyBracketSlot(res.swapped_to.match_id, res.swapped_to.slot, res.old_id, res.old_name);
        }

        drawConnectors();
    }, 'json').fail(function(){
        $select.prop('disabled', false);
        $select.val(prevId);
        alert('Server connection error while updating player.');
    });
}

// Reflect a slot's new occupant everywhere its match card shows a player: name,
// avatar, the swap-select itself, the winner buttons and the score-modal trigger.
function applyBracketSlot(matchId, slot, unitId, unitName) {
    const name  = unitName || 'TBD';
    const avatar = (unitId > 0) ? name.charAt(0).toUpperCase() : '?';
    const $card = $('#bracket-card-' + matchId);

    $('#p' + slot + '-name-' + matchId).text(name);
    $('#p' + slot + '-avatar-' + matchId).text(avatar);
    $card.attr('data-p' + slot + '-id', unitId);
    $card.attr('data-p' + slot + '-name', name);

    const $sel = $('#p' + slot + '-swap-' + matchId);
    if ($sel.length) {
        $sel.val(unitId);
        $sel.data('prev-value', unitId);
    }

    const otherSlot = (slot === 1) ? 2 : 1;
    const otherId   = parseInt($card.attr('data-p' + otherSlot + '-id'), 10) || 0;
    const otherName = $card.attr('data-p' + otherSlot + '-name') || 'TBD';

    $('#btn-win' + slot + '-txt-' + matchId).text(name + " Win's");
    $('#btn-win' + slot + '-' + matchId).attr('onclick', 'setBracketWinner(' + matchId + ', ' + unitId + ', ' + otherId + ', ' + slot + ')');
    $('#btn-win' + otherSlot + '-' + matchId).attr('onclick', 'setBracketWinner(' + matchId + ', ' + otherId + ', ' + unitId + ', ' + otherSlot + ')');

    const p1Name = (slot === 1) ? name : otherName;
    const p2Name = (slot === 2) ? name : otherName;
    const p1Id   = (slot === 1) ? unitId : otherId;
    const p2Id   = (slot === 2) ? unitId : otherId;
    const esc = s => String(s).replace(/'/g, "\\'");
    $('#p1-score-' + matchId + ', #p2-score-' + matchId)
        .attr('onclick', "openScoreModal(" + matchId + ", '" + esc(p1Name) + "', '" + esc(p2Name) + "', " + p1Id + ", " + p2Id + ")");
}

// Open score editing modal
function openScoreModal(matchId, p1Name, p2Name, p1Id, p2Id) {
    $('#modalMatchId').val(matchId);
    $('#modalTeam1Id').val(p1Id);
    $('#modalTeam2Id').val(p2Id);
    $('#modalP1Header').text(p1Name || 'Player 1');
    $('#modalP2Header').text(p2Name || 'Player 2');
    $('#scoreModalTitle').text('Update Match ' + $('#bracket-card-' + matchId + ' .card-match-title').text() + ' Scores');

    // Parse existing displayed points
    const s1Text = $('#p1-score-' + matchId).text().trim().split(/\s+/);
    const s2Text = $('#p2-score-' + matchId).text().trim().split(/\s+/);

    $('#m_s1_p1').val(s1Text[0] && s1Text[0] !== '-' ? s1Text[0] : '');
    $('#m_s1_p2').val(s2Text[0] && s2Text[0] !== '-' ? s2Text[0] : '');
    $('#m_s2_p1').val(s1Text[1] && s1Text[1] !== '-' ? s1Text[1] : '');
    $('#m_s2_p2').val(s2Text[1] && s2Text[1] !== '-' ? s2Text[1] : '');
    $('#m_s3_p1').val(s1Text[2] && s1Text[2] !== '-' ? s1Text[2] : '');
    $('#m_s3_p2').val(s2Text[2] && s2Text[2] !== '-' ? s2Text[2] : '');

    $('#scoreEditModalBackdrop').css('display', 'flex');
}

function closeScoreModal() {
    $('#scoreEditModalBackdrop').fadeOut(150);
}

// Save score submit
function saveScoreModalSubmit() {
    const $btn = $('#btnSaveScoreModal');
    $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Saving...');

    const fd = new FormData(document.getElementById('scoreModalForm'));
    const matchId = $('#modalMatchId').val();

    fetch(BASE + '/organizer/update_match_score', { method:'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            $btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk"></i> Save Scores');
            if (res.status) {
                // Update display on card
                const p1_pts = [
                    $('#m_s1_p1').val() || '-',
                    $('#m_s2_p1').val() || '-',
                    $('#m_s3_p1').val() || '-'
                ].join('  ');
                const p2_pts = [
                    $('#m_s1_p2').val() || '-',
                    $('#m_s2_p2').val() || '-',
                    $('#m_s3_p2').val() || '-'
                ].join('  ');

                $('#p1-score-' + matchId).text(p1_pts);
                $('#p2-score-' + matchId).text(p2_pts);

                closeScoreModal();
            } else {
                alert(res.message || 'Error updating scores.');
            }
        }).catch(() => {
            $btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk"></i> Save Scores');
            alert('Failed to save scores. Please check connection.');
        });
}
</script>
<?= $this->endSection(); ?>
