# Multi-cause Pledges · Step 4 Implementation Summary

## Stage 1 – Config scaffolding & selector
- Added `config/current_cause.php` plus `config/causes/` (default + template) so each campaign can live in the repo.
- Rebuilt `config/app.php` to load the selected slug, fall back gracefully, and expose structured hero/story/pricing metadata.

## Stage 2 – API bootstrapping per cause
- Hardened `api.php` to ensure the configured DB directory exists/writable before initializing SQLite.
- Default config rows now seed from the active cause file so every cause gets the correct price range/deadline on first run.

## Stage 3 – Cause metadata endpoint
- `get_config` now merges DB overrides with file-based metadata and returns a `cause` payload (hero/story, price range, research list) for the UI.
- Added helpers to shape metadata, load DB config rows, and fatal with JSON if the DB cannot be opened.

## Stage 4 – Front-end dynamic copy & estimates
- `templates/index.php` renders hero/story/research copy from the active cause config and bootstraps that data into `window.APP_CONFIG`.
- `assets/js/app.js` hydrates the hero, story, research list, goal banner, price range, and deadline from the API response so each directory stays config-driven.

## Stage 5 – Ops checklist
- Captured the runbook for adding/switching causes in `docs/plans/multi_cause_ops_checklist.md` covering config duplication, selector tweaks, DB verification, and smoke tests.

## Verification
- `php -l api.php` and `php -l templates/index.php` (syntax).
- `php -r "$_GET['action']='get_config'; include 'api.php';"` to confirm per-cause config payload and session token creation.
