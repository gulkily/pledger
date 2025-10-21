# Local Testing Guide (Ubuntu / WSL)

These steps walk through running and validating the pledge app locally on Ubuntu or Windows Subsystem for Linux.

## 1. Prerequisites
- PHP 8.x with the SQLite3 extension
- Git (already present in the repo)
- curl (optional but useful for API checks)
- A web browser on your host machine

### Install PHP + SQLite3
```bash
sudo apt update
sudo apt install php php-sqlite3
```
Verify the extension is available:
```bash
php -m | grep -i sqlite
# You should see sqlite3 listed
```

## 2. Clone or update the repository
If you haven’t cloned yet:
```bash
git clone https://github.com/gulkily/pledger.git
cd pledger
```
If already cloned, make sure you’re on the latest `main`:
```bash
cd /path/to/pledger
git checkout main
git pull --ff-only
```

## 3. Start PHP’s built-in web server
Run this from the project root:
```bash
php -S 127.0.0.1:8000
```
The server will serve files relative to the current directory, including `index.php` and `api.php`.

> Tip: keep this terminal open; it will log PHP warnings or errors.

## 4. Open the site in a browser
On Windows, open your regular browser (Edge/Chrome/etc.) and navigate to:
```
http://127.0.0.1:8000/index.php
```
You should see the updated pledge page with the new storytelling section, smaller percentage field, public name label, and private email field.

## 5. Test key scenarios
1. **Config load**: On page load, ensure the estimated flight cost and deadline display correctly.
2. **Pledge submission**:
   - Enter a name, valid email, and percentage (e.g., 25) and submit.
   - Confirm the success message appears and the pledge list updates.
   - Verify the email is *not* shown in the pledge list.
3. **Manage your pledge**:
   - Refresh the page; the “Manage your pledges” panel should display your commitment with edit/remove controls.
   - Change the percentage and name, save, and confirm the chart/list refresh with the new values.
   - Click *Remove* and ensure the pledge disappears. Check that localStorage stores `pledgerSessionToken` (in DevTools > Application).
4. **Invalid email**: Try submitting with an invalid email to see the validation error from the backend.
5. **Normalization**: Add multiple pledges so the raw total exceeds 100%; confirm the chart caps at 100% and the explanatory note appears.

## 6. Inspect the SQLite database (optional)
The app stores pledges in `pledges.db`. To inspect it:
```bash
sudo apt install sqlite3            # if not already installed
sqlite3 pledges.db "SELECT id, name, percentage, email FROM pledges;"
```
You should see the emails recorded alongside names and percentages.

## 7. Exercise the API directly (optional)
Use curl while the server is running:
- Fetch pledges:
  ```bash
  curl "http://127.0.0.1:8000/api.php?action=get_pledges"
  ```
- Submit a pledge:
  ```bash
  curl -X POST "http://127.0.0.1:8000/api.php?action=add_pledge" \
       -H "Content-Type: application/json" \
       -d '{"name":"Local Tester","email":"tester@example.com","percentage":15}'
  ```

## 8. Clean up (optional)
If you want a fresh database for repeated testing, delete `pledges.db` before restarting the server:
```bash
rm pledges.db
```
The API will recreate the schema automatically on first request.

---
You’re all set! Repeat steps 5–7 after making changes to verify everything works end-to-end.
