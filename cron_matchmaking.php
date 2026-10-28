<?php
/**
 * BookMyMatch — Matchmaking Cron Script
 *
 * Run via Windows Task Scheduler every 5 minutes:
 *   C:\xampp8.3\php\php.exe C:\xampp8.3\htdocs\bookmymatch\cron_matchmaking.php
 *
 * Logs to: C:\xampp8.3\htdocs\bookmymatch\writable\logs\matchmaking_cron.log
 */

define('LOG_FILE', __DIR__ . '/writable/logs/matchmaking_cron.log');

function cron_log(string $msg): void
{
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
    file_put_contents(LOG_FILE, $line, FILE_APPEND | LOCK_EX);
    echo $line;
}

// ---------- DB CONFIG ----------
$db_host = 'localhost';
$db_name = 'bookmymatch_live_db';
$db_user = 'root';
$db_pass = '';
// --------------------------------

try {
    $pdo = new PDO(
        'mysql:host=' . $db_host . ';dbname=' . $db_name . ';charset=utf8mb4',
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        ]
    );
} catch (PDOException $e) {
    cron_log('DB CONNECTION FAILED: ' . $e->getMessage());
    exit(1);
}

$now = date('Y-m-d H:i:s');

// Find events whose matchmaking_date has arrived and haven't been processed
$stmt = $pdo->prepare("
    SELECT listing_event_id, listing_event_title, listing_event_type,
           listing_event_listing_id, listing_event_start, listing_event_matchmaking_date
    FROM listing_events
    WHERE listing_event_active = 1
      AND listing_event_matchmaking_done = 0
      AND listing_event_matchmaking_date IS NOT NULL
      AND listing_event_matchmaking_date <= ?
");
$stmt->execute([$now]);
$events = $stmt->fetchAll();

if (empty($events)) {
    cron_log('No events ready for matchmaking.');
    exit(0);
}

foreach ($events as $event) {
    cron_log('Processing: ' . $event->listing_event_title . ' (ID: ' . $event->listing_event_id . ')');

    // Atomically claim this event — prevents double-run if cron overlaps
    $claim = $pdo->prepare("
        UPDATE listing_events
        SET listing_event_matchmaking_done = 1
        WHERE listing_event_id = ?
          AND listing_event_matchmaking_done = 0
    ");
    $claim->execute([$event->listing_event_id]);

    if ($claim->rowCount() === 0) {
        cron_log('  Skipped — already claimed by another process.');
        continue;
    }

    // Fetch registered teams (open or full)
    $ts = $pdo->prepare("
        SELECT team_id, team_name
        FROM teams
        WHERE team_listing_event_id = ?
          AND team_status IN ('open', 'full')
        ORDER BY team_created_at ASC
    ");
    $ts->execute([$event->listing_event_id]);
    $teams = $ts->fetchAll();
    $total = count($teams);

    if ($total < 2) {
        cron_log('  Skipped — only ' . $total . ' team(s) registered (minimum 2 required).');
        continue;
    }

    // Create Round 1
    $ins = $pdo->prepare("
        INSERT INTO tbl_rounds (round_name, round_group, round_tournament, round_event, round_status, round_created_at, round_updated_at)
        VALUES ('Round 1', 0, ?, ?, 1, ?, ?)
    ");
    $ins->execute([$event->listing_event_listing_id, $event->listing_event_id, $now, $now]);
    $round_id   = $pdo->lastInsertId();
    $match_date = $event->listing_event_start ?? $now;

    // Handle odd number — last team gets a bye
    $bye_team = null;
    if ($total % 2 !== 0) {
        $bye_team = array_pop($teams);
        cron_log('  Bye: ' . $bye_team->team_name . ' advances directly to Round 2');
    }

    $match_count = 0;
    foreach (array_chunk($teams, 2) as $pair) {
        $t1 = $pair[0];
        $t2 = $pair[1];

        // Insert match
        $im = $pdo->prepare("
            INSERT INTO matches
                (match_type, match_round_id, match_tournament, match_event,
                 match_team1_id, match_team2_id,
                 match_individual1_id, match_individual2_id,
                 match_date, match_status, match_created_at, match_updated_at)
            VALUES
                ('team', ?, ?, ?,
                 ?, ?,
                 ?, ?,
                 ?, 1, ?, ?)
        ");
        $im->execute([
            $round_id,
            $event->listing_event_listing_id,
            $event->listing_event_id,
            $t1->team_id, $t2->team_id,
            $t1->team_id, $t2->team_id,
            $match_date, $now, $now,
        ]);

        // Lock both teams
        $lock = $pdo->prepare("UPDATE teams SET team_status = 'locked' WHERE team_id IN (?, ?)");
        $lock->execute([$t1->team_id, $t2->team_id]);

        // Notify members
        _notify($pdo, $t1, $t2, $match_date);

        cron_log('  Match: ' . $t1->team_name . '  vs  ' . $t2->team_name);
        $match_count++;
    }

    cron_log('  Done — ' . $match_count . ' match(es) created for event "' . $event->listing_event_title . '".');
}

cron_log('Matchmaking run complete.');
exit(0);

// ---------------------------------------------------------------------------

function _notify(PDO $pdo, object $team1, object $team2, string $match_date): void
{
    $date_str = date('d M Y h:i A', strtotime($match_date));
    $created  = date('Y-m-d H:i:s');

    foreach ([$team1, $team2] as $my_team) {
        $opp      = ($my_team->team_id === $team1->team_id) ? $team2 : $team1;
        $msg_desc = 'Your team "' . $my_team->team_name . '" has been matched against "' . $opp->team_name . '" on ' . $date_str . '. Get ready!';

        $im = $pdo->prepare("
            INSERT INTO messages (msg_title, msg_desc, msg_image, msg_active, msg_created_at, msg_updated_at)
            VALUES ('Match Scheduled!', ?, NULL, 1, ?, ?)
        ");
        $im->execute([$msg_desc, $created, $created]);
        $msg_id = $pdo->lastInsertId();

        // Fetch all accepted members of this team who have a user account
        $ms = $pdo->prepare("
            SELECT tm_user_id
            FROM team_members
            WHERE tm_team_id = ?
              AND tm_status = 'accepted'
              AND tm_user_id IS NOT NULL
        ");
        $ms->execute([$my_team->team_id]);
        $members = $ms->fetchAll();

        foreach ($members as $m) {
            $in = $pdo->prepare("
                INSERT INTO notifications (notify_msg_id, notify_for, notify_from, notify_count, notify_active, notify_time)
                VALUES (?, ?, 0, 0, 1, ?)
            ");
            $in->execute([$msg_id, $m->tm_user_id, $created]);
        }
    }
}
