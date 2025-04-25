<?php

namespace App\DataFixtures;

use App\Entity\Clients;
use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $clients = [
        ];

        $client = new Clients();
        $client->setUsername('Free')
            ->setPassword($this->passwordHasher->hashPassword($client, 'bilemo2025'))
            ->setRoles(['ROLE_ADMIN'])
            ->setEmail('free@gmail.fr')
            ->setDateCreate(new \DateTime());
        $manager->persist($client);
        $clients[] = $client;
        $client = new Clients();
        $client->setUsername('SFR')
            ->setPassword($this->passwordHasher->hashPassword($client, 'bilemo2025'))
            ->setRoles(['ROLE_ADMIN'])
            ->setEmail('sfr@gmail.fr')
            ->setDateCreate(new \DateTime());
        $manager->persist($client);
        $clients[] = $client;
        $client = new Clients();
        $client->setUsername('Orange')
            ->setPassword($this->passwordHasher->hashPassword($client, 'bilemo2025'))
            ->setRoles(['ROLE_ADMIN'])
            ->setEmail('orange@gmail.fr')
            ->setDateCreate(new \DateTime());
        $manager->persist($client);
        $clients[] = $client;

        for ($i = 1; $i < 20; ++$i) {
            $user = new Users();
            $user->setUsername('username_'.$i)
                ->setPassword($this->passwordHasher->hashPassword($user, 'bilemo2025'))
                ->setRoles(['ROLE_USER'])
                ->setEmail('username_'.$i.'@gmail.fr')
                ->setLastname('lastname_'.$i)
                ->setFirstname('firstname_'.$i)
                ->setDateCreate(new \DateTime())
                ->setClient($clients[rand(0, count($clients) - 1)]);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
