<?php

namespace App\DataFixtures;

use App\Entity\Clients;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class ClientsFixtures extends Fixture
{
    private $passwordHasher;

    // Injection du service de hachage des mots de passe
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Ajout des clients avec des références correctes
        $client = new Clients();
        $client->setUsername('Free')
            ->setPassword($this->passwordHasher->hashPassword($client, 'bilemo2025'))
            ->setRoles(['ROLE_ADMIN'])
            ->setEmail('free@gmail.fr')
            ->setDateCreate(new \DateTime());
        $this->addReference('client_free', $client);  // Référence avec chaîne
        $manager->persist($client);

        $client = new Clients();
        $client->setUsername('SFR')
            ->setPassword($this->passwordHasher->hashPassword($client, 'bilemo2025'))
            ->setRoles(['ROLE_ADMIN'])
            ->setEmail('sfr@gmail.fr')
            ->setDateCreate(new \DateTime());
        $this->addReference('client_sfr', $client);  // Référence avec chaîne
        $manager->persist($client);

        $client = new Clients();
        $client->setUsername('Orange')
            ->setPassword($this->passwordHasher->hashPassword($client, 'bilemo2025'))
            ->setRoles(['ROLE_ADMIN'])
            ->setEmail('orange@gmail.fr')
            ->setDateCreate(new \DateTime());
        $this->addReference('client_orange', $client);  // Référence avec chaîne
        $manager->persist($client);

        $manager->flush();
    }
}
