# 07 — Architecture cible démo

```text
[Browser]
   |
 HTTPS 8443
   v
[Nginx reverse-proxy]
   |
   v
[Symfony App] <--> [PostgreSQL app-db]
   |    \
   |     +--> [RabbitMQ]
   +--------> [Keycloak]
   |
  /metrics
   v
[Prometheus] --> [Grafana]
```

Composants clés: Symfony (métier), Keycloak (authn/authz), RabbitMQ (messagerie), PostgreSQL (persistance), Nginx TLS, Prometheus/Grafana (observabilité).
