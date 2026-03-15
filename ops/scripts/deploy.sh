#!/usr/bin/env bash
#
# This deploy script is triggered by the webhook and is intentionally simple.
# It flips Nginx into maintenance mode, pulls the latest code, and then returns
# traffic to the live site. On small VMs, deployments can collide with traffic,
# so the maintenance switch ensures no half-rendered PHP or DB work runs while
# files are changing. The script supports a lock to prevent concurrent deploys,
# optional service stops to free memory, and a conservative default to keep the
# site in maintenance mode if hard failures occur. It also logs to
# /var/log/deploy.log so you can see exactly what happened in SSH. Git pull
# failures are treated as warnings: the deploy continues with the last working
# local checkout so a temporary GitHub/network issue does not leave the site in
# maintenance mode. The git pull runs as the sudo user by default to avoid
# ownership issues, but you can set PULL_USER if you want a specific account.
# Keep this file stable and small: it is the safety rail between your public
# site and the VM under deployment pressure.
# It reads DOMAIN and REPO_DIR from /etc/crimewiki.env.
#
set -euo pipefail

# Load VM environment (domain + repo path)
if [ -f /etc/crimewiki.env ]; then
  # shellcheck disable=SC1091
  . /etc/crimewiki.env
else
  echo "ERROR: /etc/crimewiki.env not found. Copy ops/env/crimewiki.env to /etc/crimewiki.env." >&2
  exit 1
fi

if [ -z "${DOMAIN:-}" ] || [ -z "${REPO_DIR:-}" ]; then
  echo "ERROR: DOMAIN or REPO_DIR missing in /etc/crimewiki.env" >&2
  exit 1
fi

DOMAIN_WWW="www.${DOMAIN}"

# Set to 1 if you want to stop services during deploys (more RAM headroom)
STOP_SERVICES="${STOP_SERVICES:-0}"
# Set to 1 to keep maintenance page active when deploy fails
KEEP_MAINT_ON_ERROR="${KEEP_MAINT_ON_ERROR:-1}"
# User to run git pull as (defaults to the sudo user)
PULL_USER="${PULL_USER:-${SUDO_USER:-root}}"
# Log file for deploy output
LOG_FILE="${LOG_FILE:-/var/log/deploy.log}"
GIT_PULL_FAILED=0

# Single-run lock to avoid overlapping deploys
LOCK_FILE="/tmp/deploy.lock"
if [ -z "${SKIP_LOCK:-}" ]; then
  exec 9>"$LOCK_FILE"
  if ! flock -n 9; then
    echo "Deploy already running" >&2
    exit 1
  fi
fi

touch "$LOG_FILE"
exec > >(tee -a "$LOG_FILE") 2>&1

compose_cmd() {
  if command -v docker-compose >/dev/null 2>&1; then
    echo "docker-compose"
    return
  fi
  if command -v docker >/dev/null 2>&1; then
    if docker compose version >/dev/null 2>&1; then
      echo "docker compose"
      return
    fi
  fi
  echo ""
}

COMPOSE_CMD="$(compose_cmd)"

ensure_nginx_running() {
  if systemctl is-active --quiet nginx; then
    return
  fi

  echo "WARN: nginx is not running; attempting to start it."
  systemctl start nginx

  if ! systemctl is-active --quiet nginx; then
    echo "ERROR: nginx failed to start." >&2
    return 1
  fi
}

on_exit() {
  status="$?"
  if [ "$status" -ne 0 ]; then
    echo "Deploy failed with status $status"
    if [ "$KEEP_MAINT_ON_ERROR" = "1" ]; then
      exit "$status"
    fi
  fi
  ensure_nginx_running
  ln -sf /etc/nginx/sites-available/crimewiki.conf /etc/nginx/sites-enabled/crimewiki.conf
  systemctl reload nginx
  exit "$status"
}
trap on_exit EXIT

ensure_nginx_running

# Switch to maintenance mode
ln -sf /etc/nginx/sites-available/crimewiki_maintenance.conf /etc/nginx/sites-enabled/crimewiki.conf
ensure_nginx_running
systemctl reload nginx

if [ "$STOP_SERVICES" = "1" ]; then
  if systemctl is-active --quiet mysql; then
    systemctl stop mysql
  fi

  if [ -n "$COMPOSE_CMD" ] && [ -f "$REPO_DIR/docker-compose.yml" ]; then
    $COMPOSE_CMD -f "$REPO_DIR/docker-compose.yml" stop || true
  fi
fi

# Pull code (skip on self-reexec)
if [ -z "${SKIP_PULL:-}" ]; then
  if [ "$PULL_USER" = "root" ]; then
    cd "$REPO_DIR"
    if ! git pull --ff-only origin main; then
      GIT_PULL_FAILED=1
      echo "WARN: git pull failed; continuing with the existing local checkout."
    fi
  else
    if ! su -s /bin/bash "$PULL_USER" -c "cd \"$REPO_DIR\" && git pull --ff-only origin main"; then
      GIT_PULL_FAILED=1
      echo "WARN: git pull failed for user $PULL_USER; continuing with the existing local checkout."
    fi
  fi
fi

# Sync ops files from repo to VM locations (no reloads/restarts here)
mkdir -p /etc/nginx/sites-available /etc/webhook /var/www/maintenance /etc/systemd/system
cp -f "$REPO_DIR/ops/env/crimewiki.env" /etc/crimewiki.env
sed \
  -e "s#__DOMAIN__#${DOMAIN}#g" \
  -e "s#__DOMAIN_WWW__#${DOMAIN_WWW}#g" \
  "$REPO_DIR/ops/nginx/crimewiki.conf" \
  > /etc/nginx/sites-available/crimewiki.conf
sed \
  -e "s#__DOMAIN__#${DOMAIN}#g" \
  -e "s#__DOMAIN_WWW__#${DOMAIN_WWW}#g" \
  "$REPO_DIR/ops/nginx/crimewiki_maintenance.conf" \
  > /etc/nginx/sites-available/crimewiki_maintenance.conf
cp -f "$REPO_DIR/ops/maintenance/index.html" /var/www/maintenance/index.html
cp -f "$REPO_DIR/ops/systemd/webhook.service" /etc/systemd/system/webhook.service
cp -f "$REPO_DIR/ops/systemd/crimewiki-app.service" /etc/systemd/system/crimewiki-app.service
cp -f "$REPO_DIR/ops/scripts/start_stack.sh" /usr/local/bin/crimewiki-start.sh
chmod +x /usr/local/bin/crimewiki-start.sh

if [ -f /etc/webhook/secret.env ]; then
  # shellcheck disable=SC1091
  . /etc/webhook/secret.env
fi
if [ -n "${WEBHOOK_SECRET:-}" ] && [ -f "$REPO_DIR/ops/webhook/hooks.yml" ]; then
  sed -e "s#__WEBHOOK_SECRET__#${WEBHOOK_SECRET}#g" \
    "$REPO_DIR/ops/webhook/hooks.yml" \
    > /etc/webhook/hooks.yml
else
  echo "WARN: /etc/webhook/secret.env missing or empty; skipping hooks.yml sync"
fi

# Self-update: after pull, if the repo has a newer deploy.sh, replace and re-exec once.
if [ -z "${DEPLOY_SELF_UPDATED:-}" ] && [ -f "$REPO_DIR/ops/scripts/deploy.sh" ]; then
  if ! cmp -s "$REPO_DIR/ops/scripts/deploy.sh" /usr/local/bin/deploy.sh; then
    cp -f "$REPO_DIR/ops/scripts/deploy.sh" /usr/local/bin/deploy.sh
    chmod +x /usr/local/bin/deploy.sh
    DEPLOY_SELF_UPDATED=1 SKIP_PULL=1 SKIP_LOCK=1 exec /usr/local/bin/deploy.sh
  fi
fi

systemctl daemon-reload
systemctl enable webhook crimewiki-app
if ! systemctl is-active --quiet webhook; then
  systemctl start webhook
fi
systemctl start crimewiki-app

if [ "$GIT_PULL_FAILED" = "1" ]; then
  echo "WARN: deploy finished using the previously checked out code because git pull failed."
fi

if [ "$STOP_SERVICES" = "1" ]; then
  if [ -n "$COMPOSE_CMD" ] && [ -f "$REPO_DIR/docker-compose.yml" ]; then
    $COMPOSE_CMD -f "$REPO_DIR/docker-compose.yml" up -d
  fi

  if systemctl is-enabled --quiet mysql || systemctl is-active --quiet mysql; then
    systemctl start mysql || true
  fi
fi
