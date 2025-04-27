<?php

namespace App\MessageHandler;

use App\Message\RentBook;
use App\Entity\Rental;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RentBookHandler
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(RentBook $rentBook)
    {
        $data = $rentBook->getData();
        $rental = new Rental();
        $rental->setBook($data['book']);
        $rental->setUserId($data['user']);
        $rental->setRentFrom($data['rentFrom']);
        $rental->setRentTo($data['rentTo']);
        $rental->setReturned(false);
        $this->entityManager->persist($rental);
        $this->entityManager->flush();
    }
} 