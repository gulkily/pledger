# Development Plan: Session-Based Pledge Editing

## Overview
Implement session-aware pledge management so supporters can modify or remove their own pledges, while persisting the session token in both cookies and localStorage for future feature reuse.

## Backend (PHP)
- **api.php**
  - Generate/validate `pledger_session` cookie (create helper functions).
  - Add schema migrations at runtime:
    - Ensure `sessions` table exists (`session_token TEXT PRIMARY KEY, created_at DATETIME`).
    - Add `session_token TEXT` column to `pledges` if missing.
  - Update `add_pledge` to capture the current session token and store it in `pledges.session_token`.
  - Extend `get_pledges` response to include `owned_by_session` flag per pledge (based on cookie token).
  - Add new actions:
    - `update_pledge`: validates session ownership, updates name/email/percentage.
    - `delete_pledge`: validates ownership, deletes pledge.
  - Optionally add helper `requireSession()` to encapsulate token creation/lookup and return the value so the frontend can mirror it in localStorage when needed.
- **config.php** — no changes expected unless admin needs token visibility.
- **Database**: consider a maintenance script to backfill session tokens (out of scope for first pass).

## Frontend (JS/HTML/CSS)
- **assets/js/app.js**
  - On load, read the server-provided token (via a new bootstrap endpoint or template injection) and persist it to `localStorage` (`pledgerSessionToken`).
  - Use `owned_by_session` data when rendering sponsor list; show edit/delete controls for owned pledges.
  - Add handlers for edit (prefill modal or inline form) and delete (confirmation dialog).
  - Create functions to call new API endpoints and refresh data on success; surface errors.
  - Manage UI state: disable submit during updates, display success toasts/messages.
- **templates/index.php**
  - Inject session token value into a JS bootstrapping snippet so the client can store it in localStorage.
  - Add markup for edit modal or inline editing interface (form fields, buttons).
  - Include placeholders for messages (success/error) and optional tooltips explaining that pledge management relies on this device’s browser storage.
- **assets/css/main.css**
  - Style edit/delete buttons, modal/inline form, and state messages.

## Copy / Documentation
- Update helper text in the form to mention that pledges can be edited later from the same browser (cookie + localStorage).
- Extend FAQ with a question about “How do I update or cancel my pledge?” and clarify what happens if storage is cleared.
- Add developer docs (README section or separate markdown) summarizing session behavior, cookie/localStorage usage, and limitations.

## Testing Checklist
1. Create pledge, ensure token is saved to localStorage, and verify edit/delete controls appear; modify percentage and confirm chart updates.
2. Delete pledge and confirm removal locally and via API.
3. Open new browser/incognito session to ensure controls are hidden and API rejects unauthorized edits/deletes; confirm localStorage starts empty.
4. Regression test add/config endpoints.

## Deployment Notes
- Deploy server changes before frontend update to avoid missing endpoints.
- Ensure production runs over HTTPS so session cookie can be marked `Secure`.
- Communicate to early supporters about the new manage-your-pledge feature, the “same browser” caveat, and how localStorage extends access.
