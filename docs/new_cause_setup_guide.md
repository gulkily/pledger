# New Cause Setup Guide

This walkthrough assumes you already cloned the repo and want to deploy another campaign (cause) that lives alongside the existing ones while sharing the same code.

## 1. Copy the template config
1. Duplicate `config/causes/example-cause.php` and rename it to `config/causes/<slug>.php` (e.g., `config/causes/nyc-community-retreat.php`).
2. Update the returned array:
   - `slug`: short identifier (letters, numbers, `-`).
   - `display_name`: human-friendly title for the cause selector/UI.
   - `db_path`: absolute path to the SQLite file for this cause (can live under a shared `/data` directory or in the cause folder).
   - `price_range`: adjust `min`, `max`, and `description` to match the new budget.
   - `deadline`: ISO date (`YYYY-MM-DD`).
   - `goal_banner`: short text shown under the chart.
   - `hero`, `story`, and `research_projects`: copy that appears across the UI. Most paragraphs can be pasted verbatim into the arrays.

> Tip: keep image paths (`hero.avatar.src`) relative to the repo root so each cause can ship its own assets.

## 2. Point the app at the new cause
Pick one of the following:
- **Per-directory clone**: edit `config/current_cause.php` to return your new slug.
- **Single repo + environment variables**: set `PLEDGER_CAUSE=<slug>` in your web server or `.env` so the same codebase can serve different causes without editing files.

The env var wins over `current_cause.php`, so you can keep a default slug checked in and override per deployment.

## 3. Verify the database path
1. Ensure the directory you specified in `db_path` exists or is creatable by PHP (the app will attempt to `mkdir -p`).
2. Hit the API once to bootstrap tables: `php -r "\$_GET['action']='get_config'; include 'api.php';"` or load the site in a browser.
3. Confirm the SQLite file appeared and has `pledges`, `sessions`, and `config` tables. (`sqlite3 path/to.db '.tables'`).

## 4. Smoke-test the UI copy
1. Load the cause in a browser and ensure hero text, story paragraphs, explore list, goal banner, and price range reflect your config.
2. Submit a small test pledge; verify it lands only in the new SQLite file.
3. Refresh to confirm the chart, percentage estimate, and supporter list display the new pledge.
4. Use the manage section (edit/delete) to confirm session ownership works.

## 5. Deploy and document
- Commit the new cause file so teammates can reuse it.
- Update ops notes with the active slug, DB path, and any asset requirements.
- If you later retire the cause, archive its SQLite file (`mv data/<slug>.db archive/`) and point the selector at another config.

Following this checklist keeps each cause isolated while letting you roll out shared code updates across every deployment.
