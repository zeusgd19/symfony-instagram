<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Service\ImageService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController{
    #[Route('/comments/{PostId}', name: 'comments')]
    public function getComments(int $PostId,ManagerRegistry $doctrine,ImageService $imageCache){
        $respository = $doctrine->getRepository(Comment::class);
        $postRepository = $doctrine->getRepository(Post::class);
        $post = $postRepository->find($PostId);
        $comments = $respository->findBy(['post' => $post]);
        $image = $imageCache->getPostImage($post);
        $profilePostUser = $imageCache->getUserProfileImage($post->getUser());

        $profileCommentUsers = [];
        foreach ($comments as $comment){
            $profileCommentUsers[$comment->getId()] = $imageCache->getUserProfileImage($comment->getUser());
        }
        return $this->render('partials/_comment-modal.html.twig', ['comments' => $comments, 'post' => $post, 'image' => $image,'userImage' => $profilePostUser,'commentPhoto'=>$profileCommentUsers]);
    }
}
