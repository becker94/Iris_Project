<?php

namespace App\Tests\Controller;

use App\Tests\Support\DatabaseTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests fonctionnels du SecurityController (login / logout).
 *
 * test_login_page_returns_200          → sans DB
 * test_login_post_mauvais_credentials  → sans DB (user inexistant = credentials invalides)
 * test_logout_redirige                 → avec DB (SQLite) pour que le token soit valide
 */
final class SecurityControllerTest extends WebTestCase
{
    use DatabaseTestTrait;

    /**
     * GET /login → 200 OK avec formulaire affiché.
     * Aucune auth ni DB requise.
     */
    public function test_login_page_returns_200(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form');
        self::assertSelectorExists('input[name="email"]');
        self::assertSelectorExists('input[name="password"]');
    }

    /**
     * POST /login avec mauvais email/mdp → reste sur /login avec message d'erreur.
     * Symfony redirige vers /login puis affiche l'erreur dans la template.
     * Aucune DB requise — l'email inexistant déclenche "Invalid credentials".
     */
    public function test_login_post_mauvais_credentials(): void
    {
        $client = static::createClient();

        // Récupère le formulaire (et son token CSRF généré).
        $crawler = $client->request('GET', '/login');

        $form = $crawler->filter('form')->form([
            'email'    => 'inconnu@heritage-motors.test',
            'password' => 'mauvais_mdp_!',
        ]);

        $client->submit($form);

        // Symfony redirige vers /login après échec d'auth.
        self::assertResponseRedirects('/login');

        $client->followRedirect();

        // Toujours sur la page de login avec le formulaire.
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form');
    }

    /**
     * GET /logout avec session active → 302 vers page d'accueil ou login.
     * Vérifie que Symfony invalide la session et redirige.
     * Requiert DB SQLite pour que le token de session soit valide à la requête.
     */
    public function test_logout_redirige(): void
    {
        $client = static::createClient();

        // Prépare DB et utilisateur.
        $em   = static::getContainer()->get('doctrine.orm.entity_manager');
        $this->ensureTestSchema($em);
        $user = $this->createTestUser($em, 'logout@heritage-motors.test');

        $client->loginUser($user);
        $client->request('GET', '/logout');

        // Logout doit toujours rediriger (peu importe la cible).
        self::assertResponseRedirects();
    }
}
