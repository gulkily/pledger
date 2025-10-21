# Plan: Session-Based Pledge Management

## Objective
Allow supporters to revisit the pledge page, identify their own pledges via a session token, and edit or remove them without exposing other supporters’ data. Store the token in both a secure cookie and localStorage for future personalized features.

## Current State
- Pledges are anonymous beyond the supplied name/email and are stored in SQLite without any session linkage.
- API offers `get_pledges` (read) and `add_pledge` (create) only; no endpoints for update or delete.
- Frontend displays pledges but offers no authenticated controls or persistent client-side state.

## Proposed Enhancements

### 1. Session Tracking
- Generate a cryptographically strong session token when a visitor first loads the form (e.g., UUID v4 via PHP’s `random_bytes`).
- Set it as an HTTP-only cookie (e.g., `pledger_session`) with a long expiration (90 days) and same-site protection.
- Persist the same token in `localStorage` (`pledgerSessionToken`) so future features can reuse it even if cookies are cleared but storage remains.
- Store the token server-side in a new table `sessions(session_token TEXT PRIMARY KEY, created_at DATETIME)`.
- When a pledge is created, persist the session token alongside the pledge (`pledges.session_token`), allowing multiple pledges per session if needed.

### 2. Database Changes
- Add `session_token TEXT` column to `pledges` (nullable for legacy data).
- Optionally add `updated_at` column for audit purposes.
- Consider a lightweight index on `session_token` to speed lookups.

### 3. API Extensions
- **Endpoint: `update_pledge`**
  - Accepts `pledge_id`, `name`, `email`, `percentage`.
  - Validates that the current request’s session token (from cookie) matches the pledge’s stored token; reject otherwise.
  - Applies same validation as `add_pledge` and updates the row.
- **Endpoint: `delete_pledge`**
  - Accepts `pledge_id`.
  - Validates session ownership.
  - Deletes the row (or marks it inactive if soft delete is preferred).
- **Session bootstrap**
  - Modify `get_pledges` to include a boolean `owned_by_session` flag when the pledge belongs to the caller’s session.
- Ensure all responses strip session tokens; they remain server-only.
- Consider an endpoint to regenerate session tokens if a supporter wants a fresh identity.

### 4. Frontend Enhancements
- On load, check for `pledgerSessionToken` in localStorage; if missing, read cookie value (if accessible via non-HTTP-only cookie or via an API that echoes it) and store it locally.
- When rendering the sponsor list, flag entries with `owned_by_session` and surface edit/delete controls next to them.
- Implement modals or inline forms for updating percentage/name/email (reusing the existing validation logic client-side).
- On delete, show a confirmation dialog before calling the API; upon success, refresh pledges and show a toast.
- If no session-owned pledges exist, hide the controls.
- Surface messaging that explains pledges made on this device can be managed later and that clearing cookies/localStorage removes quick access.

### 5. Edge Cases & Security
- Handle legacy pledges without session tokens: they remain read-only (no delete/edit) unless migrated manually.
- Rate-limit update/delete calls per session to avoid abuse.
- Sanitize input to prevent XSS via names/emails (already in place for creation).
- Use CSRF protection (session token doubles as authenticator if cookies are same-site; otherwise include a secondary token).
- Consider allowing “claiming” a pledge via email link if the supporter clears cookies/localStorage.
- Document that localStorage is not secure for sensitive data; token is still validated server-side.

## Testing Strategy
- **Unit/Integration**: Add automated tests for new endpoints covering valid edit/delete, mismatched session token, missing pledge, and input validation.
- **Manual**:
  1. Create a pledge, refresh the page, confirm localStorage contains the session token and edit/delete buttons appear only for your pledge.
  2. Modify pledge fields and verify list updates and database reflects changes.
  3. Delete pledge; ensure it disappears and chart updates.
  4. Attempt to edit/delete from a new incognito session; confirm API rejects the action and localStorage is empty.
- **Regression**: Ensure existing pledge creation and config editing still function.

## Deployment Considerations
- Run a database migration adding `session_token` to `pledges` and creating the `sessions` table before deploying code.
- Verify cookie headers on production (https vs http) to avoid mixed-content issues; consider using `Secure` flag if served over TLS.
- Communicate to existing supporters that edits require the same browser/storage; provide manual contact path otherwise.

## Next Steps
1. Review plan and pick desired authentication UX (session-only vs optional email reclaim).
2. Implement DB migrations and API changes.
3. Build frontend controls, including localStorage handling.
4. Test thoroughly in staging before rolling out.
