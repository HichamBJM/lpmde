# 03 — Pipeline CI/CD

Le workflow `.github/workflows/ci-cd.yml` exécute :
1. **lint_static** : `composer validate`, lint PHP, lint container.
2. **unit_tests** : tests unitaires PHPUnit + artefact JUnit.
3. **integration_tests** : tests intégration + fonctionnels avec services PostgreSQL et RabbitMQ.
4. **build_and_scan** : build Docker, scan Trivy image, `composer audit`, publication artefacts scan.

## Limites assumées
- L’intégration Keycloak complète n’est pas exécutée en CI (coût et flakiness) : couverte par scripts de démo Docker Compose.
