#!/usr/bin/env bash
set -euo pipefail

DOMAIN="__DOMAIN__"
REPO_DIR="__REPO_DIR__"

# Set to 1 if you want to stop services during deploys (more RAM headroom)
STOP_SERVICES="${STOP_SERVICES:-0}"

# Single-run lock to avoid overlapping deploys
LOCK_FILE="/tmp/deploy.lock"
exec 9>"$LOCK_FILE"
if ! flock -n 9; then
  echo "Deploy already running" >&2
  exit 1
fi

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
cd "$REPO_DIR"
git pull --ff-only origin main

if [ "$STOP_SERVICES" = "1" ]; then
  if [ -n "$COMPOSE_CMD" ] && [ -f "$REPO_DIR/docker-compose.yml" ]; then
    $COMPOSE_CMD -f "$REPO_DIR/docker-compose.yml" up -d
  fi

  if systemctl is-enabled --quiet mysql || systemctl is-active --quiet mysql; then
    systemctl start mysql || true
  fi
fi

# Back to normal
ln -sf /etc/nginx/sites-available/crimewiki.conf /etc/nginx/sites-enabled/crimewiki.conf
systemctl reload nginx
