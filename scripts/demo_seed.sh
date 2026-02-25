#!/usr/bin/env bash
set -euo pipefail

echo "[demo_seed] Application des migrations Doctrine..."
docker compose -f docker-compose.demo.yml exec -T app php bin/console doctrine:migrations:migrate -n || true

echo "[demo_seed] Comptes Keycloak déjà provisionnés via import realm:"
echo "  - demo-user / Demo123!"
echo "  - demo-admin / Demo123!"
echo "  - demo-moderator / Demo123!"
echo "[demo_seed] Emails administrateurs: admin@lpmde.local"
