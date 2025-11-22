# Cause Setup Tool · Step 4 Implementation Summary

## Stage 1 – Config prep & script scaffolding
- Added `config/causes/placeholder.php` and switched `config/current_cause.php`/`config/app.php` defaults so the repo shows a loud placeholder until a real cause is configured.
- Created `scripts/cause_wizard.php` entry point with help text and base argument parsing.

## Stage 2 – Interactive prompts
- Implemented slug/db path/price/deadline/goal/hero/story/research prompts with validation, defaults, multi-line list gathering, and update-mode prefill.

## Stage 3 – Preview rendering
- Added summary output (key metrics + changed fields) and a full PHP config preview using a custom short-array exporter for clarity.

## Stage 4 – File + selector operations
- Wizard now confirms before writing, backs up existing configs, writes new files, optionally updates `config/current_cause.php`, and scaffolds the SQLite directory/file.

## Stage 5 – Docs & LLM scaffolding
- Updated `docs/new_cause_setup_guide.md` to prioritize the wizard + placeholder notes, added `README.md` quick-start bullets, and created `docs/llm_cause_prompt.md` for agent-based config drafting.

## Verification
- `php -l scripts/cause_wizard.php`
- Ran `php scripts/cause_wizard.php create` with sample input to ensure prompts, preview, file writes, selector updates, and DB scaffolding all function (then removed the test artifacts).
