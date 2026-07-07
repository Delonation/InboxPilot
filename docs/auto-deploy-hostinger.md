# Automatic deployment: GitHub → Hostinger (no SSH required)

Every push to `main` triggers `.github/workflows/deploy.yml`, which:

1. Installs Composer dependencies (`--no-dev`) on the GitHub runner.
2. Builds the Vite/Tailwind assets (`npm run build`).
3. Uploads the finished project to Hostinger over **FTPS** (incremental — only
   changed files after the first run).

Your Hostinger plan needs **no** Composer, Node, or SSH. You only do the
one-time setup below.

---

## One-time setup

### 1. Create an FTP account in hPanel

hPanel → **Files → FTP Accounts** → create an account (or use the existing one).
Note the **FTP hostname**, **username**, and **password**. Hostinger FTPS uses
port 21 with TLS (the action handles this via `protocol: ftps`).

### 2. Decide where the app lives, and set the document root

Upload the app into a folder such as `laravel/` in your account, and point the
domain's **document root** at `laravel/public` (Laravel's real web root — this
keeps `.env`, `vendor/`, and app code out of the public web).

- hPanel → **Websites → (your site) → Dashboard → Advanced → Change site's root
  directory** (wording varies) → set it to `laravel/public`.
- If your plan can't change the document root, use the fallback in the last
  section.

### 3. Add the FTP credentials as GitHub secrets

In GitHub: **Settings → Secrets and variables → Actions → New repository secret**.
Add these four:

| Secret name      | Value                                                    |
|------------------|----------------------------------------------------------|
| `FTP_SERVER`     | Your FTP hostname (e.g. `ftp.your-domain.com` or the IP) |
| `FTP_USERNAME`   | The FTP account username                                 |
| `FTP_PASSWORD`   | The FTP account password                                 |
| `FTP_SERVER_DIR` | Deploy target path, e.g. `/laravel/` (trailing slash)    |

> `FTP_SERVER_DIR` is the path the FTP user sees. On Hostinger the main FTP
> account usually lands in the home dir, so `/laravel/` (a sibling of
> `public_html`) is typical. If your FTP account is jailed to `public_html`,
> use `/laravel/` under it and set the doc root accordingly.

### 4. Create `.env` on the server (once, by hand)

`.env` is intentionally **never** uploaded. Via hPanel File Manager, create
`laravel/.env` from `.env.example` and fill in:

- `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain.com`
- `APP_KEY=` → generate one locally with `php artisan key:generate --show` and
  paste the `base64:...` value. **Keep this key stable** — it decrypts stored
  SMTP passwords.
- `DB_*` → the MySQL database you created in hPanel.
- `MAIL_*` → your system SMTP.

### 5. First database migration

FTP can't run `php artisan migrate`. Pick one:

- **phpMyAdmin (no SSH):** export your local schema
  (`php artisan schema:dump` or a `mysqldump` of structure) and import it via
  hPanel → Databases → phpMyAdmin.
- **SSH (if your plan has it — Premium/Business do):**
  `cd laravel && php artisan migrate --force`. Far simpler for future schema
  changes; worth enabling under hPanel → Advanced → SSH Access.

### 6. First admin account

Same story — needs a CLI. With SSH:
`php artisan inboxpilot:create-admin`. Without SSH, insert an admin row via
phpMyAdmin (role = `admin`, status = `approved`, `password` = a bcrypt hash).

---

## After setup

Just work locally and `git push`. Watch the run under the repo's **Actions**
tab. First deploy uploads everything (including `vendor/`, so it's slow — a few
minutes); later deploys upload only what changed.

Trigger a deploy without a code change from **Actions → Deploy to Hostinger →
Run workflow** (that's the `workflow_dispatch` hook).

---

## Fallback if you cannot change the document root

If the domain must serve from `public_html`, deploy the app to a private
`laravel/` folder (as above) and put a tiny `public_html/index.php` that boots
it, plus copy Laravel's `public/.htaccess` into `public_html`. The
`public_html/index.php` should require:

```php
require __DIR__.'/../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';
```

and copy the built `public/build` into `public_html/build`. Ask and this can be
scripted into the workflow as a second FTP step.
