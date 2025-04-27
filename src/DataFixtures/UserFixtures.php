<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public const USERS = [
        [
            'email' => 'admin@biblioconnect.fr',
            'roles' => ['ROLE_ADMIN'],
            'firstname' => 'Admin',
            'lastname' => 'Principal',
            'reference' => 'user-admin',
            'address' => '1 rue de la Bibliothèque, 75001 Paris'
        ],
        [
            'email' => 'librarian@biblioconnect.fr',
            'roles' => ['ROLE_LIBRARIAN'],
            'firstname' => 'Librarian',
            'lastname' => 'Manager',
            'reference' => 'user-librarian',
            'address' => '2 rue des Livres, 75002 Paris'
        ],
        [
            'email' => 'user@biblioconnect.fr',
            'roles' => ['ROLE_USER'],
            'firstname' => 'User',
            'lastname' => 'Regular',
            'reference' => 'user-regular',
            'address' => '3 rue des Lecteurs, 75003 Paris'
        ]
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS as $userData) {
            $user = new User();
            $user->setEmail($userData['email'])
                ->setRoles($userData['roles'])
                ->setFirstname($userData['firstname'])
                ->setLastname($userData['lastname'])
                ->setAddress($userData['address']);

            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                'password'
            );
            $user->setPassword($hashedPassword);

            $this->addReference($userData['reference'], $user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
