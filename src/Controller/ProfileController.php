<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('page/profile.html.twig',['user' => $user]);
    }

    #[Route('/addFollower/{id}', name: 'add_follower')]
    public function addFollower(User $userToFollow, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        // Solo seguir si el usuario no se está siguiendo a sí mismo y si aún no lo sigue
        if ($user !== $userToFollow && !$user->getFollowing()->contains($userToFollow)) {
            $user->addFollowing($userToFollow);

            $manager = $doctrine->getManager();
            $manager->persist($user);
            $manager->flush();
        }

        return $this->redirectToRoute('page/profile.html.twig', ['id' => $userToFollow->getId()]);
    }
}
