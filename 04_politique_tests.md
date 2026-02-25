# 04 — Politique de tests

## Objectifs
- Garantir la conformité fonctionnelle des parcours e-commerce et communautaires.
- Réduire les régressions via l’automatisation continue.
- Fournir des preuves de qualité auditable.

## Plan de tests
| Niveau | Objectif | Responsable | Fréquence | Outil |
|---|---|---|---|---|
| Unitaires | Valider logique métier isolée | Développeur | À chaque commit | PHPUnit |
| Intégration | Valider interactions DB/services | Dev + QA | PR + nightly | PHPUnit + DB conteneurisée |
| API | Vérifier contrats REST | QA | PR | Postman/Newman |
| E2E | Vérifier parcours utilisateur clé | QA | Quotidien | Playwright |
| Sécurité | Détecter vulnérabilités | SecOps | PR + release | Semgrep, Trivy |
| Performance | Contrôler SLO | DevOps | Sprint / avant release | k6 |

## Matrice type de test x exigence
| Exigence | Unit | Intégration | API | E2E | Sécu | Perf |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| Calcul total panier | X | X | X | X |  | X |
| Authentification | X | X | X | X | X |  |
| Publication commentaire | X | X | X | X | X |  |
| Paiement (mock) |  | X | X | X | X | X |

## Jeux de tests d’exemple
1. **UT-001** : total panier avec remise 10 % sur montant > 100€.
2. **IT-004** : création commande persistée + décrément stock.
3. **API-007** : `POST /api/login` retourne JWT valide et rôles.
4. **E2E-010** : inscription -> ajout panier -> validation checkout.
5. **SEC-003** : tentative injection SQL sur recherche produit.
6. **PERF-002** : 200 utilisateurs virtuels sur `/catalogue` (10 min).

## Stratégie d’intégration pipeline
- Les tests unitaires et d’intégration sont bloquants en PR.
- Les tests E2E sont bloquants sur branche `main`.
- Les tests de performance sont “warning” en PR, bloquants avant release.
- Les rapports sont publiés en artefacts CI pour audit.

## Critères de sortie qualité
- Couverture unitaire cible : >= 75 % sur domaine métier.
- Aucun test critique en échec.
- Vulnérabilités critiques : 0 ouverte.
