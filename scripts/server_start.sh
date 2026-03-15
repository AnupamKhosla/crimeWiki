#!/usr/bin/env bash
set -euo pipefail

# Usage:
#   ./scripts/server_start.sh
#
# What this does:
# 1) Pull latest code.
# 2) Ensure swap is enabled (non-destructive if already present).
# 3) Start containers without wiping the DB volume.

REPO_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

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

if [ -z "$COMPOSE_CMD" ]; then
  echo "[server_start] ERROR: docker compose is not installed."
  exit 1
fi

echo "[server_start] Repo: $REPO_DIR"
cd "$REPO_DIR"

echo "[server_start] Pulling latest code..."
git pull --ff-only

echo "[server_start] Ensuring swap is enabled..."
if ! sudo /sbin/swapon --show | grep -q "/swapfile"; then
  # Requires sudo for swap creation; will no-op if swap already exists.
  sudo ./scripts/setup_swap.sh 8G
else
  echo "[server_start] Swap already active."
fi

echo "[server_start] Starting containers (no volume reset)..."
sudo $COMPOSE_CMD up -d --build

echo "[server_start] Done."
