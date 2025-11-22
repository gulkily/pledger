# Cause Setup Tool · Step 3 Development Plan

## Stage 1 – Config prep & script scaffolding
- **Goal**: Move the current GitHub Universe campaign into `config/causes/github-universe-trip.php`, add a `config/causes/placeholder.php` that loudly signals “configure me”, and lay down the CLI entry point (`scripts/cause_wizard.php`).
- **Dependencies**: None beyond Step 2 approval.
- **Changes**: Ensure `config/current_cause.php` references the placeholder by default; verify templates pull from config; set up basic argument parsing (default `create`, `update <slug>`, `--help`).
- **Verification**: Run `php scripts/cause_wizard.php --help` and `php scripts/cause_wizard.php update example` to confirm argument handling without prompts.
- **Risks**: None major; ensure script exits non-zero on invalid args.

## Stage 2 – Interactive prompts + validation
- **Goal**: Prompt for slug, display name, DB path, price range, deadline, goal banner, hero, story, explore, research projects.
- **Dependencies**: Stage 1 (script skeleton).
- **Changes**: Add helper functions for textual prompts, yes/no questions, multi-line input (with sentinel `.` to finish), numeric validation, and default values (prefill from existing config in update mode). Store results in structured array.
- **Verification**: Run through create and update flows manually, ensuring validation catches empty slug, non-numeric prices, invalid dates.
- **Risks**: Multi-line UX awkward; document sentinel usage in prompt.

## Stage 3 – Config generation & summary preview
- **Goal**: Render collected answers into PHP code matching existing config structure; show summary diff before writing.
- **Dependencies**: Stage 2 data model.
- **Changes**: Build template renderer (heredoc) that exports array with proper indentation; print summary (key fields + truncated copy). In update mode, show differences or highlight unchanged fields.
- **Verification**: Generate config and compare with manual example; run `php -l` on generated file.
- **Risks**: Escaping quotes/newlines incorrectly; mitigate by using `var_export` and custom formatting.

## Stage 4 – File operations (write config, selector, DB path)
- **Goal**: Persist the config file, optionally update `config/current_cause.php`, and create DB directories if missing.
- **Dependencies**: Stage 3 (rendered code snippet ready).
- **Changes**: Ensure directories exist; back up existing config when overwriting; prompt before replacing; allow user to opt into selector update; create empty SQLite file or at least touch path to verify permissions.
- **Verification**: Run wizard to create new config, confirm files appear with expected permissions; run update to ensure backups.
- **Risks**: Overwriting user customizations accidentally; mitigate with confirmation and backups (`.bak`).

## Stage 5 – Docs, LLM scaffolding & integration
- **Goal**: Document the tool, highlight the placeholder campaign behavior, and provide a prompt-friendly checklist for LLM agents to generate new configs if desired.
- **Dependencies**: Script working end-to-end.
- **Changes**: Update `docs/new_cause_setup_guide.md` to prioritize the wizard and mention the placeholder; add a short `docs/llm_cause_prompt.md` (or appendix) that lists the fields/format an agent must produce; add README snippet.
- **Verification**: Docs mention the command and placeholder; LLM prompt document exists and mirrors config schema.
- **Risks**: Docs lag; keep concise and version-controlled.
