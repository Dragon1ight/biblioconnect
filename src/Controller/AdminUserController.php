<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users')]
#[IsGranted('ROLE_ADMIN')]
class AdminUserController extends AbstractController
{
    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/{id}/role', name: 'app_admin_user_role', methods: ['POST'])]
    public function changeRole(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('role'.$user->getId(), $request->request->get('_token'))) {
            $role = $request->request->get('role');
            
            // Vérifier que le rôle est valide
            if (in_array($role, ['ROLE_USER', 'ROLE_LIBRARIAN', 'ROLE_ADMIN'])) {
                // Ne pas permettre de modifier son propre rôle
                if ($user === $this->getUser()) {
                    $this->addFlash('error', 'Vous ne pouvez pas modifier votre propre rôle.');
                    return $this->redirectToRoute('app_admin_user_index');
                }

                $user->setRoles([$role]);
                $entityManager->flush();

                $this->addFlash('success', 'Le rôle de l\'utilisateur a été modifié avec succès.');
            }
        }

        return $this->redirectToRoute('app_admin_user_index');
    }

    #[Route('/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            // Ne pas permettre de supprimer son propre compte
            if ($user === $this->getUser()) {
                $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
                return $this->redirectToRoute('app_admin_user_index');
            }

            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_admin_user_index');
    }
} 