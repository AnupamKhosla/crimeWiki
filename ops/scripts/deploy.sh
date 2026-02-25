#!/usr/bin/env bash
set -euo pipefail

DOMAIN="__DOMAIN__"
REPO_DIR="__REPO_DIR__"

# Switch to maintenance mode
ln -sf /etc/nginx/sites-available/crimewiki_maintenance.conf /etc/nginx/sites-enabled/crimewiki.conf
systemctl reload nginx

# Stop heavy services
if systemctl is-active --quiet mysql; then
  systemctl stop mysql
fi

if command -v docker >/dev/null 2>&1; then
  if [ -f "$REPO_DIR/docker-compose.yml" ]; then
    docker compose -f "$REPO_DIR/docker-compose.yml" stop || true
  fi
fi

# Pull code
cd "$REPO_DIR"
git pull --ff-only origin main

# Start services
if command -v docker >/dev/null 2>&1; then
  if [ -f "$REPO_DIR/docker-compose.yml" ]; then
    docker compose -f "$REPO_DIR/docker-compose.yml" up -d
  fi
fi

if systemctl is-enabled --quiet mysql || systemctl is-active --quiet mysql; then
  systemctl start mysql || true
fi

# Back to normal
ln -sf /etc/nginx/sites-available/crimewiki.conf /etc/nginx/sites-enabled/crimewiki.conf
systemctl reload nginx
