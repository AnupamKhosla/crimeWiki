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
# you want to reapply the proxy configuration after changes.
#
set -euo pipefail

if [ "$#" -lt 3 ]; then
  echo "Usage: $0 <domain> <email> <repo_dir>"
  exit 1
fi

DOMAIN="$1"
EMAIL="$2"
REPO_DIR="$3"
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
mkdir -p /etc/secrets
mkdir -p /etc

# Install maintenance page
cp -f "$REPO_DIR/ops/maintenance/index.html" /var/www/maintenance/index.html

# Install environment file (domain + repo path)
cp -f "$REPO_DIR/ops/env/crimewiki.env" /etc/crimewiki.env

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

# Install secrets helper and ensure all missing secrets exist
cp -f "$REPO_DIR/ops/scripts/ensure_secrets.sh" /usr/local/bin/crimewiki-ensure-secrets.sh
chmod +x /usr/local/bin/crimewiki-ensure-secrets.sh
/usr/local/bin/crimewiki-ensure-secrets.sh

if [ -f /etc/secrets/secrets.env ]; then
  # shellcheck disable=SC1091
  . /etc/secrets/secrets.env
fi

# Install deploy script
sed \
  -e "s#__DOMAIN__#${DOMAIN}#g" \
  -e "s#__REPO_DIR__#${REPO_DIR}#g" \
  "$REPO_DIR/ops/scripts/deploy.sh" \
  > /usr/local/bin/deploy.sh
chmod +x /usr/local/bin/deploy.sh

# Install boot-time app start helper
cp -f "$REPO_DIR/ops/scripts/start_stack.sh" /usr/local/bin/crimewiki-start.sh
chmod +x /usr/local/bin/crimewiki-start.sh

# Allow root (webhook) to run git in this repo
git config --system --add safe.directory "$REPO_DIR"

# Install webhook template (secret is injected by webhook.service at runtime)
cp -f "$REPO_DIR/ops/webhook/hooks.yml" /etc/webhook/hooks.yml.template

# Install systemd unit
cp -f "$REPO_DIR/ops/systemd/webhook.service" /etc/systemd/system/webhook.service
cp -f "$REPO_DIR/ops/systemd/crimewiki-app.service" /etc/systemd/system/crimewiki-app.service

systemctl daemon-reload
systemctl enable --now webhook
systemctl enable --now crimewiki-app

# Start app stack now that reverse proxy is active
if [ -n "$COMPOSE_CMD" ] && [ -f "$REPO_DIR/docker-compose.yml" ]; then
  if [ -f /etc/secrets/secrets.env ]; then
    set -a
    # shellcheck disable=SC1091
    . /etc/secrets/secrets.env
    set +a
  fi
  $COMPOSE_CMD -f "$REPO_DIR/docker-compose.yml" up -d
fi

cat <<EOF
Setup complete.
Secrets file: /etc/secrets/secrets.env
Secret recovery: sudo /usr/local/bin/crimewiki-ensure-secrets.sh
Webhook URL: https://${DOMAIN}/hooks/deploy
Webhook header: X-Webhook-Secret: ${WEBHOOK_SECRET}
EOF
