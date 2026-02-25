#!/usr/bin/env bash
set -euo pipefail

mkdir -p infra/nginx/certs var/log

find_openssl_conf() {
  local candidates=(
    "${OPENSSL_CONF:-}"
    "/mingw64/ssl/openssl.cnf"
    "/etc/ssl/openssl.cnf"
    "/usr/lib/ssl/openssl.cnf"
    "C:/Program Files/Git/mingw64/ssl/openssl.cnf"
  )

  local p
  for p in "${candidates[@]}"; do
    if [[ -n "$p" && -f "$p" ]]; then
      echo "$p"
      return 0
    fi
  done

  return 1
}

generate_cert_with_host_openssl() {
  if ! command -v openssl >/dev/null 2>&1; then
    return 1
  fi

  local conf=""
  if conf="$(find_openssl_conf)"; then
    export OPENSSL_CONF="$conf"
    echo "[demo_up] OPENSSL_CONF détecté: $OPENSSL_CONF"
  else
    echo "[demo_up] Aucun openssl.cnf local détecté, tentative sans OPENSSL_CONF..."
    unset OPENSSL_CONF || true
  fi

  openssl req -x509 -newkey rsa:2048 -sha256 -days 365 -nodes \
    -keyout infra/nginx/certs/localhost.key \
    -out infra/nginx/certs/localhost.crt \
    -subj '/CN=localhost'
}

generate_cert_with_docker() {
  echo "[demo_up] Fallback: génération certificat via conteneur OpenSSL..."
  docker run --rm -v "$(pwd)/infra/nginx/certs:/certs" alpine:3.20 sh -lc \
    "apk add --no-cache openssl >/dev/null && openssl req -x509 -newkey rsa:2048 -sha256 -days 365 -nodes -keyout /certs/localhost.key -out /certs/localhost.crt -subj '/CN=localhost'"
}

if [[ ! -f infra/nginx/certs/localhost.crt || ! -f infra/nginx/certs/localhost.key ]]; then
  echo "[demo_up] Génération d'un certificat auto-signé pour localhost..."
  if ! generate_cert_with_host_openssl; then
    generate_cert_with_docker
  fi
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
