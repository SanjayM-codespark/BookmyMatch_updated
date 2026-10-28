-- ============================================================
-- Session cleanup: remove stale CodeIgniter\Settings migration
-- record inserted when `php spark migrate --all` partially ran.
--
-- What happened:
--   Migration 1 (CreateSettingsTable) ran with IF NOT EXISTS,
--   found your existing `settings` table, skipped creation but
--   still wrote a record into the migrations table.
--   Migration 2 (AddContextColumn) failed — no DB change made.
--
-- We switched to the file handler so no settings DB table is
-- needed. This query removes the orphan record.
-- ============================================================

DELETE FROM `migrations`
WHERE `namespace` = 'CodeIgniter\\Settings';
