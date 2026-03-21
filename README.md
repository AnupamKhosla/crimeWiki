## crimeWiki.site
### Full stack php cms based wikipedea scraping project designed and developed solely by Anupam Khosla 

Wikipedea of major criminals, criminal organizations and crime events. CMS based admin panel, which is capable of scraping 500 wikipedea pages in a few seconds.

- Official site: https://crimewiki.site
- Find me at https://www.linkedin.com/in/anupamkhosla/

Admin Panel looks like :  

- Login page: https://anupamkhosla.github.io/crimeWiki/assets/img/login.png  
- Dashboard page: https://anupamkhosla.github.io/crimeWiki/assets/img/dashboard.png  
- Posts search: https://anupamkhosla.github.io/crimeWiki/assets/img/posts.png  
- Addpost page: https://anupamkhosla.github.io/crimeWiki/assets/img/addpost.png  
- Categories page: https://anupamkhosla.github.io/crimeWiki/assets/img/categories.png 
- Wikipedea page: https://anupamkhosla.github.io/crimeWiki/assets/img/wikipedea.png


------------------

#### How to install on your server or local machine

**Step 1:** Download this git repository.

**Step 2:** Decide how your database credentials are provided.
- Config file (shared hosting, most VPS setups): copy `include/config.example.php` to `include/config.php` and fill in DB values.
- Browser setup flow (this repo's default path): visit `login.php`, enter DB credentials in the setup form, and the app will generate `include/config.php` for you.
- Important: this repo's normal PHP runtime reads `include/config.php`; it does not currently load DB credentials from `.env` automatically.

```
cp include/config.example.php include/config.php
```

**Step 3:** Open `login.php` in your browser. The setup form will:
- Connect using the DB user/password you enter.
- Create the database if it doesn’t exist (and then write `include/config.php` with `SETUP = true`).
- Create tables and the first admin account after you complete the registration form.

Use a DB user that has permission to create databases/tables, or pre‑create the DB and grant it privileges.

**Step 4:** 

- Go to yourdomain/categories.php page first and create a category named `Criminals`.  Homepage will show `Criminals` category by default.  
- Add a minimum of one post through yourdomain/wikipedea.php or yourdomain/addpost.php. 
- Go to yourdomain/dashboard.php and copy `title` of that post.
- Paste the title into `Crime of the month post`.
- Set the `About The CrimeWiki text`. 

Go to yourdomain and the website will work now.

-------------------

#### Environment setup (choose one)

**Shared hosting (cPanel/DirectAdmin/etc.)**
- Upload files to `public_html` (or the web root).
- Create a MySQL database + user in the hosting panel.
- Copy `include/config.example.php` → `include/config.php` and fill in DB values.
- Visit `/login.php` to finish setup (creates tables + admin).

**VPS / bare‑metal (manual PHP + Apache/Nginx)**
- Ensure PHP, MySQL, and required extensions are installed (`mysqli`, `mbstring`, `dom`, `curl`).
- Configure your web server to point to the project root.
- Copy `include/config.example.php` → `include/config.php` and fill in DB values.
- Visit `/login.php` to finish setup.

**Docker (local or VM)**
- Build and start containers: `docker compose up -d`
- Visit `http://localhost/login.php` (or your VM IP) to run setup.
- For DB import, uncomment the seed line in `docker-compose.yml` and start with an empty DB volume.

**Reverse proxy + HTTPS + webhook deploy (VPS/VM)**
- This repo includes a lightweight ops bundle under `ops/` for:
  - Nginx reverse proxy (HTTP->HTTPS)
  - Let's Encrypt certs (non-interactive)
  - maintenance mode during deploys
  - webhook-triggered deploys on low-memory VMs
- Prereqs:
  - DNS A record for `crimewiki.site` (and `www`) pointing to the VM
  - Port 80/443 open in firewall
  - Docker app moved to host port 8080 (already set in `docker-compose.yml`)

**Recommended VPS/VM workflow**
1. Clone this repo onto the VM at the path you want to keep using long-term.
2. Prepare Docker/database env vars on the VM if your Compose stack depends on a local `.env`.
3. Run `ops/scripts/setup_server.sh` once to install Nginx, Certbot, the webhook listener, deploy/start scripts, and systemd services.
4. Complete the app setup in the browser so `include/config.php` is generated, or create `include/config.php` manually if you prefer.
5. Add the printed webhook URL and secret to GitHub.
6. From then on, normal code updates should be done by `git push`, which triggers the webhook deploy on the VM.
7. If the VM was down during a push, boot recovery will do a best-effort `git pull` and then start the stack from the latest available checkout.

**What to run on a brand new VM**
- Clone the repo:
```
git clone https://github.com/AnupamKhosla/crimeWiki.git
cd crimeWiki
```
- Run the one-time bootstrap:
```
sudo bash ops/scripts/setup_server.sh crimewiki.site admin@crimewiki.site "$(pwd)"
```
- Optional but recommended on very small VMs:
```
sudo cp scripts/setup_swap.service /etc/systemd/system/setup_swap.service
sudo systemctl daemon-reload
sudo systemctl enable --now setup_swap.service
```
- Finish the app setup by visiting `/login.php`.

**How production stays in sync with Git pushes**
- Normal case: `git push` -> GitHub webhook -> VM `/hooks/deploy` -> `/usr/local/bin/deploy.sh` -> best-effort `git pull` -> copied VM files refreshed -> app/webhook services ensured -> site switched live.
- If GitHub is reachable but `git pull` fails, deploy logs the failure and serves the last working local checkout instead of leaving the site down.
- If the VM is down when you push, GitHub cannot deliver the webhook event. When the VM later boots, `crimewiki-start.sh` does a best-effort `git pull` and then runs `docker compose up -d`.
- A reboot can catch the VM up to the latest repo checkout, but only a successful deploy path refreshes the copied files under `/etc` and `/usr/local/bin`.

**When you still need SSH**
- First-time bootstrap of a new VM.
- Repairing a VM when Nginx/webhook are too broken for GitHub webhooks to reach `/hooks/deploy`.
- Inspecting logs:
```
sudo tail -n 200 /var/log/deploy.log
sudo tail -n 200 /var/log/crimewiki-start.log
sudo journalctl -u nginx -u webhook -u crimewiki-app --no-pager -n 200
```

**One-time server bootstrap**
Run this on your VM (as root or with sudo):

```
sudo bash /path/to/repo/ops/scripts/setup_server.sh \
  crimewiki.site admin@crimewiki.site /path/to/repo
```

Outputs:
- Webhook URL to add in GitHub/GitLab: `https://crimewiki.site/hooks/deploy`
- GitHub Webhook Secret: `<value printed by script>` (uses `X-Hub-Signature-256`)
- Secrets are stored on VM in `/etc/secrets/secrets.env`
- Environment file is stored on VM in `/etc/crimewiki.env`
- The script also installs `/usr/local/bin/deploy.sh`, `/usr/local/bin/crimewiki-start.sh`, and the systemd units for `webhook` and `crimewiki-app`

**VM environment file**
- Deploy scripts load `/etc/crimewiki.env` for `DOMAIN` and `REPO_DIR`.
- The repo contains `ops/env/crimewiki.env` and deploys copy it to `/etc/crimewiki.env`.
- If you want to change domain or repo path, edit `ops/env/crimewiki.env`, push, and deploy.
```
DOMAIN=crimewiki.site
REPO_DIR=/home/anupamkhosla1993/crimeWiki
```

**If the domain changes**
- Update `/etc/crimewiki.env` with the new domain and reload Nginx:
```
sudo nano /etc/crimewiki.env
sudo nginx -t && sudo systemctl reload nginx
```

**Why `/etc` and `/usr/local/bin`**
- The webhook calls `/usr/local/bin/deploy.sh`, not the repo script, because it must be a stable entrypoint even while the repo is mid‑pull.
- `/etc` holds system configuration (Nginx, webhook), so we copy from `ops/` into `/etc` rather than running from the repo.
- This avoids partial updates, path drift, and deploy failures caused by running scripts directly from a repo that is actively changing.

**Config and secret ownership**
- `include/config.php` is app/database config. It is git-ignored and is normally created by the browser setup flow in `login.php` / `include/setup.php`.
- `/etc/crimewiki.env` is deploy-managed VM config. It currently contains only `DOMAIN` and `REPO_DIR` and is intentionally overwritten from `ops/env/crimewiki.env` during deploys.
- `/etc/secrets/secrets.env` holds live VM secrets such as `WEBHOOK_SECRET` and `PROXY_SECRET_TOKEN`. It is created by `setup_server.sh` / `crimewiki-ensure-secrets.sh` and reused on later deploys.
- `/etc/webhook/hooks.yml.template` is copied from `ops/webhook/hooks.yml`. At service start, `webhook.service` renders the live runtime config at `/run/crimewiki-hooks.yml` using `WEBHOOK_SECRET` from `/etc/secrets/secrets.env`.
- Docker Compose may also rely on a local `.env` file for `DB_NAME`, `DB_USER`, `DB_PASS`, and `DB_ROOT_PASS`. That file is git-ignored and VM-specific.

**Files copied into the VM by setup/deploy**
- `ops/env/crimewiki.env` -> `/etc/crimewiki.env`
- `ops/nginx/crimewiki.conf` -> `/etc/nginx/sites-available/crimewiki.conf`
- `ops/nginx/crimewiki_maintenance.conf` -> `/etc/nginx/sites-available/crimewiki_maintenance.conf`
- `ops/maintenance/index.html` -> `/var/www/maintenance/index.html`
- `ops/systemd/webhook.service` -> `/etc/systemd/system/webhook.service`
- `ops/systemd/crimewiki-app.service` -> `/etc/systemd/system/crimewiki-app.service`
- `ops/scripts/start_stack.sh` -> `/usr/local/bin/crimewiki-start.sh`
- `ops/scripts/deploy.sh` -> `/usr/local/bin/deploy.sh`
- `ops/scripts/ensure_secrets.sh` -> `/usr/local/bin/crimewiki-ensure-secrets.sh`

**Deploy flow**
When the webhook fires, `/usr/local/bin/deploy.sh` will:
1) Ensure Nginx is running. If it is down, try `systemctl start nginx`.
2) Switch Nginx to maintenance mode (503).
3) Optional: stop heavy services (set `STOP_SERVICES=1` in `/usr/local/bin/deploy.sh`).
4) Best-effort `git pull --ff-only origin main`. If pull fails, the script logs a warning and continues with the existing local checkout instead of leaving the site down.
5) Copy deploy-managed templates from the repo into `/etc/...` and `/usr/local/bin/...`.
6) Refresh the webhook template/service files. The running `webhook.service` renders its live runtime config from `ops/webhook/hooks.yml` using `WEBHOOK_SECRET` from `/etc/secrets/secrets.env`.
7) If the repo contains a newer `deploy.sh`, install it to `/usr/local/bin/deploy.sh` and re-exec once with `SKIP_PULL=1`.
8) `systemctl enable webhook crimewiki-app`, then start them if needed.
9) Switch Nginx back to the live app config.

**Deploy logging**
- Deploy logs go to `/var/log/deploy.log`.
- Service-level logs can also be inspected with:
```
sudo journalctl -u nginx -u webhook -u crimewiki-app --no-pager -n 200
```

**Automatic recovery after VM reboot**
- The Docker services now use `restart: unless-stopped`, so once started they are allowed to come back with the Docker daemon.
- The VM bootstrap installs a systemd unit named `crimewiki-app.service` that runs `/usr/local/bin/crimewiki-start.sh` on boot.
- `crimewiki-start.sh` best-effort pulls latest code and then runs `docker compose up -d`. If Git is unavailable, it still starts the stack from the local checkout.
- Boot/start logs go to `/var/log/crimewiki-start.log`.
- `webhook.service` is also enabled during bootstrap, so the webhook listener should come back on reboot if the VM systemd state is intact.
- If the VM was down during a Git push, the GitHub webhook event is missed. The boot-time best-effort `git pull` is what allows the VM to catch up when it comes back.
- To verify on the VM:
```
sudo systemctl status crimewiki-app --no-pager
sudo systemctl status webhook --no-pager
sudo systemctl status nginx --no-pager
sudo docker ps
```
- To enable it manually on an existing VM before the next reboot:
```
sudo cp /path/to/repo/ops/scripts/start_stack.sh /usr/local/bin/crimewiki-start.sh
sudo chmod +x /usr/local/bin/crimewiki-start.sh
sudo cp /path/to/repo/ops/systemd/crimewiki-app.service /etc/systemd/system/crimewiki-app.service
sudo systemctl daemon-reload
sudo systemctl enable --now crimewiki-app
```

**Deploy script behavior knobs**
- `KEEP_MAINT_ON_ERROR=1` (default): if deploy fails, keep maintenance mode on.
- `KEEP_MAINT_ON_ERROR=0`: always switch back, even on errors.
- `PULL_USER=...`: run `git pull` as a specific user (defaults to the sudo user).
- `LOG_FILE=/var/log/deploy.log`: append deploy logs here.
- `STOP_SERVICES=1`: stop MySQL / containers during deploy for extra RAM headroom on tiny VMs.

**Server start helper (pull + swap + docker)**
- Run on the VM when you want to update and start services:
  - `bash scripts/server_start.sh`
- This script:
  - pulls latest code
  - ensures swap is enabled
  - starts Docker containers (without wiping DB volumes)

**Low‑memory VM swap (recommended for e2‑micro / 1 GB RAM)**
- Manual method (not needed if you use `scripts/server_start.sh`).
- Create swap (VPS/VM only; not possible on shared hosting):
  - `sudo ./scripts/setup_swap.sh 8G`
- To run automatically on boot (recommended on GCP):
  1. Copy the systemd unit file:
     - `sudo cp scripts/setup_swap.service /etc/systemd/system/setup_swap.service`
  2. Edit the `ExecStart` path in the unit if your repo path is different.
  3. Enable and start it:
     - `sudo systemctl daemon-reload`
     - `sudo systemctl enable --now setup_swap.service`


**Managed platforms (env‑var based)**
- Many managed PHP platforms inject DB credentials via environment variables and expect apps to read them at runtime.
- This repo does not currently read DB credentials from environment variables in its default PHP code path, so managed platforms usually still need a platform-specific adaptation or a generated `include/config.php`.

**Meta:** php will automatically create category named `Blog` -- this is mandatory for homepage to show dynamic posts and about us section text. php will make two posts in the blog category, namely `$blog_month_post` and `$blog_about_text`. These two will be used to store about us data and monthly-post data.

**Footer note:** the homepage category filter includes `Blog`, but the footer category lists intentionally exclude `Blog`.

htaccess rewrites being used:  

```

<IfModule mod_rewrite.c>
    Options -MultiViews
    RewriteEngine On
    RewriteRule ^sitemap/sitemap-index.xml sitemap/sitemap-index.php    [QSA,B]
    RewriteRule ^sitemap/sitemap(\d+).txt sitemap/sitemap.php?page=$1   [QSA,B]
    RewriteRule ^post/(\d+$) post.php?id=$1                             [QSA,B]
    RewriteRule ^post/([^/]+)/(\d+) post.php?title=$1&repeat=$2         [QSA,B]
    RewriteRule ^post/([^/]*) post.php?title=$1                         [QSA,B]
 </IfModule>
  
 <IfModule mod_rewrite.c>
   # RewriteEngine On
   # RewriteRule ^post/(\d+(/|$)).* post.php?id=$1
   # RewriteRule ^post/(?!\d+($|/))([^/\n\r]+)($|/)(\d+)? post.php?title=$2&repeat=$4 
   # Very important regexes created for post.php page
 </IfModule>

 <IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME}.php -f
    RewriteRule (.*) $1.php [L]
 </IfModule>

```

Note June 2025: Apache rewrite was failing on URLs with spaces and special characters (e.g., %20, ', &) due to unescaped backreferences. Added [QSA,B] flags to .htaccess RewriteRule to ensure proper URL escaping. Error logged as: AH10411: Rewritten query string contains control characters or spaces.
Ref: Apache mod_rewrite Flags documentation – B (escape backreferences)

`search-code.php` file has beed modified to change `urlencode` function to be changed into `rawurlencode` to ensure proper escaping with [B].

-------------------
