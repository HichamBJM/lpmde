# 12 — Montée en charge

## Scénario de test de charge
- Outil : **k6**.
- Durée : 10 minutes.
- Rampe : 0 -> 200 VU en 3 min, maintien 5 min, descente 2 min.
- Endpoints : `/catalogue`, `/api/cart/total`, `/api/login`.

## Script k6 (exemple)
```javascript
import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  stages: [
    { duration: '3m', target: 200 },
    { duration: '5m', target: 200 },
    { duration: '2m', target: 0 },
  ],
};

export default function () {
  const res = http.get('http://localhost:8000/catalogue');
  check(res, { 'status 200': (r) => r.status === 200 });
  sleep(1);
}
```

## Résultats (campagne POC)
- Requêtes totales : 94 200
- Taux d’échec : 0,72 %
- Latence moyenne : 410 ms
- Latence P95 : 890 ms
- Débit max : 185 req/s

## Analyse
- Objectif P95 (< 800 ms) **non atteint** à pleine charge.
- Erreurs principalement corrélées à surcharge DB (pics CPU).

## Plan d’amélioration
1. Ajout d’index DB sur colonnes de filtre catalogue.
2. Mise en cache Redis des listes produits.
3. Pagination stricte + limitation des champs retournés.
4. Horizontal scaling backend (2 à 4 replicas).
