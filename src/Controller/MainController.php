<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Rental;
use App\Form\FilterBooksType;
use App\Form\RentBookType;
use App\Form\ReviewType;
use App\Message\RentBook;
use App\Service\EdifactService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {

        return $this->render('frontend/main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    #[Route('/catalog/browse', name: 'app_catalog_browse')]
    public function catalog(
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        $form = $this->createForm(FilterBooksType::class, [], [
            'action' => $this->generateUrl('app_catalog_browse'),
            'method' => 'GET',
        ]);
        $form->handleRequest($request);
        $catalog = $entityManager->getRepository(Book::class)->findAll();

        if ($form->isSubmitted()) {
            $searchPattern = $form->getData()['searchPattern'];
            $catalog = $entityManager->getRepository(Book::class)->findByAuthorOrTitle($searchPattern);

            return $this->render('frontend/main/catalog/browse.html.twig', [
                'catalog' => $catalog,
                'form' => $form->createView()
            ]);
        }

        return $this->render('frontend/main/catalog/browse.html.twig', [
            'catalog' => $catalog,
            'form' => $form->createView()
        ]);
    }

    #[Route('/catalog/rent', name: 'app_catalog_rent')]
    public function catalogRent(
        Request $request,
        MessageBusInterface $bus,
        EdifactService $edifactService
    ): Response
    {
        $form = $this->createForm(RentBookType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            /** @var Rental $rental */
            $rental = $form->getData();
            $user = $this->getUser();
            if (!$user) {
                throw $this->createAccessDeniedException('Vous devez être connecté pour emprunter un livre.');
            }

            $message = $edifactService->generateEDIFACTRentMessage(
                $rental->getRentFrom()?->format('Ymd'),
                $rental->getRentTo()?->format('Ymd'), 
                $user->getUserIdentifier(),
                $rental->getBook()?->getId()
            );

            // Publikacja wiadomości na kolejce
            $bus->dispatch(new RentBook($message));

            $this->addFlash('success', 'Excellent! La location a été effectuée avec succès, vous pouvez la visualiser sur la page de location');
            return $this->redirectToRoute('app_rents', []);

        }

        return $this->render('frontend/main/catalog/rent.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/book/{id}/details', name: 'app_book_details')]
    public function bookDetails(
        Book $book,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(ReviewType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $review = $form->getData();
            $review->setBook($book);
            $review->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute('app_book_details', ['id' => $book->getId()]);
        }
        return $this->render('frontend/main/book/details.html.twig', [
            'book' => $book,
            'form' => $form->createView()
        ]);
    }

    #[Route('/rents', name: 'app_rents')]
    public function rents(
    ): Response
    {

        return $this->render('frontend/main/rent/index.html.twig', [

        ]);
    }

    #[Route('/rents/{id}/return', name: 'app_book_return')]
    public function return(
        Rental $rental,
        EntityManagerInterface $entityManager
    ): Response
    {

        $rental->setReturned(true);
        $entityManager->persist($rental);

        $bookAvailable = $rental->getBook()->getAvailability();

        $bookAvailable->setAvailable($bookAvailable->getAvailable() + 1);
        $entityManager->persist($bookAvailable);

        $entityManager->flush();

        $this->addFlash('success', 'Merci d\'avoir rendu le livre !');
        return $this->redirectToRoute('app_rents', []);
    }
}
