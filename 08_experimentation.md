# 08 — Expérimentation technique structurante

## Sujet
Intégration **Keycloak** pour authentification centralisée et RBAC.

## Hypothèse
L’usage de Keycloak réduit le coût d’implémentation sécurité et améliore la gouvernance des rôles sans dégrader significativement la latence.

## Protocole détaillé
1. Déployer Keycloak via `docker-compose-keycloak.yml`.
2. Créer realm `lpmde`, client `web-app`, rôles `CLIENT`, `MODERATEUR`, `ADMIN`.
3. Configurer backend Symfony (OIDC, JWKS, validation tokens).
4. Exécuter 3 scénarios :
   - S1 : login standard + accès `/profil`.
   - S2 : accès admin avec rôle insuffisant (doit être refusé).
   - S3 : rafraîchissement token expiré.
5. Mesurer :
   - Temps moyen login (ms).
   - Taux de succès RBAC.
   - Complexité d’intégration (jour.homme).

## Commandes d’essai
```bash
docker compose -f docker-compose-keycloak.yml up -d
curl -X POST http://localhost:8080/realms/lpmde/protocol/openid-connect/token \
  -d "client_id=web-app" -d "grant_type=password" -d "username=test" -d "password=Test123!"
```

## Résultats observés (POC)
- Login moyen : **~320 ms** sur environnement local.
- RBAC : **100 %** des cas de tests conformes.
- Effort intégration : **2,5 j.h**.

## Limites et difficultés
- Paramétrage initial OIDC sensible (URI redirect, horloge serveur).
- Gestion des rôles composites à documenter pour l’équipe.
- Dépendance réseau supplémentaire (disponibilité IAM).

## Décision
- **Go** pour le POC élargi.
- Ajouter fallback “mode maintenance IAM” + cache JWKS.
