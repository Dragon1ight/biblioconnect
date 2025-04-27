<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\Book;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/review')]
class ReviewController extends AbstractController
{
    #[Route('/book/{id}/new', name: 'app_review_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Book $book): Response
    {
        $review = new Review();
        $review->setUser($this->getUser());
        $review->setBook($book);
        $review->setCreatedAt(new \DateTimeImmutable());
        $review->setIsModerated(false);

        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($review);
            $entityManager->flush();

            $this->addFlash('success', 'Votre avis a été publié avec succès.');
            return $this->redirectToRoute('app_book_show', ['id' => $book->getId()]);
        }

        return $this->render('review/new.html.twig', [
            'review' => $review,
            'form' => $form,
            'book' => $book,
        ]);
    }

    #[Route('/{id}/moderate', name: 'app_review_moderate', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function moderate(Request $request, Review $review, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('moderate'.$review->getId(), $request->request->get('_token'))) {
            $review->setIsModerated(true);
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire a été modéré avec succès.');
        }

        return $this->redirectToRoute('app_review_index');
    }

    #[Route('/', name: 'app_review_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(ReviewRepository $reviewRepository, Request $request): Response
    {
        $filter = $request->query->get('filter');
        $criteria = [];

        if ($filter === 'pending') {
            $criteria['isModerated'] = false;
        } elseif ($filter === 'moderated') {
            $criteria['isModerated'] = true;
        }

        return $this->render('review/index.html.twig', [
            'reviews' => $reviewRepository->findBy($criteria, ['createdAt' => 'DESC']),
        ]);
    }
} 