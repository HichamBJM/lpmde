# Gap analysis — Référentiel « Superviser et assurer le développement des applications logicielles »

| Exigence | Présent avant correction | À faire / action implémentée |
|---|---|---|
| Fonctionnalité métier implémentée + testée | Flux commande existant, tests partiels | Consolidation des tests fonctionnels + ajout tests observabilité/intégration DB |
| CI/CD avec 2 types de tests min | Workflow présent mais hétérogène | Workflow unifié avec lint + unit + intégration/functional + build + scans + artefacts |
| Processus DevSecOps + schéma CI/CD | Documents à la racine non alignés | Dossier `/docs` normalisé avec cycle, pipeline et schémas |
| Sécurité minimale (HTTPS, auth, secrets, scans) | Keycloak présent, secrets exposés, HTTPS local absent | HTTPS local via Nginx TLS, nettoyage `.env.example`, headers sécurité, rate limit login |
| Déploiement orchestré démo | Compose partiellement incohérent (conflits) | `docker-compose.demo.yml` complet et scripts one-click |
| Observabilité | Documentation partielle | Endpoints `/health` et `/metrics` + Prometheus/Grafana en compose démo |
| Montée en charge reproductible | Script d’exemple textuel | Script k6 exécutable + script shell + export rapport |
| 4 indicateurs ISO 25010 + mesure pipeline | Document existant hors `/docs` | Version `/docs` alignée pipeline/collecte |
| Plan de remédiation sécurité priorisé | Présent partiellement | Document dédié remédiation avec priorisation et échéances |
| Supports soutenance 20 min | Présents hors `/docs` | Script soutenance structuré dans `/docs` |
