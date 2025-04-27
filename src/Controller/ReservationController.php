<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reservation')]
class ReservationController extends AbstractController
{
    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, ReservationRepository $reservationRepository): Response
    {
        $criteria = ['user' => $this->getUser()];
        $status = $request->query->get('status');
        $search = $request->query->get('search');

        if ($status) {
            $criteria['status'] = $status;
        }

        $reservations = $reservationRepository->findBy($criteria, ['createdAt' => 'DESC']);

        // Filtrage par recherche (titre du livre uniquement)
        if ($search) {
            $reservations = array_filter($reservations, function($reservation) use ($search) {
                $bookTitle = strtolower($reservation->getBook()->getTitle());
                $search = strtolower($search);
                return str_contains($bookTitle, $search);
            });
        }

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/new/{id}', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Book $book, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur a déjà une réservation active pour ce livre
        $existingReservation = $entityManager->getRepository(Reservation::class)->findOneBy([
            'user' => $this->getUser(),
            'book' => $book,
            'status' => 'active'
        ]);

        if ($existingReservation) {
            $this->addFlash('warning', 'Vous avez déjà une réservation active pour ce livre.');
            return $this->redirectToRoute('app_book_show', ['id' => $book->getId()]);
        }

        // Vérifier si le livre est disponible
        if ($book->getQuantity() <= 0) {
            $this->addFlash('error', 'Ce livre n\'est plus disponible pour le moment.');
            return $this->redirectToRoute('app_book_show', ['id' => $book->getId()]);
        }

        $reservation = new Reservation();
        $reservation->setUser($this->getUser());
        $reservation->setBook($book);
        
        // Décrémenter la quantité disponible
        $book->setQuantity($book->getQuantity() - 1);
        
        $entityManager->persist($reservation);
        $entityManager->flush();

        $this->addFlash('success', 'Votre réservation a été enregistrée avec succès.');
        return $this->redirectToRoute('app_reservation_index');
    }

    #[Route('/{id}/cancel', name: 'app_reservation_cancel', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function cancel(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('cancel'.$reservation->getId(), $request->request->get('_token'))) {
            if ($reservation->getUser() !== $this->getUser() && !$this->isGranted('ROLE_LIBRARIAN')) {
                throw $this->createAccessDeniedException('Vous ne pouvez pas annuler cette réservation.');
            }

            if ($reservation->isActive()) {
                $reservation->setStatus('cancelled');
                $book = $reservation->getBook();
                $book->setQuantity($book->getQuantity() + 1);
                $entityManager->flush();
                $this->addFlash('success', 'La réservation a été annulée avec succès.');
            }
        }

        return $this->redirectToRoute('app_reservation_index');
    }

    #[Route('/manage', name: 'app_reservation_manage', methods: ['GET'])]
    #[IsGranted('ROLE_LIBRARIAN')]
    public function manage(Request $request, ReservationRepository $reservationRepository): Response
    {
        $status = $request->query->get('status');
        $search = $request->query->get('search');
        $criteria = [];
        if ($status) {
            $criteria['status'] = $status;
        }
        $reservations = $reservationRepository->findBy($criteria, ['createdAt' => 'DESC']);

        // Filtrage par recherche (titre du livre ou nom utilisateur)
        if ($search) {
            $reservations = array_filter($reservations, function($reservation) use ($search) {
                $bookTitle = strtolower($reservation->getBook()->getTitle());
                $userName = strtolower($reservation->getUser()->getFirstName() . ' ' . $reservation->getUser()->getLastName());
                $search = strtolower($search);
                return str_contains($bookTitle, $search) || str_contains($userName, $search);
            });
        }

        return $this->render('reservation/manage.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/{id}/complete', name: 'app_reservation_complete', methods: ['POST'])]
    #[IsGranted('ROLE_LIBRARIAN')]
    public function complete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('complete'.$reservation->getId(), $request->request->get('_token'))) {
            if ($reservation->isActive()) {
                $reservation->setStatus('completed');
                $entityManager->flush();
                $this->addFlash('success', 'La réservation a été marquée comme terminée.');
            }
        }

        return $this->redirectToRoute('app_reservation_manage');
    }
} 