#!/usr/bin/env bash
set -euo pipefail

if [ "$#" -lt 3 ]; then
  echo "Usage: $0 <domain> <email> <repo_dir> [webhook_secret]"
  exit 1
fi

DOMAIN="$1"
EMAIL="$2"
REPO_DIR="$3"
WEBHOOK_SECRET="${4:-}"

if [ -z "$WEBHOOK_SECRET" ]; then
  WEBHOOK_SECRET="$(openssl rand -hex 24)"
fi

DOMAIN_WWW="www.${DOMAIN}"

apt-get update
apt-get install -y nginx certbot python3-certbot-nginx webhook

mkdir -p /var/www/letsencrypt
mkdir -p /var/www/maintenance

# Install maintenance page
cp -f "$REPO_DIR/ops/maintenance/index.html" /var/www/maintenance/index.html

# Stop services that can block port 80 during initial cert issuance
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

# Issue certs (non-interactive)
certbot certonly --nginx \
  -d "$DOMAIN" -d "$DOMAIN_WWW" \
  --non-interactive --agree-tos -m "$EMAIL"

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

# Start app stack now that reverse proxy is active
if [ -n "$COMPOSE_CMD" ] && [ -f "$REPO_DIR/docker-compose.yml" ]; then
  $COMPOSE_CMD -f "$REPO_DIR/docker-compose.yml" up -d
fi

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
  "$REPO_DIR/ops/webhook/hooks.json" \
  > /etc/webhook/hooks.json

# Install systemd unit
cp -f "$REPO_DIR/ops/systemd/webhook.service" /etc/systemd/system/webhook.service

systemctl daemon-reload
systemctl enable --now webhook

cat <<EOF
Setup complete.
Webhook URL: https://${DOMAIN}/hooks/deploy
Webhook header: X-Webhook-Secret: ${WEBHOOK_SECRET}
EOF
