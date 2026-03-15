#!/usr/bin/env bash
set -euo pipefail

ACTION="${1:-start}"
LOG_FILE="${LOG_FILE:-/var/log/crimewiki-start.log}"
PULL_ON_START="${PULL_ON_START:-1}"
PULL_USER="${PULL_USER:-${SUDO_USER:-root}}"

if touch "$LOG_FILE" 2>/dev/null; then
  exec > >(tee -a "$LOG_FILE") 2>&1
fi

if [ -f /etc/crimewiki.env ]; then
  # shellcheck disable=SC1091
  . /etc/crimewiki.env
else
  echo "ERROR: /etc/crimewiki.env not found." >&2
  exit 1
fi

if [ -z "${REPO_DIR:-}" ]; then
  echo "ERROR: REPO_DIR missing in /etc/crimewiki.env" >&2
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

if [ -z "$COMPOSE_CMD" ]; then
  echo "ERROR: docker compose is not installed." >&2
  exit 1
fi

cd "$REPO_DIR"

case "$ACTION" in
  start)
    if [ "$PULL_ON_START" = "1" ]; then
      echo "[crimewiki-start] Checking for new code before starting containers..."
      if [ "$PULL_USER" = "root" ]; then
        if ! git pull --ff-only origin main; then
          echo "[crimewiki-start] WARN: git pull failed; starting containers from the existing local checkout."
        fi
      else
        if ! su -s /bin/bash "$PULL_USER" -c "cd \"$REPO_DIR\" && git pull --ff-only origin main"; then
          echo "[crimewiki-start] WARN: git pull failed for user $PULL_USER; starting containers from the existing local checkout."
        fi
      fi
    fi
    exec $COMPOSE_CMD -f "$REPO_DIR/docker-compose.yml" up -d
    ;;
  stop)
    exec $COMPOSE_CMD -f "$REPO_DIR/docker-compose.yml" stop
    ;;
  restart)
    $COMPOSE_CMD -f "$REPO_DIR/docker-compose.yml" up -d
    ;;
  *)
    echo "Usage: $0 {start|stop|restart}" >&2
    exit 1
    ;;
esac
