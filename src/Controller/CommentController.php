<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController{
    #[Route('/comments/{PostId}', name: 'comments')]
    public function getComments(int $PostId,ManagerRegistry $doctrine){
        $respository = $doctrine->getRepository(Comment::class);
        $postRepository = $doctrine->getRepository(Post::class);
        $post = $postRepository->find($PostId);
        $comments = $respository->findBy(['post' => $post]);
        return $this->render('partials/_comment-modal.html.twig', ['comments' => $comments, 'post' => $post]);
    }
}
