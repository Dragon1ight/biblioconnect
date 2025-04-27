<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\RentalRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(
        BookRepository $bookRepository,
        UserRepository $userRepository,
        RentalRepository $rentalRepository
    ): Response
    {
        return $this->render('admin/index.html.twig', [
            'totalBooks' => $bookRepository->count([]),
            'totalUsers' => $userRepository->count([]),
            'totalRentals' => $rentalRepository->count([]),
            'activeRentals' => $rentalRepository->count(['returned' => false]),
        ]);
    }
} 