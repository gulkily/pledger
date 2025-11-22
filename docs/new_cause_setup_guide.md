# New Cause Setup Guide

Use this to spin up a campaign quickly while keeping causes isolated and the shared codebase intact.

## 1. Run the CLI wizard (recommended)
1. From the repo root run `php scripts/cause_wizard.php create`.
2. Fill in the prompts (slug, price range, goal banner, hero copy, story paragraphs, research projects). For lists, enter one item per line and finish with a single `.`.
3. Review the summary + config preview, then confirm to write the config, update `config/current_cause.php`, and scaffold the SQLite path.
4. Need revisions later? Run `php scripts/cause_wizard.php update <slug>`; existing values are prefilled so you only change what you need.

> Want an LLM to draft the values first? Use the template in `docs/llm_cause_prompt.md` and paste the outputs into the wizard.

## 2. Placeholder campaign expectations
- The repo ships with a `placeholder` cause that loudly says "Set up your pledge campaign" when no real config exists.
- After you create a real campaign, either let the wizard update `config/current_cause.php` or set `PLEDGER_CAUSE=<slug>` on the server to switch directories/hosts.

## 3. Manual editing (fallback)
If the wizard isn’t an option, duplicate `config/causes/example-cause.php` and edit the array by hand (slug, display name, db path, price range, deadline, goal banner, hero, story, research projects). Then update `config/current_cause.php` or the env var to point at the new slug.

## 4. Verify the database path
1. Ensure the directory used in `db_path` exists (the wizard will `mkdir -p` + `touch` it for you).
2. Hit the API once (`php -r "\$_GET['action']='get_config'; include 'api.php';"`) or load the page to auto-create tables.
3. Confirm `pledges`, `sessions`, and `config` tables exist via `sqlite3 path/to.db '.tables'`.

## 5. Smoke-test the UI copy
1. Load the cause in a browser and confirm hero text, story paragraphs, explore list, goal banner, and price range match your config.
2. Submit a tiny pledge; verify it only appears in that cause’s SQLite file.
3. Use the manage section (edit/delete) to ensure session ownership works end-to-end.

## 6. Deploy and document
- Commit the new cause file so teammates inherit it.
- Update ops notes with the active slug, DB path, and asset requirements.
- Archive retired runs by moving their SQLite files (e.g., `mv data/<slug>.db archive/`) and pointing the selector at another slug.

Following the wizard-first flow keeps setup under five minutes and eliminates copy/paste errors.
