# 06 — Backlog fonctionnel organisé

## Épopées
- EPIC-1 : Catalogue et panier e-commerce.
- EPIC-2 : Communauté (posts/commentaires/réactions).
- EPIC-3 : Compte client et sécurité.
- EPIC-4 : Exploitation DevSecOps.

## User Stories (MoSCoW)

| ID | User Story | Critères d’acceptation | Priorité |
|---|---|---|---|
| US-01 | En tant que visiteur, je veux consulter le catalogue pour découvrir les produits. | Liste paginée, filtres catégorie/prix, temps de réponse < 1s P95. | Must |
| US-02 | En tant que client, je veux ajouter un produit au panier pour préparer un achat. | Ajout/suppression, recalcul total fiable, persistance session. | Must |
| US-03 | En tant que client, je veux finaliser une commande mockée pour valider le tunnel. | Adresse + paiement mock + confirmation mail simulée. | Must |
| US-04 | En tant qu’utilisateur, je veux créer un compte et me connecter. | Mot de passe conforme, email unique, JWT/session valide. | Must |
| US-05 | En tant que membre, je veux publier un post communautaire. | Création/édition/suppression de mon post. | Should |
| US-06 | En tant que modérateur, je veux masquer un contenu inapproprié. | Action tracée, visibilité retirée immédiatement. | Should |
| US-07 | En tant qu’admin, je veux voir un dashboard d’activité. | KPI ventes, inscriptions, erreurs 5xx. | Should |
| US-08 | En tant que DevOps, je veux un pipeline CI/CD complet. | Build/tests/scans/déploiement staging automatisés. | Must |
| US-09 | En tant qu’équipe, je veux des alertes temps réel. | Alertes Slack/email sur erreurs critiques. | Could |
| US-10 | En tant que client, je veux ajouter des avis produits. | Note + commentaire modérable. | Could |
| US-11 | En tant que PO, je veux l’export CSV des commandes. | Filtre date, format standard. | Won’t (v1) |

## Dépendances
- US-03 dépend de US-02 et US-04.
- US-06 dépend de US-05.
- US-07 dépend de US-08 et US-09.

## Définition of Done
- Code revu, tests passants, scans sécurité passants, documentation mise à jour.
