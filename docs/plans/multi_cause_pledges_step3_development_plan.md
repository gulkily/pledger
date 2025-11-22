# Multi-cause Pledges · Step 3 Development Plan

## Stage 1 – Config scaffolding and selector
- **Goal**: Support repo-stored cause configs plus a selector so any directory can target a specific campaign without touching application code.
- **Dependencies**: Step 2 approved; no code changes yet.
- **Changes**: Create `config/causes/<slug>/app.php` samples; introduce `config/current_cause.php` (or env override) that returns the slug to load; update `config/app.php` to require the selected cause config and expose canonical keys (slug, name, hero copy, price estimates, deadlines, db path, assets, story snippets).
- **Verification**: Hit a simple PHP script or `php -r "var_dump(require 'config/app.php');"` to confirm the merged config resolves without errors and switches when the selector changes.
- **Risks**: Misconfigured file paths could break bootstrap; need guardrails/default cause to avoid fatal errors.

## Stage 2 – API bootstrapping per cause
- **Goal**: Make `api.php` pull DB path and cause metadata from the new config and create per-cause tables if missing.
- **Dependencies**: Stage 1 config loader complete.
- **Changes**: Update DB initialization to use `config['db_path']`; ensure tables are created inside that DB; thread the config array through helper functions; add validation that the configured DB is writable; keep session handling unchanged but scoped to whichever DB file is active.
- **Verification**: With two different configs pointing to separate DB files, call `curl api.php?action=get_pledges` from each directory and confirm the `pledges` table is auto-created per file and totals don’t bleed across directories.
- **Risks**: File permission issues or leftover DB paths from previous installs; accidental leakage if globals persist between directories.

## Stage 3 – Cause metadata endpoint updates
- **Goal**: Extend `get_config` and related responses to return the selected cause’s metadata so the UI can populate copy dynamically.
- **Dependencies**: Stage 2 (API already reading structured config).
- **Changes**: Structure the config payload (`cause_name`, `hero_headline`, `hero_subtext`, `story_paragraphs`, `price_range`, `deadline`, `goal_banner`, etc.); ensure defaults exist so legacy templates don’t break; document fields inline.
- **Verification**: Fetch `api.php?action=get_config` and confirm the JSON mirrors the config overrides for at least two different causes.
- **Risks**: Oversized payloads if we embed huge text blobs; mismatched field names between backend and frontend.

## Stage 4 – Front-end dynamic copy & price estimates
- **Goal**: Replace hard-coded hero/story/form copy in `templates/index.php` + `assets/js/app.js` with config-driven text and price ranges.
- **Dependencies**: Stage 3 delivering metadata.
- **Changes**: Introduce placeholders in the template (data attributes or IDs) and populate them via JS when config loads; update estimate/goal elements to draw from the API; handle missing fields gracefully with fallbacks.
- **Verification**: Serve two directories with different config copy and confirm the UI swaps text, price range, and deadline accordingly without editing template PHP.
- **Risks**: FOUC/empty states if JS fails; some copy (FAQ, research list) might remain shared by design—document what is and isn’t configurable.

## Stage 5 – Multi-cause ops checklist doc
- **Goal**: Document the process for adding/switching campaigns using repo configs + selectors.
- **Dependencies**: Prior stages complete so docs reflect reality.
- **Changes**: Add `docs/plans/multi_cause_ops_checklist.md` (or similar) covering: duplicating directory vs reusing repo, editing selector, customizing config/copy assets, verifying DB creation, smoke tests.
- **Verification**: Walk through the checklist on a fresh directory and confirm it yields a working instance without code edits.
- **Risks**: Docs lag reality; keep instructions versioned with the repo to avoid confusion.
