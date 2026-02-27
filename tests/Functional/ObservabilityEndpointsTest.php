<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ObservabilityEndpointsTest extends WebTestCase
{
    public function testHealthEndpointIsAvailable(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testMetricsEndpointIsAvailable(): void
    {
        $client = static::createClient();
        $client->request('GET', '/metrics');

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('lpmde_app_info', $client->getResponse()->getContent() ?? '');
    }
}
