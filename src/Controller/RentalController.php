<?php

namespace App\Controller;

use App\Entity\Rental;
use App\Repository\RentalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/rental')]
class RentalController extends AbstractController
{
    #[Route('/', name: 'app_rental_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(RentalRepository $rentalRepository): Response
    {
        return $this->render('rental/index.html.twig', [
            'rentals' => $rentalRepository->findAll(),
        ]);
    }

    #[Route('/admin', name: 'app_rental_admin', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function admin(RentalRepository $rentalRepository): Response
    {
        return $this->render('rental/admin.html.twig', [
            'rentals' => $rentalRepository->findAll(),
        ]);
    }
} 