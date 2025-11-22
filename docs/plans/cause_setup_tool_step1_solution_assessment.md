# Cause Setup Tool · Step 1 Solution Assessment

**Problem statement**: Creating a new cause currently means editing PHP arrays by hand; we need a lightweight utility that guides maintainers through the config fields without digging into code, reducing errors and speeding up launches.

**Option 1 – CLI wizard (recommended)**
- Pros: Runs on any server where PHP/Node is already available; easy to gatekeep since only shell users can run it; can prompt/validate fields interactively and write the config + selector updates directly; no authentication or hosting complexity.
- Cons: Requires SSH/terminal access; harder to preview long-form copy while typing; cannot be used from mobile.

**Option 2 – Web-based admin form (local-only)**
- Pros: Friendly UI with live previews; can be used from any device with a browser; leverages existing front-end stack for immediate WYSIWYG feedback.
- Cons: Needs auth/secret-token plumbing to avoid exposing admin UI; must persist drafts before writing files; riskier to deploy on public hosting.

**Option 3 – Config generator script (non-interactive)**
- Pros: Accepts a JSON/YAML input and outputs the PHP config file, making it easy to automate or tie into other tooling; minimal UX to build.
- Cons: Still forces maintainers to prepare the JSON manually; offers no guardrails for missing fields; doesn’t update selectors/DB paths automatically.

**Recommendation**: Option 1. A CLI wizard keeps scope tight, works wherever the repo is checked out, and still allows us to extend later with templates or previews. Once that exists, we can revisit layering in a web editor if non-technical collaborators need it.
