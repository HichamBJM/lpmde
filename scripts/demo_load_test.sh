#!/usr/bin/env bash
set -euo pipefail

if ! command -v k6 >/dev/null 2>&1; then
  echo "[demo_load_test] k6 non installé. Installez-le puis relancez." >&2
  exit 1
fi

K6_PROM_RW_URL="${K6_PROM_RW_URL:-http://localhost:9090/api/v1/write}"

mkdir -p var/reports
k6 run \
  --insecure-skip-tls-verify \
  --summary-export var/reports/k6-summary.json \
  --out experimental-prometheus-rw="${K6_PROM_RW_URL}" \
  scripts/k6_checkout.js | tee var/reports/k6-run.log

echo "[demo_load_test] Rapports générés :"
echo "  - var/reports/k6-summary.json"
echo "  - var/reports/k6-run.log"
echo "[demo_load_test] Métriques k6 envoyées vers Prometheus: ${K6_PROM_RW_URL}"
