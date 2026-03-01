import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  stages: [
    { duration: '1m', target: 50 },
    { duration: '3m', target: 100 },
    { duration: '1m', target: 0 }
  ],
  thresholds: {
    http_req_failed: ['rate<0.02'],
    http_req_duration: ['p(95)<800']
  }
};

export default function () {
  const res = http.get('https://localhost:8443/panier', { insecureSkipTLSVerify: true });
  check(res, { 'status is 200': (r) => r.status === 200 });
  sleep(1);
}
