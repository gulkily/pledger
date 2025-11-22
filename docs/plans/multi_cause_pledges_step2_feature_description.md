# Multi-cause Pledges · Step 2 Feature Description

**Problem**: Ilya needs to run several pledge drives simultaneously without rewriting code. Each cause must live in its own directory with its own SQLite file and copy, but all directories should share the same code, API behavior, and improvements.

**User stories**
- As the maintainer, I can duplicate the pledger directory, edit one config file (cause slug, copy, price range, DB path), and instantly have a new cause instance that inherits future code updates.
- As a supporter, I see the correct cause name, narrative, and funding progress that match the directory I’m visiting, and my pledge only affects that cause’s total.
- As the maintainer, I can confirm that a pledge I manage in one cause never appears in another, even if the instances are running on the same server.
- As a partner reviewing progress, I can read the per-cause stats (goal text, total % pledged, supporter list) without worrying about data leakage from other causes.

**Core requirements**
- Introduce a richer `config/app.php` schema (cause slug, display name, goal blurb, min/max price estimates, pledge deadline, dedicated SQLite path) that each directory can override without editing code files.
- Store canonical configs for every active campaign inside the repo (e.g., `config/causes/<slug>/app.php`) plus a simple selector (`config/current_cause.php` or env var) so the server can switch campaigns without touching code; local clones can point at any cause by changing the selector.
- Update the PHP API layer so every endpoint reads from the configured DB file and surfaces the configured cause metadata in `get_config`, ensuring tables auto-initialize if the file is new.
- Ensure the front-end loads copy and progress numbers from the API so the hero, story card, and pledge form stay consistent with the per-cause config (no hard-coded strings tied to one trip).
- Provide a lightweight checklist (in docs) for spinning up a new cause directory: copy folder, tweak config values, optional assets, verify.
- Keep room for a future merge (e.g., optional aggregate view) by keeping cause-specific identifiers consistent and avoiding schema changes that would block consolidation later.

**User flow**
1. Maintainer duplicates the pledger directory, renames it for the new cause, and edits `config/app.php` with the new slug, copy, and DB path.
2. Maintainer deploys/serves that directory; the first API call creates the per-cause SQLite file/tables automatically.
3. Supporter visits the cause’s URL; the front-end requests `get_config`, which returns cause metadata that populates hero text, price estimates, and deadline messaging.
4. Supporter submits a pledge; the API writes it into the cause’s configured DB file and returns updated totals scoped to that file only.
5. Maintainer (or supporter with session) manages pledges for that cause; other directories stay isolated because they point at different DB files and configs.

**Success criteria**
- Duplicating the directory and editing only `config/app.php` is sufficient to launch a new cause with accurate UI copy and functioning pledges.
- Each cause’s pledge totals and supporter lists remain isolated (confirmed by creating sample pledges in two directories and verifying no cross-contamination).
- Non-technical maintainers can follow the documented checklist to launch a cause without touching PHP/JS.
- Future consolidation remains possible because cause metadata (slug, name) is structured rather than baked into templates.
