#!/usr/bin/env bash
#
# This script updates only the deploy pipeline on an existing VM. It does not
# touch certificates or Nginx bootstrap logic; instead it refreshes the deploy
# script and the webhook configuration using the currently stored secret. This
# is useful when you change deploy behavior in git and want the VM to pick it up
# without re-running the full server setup. It reads the secret from
# /etc/webhook/secret.env if present, then regenerates /etc/webhook/hooks.yml and
# installs /usr/local/bin/deploy.sh. It leaves your running services alone so
# production traffic is not interrupted. Use this after a simple `git pull` when
# you only want the deployment automation updated. If the secret file is missing
# it will prompt or auto-generate one, so it can still recover a broken webhook
# state. Keep this script small and safe because it may be used frequently.
#
set -euo pipefail

if [ "$#" -lt 2 ]; then
  echo "Usage: $0 <domain> <repo_dir> [webhook_secret]"
  exit 1
fi

DOMAIN="$1"
REPO_DIR="$2"
WEBHOOK_SECRET="${3:-}"

NONINTERACTIVE="${NONINTERACTIVE:-0}"

mkdir -p /etc/webhook

SECRET_FILE="/etc/webhook/secret.env"
if [ -z "$WEBHOOK_SECRET" ]; then
  if [ -f "$SECRET_FILE" ]; then
    # shellcheck disable=SC1090
    . "$SECRET_FILE"
  fi
fi

if [ -z "${WEBHOOK_SECRET:-}" ]; then
  if [ "$NONINTERACTIVE" = "1" ]; then
    WEBHOOK_SECRET="$(openssl rand -hex 24)"
  else
    read -r -p "Webhook secret (leave empty to auto-generate): " INPUT_SECRET
    if [ -n "$INPUT_SECRET" ]; then
      WEBHOOK_SECRET="$INPUT_SECRET"
    else
      WEBHOOK_SECRET="$(openssl rand -hex 24)"
    fi
  fi
fi

cat > "$SECRET_FILE" <<EOF
WEBHOOK_SECRET=${WEBHOOK_SECRET}
EOF
chmod 600 "$SECRET_FILE"

# Install deploy script
sed \
  -e "s#__DOMAIN__#${DOMAIN}#g" \
  -e "s#__REPO_DIR__#${REPO_DIR}#g" \
  "$REPO_DIR/ops/scripts/deploy.sh" \
  > /usr/local/bin/deploy.sh
chmod +x /usr/local/bin/deploy.sh

# Install webhook config
sed \
  -e "s#__WEBHOOK_SECRET__#${WEBHOOK_SECRET}#g" \
  "$REPO_DIR/ops/webhook/hooks.yml" \
  > /etc/webhook/hooks.yml

# Ensure webhook unit is up to date (hooks.yml path)
cp -f "$REPO_DIR/ops/systemd/webhook.service" /etc/systemd/system/webhook.service
systemctl daemon-reload
systemctl restart webhook

echo "Deploy automation updated."
