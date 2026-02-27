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


## Dépannage Windows (Git Bash)
Si `demo_up.sh` échoue sur `openssl.cnf`, le script tente maintenant:
1) détection automatique des chemins OpenSSL Git Bash (`/mingw64/ssl/openssl.cnf`, etc.)
2) fallback via conteneur Docker OpenSSL.

Vous pouvez simplement relancer:
```bash
./scripts/demo_up.sh
```


Si vous voyez l’erreur `subject name is expected` ou `Can't open "/certs/localhost.key"`, utilisez la dernière version du script (il gère désormais `MSYS_NO_PATHCONV=1`).




Si vous voyez `Attempted to load class "DebugBundle"`, cela signifie que le conteneur tourne en `APP_ENV=dev` alors que l’image a été buildée sans dépendances dev.
Relancez en reconstruisant: 
```bash
./scripts/demo_down.sh
./scripts/demo_up.sh
```


Si l’erreur persiste, forcez une reconstruction propre des conteneurs/images:
```bash
./scripts/demo_down.sh
docker compose -f docker-compose.demo.yml build --no-cache
./scripts/demo_up.sh
```


Diagnostic rapide du mode d'exécution:
```bash
docker compose -f docker-compose.demo.yml exec app printenv APP_ENV
```
Résultat attendu: `prod`.

Si besoin, nettoyez aussi le cache Symfony dans le conteneur:
```bash
docker compose -f docker-compose.demo.yml exec app rm -rf var/cache/*
```


Si la connexion Keycloak ouvre une page `Not Found`, vérifiez la séparation URL interne/externe:
- `KEYCLOAK_URL=http://keycloak:8080` (backend conteneur -> Keycloak)
- `KEYCLOAK_PUBLIC_URL=http://localhost:8081` (navigateur utilisateur -> Keycloak)
