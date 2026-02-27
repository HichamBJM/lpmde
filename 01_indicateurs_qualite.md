# 01 — Indicateurs de qualité logicielle (ISO/IEC 25010)

## Contexte
Application cible : plateforme web e-commerce et communautaire « La Petite Maison de l’Épouvante ».
Périmètre : POC industrialisable avec pilotage qualité continu.

## Indicateur 1 — Fiabilité : Taux d’échec en production
- **Caractéristique ISO 25010** : Fiabilité (Maturité / Tolérance aux fautes).
- **Définition** : proportion de requêtes HTTP terminant en erreur (5xx) sur une période donnée.
- **Formule** : `(Nombre de réponses 5xx / Nombre total de requêtes) x 100`.
- **Cible** : `< 0,5 %` en régime nominal.
- **Justification métier** : réduit les abandons panier et protège le chiffre d’affaires en période de pics (évènements, promotions).
- **Outil de collecte** : Prometheus (`http_requests_total`), Grafana, logs applicatifs (Monolog/ELK).

## Indicateur 2 — Performance : Latence P95 des endpoints critiques
- **Caractéristique ISO 25010** : Efficience de performance.
- **Définition** : temps de réponse du 95e percentile pour `/catalogue`, `/panier`, `/checkout`.
- **Formule** : `P95(temps_réponse_ms)` calculé par endpoint.
- **Cible** : `P95 < 800 ms` sur endpoints transactionnels.
- **Justification métier** : une latence maîtrisée augmente la conversion et améliore l’expérience communautaire (posts/commentaires).
- **Outil de collecte** : Prometheus + Grafana, k6 pour les campagnes de charge.

## Indicateur 3 — Maintenabilité : Dette technique SonarQube
- **Caractéristique ISO 25010** : Maintenabilité (Analysabilité / Modifiabilité).
- **Définition** : estimation du temps nécessaire pour corriger les anomalies de code détectées.
- **Formule** : `Somme des temps de remédiation estimés (minutes)`.
- **Cible** : ratio dette/effort `< 5 %`, note maintenabilité `A`.
- **Justification métier** : accélère les livraisons et réduit le coût de correction en phase d’exploitation.
- **Outil de collecte** : SonarQube (règles PHP, sécurité et duplication).

## Indicateur 4 — Sécurité : Densité de vulnérabilités critiques
- **Caractéristique ISO 25010** : Sécurité (Confidentialité / Intégrité / Non-répudiation).
- **Définition** : nombre de vulnérabilités critiques détectées par KLOC.
- **Formule** : `Nb vulnérabilités critiques / (Lignes de code / 1000)`.
- **Cible** : `0` vulnérabilité critique ouverte avant mise en production.
- **Justification métier** : protège les données clients et limite les impacts légaux (RGPD, réputation).
- **Outil de collecte** : Trivy, SAST (Semgrep), Dependency-Check/Composer audit.

## Indicateur 5 — Compatibilité : Taux de succès des tests cross-browser
- **Caractéristique ISO 25010** : Compatibilité (Interopérabilité / Coexistence).
- **Définition** : pourcentage de scénarios E2E réussis sur navigateurs cibles.
- **Formule** : `(Scénarios réussis multi-navigateurs / Scénarios exécutés) x 100`.
- **Cible** : `>= 98 %` sur Chrome/Firefox.
- **Justification métier** : garantit l’accès aux parcours d’achat pour l’ensemble des utilisateurs.
- **Outil de collecte** : Playwright + rapport Allure.

## Cadence de suivi
- Revue hebdomadaire en comité produit/technique.
- Alerte automatique si franchissement de seuil via Grafana Alerting.
