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

    public function testCartAddAndCreateOrderFlow(): void
    {
        $client = static::createClient();

        $client->request('POST', '/panier/ajouter/haunted-castle-collector');
        self::assertResponseRedirects('/panier');

        $client->followRedirect();
        self::assertSelectorTextContains('h1', 'Panier');

        $client->request('POST', '/commandes/valider');
        self::assertResponseRedirects('/commandes');

        $client->followRedirect();
        self::assertSelectorExists('article');
        self::assertPageTitleContains('Commandes');
    }
}
