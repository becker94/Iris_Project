<?php

namespace App\Tests\Support;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Trait partagé par les tests qui ont besoin de créer des utilisateurs en base.
 * Utilise la base SQLite définie dans .env.test (var/test.db).
 */
trait DatabaseTestTrait
{
    /**
     * Recrée le schéma complet dans la base de test (drop + create).
     * À appeler une fois après createClient() dans chaque test qui touche la DB.
     */
    protected function ensureTestSchema(EntityManagerInterface $em): void
    {
        $tool     = new SchemaTool($em);
        $metadata = $em->getMetadataFactory()->getAllMetadata();

        try {
            $tool->dropSchema($metadata);
        } catch (\Throwable) {
            // Le schéma n'existait pas encore — pas de problème.
        }

        $tool->createSchema($metadata);
    }

    /**
     * Crée et persiste un utilisateur de test avec ROLE_USER.
     * L'EntityManager passé doit être celui du kernel actif (static::getContainer()).
     */
    protected function createTestUser(
        EntityManagerInterface $em,
        string $email    = 'test@heritage-motors.test',
        string $password = 'Test_Password_123!'
    ): User {
        /** @var UserPasswordHasherInterface $hasher */
        $hasher = static::getContainer()->get('security.user_password_hasher');

        $user = new User();
        $user->setEmail($email);
        $user->setNom('Dupont');
        $user->setPrenom('Jean');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($hasher->hashPassword($user, $password));

        $em->persist($user);
        $em->flush();

        return $user;
    }
}
