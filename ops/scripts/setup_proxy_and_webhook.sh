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

# Install nginx configs
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
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx

# Issue certs (non-interactive)
certbot certonly --nginx \
  -d "$DOMAIN" -d "$DOMAIN_WWW" \
  --non-interactive --agree-tos -m "$EMAIL"

systemctl reload nginx

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
