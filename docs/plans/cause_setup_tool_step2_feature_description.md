# Cause Setup Tool · Step 2 Feature Description

**Problem**: Maintainers currently duplicate cause configs by hand, which is error-prone and slow. We need a guided tool that asks for the core fields (slug, names, prices, copy) and writes the correct PHP config + selector updates so launching a new cause takes minutes instead of editing arrays manually.

**User stories**
- As the maintainer, I can run one command, answer prompts (slug, price range, copy snippets), and have a new `config/causes/<slug>.php` file plus optional selector update authored for me.
- As a maintainer, I can preview the generated config before saving and cancel if something looks off.
- As the maintainer, I can rerun the tool with `--update <slug>` to tweak existing configs without editing PHP directly.
- As a maintainer, I can point the tool at custom DB directories so each cause’s SQLite file is automatically scaffolded.

**Core requirements**
- Implement a CLI wizard (likely PHP script or lightweight Node) invoked via `php scripts/cause_wizard.php` (or similar) that walks through the required fields with validation and writes the config file.
- Support two modes: `create` (new config file) and `update` (load existing config, prefill answers, overwrite file).
- Offer optional steps to update `config/current_cause.php` and create the target DB directory/file if it doesn’t exist.
- Allow maintainers to supply long-form copy via editor prompts (e.g., open `$EDITOR`) or paste multi-line responses, trimming and normalizing arrays.
- Produce a summary before saving and require explicit confirmation (Y/N).

**User flow**
1. Maintainer runs `php scripts/cause_wizard.php create`.
2. Wizard asks for slug, display name, DB path (with default under `/data/<slug>.db`), price range, deadline, goal banner, hero text/avatar, story paragraphs, explore list, research projects.
3. Answers are validated (non-empty slug, numeric price bounds, valid URL for avatar, etc.).
4. Tool shows a summary preview and asks “Write config? (y/N)”.
5. On confirmation, it writes `config/causes/<slug>.php`, optionally updates `config/current_cause.php`, and creates the DB directory if missing.
6. Maintainer runs the checklist (already documented) to smoke-test the UI.

**Success criteria**
- Running the wizard from a clean repo produces a valid PHP config file identical in structure to hand-authored ones.
- Update mode preserves existing fields unless changed and warns before overwriting.
- Wizard prevents common mistakes (empty slug, DB path outside project, invalid URLs) and exits gracefully if validation fails.
- Optional selector update works so maintainers can immediately switch to the new cause.
- Docs mention this tool as the preferred setup path.
