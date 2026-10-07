-- ==============================================================================
-- KELVS Blog Translatable JSON Restoration (Universal)
-- ==============================================================================
-- This query wraps any blog post stored as raw HTML into Spatie Translatable's
-- expected JSON structure: {"en": "<p>...</p>"}
--
-- Safe & Idempotent:
-- - Any post already starting with {"en": is completely untouched.
-- - Posts that are NULL or empty are untouched.
-- - All raw HTML posts (including Post #7 and newer production posts like #41)
--   are safely converted using native MySQL JSON_OBJECT escaping.
-- ==============================================================================

UPDATE `posts`
SET `content` = JSON_OBJECT('en', `content`)
WHERE `content` NOT LIKE '{"en":%'
  AND `content` IS NOT NULL
  AND `content` != '';
