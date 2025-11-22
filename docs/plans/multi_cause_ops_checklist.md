# Multi-cause Ops Checklist

Use this checklist whenever you spin up a new campaign or switch the active one on a server.

## 1. Duplicate or create cause config
- Copy `config/causes/example-cause.php` to `config/causes/<your-slug>.php` and customize the array:
  - `slug` + `display_name`
  - Price range (`min`, `max`, `description`)
  - Deadline (ISO `YYYY-MM-DD`)
  - Hero copy + avatar
  - Story paragraphs, explore list, research projects
  - `db_path` pointing to a dedicated SQLite file (per directory or under `data/`)
- Commit the new config so it travels with the repo.

## 2. Tell the app which cause to load
- Update `config/current_cause.php` to return your slug **or** set the `PLEDGER_CAUSE=<slug>` environment variable in the web server.
- For multi-directory deployments, each folder can keep its own selector file while sharing the same codebase.

## 3. Verify database path + bootstrap
- Ensure the directory that will contain the SQLite file exists or let the app create it (paths under `data/` are recommended).
- Hit `php -r "\$_GET['action']='get_config'; include 'api.php';"` or open the site in a browser; the API will create tables in the configured file on first run.
- Confirm `pledges.db` (or your custom filename) shows up in the expected directory.

## 4. Smoke-test the UI copy
- Load the page and confirm hero text, story paragraphs, goal banner, and experiment list reflect the new config.
- Submit a small pledge; verify the pledge appears only in this cause’s `pledges` table and not in any other directory.
- Check the manage-your-pledge section still works (edit/delete) and that session tokens remain scoped per database.

## 5. Launch + document
- Update any deployment notes or runbooks with the active slug and DB path.
- If you need to switch causes later, repeat steps 2–4 and archive/export the previous DB file for safekeeping.
