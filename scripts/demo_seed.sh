#!/usr/bin/env bash
set -euo pipefail

COMPOSE="docker compose -f docker-compose.demo.yml"

wait_keycloak() {
  echo "[demo_seed] Attente de Keycloak..."
  for i in {1..30}; do
    if $COMPOSE exec -T keycloak sh -lc 'curl -fsS http://localhost:8080/health/ready >/dev/null'; then
      echo "[demo_seed] Keycloak prêt"
      return 0
    fi
    sleep 3
  done
  echo "[demo_seed] Keycloak non prêt après attente" >&2
  return 1
}

kc() {
  $COMPOSE exec -T keycloak /opt/keycloak/bin/kcadm.sh "$@"
}

get_client_id() {
  kc get clients -r lpmde -q clientId=symfony-app | sed -n 's/.*"id" : "\([^"]*\)".*/\1/p' | head -n1
}

get_user_id() {
  local username="$1"
  kc get users -r lpmde -q username="$username" | sed -n 's/.*"id" : "\([^"]*\)".*/\1/p' | head -n1
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

echo "[demo_seed] Auth admin Keycloak..."
kc config credentials --server http://localhost:8080 --realm master --user admin --password admin >/dev/null

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
  -s 'redirectUris=["https://localhost:8443/*","http://localhost:8000/*"]' >/dev/null 2>&1 || true

client_id="$(get_client_id)"
if [[ -n "$client_id" ]]; then
  kc update clients/"$client_id" -r lpmde \
    -s secret=symfony-app-secret \
    -s 'redirectUris=["https://localhost:8443/*","http://localhost:8000/*"]' \
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
$COMPOSE exec -T app php bin/console doctrine:migrations:migrate -n || true

echo "[demo_seed] Comptes Keycloak disponibles :"
echo "  - demo-user / Demo123!"
echo "  - demo-admin / Demo123!"
echo "  - demo-moderator / Demo123!"
echo "[demo_seed] Client OAuth: symfony-app (secret: symfony-app-secret)"
echo "[demo_seed] Realm: lpmde"
