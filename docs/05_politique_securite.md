# 05 — Politique de sécurité

## Exigences minimales
- HTTPS local obligatoire via reverse-proxy Nginx (`https://localhost:8443`).
- Authentification via Keycloak (OIDC).
- Autorisation côté Symfony (contrôle admin sur actions sensibles).
- Secrets hors dépôt dans `.env.local` (fichier `.env.example` sans secrets réels).
- Scan des vulnérabilités en CI (Trivy + composer audit).

## Bonnes pratiques implémentées
1. **Headers de sécurité** via `SecurityHeadersSubscriber` (CSP, HSTS, X-Frame-Options, nosniff).
2. **Rate limiting applicatif** sur démarrage du flux login Keycloak.
3. **Validation d’entrée** : contraintes de format SKU + bornage quantité panier.
- CSP adaptée au front: autorisation explicite `cdn.tailwindcss.com`, `fonts.googleapis.com` et `fonts.gstatic.com` pour éviter la régression visuelle.
