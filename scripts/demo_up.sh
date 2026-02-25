#!/usr/bin/env bash
set -euo pipefail

mkdir -p infra/nginx/certs var/log

if [[ ! -f infra/nginx/certs/localhost.crt || ! -f infra/nginx/certs/localhost.key ]]; then
  echo "[demo_up] Génération d'un certificat auto-signé pour localhost..."
  openssl req -x509 -newkey rsa:2048 -sha256 -days 365 -nodes \
    -keyout infra/nginx/certs/localhost.key \
    -out infra/nginx/certs/localhost.crt \
    -subj '/CN=localhost'
fi

echo "[demo_up] Démarrage de la stack démo..."
docker compose -f docker-compose.demo.yml up -d --build

echo "[demo_up] Attente health app..."
for i in {1..30}; do
  if curl -ksf https://localhost:8443/health >/dev/null; then
    echo "[demo_up] Application disponible sur https://localhost:8443"
    exit 0
  fi
  sleep 3
done

echo "[demo_up] Timeout: application non prête" >&2
exit 1
