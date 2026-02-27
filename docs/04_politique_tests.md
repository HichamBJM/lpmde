# 04 — Politique de tests

## Stratégie
- **Unitaires** : logique métier (`tests/Unit`).
- **Intégration** : persistance Doctrine (`tests/Integration`).
- **Fonctionnels** : parcours HTTP et RBAC (`tests/Functional`).

## Matrice rapide
| Exigence | Unit | Intégration | Fonctionnel |
|---|:---:|:---:|:---:|
| Cycle commande | X | X | X |
| RBAC admin/non-admin |  |  | X |
| Persistance utilisateur |  | X |  |
| Endpoints health/metrics |  |  | X |

## Critères d’acceptation
- 0 test rouge sur `unit`, `integration`, `functional`.
- Rapport JUnit en artefact CI.
