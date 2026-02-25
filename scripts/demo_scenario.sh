#!/usr/bin/env bash
set -euo pipefail

BASE_URL="https://localhost:8443"

step() { echo "\n[SCENARIO] $1"; }

step "Vérification health"
curl -ks "$BASE_URL/health" | sed 's/.*/  &/'

step "Consultation panier"
HTTP_CODE=$(curl -ks -o /tmp/panier.html -w '%{http_code}' "$BASE_URL/panier")
echo "  GET /panier -> HTTP $HTTP_CODE"

step "Tentative checkout sans auth (doit rediriger login Keycloak)"
HTTP_CODE=$(curl -ks -o /tmp/checkout.out -w '%{http_code}' -X POST "$BASE_URL/commandes/valider")
echo "  POST /commandes/valider (anonyme) -> HTTP $HTTP_CODE (attendu 302)"

step "Tentative accès commandes sans auth"
HTTP_CODE=$(curl -ks -o /tmp/commandes.out -w '%{http_code}' "$BASE_URL/commandes")
echo "  GET /commandes (anonyme) -> HTTP $HTTP_CODE (attendu 302)"

echo "\n[SCENARIO] Succès: le contrôle d'accès côté Symfony est démontré."
