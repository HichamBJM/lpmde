#!/usr/bin/env bash
set -euo pipefail

RABBIT_URL="${RABBIT_URL:-http://localhost:15672}"
RABBIT_USER="${RABBIT_USER:-guest}"
RABBIT_PASSWORD="${RABBIT_PASSWORD:-guest}"
VHOST_ENCODED="%2F"
QUEUE_NAME="demo-smoke-$(date +%s)"
PAYLOAD='{"event":"demo_smoke_test","source":"demo_rabbitmq_test.sh"}'

step() { echo "[rabbitmq_test] $1"; }
api() {
  local method="$1"; shift
  local path="$1"; shift
  curl -sS -u "${RABBIT_USER}:${RABBIT_PASSWORD}" \
    -H 'content-type: application/json' \
    -X "$method" \
    "${RABBIT_URL}${path}" \
    "$@"
}

step "Vérification API management"
api GET "/api/overview" >/dev/null

step "Création file temporaire ${QUEUE_NAME}"
api PUT "/api/queues/${VHOST_ENCODED}/${QUEUE_NAME}" \
  --data '{"auto_delete":true,"durable":false,"arguments":{}}' >/dev/null

step "Publication d'un message de test"
PUBLISH_RESPONSE=$(api POST "/api/exchanges/${VHOST_ENCODED}/amq.default/publish" \
  --data "{\"properties\":{},\"routing_key\":\"${QUEUE_NAME}\",\"payload\":\"${PAYLOAD}\",\"payload_encoding\":\"string\"}")

if ! printf '%s' "$PUBLISH_RESPONSE" | grep -q '"routed":true'; then
  echo "[rabbitmq_test] ERREUR: message non routé. Réponse: $PUBLISH_RESPONSE" >&2
  exit 1
fi

step "Lecture du message depuis la file"
GET_RESPONSE=$(api POST "/api/queues/${VHOST_ENCODED}/${QUEUE_NAME}/get" \
  --data '{"count":1,"ackmode":"ack_requeue_false","encoding":"auto","truncate":50000}')

if ! printf '%s' "$GET_RESPONSE" | grep -q 'demo_smoke_test'; then
  echo "[rabbitmq_test] ERREUR: message de test introuvable. Réponse: $GET_RESPONSE" >&2
  exit 1
fi

step "Suppression file temporaire"
api DELETE "/api/queues/${VHOST_ENCODED}/${QUEUE_NAME}" >/dev/null

step "Succès: RabbitMQ publish/consume OK"
