#!/usr/bin/env bash
#
# Deprecated wrapper. This script existed before the setup was simplified. It
# now forwards to setup_server.sh to preserve backward compatibility with old
# instructions and bookmarks. The new model is: run setup_server.sh once per VM
# to install Nginx, Certbot, the webhook listener, the env file, and the deploy
# script. If you see this file in docs or history, you can still use it, but
# prefer the new script for clarity and fewer moving parts. Keeping
# this wrapper means you won’t have to re-learn the command when upgrading the
# repo, and it prevents confusion on live servers where scripts are called by
# habit. This file will remain until the new setup is well established.
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
exec "$SCRIPT_DIR/setup_server.sh" "$@"
