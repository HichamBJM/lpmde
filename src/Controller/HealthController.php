<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HealthController extends AbstractController
{
    #[Route('/health', name: 'app_health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        return $this->json([
            'status' => 'ok',
            'service' => 'lpmde',
            'timestamp' => (new \DateTimeImmutable())->format(DATE_ATOM),
        ]);
    }

    #[Route('/metrics', name: 'app_metrics', methods: ['GET'])]
    public function metrics(Request $request): Response
    {
        $content = implode("\n", [
            '# HELP lpmde_app_info Informations applicatives',
            '# TYPE lpmde_app_info gauge',
            'lpmde_app_info{app="lpmde",env="'.($_ENV['APP_ENV'] ?? 'dev').'"} 1',
            '# HELP lpmde_http_requests_total_total Compteur HTTP simplifié (démo)',
            '# TYPE lpmde_http_requests_total_total counter',
            'lpmde_http_requests_total_total{path="'.$request->getPathInfo().'"} 1',
            '# HELP lpmde_php_memory_usage_bytes Mémoire PHP utilisée',
            '# TYPE lpmde_php_memory_usage_bytes gauge',
            'lpmde_php_memory_usage_bytes '.memory_get_usage(true),
        ])."\n";

        return new Response($content, 200, ['Content-Type' => 'text/plain; version=0.0.4']);
    }
}
