# Automatic deployment: GitHub → Hostinger (over SSH)

Every push to `main` triggers `.github/workflows/deploy.yml`, which:

1. Installs Composer dependencies (`--no-dev`) on the GitHub runner.
2. Builds the Vite/Tailwind assets (`npm run build`).
3. `rsync`s the finished project to Hostinger over SSH (incremental).
4. Runs `php artisan migrate --force` and rebuilds config/route/view caches.

Fully automatic — after the one-time setup below, you just `git push`.

---

## One-time setup

### 1. Add the deploy key to Hostinger

A dedicated keypair was generated for CI. Add the **public** key in
hPanel → **Advanced → SSH Access → Add SSH key** (paste the `ssh-ed25519 …`
line).

### 2. Add GitHub secrets

Repo → **Settings → Secrets and variables → Actions → New repository secret**:

| Secret name       | Value                                                            |
|-------------------|-----------------------------------------------------------------|
| `SSH_HOST`        | `46.202.182.229`                                                |
| `SSH_PORT`        | `65002`                                                         |
| `SSH_USERNAME`    | `u900210542`                                                    |
| `SSH_PRIVATE_KEY` | the full `-----BEGIN OPENSSH PRIVATE KEY-----` block            |
| `DEPLOY_PATH`     | absolute path to the app folder, e.g. `/home/u900210542/domains/inboxflight.dlnwebstudio.com/laravel` |

Find the exact `DEPLOY_PATH` by SSHing in once and running `pwd` inside the
site's folder:

```bash
ssh -p 65002 u900210542@46.202.182.229
ls domains/                      # find the site folder
# choose a private app dir alongside its public_html, e.g. .../laravel
```

### 3. Point the document root at `laravel/public`

The app deploys to `DEPLOY_PATH` (e.g. `.../laravel`). Set the subdomain's
document root to that folder's **`/public`** so `.env`, `vendor/`, and app code
stay out of the web root:

hPanel → **Websites → (inboxflight…) → Advanced → Change site's root
directory** → `.../laravel/public`.

### 4. Create `.env` on the server (once, by hand)

`.env` is intentionally **never** uploaded (see `.deployignore`). Create
`DEPLOY_PATH/.env` from `.env.example` and set:

- `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://inboxflight.dlnwebstudio.com`
- `APP_KEY=` → generate locally with `php artisan key:generate --show` and paste
  the `base64:...` value. **Keep it stable** — it decrypts stored SMTP passwords.
- `DB_*` → the MySQL database from hPanel → Databases.
- `MAIL_*` → your system SMTP.

### 5. Push

```bash
git add .github/workflows/deploy.yml .deployignore docs/auto-deploy-hostinger.md
git commit -m "Add automatic Hostinger deployment via GitHub Actions (SSH)"
git push
```

Watch the run under the repo's **Actions** tab. The first deploy uploads
everything (including `vendor/`) so it takes a few minutes; later deploys sync
only what changed and run migrations automatically.

### 6. First admin account

After the first successful deploy, SSH in once:

```bash
ssh -p 65002 u900210542@46.202.182.229
cd <DEPLOY_PATH>
php artisan inboxpilot:create-admin
```

---

## Notes & gotchas

- **PHP CLI version.** The `php` used over SSH must be 8.2+. If `php -v` shows an
  older version, set the default in hPanel → Advanced → PHP Configuration, or
  use the versioned binary (e.g. `/usr/bin/php8.2`) — tell me and I'll pin it in
  the workflow.
- **`rsync --delete`** removes server files that no longer exist in the repo, so
  the server mirrors `main`. `.env` and `storage/` are excluded, so your
  environment file, logs, and uploads are never touched.
- **Manual deploy.** Trigger without a code change from Actions → Deploy to
  Hostinger → **Run workflow** (the `workflow_dispatch` hook).
- **Rotate the key** anytime by regenerating the pair, updating the Hostinger
  key and the `SSH_PRIVATE_KEY` secret.
