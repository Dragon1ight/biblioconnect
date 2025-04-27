<?php

namespace App\DataFixtures;

use App\Entity\Reservation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReservationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Réservation active pour l'utilisateur standard
        $reservation1 = new Reservation();
        $reservation1->setUser($this->getReference('user-regular'))
            ->setBook($this->getReference('book-les-miserables'))
            ->setStatus('active');
        $manager->persist($reservation1);

        // Réservation terminée pour le bibliothécaire
        $reservation2 = new Reservation();
        $reservation2->setUser($this->getReference('user-librarian'))
            ->setBook($this->getReference('book-madame-bovary'))
            ->setStatus('completed');
        $manager->persist($reservation2);

        // Réservation annulée pour l'administrateur
        $reservation3 = new Reservation();
        $reservation3->setUser($this->getReference('user-admin'))
            ->setBook($this->getReference('book-germinal'))
            ->setStatus('cancelled');
        $manager->persist($reservation3);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            BookFixtures::class,
        ];
    }
} 