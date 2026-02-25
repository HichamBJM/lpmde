# 09 — Observabilité

## Implémentation
- Endpoint santé : `GET /health`.
- Endpoint métriques : `GET /metrics` (format Prometheus).
- Stack : Prometheus (`:9090`) + Grafana (`:3000`).

## Vérifications
```bash
curl -k https://localhost:8443/health
curl -k https://localhost:8443/metrics
```

## Dashboard
Dans Grafana, source Prometheus pré-provisionnée automatiquement (`infra/grafana/provisioning/datasources/prometheus.yml`).
