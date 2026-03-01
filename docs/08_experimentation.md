# 08 — Expérimentation technique

## Sujet
Validation d’un environnement démo complet Docker Compose avec Keycloak + RabbitMQ + Observabilité.

## Protocole
1. `./scripts/demo_up.sh`
2. `./scripts/demo_seed.sh`
3. `./scripts/demo_scenario.sh`
4. `./scripts/demo_load_test.sh`

## Difficultés
- Gestion TLS local (certificat auto-signé) -> automatisée via `openssl`.
- Initialisation Keycloak -> résolue via import realm JSON.
- CI Keycloak instable -> limité en CI, couvert en démo locale.

## Résultats
- Démo fonctionnelle reproductible en local.
- Contrôle d’accès anonyme/authentifié vérifié.
- Observabilité accessible sur Prometheus/Grafana.
