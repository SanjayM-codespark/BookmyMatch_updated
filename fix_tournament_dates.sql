-- Fix sample tournament and event dates to 2026
-- (original dates were 2025 which are already expired)

UPDATE `listings` SET
    `listing_start_date` = '2026-08-01 06:00:00',
    `listing_end_date`   = '2026-08-31 22:00:00'
WHERE `listing_title` = 'Chennai Cricket Premier League 2025';

-- Push all event dates + deadlines forward by 1 year for this tournament
UPDATE `listing_events` SET
    `listing_event_start`        = DATE_ADD(`listing_event_start`,        INTERVAL 1 YEAR),
    `listing_event_end`          = DATE_ADD(`listing_event_end`,          INTERVAL 1 YEAR),
    `listing_event_lastbook_date`= DATE_ADD(`listing_event_lastbook_date`,INTERVAL 1 YEAR)
WHERE `listing_event_listing_id` = (
    SELECT `listing_id` FROM `listings`
    WHERE `listing_title` = 'Chennai Cricket Premier League 2025'
    LIMIT 1
);

-- Verify
SELECT
    listing_event_id,
    listing_event_title,
    listing_event_lastbook_date,
    listing_event_start
FROM listing_events
WHERE listing_event_listing_id = (
    SELECT listing_id FROM listings
    WHERE listing_title = 'Chennai Cricket Premier League 2025'
    LIMIT 1
)
ORDER BY listing_event_id;
