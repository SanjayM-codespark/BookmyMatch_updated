-- Add matchmaking_date column to listing_events
SET @s = IF(
    NOT EXISTS(
        SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'listing_events'
        AND COLUMN_NAME = 'listing_event_matchmaking_date'
    ),
    'ALTER TABLE `listing_events` ADD COLUMN `listing_event_matchmaking_date` DATETIME NULL DEFAULT NULL AFTER `listing_event_lastbook_date`',
    'SELECT 1'
);
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- Track whether matchmaking has already been run for an event
SET @s = IF(
    NOT EXISTS(
        SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'listing_events'
        AND COLUMN_NAME = 'listing_event_matchmaking_done'
    ),
    'ALTER TABLE `listing_events` ADD COLUMN `listing_event_matchmaking_done` TINYINT(1) NOT NULL DEFAULT 0 AFTER `listing_event_matchmaking_date`',
    'SELECT 1'
);
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- Max players per team
SET @s = IF(
    NOT EXISTS(
        SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'listing_events'
        AND COLUMN_NAME = 'listing_event_max_players'
    ),
    'ALTER TABLE `listing_events` ADD COLUMN `listing_event_max_players` INT(11) NOT NULL DEFAULT 1 AFTER `listing_event_matchmaking_done`',
    'SELECT 1'
);
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- Max teams allowed for an event (0 = unlimited)
SET @s = IF(
    NOT EXISTS(
        SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'listing_events'
        AND COLUMN_NAME = 'listing_event_max_teams'
    ),
    'ALTER TABLE `listing_events` ADD COLUMN `listing_event_max_teams` INT(11) NOT NULL DEFAULT 0 AFTER `listing_event_max_players`',
    'SELECT 1'
);
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;
