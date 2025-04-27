<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/new/{id}', name: 'app_comment_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $comment->setBook($book);
        $comment->setUser($this->getUser());
        
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Votre commentaire a été enregistré et sera visible après modération.');
            return $this->redirectToRoute('app_book_show', ['id' => $book->getId()]);
        }

        return $this->render('comment/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/moderate', name: 'app_comment_moderate', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function moderate(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findBy(
            ['isApproved' => false],
            ['createdAt' => 'DESC']
        );

        return $this->render('comment/moderate.html.twig', [
            'comments' => $comments,
        ]);
    }

    #[Route('/{id}/approve', name: 'app_comment_approve', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function approve(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('approve'.$comment->getId(), $request->request->get('_token'))) {
            $comment->setIsApproved(true);
            $entityManager->flush();
            
            $this->addFlash('success', 'Le commentaire a été approuvé.');
        }

        return $this->redirectToRoute('app_comment_moderate');
    }

    #[Route('/{id}/reject', name: 'app_comment_reject', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function reject(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('reject'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
            
            $this->addFlash('success', 'Le commentaire a été rejeté et supprimé.');
        }

        return $this->redirectToRoute('app_comment_moderate');
    }

    #[Route('/{id}', name: 'app_comment_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Le commentaire a été supprimé avec succès.');
        }
        return $this->redirectToRoute('app_book_show', ['id' => $comment->getBook()->getId()]);
    }
} 