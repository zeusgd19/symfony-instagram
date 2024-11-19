<?php

namespace App\Controller;

use App\Entity\UserPostgres;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search/{username}', name: 'search')]
    public function searchUsers(string $username, ManagerRegistry $doctrine){

        $repository = $doctrine->getRepository(UserPostgres::class);
        $query = $repository->createQueryBuilder('u')
            ->where('u.username LIKE :username')
            ->setParameter('username', $username . '%') // Comienza con o coincide
            ->getQuery();

        $users = $query->getResult();

        return $this->render('page/search.html.twig',[
            'users'=>$users
        ]);
    }
}
