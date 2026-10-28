-- ============================================================
-- OPTIONAL — only run this if you ever switch Settings handler
-- back to 'database' in app/Config/Settings.php.
--
-- Currently NOT needed (file handler is active).
-- Table name is `ci_settings` to avoid conflict with the
-- app's own `settings` table (logo, FCM key, etc.).
-- ============================================================

CREATE TABLE IF NOT EXISTS `ci_settings` (
    `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `class`      VARCHAR(255)     NOT NULL,
    `key`        VARCHAR(255)     NOT NULL,
    `value`      TEXT             NULL,
    `type`       VARCHAR(31)      NOT NULL DEFAULT 'string',
    `context`    VARCHAR(255)     NULL,
    `created_at` DATETIME         NOT NULL,
    `updated_at` DATETIME         NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
