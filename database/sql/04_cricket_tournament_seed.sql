-- ================================================================
-- CRICKET TOURNAMENT SEED DATA
-- Organizer: noonesports (user_id = 64)
-- Sport: Cricket (sports_id = 3)
-- 5 Teams · up to 11 players · July 2026
-- Run in phpMyAdmin: select bookmymatch_live_db, then import/execute
-- ================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------
-- 1. TOURNAMENT (listing)
-- ----------------------------------------------------------------
INSERT INTO listings (
    listing_user_id,
    listing_title,
    listing_sports_id,
    listing_start_date,
    listing_end_date,
    listing_address,
    listing_details,
    listing_active,
    listing_created_at,
    listing_updated_at
) VALUES (
    64,
    'Noonesports IPL Style Cricket Cup 2026',
    3,
    '2026-07-05 09:00:00',
    '2026-07-31 18:00:00',
    'Chennai Cricket Ground, Anna Nagar, Chennai',
    'A 5-team T20 knockout cricket tournament. Best of 1 match per round.',
    1,
    NOW(),
    NOW()
);

SET @tourney_id = LAST_INSERT_ID();

-- ----------------------------------------------------------------
-- 2. EVENT (listing_event) — single event under this tournament
-- ----------------------------------------------------------------
INSERT INTO listing_events (
    listing_event_listing_id,
    listing_event_title,
    listing_event_type,
    listing_match_type,
    listing_event_fee,
    listing_event_max_players,
    listing_event_max_teams,
    listing_event_start,
    listing_event_end,
    listing_event_lastbook_date,
    listing_event_matchmaking_date,
    listing_event_matchmaking_done,
    listing_event_active,
    listing_event_created_at,
    listing_event_updated_at
) VALUES (
    @tourney_id,
    'T20 Cricket Open Team',   -- plain text title (consistent with recent events)
    'team',
    'knockout',
    '500',
    11,                        -- max 11 players per team
    5,                         -- max 5 teams
    '2026-07-05 09:00:00',
    '2026-07-31 18:00:00',
    '2026-07-04 23:59:59',     -- registration closes July 4
    '2026-07-05 08:00:00',     -- matchmaking fires July 5 at 8 AM
    0,
    1,
    NOW(),
    NOW()
);

SET @event_id = LAST_INSERT_ID();

-- ----------------------------------------------------------------
-- 3. TEAMS
--    Active-membership users available (role=2):
--      20=ram, 31=mani, 32=Tester spider, 33=Vishnu,
--      35=daman, 36=rmr, 38=rajen, 39=testing211,
--      78=tamilraji, 79=raji, 80=suriya
--    Team 5 captain = organizer user 64 (noonesports)
-- ----------------------------------------------------------------

-- ── TEAM 1: Chennai Super Kings ──────────────────────────────────
INSERT INTO teams (
    team_name, team_leader_id, team_listing_event_id,
    team_tournament, team_event,
    team_join_token, team_max_players, team_status, team_created_at
) VALUES (
    'Chennai Super Kings',
    20,
    @event_id,
    @tourney_id, @event_id,
    MD5(CONCAT('csk_seed_', @event_id)),
    11, 'open', NOW()
);
SET @t1 = LAST_INSERT_ID();

INSERT INTO team_members (tm_team_id, tm_user_id, tm_name, tm_mobile, tm_email, tm_status, tm_added_by, tm_created_at) VALUES
(@t1, 20, 'Ram',           '8056566771',  'ram@gmail.com',          'accepted', 'leader', NOW()),
(@t1, 31, 'Mani',          '8531097819',  'mani@gmail.com',         'accepted', 'leader', NOW()),
(@t1, 32, 'Tester Spider', '9876543212',  'tester005@gmail.com',    'accepted', 'leader', NOW());

-- ── TEAM 2: Mumbai Indians ─────────────────────────────────────────
INSERT INTO teams (
    team_name, team_leader_id, team_listing_event_id,
    team_tournament, team_event,
    team_join_token, team_max_players, team_status, team_created_at
) VALUES (
    'Mumbai Indians',
    33,
    @event_id,
    @tourney_id, @event_id,
    MD5(CONCAT('mi_seed_', @event_id)),
    11, 'open', NOW()
);
SET @t2 = LAST_INSERT_ID();

INSERT INTO team_members (tm_team_id, tm_user_id, tm_name, tm_mobile, tm_email, tm_status, tm_added_by, tm_created_at) VALUES
(@t2, 33, 'Vishnu',       '9876543213',  'vis321@gmail.com',       'accepted', 'leader', NOW()),
(@t2, 35, 'Daman Sarjun', '8939945056',  'daman.sarjun@gmail.com', 'accepted', 'leader', NOW()),
(@t2, 36, 'RMR',          '8056566769',  'rmr@gmail.com',          'accepted', 'leader', NOW());

-- ── TEAM 3: Royal Challengers Bangalore ────────────────────────────
INSERT INTO teams (
    team_name, team_leader_id, team_listing_event_id,
    team_tournament, team_event,
    team_join_token, team_max_players, team_status, team_created_at
) VALUES (
    'Royal Challengers Bangalore',
    38,
    @event_id,
    @tourney_id, @event_id,
    MD5(CONCAT('rcb_seed_', @event_id)),
    11, 'open', NOW()
);
SET @t3 = LAST_INSERT_ID();

INSERT INTO team_members (tm_team_id, tm_user_id, tm_name, tm_mobile, tm_email, tm_status, tm_added_by, tm_created_at) VALUES
(@t3, 38, 'Rajen',       '8056566771',  'rajen@gmail.com',         'accepted', 'leader', NOW()),
(@t3, 39, 'Testing211',  '8072802065',  'testing211@gmail.com',    'accepted', 'leader', NOW()),
(@t3, 78, 'Tamilraji',   '7356892487',  'tamilraji@gamil.com',     'accepted', 'leader', NOW());

-- ── TEAM 4: Delhi Capitals ─────────────────────────────────────────
INSERT INTO teams (
    team_name, team_leader_id, team_listing_event_id,
    team_tournament, team_event,
    team_join_token, team_max_players, team_status, team_created_at
) VALUES (
    'Delhi Capitals',
    79,
    @event_id,
    @tourney_id, @event_id,
    MD5(CONCAT('dc_seed_', @event_id)),
    11, 'open', NOW()
);
SET @t4 = LAST_INSERT_ID();

INSERT INTO team_members (tm_team_id, tm_user_id, tm_name, tm_mobile, tm_email, tm_status, tm_added_by, tm_created_at) VALUES
(@t4, 79, 'Raji',   '7356892489',  'raji@gamil.com',        'accepted', 'leader', NOW()),
(@t4, 80, 'Suriya', '7356892488',  'suriyaraji@gamil.com',  'accepted', 'leader', NOW());

-- ── TEAM 5: Kolkata Knight Riders ──────────────────────────────────
-- Captain = organizer noonesports (user_id=64), no other members used up
INSERT INTO teams (
    team_name, team_leader_id, team_listing_event_id,
    team_tournament, team_event,
    team_join_token, team_max_players, team_status, team_created_at
) VALUES (
    'Kolkata Knight Riders',
    64,
    @event_id,
    @tourney_id, @event_id,
    MD5(CONCAT('kkr_seed_', @event_id)),
    11, 'open', NOW()
);
SET @t5 = LAST_INSERT_ID();

INSERT INTO team_members (tm_team_id, tm_user_id, tm_name, tm_mobile, tm_email, tm_status, tm_added_by, tm_created_at) VALUES
(@t5, 64, 'Noonesports', '9361303772', 'developer@spiderindia.com', 'accepted', 'leader', NOW());

-- ----------------------------------------------------------------
-- 4. VERIFY — confirm what was inserted
-- ----------------------------------------------------------------
SELECT
    t.team_id,
    t.team_name,
    t.team_leader_id,
    t.team_status,
    COUNT(tm.tm_id) AS members,
    le.listing_event_id,
    le.listing_event_title,
    l.listing_title AS tournament
FROM teams t
JOIN team_members  tm ON tm.tm_team_id  = t.team_id AND tm.tm_status = 'accepted'
JOIN listing_events le ON le.listing_event_id = t.team_listing_event_id
JOIN listings       l  ON l.listing_id = le.listing_event_listing_id
WHERE t.team_listing_event_id = @event_id
GROUP BY t.team_id, t.team_name, t.team_leader_id, t.team_status,
         le.listing_event_id, le.listing_event_title, l.listing_title
ORDER BY t.team_id;

SELECT
    @tourney_id AS new_tournament_id,
    @event_id   AS new_event_id,
    '2026-07-05 08:00:00' AS matchmaking_fires_at;

SET FOREIGN_KEY_CHECKS = 1;
