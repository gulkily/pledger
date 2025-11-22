# LLM Cause Prompt Template

Use this when you want an LLM coding agent to prepare the copy/structure for a new campaign before running the CLI wizard.

## Instructions to give the agent
1. Read this repo context: we need a PHP array shaped like `config/causes/example-cause.php`.
2. Fill in these fields:
   - `slug` (kebab-case)
   - `display_name`
   - `db_path` suggestion (e.g., `./data/<slug>.db`)
   - `price_range.min`, `price_range.max`, `price_range.description`
   - `deadline` (`YYYY-MM-DD`)
   - `goal_banner`
   - `hero.headline`, `hero.tagline`, `hero.subtext`, `hero.avatar` (src, alt, link)
   - `story.why_it_matters[]` (3-5 paragraphs)
   - `story.explore.heading` + `story.explore.items[]`
   - `research_projects[]` (title, tags, description for 2-4 highlights)
3. Output JSON with the keys above so I can paste values into the wizard.
4. Keep copy concise but evocative; max ~3 sentences per paragraph.

## Example prompt
```
You are helping configure a pledge campaign for <cause>. Produce JSON matching this shape:
{
  "slug": "",
  "display_name": "",
  "db_path": "./data/<slug>.db",
  "price_range": {"min": 0, "max": 0, "description": ""},
  "deadline": "YYYY-MM-DD",
  "goal_banner": "",
  "hero": {
    "headline": "",
    "tagline": "",
    "subtext": "",
    "avatar": {"src": "image/<something>.png", "alt": "", "link": "https://"}
  },
  "story": {
    "why_it_matters": ["para", "para", "para"],
    "explore": {"heading": "", "items": ["", "", ""]}
  },
  "research_projects": [
    {"title": "", "tags": ["#"], "description": ""}
  ]
}
Focus on realistic copy, respect newline/quote escaping, and omit trailing commentary.
```

Paste the result into the wizard when prompted.
