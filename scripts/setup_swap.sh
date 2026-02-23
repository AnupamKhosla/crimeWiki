#!/usr/bin/env bash
set -euo pipefail

# Usage: sudo ./scripts/setup_swap.sh 8G
# Why: e2-micro class VMs (1 GB RAM) can hang under DB imports or traffic spikes.
# Swap gives the kernel extra breathing room so the VM stays responsive.
# This is safe on VPS/VMs where you control the OS. Shared hosting cannot do this.

SWAP_SIZE="${1:-8G}"
SWAP_FILE="/swapfile"

if swapon --show | grep -q "$SWAP_FILE"; then
  echo "Swapfile already active at $SWAP_FILE"
  exit 0
fi

# If the swapfile already exists (from a previous run), just enable it.
if [ -f "$SWAP_FILE" ]; then
  echo "Swapfile exists but is not active. Enabling..."
else
  # Create the swapfile, lock permissions, and format it as swap.
  echo "Creating swapfile of size $SWAP_SIZE at $SWAP_FILE"
  fallocate -l "$SWAP_SIZE" "$SWAP_FILE"
  chmod 600 "$SWAP_FILE"
  mkswap "$SWAP_FILE"
fi

# Turn swap on for this boot.
swapon "$SWAP_FILE"

# Persist across reboots if not already in /etc/fstab.
# Persist swap across reboots.
if ! grep -q "^$SWAP_FILE " /etc/fstab; then
  echo "$SWAP_FILE none swap sw 0 0" >> /etc/fstab
fi

echo "Swap setup complete."
free -h
