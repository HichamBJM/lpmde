# 12 — Script de soutenance (20 minutes)

## Trame
- 0-3 min : contexte, objectifs, backlog Must/Should.
- 3-7 min : architecture (Symfony, Keycloak, RabbitMQ, DB, observabilité).
- 7-11 min : pipeline CI/CD et preuves (tests + scans + artefacts).
- 11-16 min : démo live (`demo_up`, `demo_scenario`, `/health`, Grafana).
- 16-19 min : charge k6 et analyse des résultats.
- 19-20 min : limites, plan de remédiation et roadmap.

## Démo pas à pas
```bash
./scripts/demo_up.sh
./scripts/demo_seed.sh
./scripts/demo_scenario.sh
./scripts/demo_load_test.sh
```
