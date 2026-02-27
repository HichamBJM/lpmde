#!/usr/bin/env bash
set -euo pipefail

COMPOSE=(env MSYS_NO_PATHCONV=1 MSYS2_ARG_CONV_EXCL='*' docker compose -f docker-compose.demo.yml)
KCADM_CONFIG_PATH="/tmp/kcadm.config"

keycloak_exec() {
  "${COMPOSE[@]}" exec -T -e KCADM_CONFIG="$KCADM_CONFIG_PATH" keycloak "$@"
}

wait_keycloak() {
  local last_error=""

  echo "[demo_seed] Attente de Keycloak..."
  for i in {1..30}; do
    if output="$(keycloak_exec /opt/keycloak/bin/kcadm.sh config credentials --server http://localhost:8080 --realm master --user admin --password admin 2>&1)"; then
      echo "[demo_seed] Keycloak prêt"
      return 0
    fi

    last_error="$output"

    if (( i % 10 == 0 )); then
      echo "[demo_seed] Keycloak pas encore prêt (${i}/30)..."
    fi

    sleep 3
  done

  if printf '%s' "$last_error" | grep -qi 'invalid user credentials'; then
    echo "[demo_seed] Les credentials admin/admin sont refusés."
    echo "[demo_seed] Astuce: supprime les volumes puis relance la démo (./scripts/demo_down.sh && docker compose -f docker-compose.demo.yml down -v)." >&2
  fi

  echo "[demo_seed] Keycloak non prêt après attente" >&2
  if [[ -n "$last_error" ]]; then
    echo "[demo_seed] Dernière erreur kcadm: $last_error" >&2
  fi
  echo "[demo_seed] Derniers logs Keycloak:" >&2
  "${COMPOSE[@]}" logs --tail=40 keycloak >&2 || true
  return 1
}

kc() {
  keycloak_exec /opt/keycloak/bin/kcadm.sh "$@"
}

get_client_id() {
  kc get clients -r lpmde -q clientId=symfony-app | sed -nE 's/.*"id"[[:space:]]*:[[:space:]]*"([^"]+)".*/\1/p' | head -n1
}

get_user_id() {
  local username="$1"
  kc get users -r lpmde -q username="$username" | sed -nE 's/.*"id"[[:space:]]*:[[:space:]]*"([^"]+)".*/\1/p' | head -n1
}

ensure_role() {
  local role="$1"
  kc create roles -r lpmde -s name="$role" >/dev/null 2>&1 || true
}

ensure_user() {
  local username="$1" email="$2" first="$3" last="$4" role="$5"
  kc create users -r lpmde \
    -s username="$username" \
    -s enabled=true \
    -s email="$email" \
    -s firstName="$first" \
    -s lastName="$last" >/dev/null 2>&1 || true

  local user_id
  user_id="$(get_user_id "$username")"
  if [[ -n "$user_id" ]]; then
    kc set-password -r lpmde --userid "$user_id" --new-password Demo123! >/dev/null
    kc add-roles -r lpmde --uid "$user_id" --rolename "$role" >/dev/null 2>&1 || true
  fi
}

wait_keycloak

echo "[demo_seed] Session admin Keycloak prête"

echo "[demo_seed] Création/validation realm lpmde..."
kc create realms -s realm=lpmde -s enabled=true >/dev/null 2>&1 || true

echo "[demo_seed] Création/validation client symfony-app..."
kc create clients -r lpmde \
  -s clientId=symfony-app \
  -s enabled=true \
  -s publicClient=false \
  -s secret=symfony-app-secret \
  -s standardFlowEnabled=true \
  -s directAccessGrantsEnabled=true \
  -s 'redirectUris=["https://localhost:8443/*","http://localhost:8088/*"]' \
  -s 'webOrigins=["https://localhost:8443","http://localhost:8088"]' >/dev/null 2>&1 || true

client_id="$(get_client_id)"
if [[ -z "$client_id" ]]; then
  echo "[demo_seed] ERREUR: client symfony-app introuvable après création." >&2
  exit 1
fi

if [[ -n "$client_id" ]]; then
  kc update clients/"$client_id" -r lpmde \
    -s secret=symfony-app-secret \
    -s 'redirectUris=["https://localhost:8443/*","http://localhost:8088/*"]' \
    -s 'webOrigins=["https://localhost:8443","http://localhost:8088"]' \
    -s standardFlowEnabled=true \
    -s directAccessGrantsEnabled=true >/dev/null
fi

echo "[demo_seed] Création/validation rôles et utilisateurs démo..."
ensure_role CLIENT
ensure_role MODERATEUR
ensure_role ADMIN

ensure_user demo-user user@lpmde.local Demo User CLIENT
ensure_user demo-admin admin@lpmde.local Demo Admin ADMIN
ensure_user demo-moderator moderator@lpmde.local Demo Moderator MODERATEUR

echo "[demo_seed] Application des migrations Doctrine..."
if "${COMPOSE[@]}" exec -T app sh -lc 'ls -1 migrations/*.php >/dev/null 2>&1'; then
  "${COMPOSE[@]}" exec -T app php bin/console doctrine:migrations:migrate -n
else
  echo "[demo_seed] Aucune migration trouvée, étape ignorée."
fi

echo "[demo_seed] Comptes Keycloak disponibles :"
echo "  - demo-user / Demo123!"
echo "  - demo-admin / Demo123!"
echo "  - demo-moderator / Demo123!"
echo "[demo_seed] Client OAuth: symfony-app (secret: symfony-app-secret)"
echo "[demo_seed] Realm: lpmde"
echo "[demo_seed] Ouvre l'application via https://localhost:8443 (ou http://localhost:8088), pas via http://localhost:8000."
