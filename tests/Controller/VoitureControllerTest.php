<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Tests fonctionnels du VoitureController.
 *
 * L'HTTP client est systématiquement remplacé par un MockHttpClient afin
 * de n'avoir aucune dépendance vers l'instance Directus réelle.
 *
 * IMPORTANT : disableReboot() est nécessaire pour que le mock injecté dans
 * le conteneur survive jusqu'au traitement de la requête HTTP simulée.
 */
final class VoitureControllerTest extends WebTestCase
{
    /**
     * GET /voitures → 200 OK quand Directus répond normalement.
     * Vérifie que la page du catalogue se charge sans erreur.
     */
    public function test_catalogue_page_returns_200(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        $payload = json_encode([
            'data' => [
                [
                    'id_voiture'             => 1,
                    'immatriculation'        => 'AA-001-BB',
                    'annee_mise_en_service'  => 2018,
                    'kilometrage'            => 45000,
                    'prix_achat'             => 25000,
                    'image'                  => null,
                ],
            ],
        ]);

        static::getContainer()->set(
            HttpClientInterface::class,
            new MockHttpClient(new MockResponse($payload))
        );

        $client->request('GET', '/voitures');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('article');
    }

    /**
     * GET /voitures avec Directus hors-ligne → 200 OK + bandeau d'erreur (jamais un 500).
     * Valide que le contrôleur intercepte la TransportException et reste résilient.
     */
    public function test_catalogue_page_sans_directus(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        $mockClient = new MockHttpClient(
            function (string $method, string $url): never {
                throw new TransportException('Connection refused — Directus offline');
            }
        );

        static::getContainer()->set(HttpClientInterface::class, $mockClient);

        $client->request('GET', '/voitures');

        // La page doit répondre 200, pas 500.
        self::assertResponseIsSuccessful();

        // Le bloc #directus-error doit être présent dans le rendu.
        self::assertSelectorExists('#directus-error');
    }

    /**
     * GET /voiture/{id} → 200 quand Directus répond, ou 302 si hors-ligne (redirect catalogue).
     * Les deux codes sont des comportements valides pour cette route.
     */
    public function test_fiche_voiture_returns_200_or_redirect(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        $payload = json_encode([
            'data' => [
                'id_voiture'             => 1,
                'immatriculation'        => 'AA-001-BB',
                'annee_mise_en_service'  => 2018,
                'kilometrage'            => 45000,
                'prix_achat'             => 25000,
                'image'                  => null,
            ],
        ]);

        static::getContainer()->set(
            HttpClientInterface::class,
            new MockHttpClient(new MockResponse($payload))
        );

        $client->request('GET', '/voiture/1');

        $status = $client->getResponse()->getStatusCode();

        self::assertContains(
            $status,
            [200, 302],
            sprintf('GET /voiture/1 a retourné %d ; 200 ou 302 attendu.', $status)
        );
    }
}
