# 02 — Cycle DevSecOps

## Description des étapes
1. **Plan** : cadrage besoin, user stories, risques sécurité, critères qualité.
2. **Code** : développement feature branch, conventions MVC, revues PR.
3. **Build** : compilation des assets, packaging image Docker immuable.
4. **Test** : tests unitaires, intégration, API, E2E ciblés.
5. **Security** : SAST, scan dépendances, scan image conteneur, secrets.
6. **Release** : versionnage, changelog, artefact signé.
7. **Deploy** : déploiement progressif (staging -> prod), stratégie rollback.
8. **Operate** : supervision, incidents, SLA/SLO.
9. **Monitor & Feedback** : métriques, post-mortem, amélioration continue.

## Schéma ASCII
```text
+------+   +------+   +-------+   +------+   +----------+
| Plan |-->| Code |-->| Build |-->| Test |-->| Security |
+------+   +------+   +-------+   +------+   +----------+
    ^                                               |
    |                                               v
+----------+   +---------+   +--------+   +----------------+
| Feedback |<--| Monitor |<--| Operate|<--| Release/Deploy |
+----------+   +---------+   +--------+   +----------------+
```

## Outils par étape
| Étape | Outils | Livrable |
|---|---|---|
| Plan | Jira, GitHub Projects, OWASP ASVS checklist | Backlog priorisé |
| Code | Git, PHP CS Fixer, Rector | PR + revues |
| Build | Docker, Composer, npm | Image versionnée |
| Test | PHPUnit, Postman/Newman, Playwright | Rapports de tests |
| Security | Semgrep, Trivy, `composer audit`, Gitleaks | Rapport vulnérabilités |
| Release | Git tags, Conventional Commits | Notes de version |
| Deploy | GitHub Actions/GitLab CI, Helm/K8s | Déploiement traçable |
| Operate | Kubernetes, Nginx, Redis, PostgreSQL | Service en production |
| Monitor | Prometheus, Grafana, Loki/ELK, Alertmanager | KPI et alertes |

## Garde-fous
- Merge interdit si quality gate échoue.
- Politique “security by default” : scans bloquants pour sévérité Critical/High.
- Evidence pack conservé pour audit académique/professionnel.
