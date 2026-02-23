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

echo "[server_start] Repo: $REPO_DIR"
cd "$REPO_DIR"

echo "[server_start] Pulling latest code..."
git pull --ff-only

echo "[server_start] Ensuring swap is enabled..."
if ! swapon --show | grep -q "/swapfile"; then
  # Requires sudo for swap creation; will no-op if swap already exists.
  sudo ./scripts/setup_swap.sh 8G
else
  echo "[server_start] Swap already active."
fi

echo "[server_start] Starting containers (no volume reset)..."
sudo docker-compose up -d --build

echo "[server_start] Done."
