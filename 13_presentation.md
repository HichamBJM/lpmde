# 13 — Présentation finale (20 minutes)

## Script oral structuré

### 0:00 - 2:00 | Introduction
- Contexte entreprise : e-commerce + communauté.
- Problématique : livrer vite sans compromettre qualité/sécurité.
- Objectif du projet : POC industrialisable piloté DevSecOps.

### 2:00 - 6:00 | Besoin & backlog
- Présentation des EPIC et priorisation MoSCoW.
- Focus sur parcours critique : catalogue -> panier -> commande.

### 6:00 - 10:00 | Architecture & choix techniques
- Architecture modulaire monolithe.
- Justification de Keycloak, PostgreSQL, Redis.
- Gouvernance qualité ISO 25010.

### 10:00 - 14:00 | Pipeline DevSecOps
- Étapes CI/CD : build, tests, scans sécurité, déploiement staging.
- Gates qualité/sécurité.
- Traçabilité et preuves d’audit.

### 14:00 - 17:00 | Démonstration pas à pas
1. Push d’une feature.
2. Déclenchement pipeline.
3. Exécution tests unitaires/intégration.
4. Scan sécurité.
5. Déploiement staging.
6. Vérification dashboard Grafana.

### 17:00 - 19:00 | Résultats, limites, plan d’amélioration
- KPI atteints / non atteints (P95 charge).
- Limites POC.
- Roadmap v2 : optimisation perf + DAST automatisé.

### 19:00 - 20:00 | Conclusion
- Bilan compétence : supervision développement, sécurité, qualité, exploitation.

---

## Slides textuels prêts à exporter

### Slide 1 — Titre
**Plateforme La Petite Maison de l’Épouvante**
POC DevSecOps & Qualité logicielle

### Slide 2 — Objectifs
- POC fonctionnel e-commerce/communauté
- Pipeline CI/CD sécurisé
- Pilotage qualité ISO 25010

### Slide 3 — Backlog priorisé
- Must/Should/Could
- Focus sur valeur métier immédiate

### Slide 4 — Architecture
- Frontend + Backend Symfony + DB + Redis + Keycloak
- Observabilité intégrée

### Slide 5 — DevSecOps
- Cycle complet Plan -> Feedback
- Outils et contrôles automatisés

### Slide 6 — Pipeline
- Build
- Unit + Intégration
- SAST/SCA
- Déploiement staging

### Slide 7 — Qualité
- Taux erreur, P95, dette technique, vulnérabilités
- Seuils et alertes

### Slide 8 — Sécurité
- RBAC, HTTPS, secrets management
- Plan de remédiation priorisé

### Slide 9 — Charge & performance
- Résultats k6
- Axes d’optimisation

### Slide 10 — Conclusion
- Acquis, risques restants, roadmap
