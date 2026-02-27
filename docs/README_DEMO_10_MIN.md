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


Si vous êtes redirigé vers `http://localhost:8080/realms/master/...`, c'est que les variables Keycloak chargées sont incorrectes/anciennes.
Vérifiez et corrigez dans le conteneur `app`:
```bash
docker compose -f docker-compose.demo.yml exec app printenv | grep KEYCLOAK
```
Valeurs attendues:
- `KEYCLOAK_URL=http://keycloak:8080` (interne)
- `KEYCLOAK_PUBLIC_URL=http://localhost:8081` (navigateur)
- `KEYCLOAK_REALM=lpmde`

Puis redémarrez proprement:
```bash
./scripts/demo_down.sh
docker compose -f docker-compose.demo.yml up -d --build --force-recreate
```


Si Keycloak affiche **"Client not found"**, rejouez le seed idempotent (realm/client/users):
```bash
./scripts/demo_seed.sh
```
Puis vérifiez dans Keycloak:
- realm: `lpmde`
- client: `symfony-app`
- redirect URI: `https://localhost:8443/*`



Si vous voyez `Failed to connect to localhost port 8081 ... /token`, cela signifie que le backend tente d'appeler Keycloak via l'URL publique.
Dans la stack Docker, l'URL backend doit être **interne** :
- `KEYCLOAK_URL=http://keycloak:8080`
- `KEYCLOAK_PUBLIC_URL=http://localhost:8081`

Vérification rapide :
```bash
docker compose -f docker-compose.demo.yml exec app printenv | grep KEYCLOAK
```



Si vous voyez `401 Unauthorized ... /userinfo`, le plus souvent le client Keycloak est incohérent (secret/realm).
Réparez avec:
```bash
./scripts/demo_seed.sh
```

Si ça persiste, réinitialisez entièrement la stack (volumes inclus) puis reseed:
```bash
./scripts/demo_down.sh
docker compose -f docker-compose.demo.yml down -v
./scripts/demo_up.sh
./scripts/demo_seed.sh
```
