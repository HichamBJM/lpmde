#!/usr/bin/env bash
set -euo pipefail

if ! command -v k6 >/dev/null 2>&1; then
  echo "[demo_load_test] k6 non installé. Installez-le puis relancez." >&2
  exit 1
fi

mkdir -p var/reports
k6 run --insecure-skip-tls-verify --summary-export var/reports/k6-summary.json scripts/k6_checkout.js | tee var/reports/k6-run.log

echo "[demo_load_test] Rapports générés :"
echo "  - var/reports/k6-summary.json"
echo "  - var/reports/k6-run.log"
