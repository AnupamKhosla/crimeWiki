## crimeWiki.in
### Full stack php cms based wikipedea scraping project designed and developed solely by Anupam Khosla 

Wikipedea of major criminals, criminal organizations and crime events. CMS based admin panel, which is capable of scraping 500 wikipedea pages in a few seconds.

- Official site: http://crimewiki.in 
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
- Environment variables (managed platforms): use platform‑provided env vars and skip manual DB entry in the UI.

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

**One-time server bootstrap**
Run this on your VM (as root or with sudo):

```
sudo bash /path/to/repo/ops/scripts/setup_server.sh \
  crimewiki.site admin@crimewiki.site /path/to/repo
```

Outputs:
- Webhook URL to add in GitHub/GitLab: `https://crimewiki.site/hooks/deploy`
- Header required: `X-Webhook-Secret: <value printed by script>`
- Secret is stored on VM in `/etc/webhook/secret.env`

**Update deploy automation only (no cert/NGINX changes)**
```
sudo bash /path/to/repo/ops/scripts/update_deploy.sh \
  crimewiki.site /path/to/repo
```

**Why `/etc` and `/usr/local/bin`**
- The webhook calls `/usr/local/bin/deploy.sh`, not the repo script, because it must be a stable entrypoint even while the repo is mid‑pull.
- `/etc` holds system configuration (Nginx, webhook), so we copy from `ops/` into `/etc` rather than running from the repo.
- This avoids partial updates, path drift, and deploy failures caused by running scripts directly from a repo that is actively changing.

**Deploy flow**
When the webhook fires, `/usr/local/bin/deploy.sh` will:
1) Switch Nginx to maintenance mode (503).
2) Optional: stop heavy services (set `STOP_SERVICES=1` in `/usr/local/bin/deploy.sh`).
3) `git pull --ff-only origin main`
4) Optional: start services again (if `STOP_SERVICES=1`).
5) Switch Nginx back to the app unless a failure occurs and `KEEP_MAINT_ON_ERROR=1`.

**Deploy script behavior knobs**
- `KEEP_MAINT_ON_ERROR=1` (default): if deploy fails, keep maintenance mode on.
- `KEEP_MAINT_ON_ERROR=0`: always switch back, even on errors.
- `PULL_USER=...`: run `git pull` as a specific user (defaults to the sudo user).
- `LOG_FILE=/var/log/deploy.log`: append deploy logs here.

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
- Many managed PHP platforms inject DB credentials via environment variables and expect apps to read them at runtime. Examples include Platform.sh / Upsun and Pantheon.
- In that model, you should avoid hard‑coding credentials and let the platform supply them.

**Meta:** php will automatically create category named `Blog` -- this is mandatory for homepage to show dynamic posts and about us section text. php will make two posts in the blog category, namely `$blog_month_post` and `$blog_about_text`. These two will be used to store about us data and monthly-post data.

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
