# Pledger

A lightweight PHP app for running multi-cause pledge drives.

## Quick start for ops
- `php scripts/cause_wizard.php create` — guided prompts to spin up a new cause config (writes to `config/causes/<slug>.php`).
- `php scripts/cause_wizard.php update <slug>` — load an existing config, update copy/pricing, and rewrite the file.
- `docs/new_cause_setup_guide.md` — end-to-end checklist covering the placeholder cause, wizard flow, and manual fallback.
- `docs/llm_cause_prompt.md` — prompt template if you want an LLM to draft config fields before running the wizard.

After creating a cause, run through the smoke tests, point `config/current_cause.php` (or `PLEDGER_CAUSE`) at the new slug, and deploy.

## Local testing
Use PHP’s built-in server to preview changes quickly:

```
php -S 127.0.0.1:8000
```

Then open http://127.0.0.1:8000 in a browser. Stop the server with Ctrl+C when you’re done.
