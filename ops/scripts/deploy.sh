#!/usr/bin/env bash
#
# This deploy script is triggered by the webhook and is intentionally simple.
# It flips Nginx into maintenance mode, pulls the latest code, and then returns
# traffic to the live site. On small VMs, deployments can collide with traffic,
# so the maintenance switch ensures no half-rendered PHP or DB work runs while
# files are changing. The script supports a lock to prevent concurrent deploys,
# optional service stops to free memory, and a conservative default to keep the
# site in maintenance mode if anything fails. It also logs to /var/log/deploy.log
# so you can see exactly what happened in SSH. The git pull runs as the sudo
# user by default to avoid ownership issues, but you can set PULL_USER if you
# want a specific account. If you prefer automatic recovery, set
# KEEP_MAINT_ON_ERROR=0. Keep this file stable and small: it is the safety rail
# between your public site and the VM under deployment pressure.
#
set -euo pipefail

DOMAIN="__DOMAIN__"
REPO_DIR="__REPO_DIR__"
DOMAIN_WWW="www.${DOMAIN}"

# Set to 1 if you want to stop services during deploys (more RAM headroom)
STOP_SERVICES="${STOP_SERVICES:-0}"
# Set to 1 to keep maintenance page active when deploy fails
KEEP_MAINT_ON_ERROR="${KEEP_MAINT_ON_ERROR:-1}"
# User to run git pull as (defaults to the sudo user)
PULL_USER="${PULL_USER:-${SUDO_USER:-root}}"
# Log file for deploy output
LOG_FILE="${LOG_FILE:-/var/log/deploy.log}"

# Single-run lock to avoid overlapping deploys
LOCK_FILE="/tmp/deploy.lock"
exec 9>"$LOCK_FILE"
if ! flock -n 9; then
  echo "Deploy already running" >&2
  exit 1
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

on_exit() {
  status="$?"
  if [ "$status" -ne 0 ]; then
    echo "Deploy failed with status $status"
    if [ "$KEEP_MAINT_ON_ERROR" = "1" ]; then
      exit "$status"
    fi
  fi
  ln -sf /etc/nginx/sites-available/crimewiki.conf /etc/nginx/sites-enabled/crimewiki.conf
  systemctl reload nginx
  exit "$status"
}
trap on_exit EXIT

# Switch to maintenance mode
ln -sf /etc/nginx/sites-available/crimewiki_maintenance.conf /etc/nginx/sites-enabled/crimewiki.conf
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
    git pull --ff-only origin main
  else
    su -s /bin/bash "$PULL_USER" -c "cd \"$REPO_DIR\" && git pull --ff-only origin main"
  fi
fi

# Sync ops files from repo to VM locations (no reloads/restarts here)
mkdir -p /etc/nginx/sites-available /etc/webhook /var/www/maintenance /etc/systemd/system
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
    DEPLOY_SELF_UPDATED=1 SKIP_PULL=1 exec /usr/local/bin/deploy.sh
  fi
fi

if [ "$STOP_SERVICES" = "1" ]; then
  if [ -n "$COMPOSE_CMD" ] && [ -f "$REPO_DIR/docker-compose.yml" ]; then
    $COMPOSE_CMD -f "$REPO_DIR/docker-compose.yml" up -d
  fi

  if systemctl is-enabled --quiet mysql || systemctl is-active --quiet mysql; then
    systemctl start mysql || true
  fi
fi
