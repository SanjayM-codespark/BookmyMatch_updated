-- =============================================================
-- Sport Scoring Updates  (safe to run multiple times)
-- Run on bookmymatch_live_db
-- =============================================================

-- 1. Expand match_cricket to TEXT (MODIFY is always safe to re-run)
ALTER TABLE matches
    MODIFY COLUMN match_cricket TEXT DEFAULT NULL;

-- 2. Expand match_winning_desc to TEXT
ALTER TABLE matches
    MODIFY COLUMN match_winning_desc TEXT DEFAULT NULL;

-- 3. Add match_sport_id only if it does not already exist
SET @col_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'matches'
      AND COLUMN_NAME  = 'match_sport_id'
);
SET @sql = IF(
    @col_exists = 0,
    'ALTER TABLE matches ADD COLUMN match_sport_id INT(11) NOT NULL DEFAULT 0 AFTER match_event',
    'SELECT "match_sport_id already exists — skipped" AS info'
);
PREPARE _stmt FROM @sql;
EXECUTE _stmt;
DEALLOCATE PREPARE _stmt;

-- 4. Backfill match_sport_id for existing matches
UPDATE matches m
JOIN listing_events le ON le.listing_event_id   = m.match_event
JOIN listings l        ON l.listing_id          = le.listing_event_listing_id
SET m.match_sport_id = l.listing_sports_id
WHERE m.match_sport_id = 0;

-- 5. Verify
SELECT m.match_id, m.match_sport_id, COALESCE(s.sports_name, 'Unknown') AS sport
FROM matches m
LEFT JOIN sports s ON s.sports_id = m.match_sport_id
ORDER BY m.match_id;
