<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommandeFlowTest extends WebTestCase
{
    public function testPanierIsPublicButCommandesRequiresAuthentication(): void
    {
        $client = static::createClient();

        $client->request('GET', '/panier');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/commandes');
        self::assertResponseRedirects('/login/keycloak');
    }

    public function testCheckoutRequiresAuthentication(): void
    {
        $client = static::createClient();

        $client->request('POST', '/panier/ajouter/haunted-castle-collector');
        self::assertResponseRedirects('/panier');

        $client->followRedirect();
        self::assertSelectorTextContains('main h1', 'Panier');

        $client->request('POST', '/commandes/valider');
        self::assertResponseRedirects('/login/keycloak');
    }

    public function testAdminCanUpdateOrderStatuses(): void
    {
        $client = static::createClient();
        $this->authenticate($client, true);

        $client->request('POST', '/panier/ajouter/haunted-castle-collector');
        $client->request('POST', '/commandes/valider');
        self::assertResponseRedirects('/commandes');

        $crawler = $client->followRedirect();
        $orderNumber = trim((string) $crawler->filter('article h2')->first()->text());

        $client->request('POST', sprintf('/commandes/%s/expedier', $orderNumber));
        self::assertResponseRedirects('/commandes');
        $crawler = $client->followRedirect();
        self::assertStringContainsString('EXPEDIEE', $crawler->filter('article')->first()->text());

        $client->request('POST', sprintf('/commandes/%s/livrer', $orderNumber));
        self::assertResponseRedirects('/commandes');
        $crawler = $client->followRedirect();
        self::assertStringContainsString('LIVREE', $crawler->filter('article')->first()->text());
    }

    public function testNonAdminCannotUpdateOrderStatuses(): void
    {
        $client = static::createClient();
        $this->authenticate($client, false);

        $client->request('POST', '/panier/ajouter/haunted-castle-collector');
        $client->request('POST', '/commandes/valider');
        $crawler = $client->followRedirect();
        $orderNumber = trim((string) $crawler->filter('article h2')->first()->text());

        $client->request('POST', sprintf('/commandes/%s/expedier', $orderNumber));
        self::assertResponseRedirects('/commandes');
        $crawler = $client->followRedirect();
        self::assertStringContainsString('EN_PREPARATION', $crawler->filter('article')->first()->text());
    }

    private function authenticate(KernelBrowser $client, bool $isAdmin): void
    {
        $session = self::getContainer()->get('session.factory')->createSession();
        $session->set('is_authenticated', true);
        $session->set('is_admin', $isAdmin);
        $session->set('user_name', $isAdmin ? 'Admin User' : 'Simple User');
        $session->set('user_email', $isAdmin ? 'admin@example.com' : 'user@example.com');
        $session->save();

        $client->getCookieJar()->set(
            new \Symfony\Component\BrowserKit\Cookie($session->getName(), $session->getId())
        );
    }
}
