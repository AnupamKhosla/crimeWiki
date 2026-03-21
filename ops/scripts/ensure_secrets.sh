#!/usr/bin/env bash
set -euo pipefail

SECRETS_DIR="${SECRETS_DIR:-/etc/secrets}"
SECRETS_FILE="${SECRETS_FILE:-$SECRETS_DIR/secrets.env}"
NONINTERACTIVE="${NONINTERACTIVE:-0}"

mkdir -p "$SECRETS_DIR"
chmod 700 "$SECRETS_DIR"

if [ -f "$SECRETS_FILE" ]; then
  # shellcheck disable=SC1090
  . "$SECRETS_FILE"
fi

prompt_for_secret() {
  local var_name="$1"
  local prompt_label="$2"
  local current_value="${!var_name:-}"
  local input=""

  if [ -n "$current_value" ]; then
    return
  fi

  if [ "$NONINTERACTIVE" = "1" ]; then
    printf -v "$var_name" '%s' "$(openssl rand -hex 24)"
    return
  fi

  echo "$prompt_label is missing."
  read -r -p "Paste $prompt_label or press Enter to auto-generate: " input
  if [ -n "$input" ]; then
    printf -v "$var_name" '%s' "$input"
  else
    printf -v "$var_name" '%s' "$(openssl rand -hex 24)"
  fi
}

prompt_for_secret "WEBHOOK_SECRET" "WEBHOOK_SECRET"
prompt_for_secret "PROXY_SECRET_TOKEN" "PROXY_SECRET_TOKEN"

cat > "$SECRETS_FILE" <<EOF
WEBHOOK_SECRET=${WEBHOOK_SECRET}
PROXY_SECRET_TOKEN=${PROXY_SECRET_TOKEN}
EOF
chmod 600 "$SECRETS_FILE"

if command -v systemctl >/dev/null 2>&1; then
  systemctl restart webhook 2>/dev/null || true
  systemctl restart crimewiki-app 2>/dev/null || true
fi

echo "Secrets ensured at $SECRETS_FILE"
echo "WEBHOOK_SECRET=$WEBHOOK_SECRET"
echo "PROXY_SECRET_TOKEN=$PROXY_SECRET_TOKEN"
