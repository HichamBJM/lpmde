# 11 — Analyse sécurité & remédiation

| Risque | Niveau | État | Remédiation priorisée |
|---|---|---|---|
| Secrets dans fichiers versionnés | High | traité partiellement | placeholders `.env`, secrets en `.env.local`/CI vars |
| Vulnérabilités dépendances | Medium/High | surveillance active | `composer audit` + Trivy à chaque push |
| Faiblesse des protections HTTP | High | traité | headers sécurité globaux + HTTPS local |
| Brute force auth | Medium | traité | rate limit flux login Keycloak |

## Priorisation
1. Critical/High sous 24-72h.
2. Medium en sprint courant.
3. Low planifié backlog.
