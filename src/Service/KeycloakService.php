<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class KeycloakService
{
    private string $keycloakUrl;
    private string $keycloakPublicUrl;
    private string $realm;
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;

    public function __construct(
        private HttpClientInterface $httpClient
    ) {
        // Configuration Keycloak - À adapter selon votre installation
        $this->keycloakUrl = $this->env('KEYCLOAK_URL', 'http://localhost:8080');
        $this->keycloakPublicUrl = $this->env('KEYCLOAK_PUBLIC_URL', $this->keycloakUrl);
        $this->realm = $this->env('KEYCLOAK_REALM', 'master');
        $this->clientId = $this->env('KEYCLOAK_CLIENT_ID', 'symfony-app');
        $this->clientSecret = $this->env('KEYCLOAK_CLIENT_SECRET', '');
        $this->redirectUri = $this->env('KEYCLOAK_REDIRECT_URI', 'http://localhost:8000/login/keycloak/callback');
    }

    public function getAuthorizationUrl(string $state): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'state' => $state,
        ];

        return sprintf(
            '%s/realms/%s/protocol/openid-connect/auth?%s',
            $this->keycloakPublicUrl,
            $this->realm,
            http_build_query($params)
        );
    }

    public function getAccessToken(string $code): array
    {
        $response = $this->httpClient->request('POST', $this->getTokenUrl(), [
            'body' => [
                'grant_type' => 'authorization_code',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
            ],
        ]);

        return $response->toArray();
    }

    public function getUserInfo(string $accessToken): array
    {
        $response = $this->httpClient->request('GET', $this->getUserInfoUrl(), [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        return $response->toArray();
    }

    private function getTokenUrl(): string
    {
        return sprintf(
            '%s/realms/%s/protocol/openid-connect/token',
            $this->keycloakUrl,
            $this->realm
        );
    }

    private function getUserInfoUrl(): string
    {
        return sprintf(
            '%s/realms/%s/protocol/openid-connect/userinfo',
            $this->keycloakUrl,
            $this->realm
        );
    }
    private function env(string $name, string $default): string
    {
        return $_ENV[$name] ?? $_SERVER[$name] ?? $default;
    }
}
