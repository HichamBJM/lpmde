# Démo en 10 minutes — LPMDE

## 1) Démarrer la stack
```bash
./scripts/demo_up.sh
```

## 2) Initialiser les données et comptes
```bash
./scripts/demo_seed.sh
```

## 3) Exécuter le scénario fonctionnel + sécurité
```bash
./scripts/demo_scenario.sh
```

## 4) (Optionnel) Lancer la montée en charge
```bash
./scripts/demo_load_test.sh
```

## 5) Accès outils
- Application HTTPS : `https://localhost:8443`
- Keycloak : `http://localhost:8081` (`admin/admin`)
- RabbitMQ : `http://localhost:15672` (`guest/guest`)
- Prometheus : `http://localhost:9090`
- Grafana : `http://localhost:3000` (`admin/admin`)

## 6) Arrêt + nettoyage
```bash
./scripts/demo_down.sh
```
