#!/usr/bin/env bash
#
# This script performs the one-time or occasional server bootstrap for a small VM.
# It installs Nginx, Certbot, and the webhook listener, provisions TLS certificates
# for your domain, and installs the reverse proxy plus maintenance-mode configs.
# It is designed to be idempotent: if certificates already exist, it will skip
# re-issuing them unless you force it. It also installs the deploy script into
# /usr/local/bin so the webhook can run it, and creates the maintenance page.
# The goal is a reliable, low-RAM setup that keeps public traffic on HTTPS while
# forwarding app traffic to localhost:8080, and allows safe deploys even on
# constrained machines. Use this script when first setting up a new VM or when
# you want to reapply the proxy configuration after changes. It can preserve an
# existing webhook secret or accept a new one. For frequent updates, use the
# update_deploy.sh script instead so you don’t touch TLS or Nginx.
#
set -euo pipefail

if [ "$#" -lt 3 ]; then
  echo "Usage: $0 <domain> <email> <repo_dir> [webhook_secret]"
  exit 1
fi

DOMAIN="$1"
EMAIL="$2"
REPO_DIR="$3"
WEBHOOK_SECRET="${4:-}"
DOMAIN_WWW="www.${DOMAIN}"

FORCE_CERT="${FORCE_CERT:-0}"
NONINTERACTIVE="${NONINTERACTIVE:-0}"

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

apt-get update
apt-get install -y nginx certbot python3-certbot-nginx webhook

mkdir -p /var/www/letsencrypt
mkdir -p /var/www/maintenance
mkdir -p /etc/webhook

# Install maintenance page
cp -f "$REPO_DIR/ops/maintenance/index.html" /var/www/maintenance/index.html

# Stop services that can block port 80 during initial cert issuance
if [ -n "$COMPOSE_CMD" ] && [ -f "$REPO_DIR/docker-compose.yml" ]; then
  $COMPOSE_CMD -f "$REPO_DIR/docker-compose.yml" down || true
fi
systemctl stop nginx || true

# Bootstrap Nginx with HTTP-only config for ACME
cat > /etc/nginx/sites-available/crimewiki_bootstrap.conf <<NGINX
server {
  listen 80;
  server_name ${DOMAIN} ${DOMAIN_WWW};

  location /.well-known/acme-challenge/ {
    root /var/www/letsencrypt;
  }
}
NGINX

ln -sf /etc/nginx/sites-available/crimewiki_bootstrap.conf /etc/nginx/sites-enabled/crimewiki.conf
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl start nginx

CERT_PATH="/etc/letsencrypt/live/${DOMAIN}/fullchain.pem"
if [ "$FORCE_CERT" = "1" ] || [ ! -f "$CERT_PATH" ]; then
  certbot certonly --nginx \
    -d "$DOMAIN" -d "$DOMAIN_WWW" \
    --non-interactive --agree-tos -m "$EMAIL"
fi

# Install nginx configs (now that certs exist)
sed \
  -e "s/__DOMAIN__/${DOMAIN}/g" \
  -e "s/__DOMAIN_WWW__/${DOMAIN_WWW}/g" \
  "$REPO_DIR/ops/nginx/crimewiki.conf" \
  > /etc/nginx/sites-available/crimewiki.conf

sed \
  -e "s/__DOMAIN__/${DOMAIN}/g" \
  -e "s/__DOMAIN_WWW__/${DOMAIN_WWW}/g" \
  "$REPO_DIR/ops/nginx/crimewiki_maintenance.conf" \
  > /etc/nginx/sites-available/crimewiki_maintenance.conf

ln -sf /etc/nginx/sites-available/crimewiki.conf /etc/nginx/sites-enabled/crimewiki.conf
nginx -t
systemctl reload nginx

# Webhook secret handling
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

# Allow root (webhook) to run git in this repo
git config --system --add safe.directory "$REPO_DIR"

# Install webhook config (YAML with comments)
sed \
  -e "s#__WEBHOOK_SECRET__#${WEBHOOK_SECRET}#g" \
  "$REPO_DIR/ops/webhook/hooks.yml" \
  > /etc/webhook/hooks.yml

# Install systemd unit
cp -f "$REPO_DIR/ops/systemd/webhook.service" /etc/systemd/system/webhook.service

systemctl daemon-reload
systemctl enable --now webhook

# Start app stack now that reverse proxy is active
if [ -n "$COMPOSE_CMD" ] && [ -f "$REPO_DIR/docker-compose.yml" ]; then
  $COMPOSE_CMD -f "$REPO_DIR/docker-compose.yml" up -d
fi

cat <<EOF
Setup complete.
Webhook URL: https://${DOMAIN}/hooks/deploy
Webhook header: X-Webhook-Secret: ${WEBHOOK_SECRET}
EOF
