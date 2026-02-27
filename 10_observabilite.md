# 10 — Observabilité et monitoring

## Stack retenue
- **Métriques** : Prometheus.
- **Visualisation** : Grafana.
- **Logs** : Loki + Promtail (ou ELK en variante).
- **Alerting** : Alertmanager (mail/Slack).

## Intégration technique
- Exposition endpoint `/metrics` côté application.
- Scrape Prometheus toutes les 15 secondes.
- Centralisation logs Nginx + applicatifs (niveau INFO/ERROR).

## Exemples de métriques suivies
- `http_requests_total{status="5xx"}`
- `http_request_duration_seconds_bucket`
- `checkout_completed_total`
- `login_failed_total`

## Dashboards exemples
1. **Dashboard Produit/Business**
   - Conversion panier -> commande
   - CA/jour (POC simulé)
2. **Dashboard Technique**
   - Latence P50/P95/P99
   - Taux d’erreurs 4xx/5xx
   - Saturation CPU/Mémoire
3. **Dashboard Sécurité**
   - Tentatives login échouées
   - Évolution vulnérabilités ouvertes

## Exemple de règle d’alerte
```yaml
groups:
  - name: lpmde-alerts
    rules:
      - alert: HighErrorRate
        expr: sum(rate(http_requests_total{status=~"5.."}[5m])) / sum(rate(http_requests_total[5m])) > 0.02
        for: 10m
        labels:
          severity: critical
        annotations:
          summary: "Taux d'erreur > 2% sur 10 minutes"
```
