<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommandeFlowTest extends WebTestCase
{
    public function testPagesAreReachable(): void
    {
        $client = static::createClient();

        $client->request('GET', '/panier');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/commandes');
        self::assertResponseIsSuccessful();
    }

    public function testCartAddAndCheckoutRequiresAuthentication(): void
    {
        $client = static::createClient();

        $client->request('POST', '/panier/ajouter/haunted-castle-collector');
        self::assertResponseRedirects('/panier');

        $client->followRedirect();
        self::assertSelectorTextContains('main h1', 'Panier');

        $client->request('POST', '/commandes/valider');
        self::assertResponseRedirects('/login/keycloak');
    }
}
