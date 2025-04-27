<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Form\CommentType;
use App\Repository\BookRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/book')]
class BookController extends AbstractController
{
    #[Route('/', name: 'app_book_index', methods: ['GET'])]
    public function index(Request $request, BookRepository $bookRepository): Response
    {
        $search = $request->query->get('search');
        $category = $request->query->get('category');
        $language = $request->query->get('language');

        $criteria = [];
        if ($category) {
            $criteria['category'] = $category;
        }
        if ($language) {
            $criteria['language'] = $language;
        }

        $books = $search 
            ? $bookRepository->findBySearch($search) 
            : $bookRepository->findBy($criteria, ['title' => 'ASC']);

        return $this->render('book/index.html.twig', [
            'books' => $books,
            'categories' => $bookRepository->findUniqueCategories(),
            'languages' => $bookRepository->findUniqueLanguages(),
            'selectedCategory' => $category,
            'selectedLanguage' => $language,
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'app_book_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LIBRARIAN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();

            $this->addFlash('success', 'Le livre a été ajouté avec succès.');

            return $this->redirectToRoute('app_book_index');
        }

        return $this->render('book/new.html.twig', [
            'book' => $book,
            'form' => $form,
            'edit_mode' => false,
        ]);
    }

    #[Route('/{id}', name: 'app_book_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Book $book, CommentRepository $commentRepository, EntityManagerInterface $entityManager): Response
    {
        $comment = new \App\Entity\Comment();
        $comment->setBook($book);
        $comment->setUser($this->getUser());

        $commentForm = $this->createForm(\App\Form\CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Votre commentaire a été enregistré et sera visible après modération.');
            return $this->redirectToRoute('app_book_show', ['id' => $book->getId()]);
        }

        $approvedComments = $commentRepository->findByBook($book);

        return $this->render('book/show.html.twig', [
            'book' => $book,
            'commentForm' => $commentForm->createView(),
            'comments' => $approvedComments,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_book_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LIBRARIAN')]
    public function edit(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le livre a été modifié avec succès.');

            return $this->redirectToRoute('app_book_index');
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
            'edit_mode' => true,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_book_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $book->getId(), $request->request->get('_token'))) {
            $entityManager->remove($book);
            $entityManager->flush();

            $this->addFlash('success', 'Le livre a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_book_index');
    }
}
