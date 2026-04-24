<?php

namespace App\Tests\Controller;

use App\Tests\Support\DatabaseTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests fonctionnels du CheckoutController.
 *
 * Route protégée : access_control exige ROLE_USER sur ^/checkout.
 * Le test avec auth utilise SQLite (var/test.db) — schéma créé à la volée.
 */
final class CheckoutControllerTest extends WebTestCase
{
    use DatabaseTestTrait;

    /**
     * GET /checkout/1 sans session → 302 vers /login.
     * Vérifie que l'access_control Symfony bloque bien les anonymes.
     * Aucune DB ni Directus requis.
     */
    public function test_checkout_sans_connexion_redirige_login(): void
    {
        $client = static::createClient();

        $client->request('GET', '/checkout/1');

        self::assertResponseRedirects('/login');
    }

    /**
     * GET /checkout/1 avec utilisateur ROLE_USER connecté → 200 ou redirect paiement.
     * Vérifie que l'accès est accordé et que le contrôleur répond correctement.
     * Requiert SQLite — schéma créé dans ce test.
     */
    public function test_checkout_avec_connexion(): void
    {
        $client = static::createClient();

        // Prépare la DB SQLite et crée l'utilisateur de test.
        $em   = static::getContainer()->get('doctrine.orm.entity_manager');
        $this->ensureTestSchema($em);
        $user = $this->createTestUser($em);

        $client->loginUser($user);
        $client->request('GET', '/checkout/1');

        $status = $client->getResponse()->getStatusCode();

        self::assertContains(
            $status,
            [200, 302],
            sprintf('GET /checkout/1 (auth) → %d ; attendu 200 ou 302.', $status)
        );

        // Si redirect, ne doit PAS aller vers /login (ce serait un bug auth).
        if ($status === 302) {
            $location = $client->getResponse()->headers->get('Location') ?? '';
            self::assertStringNotContainsString(
                'login',
                $location,
                'Utilisateur connecté redirigé vers /login — problème auth.'
            );
        }
    }
}
