<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(
        BookRepository $bookRepository,
        ReservationRepository $reservationRepository,
        UserRepository $userRepository
    ): Response {
        // Statistiques des livres
        $totalBooks = $bookRepository->count([]);
        $lowStockBooks = $bookRepository->findLowStockBooks();
        $booksByCategory = $bookRepository->countByCategory();
        $booksByLanguage = $bookRepository->countByLanguage();

        // Statistiques des réservations
        $totalReservations = $reservationRepository->count([]);
        $activeReservations = $reservationRepository->countActiveReservations();
        $reservationsByStatus = $reservationRepository->countByStatus();

        // Statistiques des utilisateurs
        $totalUsers = $userRepository->count([]);
        $usersByRole = $userRepository->countByRole();

        return $this->render('dashboard/index.html.twig', [
            'totalBooks' => $totalBooks,
            'lowStockBooks' => $lowStockBooks,
            'booksByCategory' => $booksByCategory,
            'booksByLanguage' => $booksByLanguage,
            'totalReservations' => $totalReservations,
            'activeReservations' => $activeReservations,
            'reservationsByStatus' => $reservationsByStatus,
            'totalUsers' => $totalUsers,
            'usersByRole' => $usersByRole,
        ]);
    }
} 