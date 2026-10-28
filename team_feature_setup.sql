-- ============================================================
-- BookMyMatch — Team Feature Setup (MySQL 8 safe, re-runnable)
-- Paste the ENTIRE file into phpMyAdmin SQL tab and click Go
-- Uses INFORMATION_SCHEMA checks so duplicate columns are skipped
-- ============================================================

-- sports: sports_max_players (superadmin sets default team size per sport)
SET @s = IF(
  NOT EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='sports' AND COLUMN_NAME='sports_max_players'),
  'ALTER TABLE `sports` ADD COLUMN `sports_max_players` INT NOT NULL DEFAULT 1',
  'SELECT 1'
); PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- listing_events: listing_event_max_players
SET @s = IF(
  NOT EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='listing_events' AND COLUMN_NAME='listing_event_max_players'),
  'ALTER TABLE `listing_events` ADD COLUMN `listing_event_max_players` INT NOT NULL DEFAULT 1',
  'SELECT 1'
); PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- listing_events: listing_event_max_teams
SET @s = IF(
  NOT EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='listing_events' AND COLUMN_NAME='listing_event_max_teams'),
  'ALTER TABLE `listing_events` ADD COLUMN `listing_event_max_teams` INT NOT NULL DEFAULT 0',
  'SELECT 1'
); PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- teams: team_leader_id
SET @s = IF(
  NOT EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teams' AND COLUMN_NAME='team_leader_id'),
  'ALTER TABLE `teams` ADD COLUMN `team_leader_id` INT NULL',
  'SELECT 1'
); PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- teams: team_join_token
SET @s = IF(
  NOT EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teams' AND COLUMN_NAME='team_join_token'),
  'ALTER TABLE `teams` ADD COLUMN `team_join_token` VARCHAR(64) NULL',
  'SELECT 1'
); PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- teams: team_max_players
SET @s = IF(
  NOT EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teams' AND COLUMN_NAME='team_max_players'),
  'ALTER TABLE `teams` ADD COLUMN `team_max_players` INT NOT NULL DEFAULT 1',
  'SELECT 1'
); PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- teams: team_status
SET @s = IF(
  NOT EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teams' AND COLUMN_NAME='team_status'),
  "ALTER TABLE `teams` ADD COLUMN `team_status` ENUM('open','full','locked') NOT NULL DEFAULT 'open'",
  'SELECT 1'
); PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- teams: team_listing_event_id
SET @s = IF(
  NOT EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teams' AND COLUMN_NAME='team_listing_event_id'),
  'ALTER TABLE `teams` ADD COLUMN `team_listing_event_id` INT NULL',
  'SELECT 1'
); PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- team_members table (CREATE IF NOT EXISTS is always safe)
CREATE TABLE IF NOT EXISTS `team_members` (
  `tm_id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tm_team_id`    INT          NOT NULL,
  `tm_user_id`    INT          NULL,
  `tm_name`       VARCHAR(100) NOT NULL,
  `tm_mobile`     VARCHAR(20)  NULL,
  `tm_email`      VARCHAR(100) NULL,
  `tm_status`     ENUM('pending','accepted','rejected') NOT NULL DEFAULT 'accepted',
  `tm_added_by`   ENUM('leader','self','request')       NOT NULL DEFAULT 'leader',
  `tm_created_at` DATETIME     NOT NULL,
  INDEX `idx_tm_team` (`tm_team_id`),
  INDEX `idx_tm_user` (`tm_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
