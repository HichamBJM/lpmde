# 01 — Indicateurs qualité (ISO/IEC 25010)

## Indicateur 1 — Fiabilité (taux d’erreurs 5xx)
- **Formule** : `5xx / requêtes totales`.
- **Mesure pipeline** : test de smoke + métrique Prometheus `http_requests_total`.
- **Seuil** : `< 0,5 %`.

## Indicateur 2 — Efficience (latence P95)
- **Formule** : `P95(http_req_duration)`.
- **Mesure pipeline** : campagne k6 (`scripts/k6_checkout.js`).
- **Seuil** : `< 800 ms`.

## Indicateur 3 — Maintenabilité (qualité statique)
- **Formule** : nb d’anomalies lint/static analysis.
- **Mesure pipeline** : `php -l`, `composer validate`, `lint:container`.
- **Seuil** : 0 erreur bloquante.

## Indicateur 4 — Sécurité (vulnérabilités High/Critical)
- **Formule** : `nb vulnérabilités High/Critical ouvertes`.
- **Mesure pipeline** : `composer audit`, Trivy image.
- **Seuil** : 0 Critical ouverte avant merge/release.
