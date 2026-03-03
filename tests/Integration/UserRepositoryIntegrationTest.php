<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Entity\User;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class UserRepositoryIntegrationTest extends KernelTestCase
{
    public function testPersistAndFindUserByEmail(): void
    {
        self::bootKernel();

        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        $user = (new User())
            ->setEmail('integration@example.com')
            ->setUsername('integration-user')
            ->setKeycloakId('kc-integration-id')
            ->setRoles(['ROLE_USER']);

        $entityManager->persist($user);
        $entityManager->flush();

        $found = $entityManager->getRepository(User::class)->findOneBy(['email' => 'integration@example.com']);

        self::assertNotNull($found);
        self::assertSame('integration-user', $found->getUsername());
    }
}
