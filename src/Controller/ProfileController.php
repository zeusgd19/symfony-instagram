<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\throwException;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('page/profile.html.twig',['user' => $user]);
    }

    #[Route('/profile/{username}', name: 'profile_page')]
    public function profile(string $username, ManagerRegistry $doctrine): Response
    {
        $user = $doctrine->getRepository(User::class)->findOneBy(['username'=>$username]);

        $allPosts = $doctrine->getRepository(Post::class)->findAll();

        $posts = [];
        $postsCount = count($posts);

        $followersCount = $doctrine->getRepository(User::class);


        foreach ($allPosts as $post){
            if(!$post){
                throw $this->createNotFoundException("No hay posts.");
            }
            if ($post->getUser() == $user){
                array_push($posts,$post);
            }
        }

        return $this->render('page/profile.html.twig',['user'=>$user,'posts'=>$posts]);
    }

    #[Route('/addFollowing/{id}', name: 'add_following')]
    public function addFollower(ManagerRegistry $doctrine, int $id): Response
    {
        $repository = $doctrine->getRepository(User::class);

        $user = $this->getUser();
        $userToFollow = $repository->find($id);

        // Solo seguir si el usuario no se está siguiendo a sí mismo y si aún no lo sigue
        if ($user != $userToFollow) {

            $user->addFollowing($userToFollow);

            $manager = $doctrine->getManager();
            $manager->persist($user);
            $manager->flush();
        }

        return $this->redirectToRoute('profile');
    }

    #[Route('/removeFollowing/{id}', name: 'remove_following')]
    public function removeFollowing(ManagerRegistry $doctrine, int $id): Response
    {
        $repository = $doctrine->getRepository(User::class);

        $user = $this->getUser();
        $userToFollow = $repository->find($id);

        // Solo seguir si el usuario no se está siguiendo a sí mismo y si aún no lo sigue
        if ($user != $userToFollow) {
            $user->removeFollowing($userToFollow);

            $manager = $doctrine->getManager();
            $manager->persist($user);
            $manager->flush();
        }

        return $this->redirectToRoute('profile');
    }
}
