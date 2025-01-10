<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UsersFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [];

        for ($i = 0; $i < 50; $i++) {
            $user = new Users();
            $user->setUsername('user'.$i)
            ->setLastname('lastname'.$i)
            ->setFirstname('firstname'.$i)
                ->setEmail('user'.$i.'@gmail.com')
                ->setPassword($this->passwordHasher->hashPassword($user, 'bilemo2025'))
                ->setDateCreate(new \Datetime)
                ->setRoles(['ROLE_USER']);
            $manager->persist($user);
            $users[] = $user;

    }
        $manager->flush();

    }

}