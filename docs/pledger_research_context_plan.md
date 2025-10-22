# Plan: Integrate Research Context into Pledger Page

## Goals
- Surface your ongoing research work (PrayerLift, Pollyanna, ARKOS contribution, Prayer Record Text Format) so sponsors understand the broader impact.
- Keep language inclusive for both faith-aligned supporters and secular allies.
- Maintain the current page’s minimal, trust-based tone while offering opt-in depth.

## Proposed Updates

### 1. Story Card Enhancements
- Add a short “What I’m exploring” section in the main story card (e.g., three bullets) summarizing:
  - Consent-first community infrastructure (Pollyanna).
  - Support tooling for mutual-aid spaces (PrayerLift/ThyWill, Prayer Record Format).
  - Responsible AI assistants (PrayerLift, ARKOS contribution).
- Use inclusive language (“faith communities are one testbed” instead of overt religious framing).

### 2. Research Spotlight Block
- Insert a slim, GitHub-esque card below the story card titled “Current Experiments” or “Active Research Threads”.
- Include 3–4 bullet lines (repo name + one-line description). Example:
  - “Pollyanna – consent-centered framework for resilient online communities.”
  - “PrayerLift – respectful AI-generated responses for mutual support networks.”
  - “Prayer Record Text Format – portable spec for tracking care/prayer interactions.”
  - “ARKOS (contributor) – open-source interface for locally-run memory-augmented AI.”
- Optional: add small tags like “#consent-first”, “#ai-assistive”, “#community-research”.

### 3. FAQ Additions
- Add a question: “Is this only relevant to faith communities?”
  - Answer: emphasize that the research methods generalize to any community seeking humane, user-controlled interaction.
- Optionally refine the existing anchor-sponsor FAQ to mention collaborative experimentation.

### 4. Optional Footer Link-Outs
- At the bottom (before FAQ or in the manage section), add “Want to explore the projects?” with neutral links to key repos.
- Stress that everything is open-source experimentation so sponsors can inspect or contribute.

## Accessibility & Tone
- Ensure new content uses existing theme variables for light/dark mode compatibility.
- Keep paragraphs short and scannable; rely on bullets and tags for quick comprehension.
- Maintain the trust-based pledge copy (manual payments, no auto-charge) so the page doesn’t feel transactional.

## Implementation Steps
1. Update `templates/index.php` story card and add the research spotlight markup.
2. Extend FAQ with cross-community applicability and verify anchor-sponsor wording.
3. Style the new block in `assets/css/main.css` (borrowing `.story-card` foundation).
4. Optionally add iconography/emoji to tags for approachability.
5. Test in light/dark themes to ensure contrast.
