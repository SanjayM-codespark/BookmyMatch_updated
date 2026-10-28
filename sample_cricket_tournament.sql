-- ============================================================
--  BookMyMatch — Sample Cricket Tournament with Events
--  Run this in phpMyAdmin on: bookmymatch_live_db
-- ============================================================

-- Fix team_status column (run once — safe to run again)
ALTER TABLE `teams`
  MODIFY COLUMN `team_status` ENUM('open','full','locked') NOT NULL DEFAULT 'open';

-- ─── Insert Tournament ────────────────────────────────────────
INSERT INTO `listings` (
    `listing_user_id`,
    `listing_title`,
    `listing_sports_id`,
    `listing_start_date`,
    `listing_end_date`,
    `listing_city_id`,
    `listing_state_id`,
    `listing_district_id`,
    `listing_country_id`,
    `listing_address`,
    `listing_details`,
    `listing_image`,
    `listing_rules`,
    `listing_cancel`,
    `listing_active`,
    `listing_created_at`,
    `listing_updated_at`
) VALUES (
    2,                                          -- organizer (Admin user)
    'Chennai Cricket Premier League 2026',
    3,                                          -- sports_id 3 = Cricket
    '2026-08-01 06:00:00',
    '2026-08-31 22:00:00',
    2,                                          -- Chennai
    2,                                          -- Andhra Pradesh (demo state)
    1,
    1,                                          -- India
    'MA Chidambaram Stadium, Chennai, Tamil Nadu',
    'Annual cricket championship open to all age groups. Teams and individual players welcome. T20 format for all categories.',
    NULL,
    '1. Players must carry valid age proof.\n2. Team must have minimum 7 players to start a match.\n3. DRS is not available.\n4. No bodyline bowling.\n5. Umpire decision is final.',
    0,
    1,
    NOW(),
    NOW()
);

-- Capture the tournament ID for events
SET @tid = LAST_INSERT_ID();

-- ─── Listing Events ───────────────────────────────────────────
-- Event 1: Under-18 Individual
INSERT INTO `listing_events` (
    `listing_event_listing_id`, `listing_event_title`, `listing_event_type`,
    `listing_min_member`, `listing_match_type`, `listing_event_fee`,
    `listing_event_duration`, `listing_event_start`, `listing_event_end`,
    `listing_event_lastbook_date`, `listing_event_dob`, `listing_event_tnba_id`,
    `listing_event_bai_id`, `listing_event_other_id`, `listing_event_active`,
    `listing_event_max_players`, `listing_event_max_teams`,
    `listing_event_created_at`, `listing_event_updated_at`
) VALUES (
    @tid, 'Under-18 Individual', 'individual',
    1, 'T20', '150',
    '20 overs', '2026-08-02 08:00:00', '2026-08-05 20:00:00',
    '2026-07-25 23:59:59', 1, 0,
    0, 0, 1,
    1, 0,
    NOW(), NOW()
);

-- Event 2: Under-18 Team (11 players, max 8 teams)
INSERT INTO `listing_events` (
    `listing_event_listing_id`, `listing_event_title`, `listing_event_type`,
    `listing_min_member`, `listing_match_type`, `listing_event_fee`,
    `listing_event_duration`, `listing_event_start`, `listing_event_end`,
    `listing_event_lastbook_date`, `listing_event_dob`, `listing_event_tnba_id`,
    `listing_event_bai_id`, `listing_event_other_id`, `listing_event_active`,
    `listing_event_max_players`, `listing_event_max_teams`,
    `listing_event_created_at`, `listing_event_updated_at`
) VALUES (
    @tid, 'Under-18 Team', 'team',
    7, 'T20', '1200',
    '20 overs', '2026-08-06 08:00:00', '2026-08-10 20:00:00',
    '2026-07-28 23:59:59', 1, 0,
    0, 0, 1,
    11, 8,
    NOW(), NOW()
);

-- Event 3: Under-23 Individual
INSERT INTO `listing_events` (
    `listing_event_listing_id`, `listing_event_title`, `listing_event_type`,
    `listing_min_member`, `listing_match_type`, `listing_event_fee`,
    `listing_event_duration`, `listing_event_start`, `listing_event_end`,
    `listing_event_lastbook_date`, `listing_event_dob`, `listing_event_tnba_id`,
    `listing_event_bai_id`, `listing_event_other_id`, `listing_event_active`,
    `listing_event_max_players`, `listing_event_max_teams`,
    `listing_event_created_at`, `listing_event_updated_at`
) VALUES (
    @tid, 'Under-23 Individual', 'individual',
    1, 'T20', '200',
    '20 overs', '2026-08-07 08:00:00', '2026-08-11 20:00:00',
    '2026-07-28 23:59:59', 1, 0,
    0, 0, 1,
    1, 0,
    NOW(), NOW()
);

-- Event 4: Under-23 Team (11 players, max 8 teams)
INSERT INTO `listing_events` (
    `listing_event_listing_id`, `listing_event_title`, `listing_event_type`,
    `listing_min_member`, `listing_match_type`, `listing_event_fee`,
    `listing_event_duration`, `listing_event_start`, `listing_event_end`,
    `listing_event_lastbook_date`, `listing_event_dob`, `listing_event_tnba_id`,
    `listing_event_bai_id`, `listing_event_other_id`, `listing_event_active`,
    `listing_event_max_players`, `listing_event_max_teams`,
    `listing_event_created_at`, `listing_event_updated_at`
) VALUES (
    @tid, 'Under-23 Team', 'team',
    7, 'T20', '1500',
    '20 overs', '2026-08-12 08:00:00', '2026-08-16 20:00:00',
    '2026-07-30 23:59:59', 1, 0,
    0, 0, 1,
    11, 8,
    NOW(), NOW()
);

-- Event 5: Above-19 / Open Individual
INSERT INTO `listing_events` (
    `listing_event_listing_id`, `listing_event_title`, `listing_event_type`,
    `listing_min_member`, `listing_match_type`, `listing_event_fee`,
    `listing_event_duration`, `listing_event_start`, `listing_event_end`,
    `listing_event_lastbook_date`, `listing_event_dob`, `listing_event_tnba_id`,
    `listing_event_bai_id`, `listing_event_other_id`, `listing_event_active`,
    `listing_event_max_players`, `listing_event_max_teams`,
    `listing_event_created_at`, `listing_event_updated_at`
) VALUES (
    @tid, 'Above-19 Open Individual', 'individual',
    1, 'T20', '250',
    '20 overs', '2026-08-17 08:00:00', '2026-08-21 20:00:00',
    '2026-08-01 23:59:59', 0, 0,
    0, 0, 1,
    1, 0,
    NOW(), NOW()
);

-- Event 6: Above-19 / Open Team (11 players, max 16 teams)
INSERT INTO `listing_events` (
    `listing_event_listing_id`, `listing_event_title`, `listing_event_type`,
    `listing_min_member`, `listing_match_type`, `listing_event_fee`,
    `listing_event_duration`, `listing_event_start`, `listing_event_end`,
    `listing_event_lastbook_date`, `listing_event_dob`, `listing_event_tnba_id`,
    `listing_event_bai_id`, `listing_event_other_id`, `listing_event_active`,
    `listing_event_max_players`, `listing_event_max_teams`,
    `listing_event_created_at`, `listing_event_updated_at`
) VALUES (
    @tid, 'Above-19 Open Team', 'team',
    7, 'T20', '2000',
    '20 overs', '2026-08-22 08:00:00', '2026-08-27 20:00:00',
    '2026-08-05 23:59:59', 0, 0,
    0, 0, 1,
    11, 16,
    NOW(), NOW()
);

-- Event 7: Women's Team (11 players, max 8 teams)
INSERT INTO `listing_events` (
    `listing_event_listing_id`, `listing_event_title`, `listing_event_type`,
    `listing_min_member`, `listing_match_type`, `listing_event_fee`,
    `listing_event_duration`, `listing_event_start`, `listing_event_end`,
    `listing_event_lastbook_date`, `listing_event_dob`, `listing_event_tnba_id`,
    `listing_event_bai_id`, `listing_event_other_id`, `listing_event_active`,
    `listing_event_max_players`, `listing_event_max_teams`,
    `listing_event_created_at`, `listing_event_updated_at`
) VALUES (
    @tid, 'Women\'s Open Team', 'team',
    7, 'T20', '1000',
    '20 overs', '2026-08-28 08:00:00', '2026-08-31 20:00:00',
    '2026-08-10 23:59:59', 0, 0,
    0, 0, 1,
    11, 8,
    NOW(), NOW()
);

-- ─── Verify ───────────────────────────────────────────────────
SELECT
    l.listing_id,
    l.listing_title,
    COUNT(e.listing_event_id) AS total_events
FROM listings l
LEFT JOIN listing_events e ON e.listing_event_listing_id = l.listing_id
WHERE l.listing_title = 'Chennai Cricket Premier League 2026'
GROUP BY l.listing_id;

SELECT
    listing_event_id,
    listing_event_title,
    listing_event_type,
    listing_event_fee,
    listing_event_max_players,
    listing_event_max_teams,
    listing_event_start
FROM listing_events
WHERE listing_event_listing_id = @tid
ORDER BY listing_event_id;
