# 11 — Analyse de sécurité

## Résultats des scans (exemple POC)
| Outil | Portée | Résultat |
|---|---|---|
| Semgrep (OWASP) | Code applicatif | 2 alertes Medium, 0 Critical |
| Trivy FS | Dépendances + fichiers | 1 High sur dépendance transitive |
| Composer audit | Dépendances PHP | 0 Critical, 1 Medium |
| Gitleaks | Historique Git | 0 secret détecté |

## Classification des risques
| ID | Risque | Probabilité | Impact | Niveau |
|---|---|---|---|---|
| R1 | Injection SQL sur endpoint recherche | Moyenne | Élevé | High |
| R2 | Mauvaise configuration CORS | Moyenne | Moyen | Medium |
| R3 | Dépendance vulnérable (DoS) | Faible | Moyen | Medium |
| R4 | Vol de session sans HTTPS strict | Faible | Élevé | High |

## Plan de remédiation priorisé
1. **R1 (High)** : paramétrer requêtes préparées + tests d’intrusion ciblés.
2. **R4 (High)** : forcer HSTS + cookies `Secure`, `HttpOnly`, `SameSite=Strict`.
3. **R3 (Medium)** : mise à jour dépendance et verrouillage version.
4. **R2 (Medium)** : politique CORS stricte par environnement.

## Gouvernance
- Revue mensuelle sécurité.
- Ticket JIRA par vulnérabilité avec SLA selon sévérité.
- Validation “security gate” obligatoire avant release.
