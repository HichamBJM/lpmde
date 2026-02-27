# 05 — Politique de sécurité logicielle

## Exigences minimales
1. **Authentification forte**
   - MFA pour comptes administrateurs.
   - Mot de passe conforme ANSSI (longueur + complexité).
2. **Autorisation (RBAC)**
   - Rôles : `VISITEUR`, `CLIENT`, `MODERATEUR`, `ADMIN`.
   - Contrôle d’accès serveur systématique.
3. **Chiffrement**
   - HTTPS/TLS 1.2+ obligatoire.
   - Secrets stockés hors code (vault/variables CI protégées).
4. **Protection applicative**
   - Validation des entrées, anti-CSRF, protection XSS/SQLi.
   - Headers sécurité (CSP, HSTS, X-Frame-Options).
5. **Traçabilité**
   - Journalisation des connexions, actions admin, échecs auth.
6. **Gestion des dépendances**
   - Scan hebdomadaire et en PR.
7. **Sauvegarde et reprise**
   - Backup DB quotidien, test de restauration mensuel.

## Standards et référentiels
- OWASP ASVS (niveau 2 cible).
- OWASP Top 10.
- ISO/IEC 27001 (principes).
- RGPD (minimisation, consentement, droit d’effacement).

## Outils retenus
- **SAST** : Semgrep.
- **SCA** : `composer audit`, Trivy.
- **Secrets scanning** : Gitleaks.
- **DAST (optionnel POC+)** : OWASP ZAP.
- **IAM/SSO** : Keycloak.

## Politique de correction
| Sévérité | Délai max | Action |
|---|---:|---|
| Critique | 24h | Correctif immédiat + hotfix |
| Haute | 5 jours ouvrés | Correctif sprint courant |
| Moyenne | 30 jours | Planification backlog |
| Faible | 90 jours | Opportuniste |
