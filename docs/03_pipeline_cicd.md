# 03 — Pipeline CI/CD

Le workflow `.github/workflows/ci-cd.yml` exécute :
1. **quality_checks** : `composer validate`, lint PHP, lint container.
2. **unit_tests** : tests unitaires PHPUnit + artefact JUnit.
3. **integration_functional_tests** : tests d’intégration + fonctionnels avec PostgreSQL et RabbitMQ.
4. **composer_audit** : audit dépendances Composer + artefact.
5. **trivy_fs_scan** : scan Trivy filesystem + artefact.
6. **build_image** : build/push image GHCR (tags `sha` + `latest`).
7. **deploy_staging** : déploiement HTTPS complet de la stack démo (app + worker + rabbitmq + keycloak + prometheus + grafana) via `docker-compose.demo.yml` sur serveur staging.

## Secrets requis pour le déploiement staging
- `STAGING_SSH_HOST`
- `STAGING_SSH_USER`
- `STAGING_SSH_KEY`
- Optionnels :
  - `STAGING_SSH_PORT` (par défaut `22`)
  - `STAGING_APP_DIR` (par défaut `/opt/lpmde`)
  - `STAGING_REPO_URL` (par défaut `https://github.com/<owner>/<repo>.git`)

## Limites assumées
- L’intégration Keycloak complète n’est pas exécutée pendant les tests CI (coût/flakiness), mais est couverte par le déploiement de la stack de démo et les scripts de seed/scénario.
