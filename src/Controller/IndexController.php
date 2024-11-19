<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\UserPostgres;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(UserPostgres::class);
        $allUsers = $repository->findAll();
        $users = [];

        $repositoryPosts = $doctrine->getRepository(Post::class);
        $posts = $repositoryPosts->findAll();
        foreach ($allUsers as $user) {
            if($user != $this->getUser()) {
                $users[] = $user;
            }
        }
        return $this->render('page/index.html.twig',[
            'users'=> $users,
            'user' => $this->getUser(),
            'posts' => $posts
        ]);
    }
}
