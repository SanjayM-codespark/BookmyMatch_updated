-- ============================================================
-- BookMyMatch — Packages & Memberships Setup
-- Run this in phpMyAdmin SQL tab on bookmymatch_live_db
-- Safe to re-run: uses CREATE TABLE IF NOT EXISTS
-- ============================================================

-- 1. PACKAGES TABLE
CREATE TABLE IF NOT EXISTS `packages` (
  `package_id`                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `package_name`                 VARCHAR(100)  NOT NULL,
  `package_description`          TEXT          NULL,
  `package_duration_type`        ENUM('monthly','yearly','lifetime') NOT NULL DEFAULT 'monthly',
  `package_duration_days`        INT           NOT NULL DEFAULT 30   COMMENT '30=monthly, 365=yearly, 0=lifetime',
  `package_price`                DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '0 = free',
  `package_max_matches`          INT           NOT NULL DEFAULT 0    COMMENT '0 = unlimited',
  `package_max_tournaments`      INT           NOT NULL DEFAULT 0    COMMENT '0 = unlimited (for organizers)',
  `package_max_teams`            INT           NOT NULL DEFAULT 0    COMMENT '0 = unlimited',
  `package_can_create_tournament` TINYINT      NOT NULL DEFAULT 0    COMMENT '1 = yes, 0 = no',
  `package_priority_booking`     TINYINT       NOT NULL DEFAULT 0    COMMENT '1 = yes',
  `package_is_featured`          TINYINT       NOT NULL DEFAULT 0    COMMENT '1 = highlight this plan',
  `package_sort_order`           INT           NOT NULL DEFAULT 0,
  `package_active`               TINYINT       NOT NULL DEFAULT 1,
  `package_created_at`           DATETIME      NOT NULL,
  `package_updated_at`           DATETIME      NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. MEMBERSHIPS TABLE
CREATE TABLE IF NOT EXISTS `memberships` (
  `membership_id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `membership_user_id`      INT           NOT NULL,
  `membership_package_id`   INT           NOT NULL,
  `membership_start_date`   DATETIME      NOT NULL,
  `membership_end_date`     DATETIME      NULL        COMMENT 'NULL for lifetime packages',
  `membership_status`       ENUM('active','expired','cancelled') NOT NULL DEFAULT 'active',
  `membership_payment_ref`  VARCHAR(100)  NULL        COMMENT 'Razorpay payment ID or reference',
  `membership_price_paid`   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `membership_created_at`   DATETIME      NOT NULL,
  `membership_updated_at`   DATETIME      NULL,
  INDEX `idx_mem_user`    (`membership_user_id`),
  INDEX `idx_mem_package` (`membership_package_id`),
  INDEX `idx_mem_status`  (`membership_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. SEED: Insert sample packages
INSERT IGNORE INTO `packages`
  (`package_name`, `package_description`, `package_duration_type`, `package_duration_days`,
   `package_price`, `package_max_matches`, `package_max_tournaments`, `package_max_teams`,
   `package_can_create_tournament`, `package_priority_booking`, `package_is_featured`,
   `package_sort_order`, `package_active`, `package_created_at`)
VALUES
  ('Free',    'Basic access — try it out',         'monthly',  30,   0.00,  5,  1, 1, 0, 0, 0, 1, 1, NOW()),
  ('Starter', 'Perfect for casual players',        'monthly',  30,  99.00, 20,  3, 5, 0, 0, 0, 2, 1, NOW()),
  ('Pro',     'For serious competitors',           'monthly',  30, 199.00,  0,  0, 0, 1, 1, 1, 3, 1, NOW()),
  ('Annual',  'Best value — full year unlimited',  'yearly',  365, 999.00,  0,  0, 0, 1, 1, 0, 4, 1, NOW());
