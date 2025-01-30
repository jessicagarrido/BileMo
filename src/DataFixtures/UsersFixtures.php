<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UsersFixtures extends Fixture implements DependentFixtureInterface
{
    private $passwordHasher;

    // Injection du service de hachage des mots de passe
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Liste des noms des références définies dans ClientsFixtures
        $clientReferences = ['client_free', 'client_sfr', 'client_orange'];

        for ($i = 1; $i < 20; $i++) {
            $user = new Users();
            $user->setUsername('username_' . $i)
                ->setPassword($this->passwordHasher->hashPassword($user, 'bilemo2025')) // Hachage du mot de passe
                ->setRoles(['ROLE_USER'])
                ->setEmail('username_' . $i . '@gmail.fr')
                ->setLastname('lastname_' . $i)
                ->setFirstname('firstname_' . $i)
                ->setDateCreate(new \DateTime())
                ->setClient($this->getReference($clientReferences[rand(0, 2)])); // Utilisation correcte de getReference

            $manager->persist($user);
        }

        $manager->flush();
    }

    // Assurez-vous que ClientsFixtures est chargé avant UsersFixtures
    public function getDependencies(): array
    {
        return [
            ClientsFixtures::class, // Assurez-vous que ClientsFixtures est chargé avant UsersFixtures
        ];
    }
}
