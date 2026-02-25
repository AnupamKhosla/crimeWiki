#!/usr/bin/env bash
set -euo pipefail

DOMAIN="__DOMAIN__"
REPO_DIR="__REPO_DIR__"

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

# Pull code
if [ "$PULL_USER" = "root" ]; then
  cd "$REPO_DIR"
  git pull --ff-only origin main
else
  su -s /bin/bash "$PULL_USER" -c "cd \"$REPO_DIR\" && git pull --ff-only origin main"
fi

if [ "$STOP_SERVICES" = "1" ]; then
  if [ -n "$COMPOSE_CMD" ] && [ -f "$REPO_DIR/docker-compose.yml" ]; then
    $COMPOSE_CMD -f "$REPO_DIR/docker-compose.yml" up -d
  fi

  if systemctl is-enabled --quiet mysql || systemctl is-active --quiet mysql; then
    systemctl start mysql || true
  fi
fi
