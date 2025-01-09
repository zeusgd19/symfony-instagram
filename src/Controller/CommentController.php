<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Notification;
use App\Entity\Post;
use App\Service\ImageService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

class CommentController extends AbstractController{

    #[Route('/comment/new', name: 'new_comment')]
    public function newComment(ManagerRegistry $doctrine, Request $request,CacheInterface $cache){
        $data = json_decode($request->getContent(), true);

        if (!isset($data['postId'], $data['comment'])) {
            return $this->json(['error' => 'Datos incompletos'], 400);
        }

        $manager = $doctrine->getManager();
        $postRepository = $doctrine->getRepository(Post::class);

        // Busca al receptor
        $post = $postRepository->find($data['postId']);
        if (!$post) {
            throw $this->createNotFoundException('Usuario receptor no encontrado');
        }

        // Crea el mensaje
        $comment = new Comment();
        $comment->setUser($this->getUser());
        $comment->setPost($post);
        $comment->setText($data['comment']);

        $notification = new Notification();
        $notification->setGeneratedNotifyBy($this->getUser());
        $notification->setNotifiedUser($post->getUser());
        $notification->setType('comment');
        $notification->setContentComment($data['comment']);
        $notification->setPost($post);

        if($notification->getGeneratedNotifyBy() !== $notification->getNotifiedUser()){
            $manager->persist($notification);
        }
        $manager->persist($comment);
        $manager->flush();

        $cache->delete('users_list_cache_key');
        $cache->delete('posts_list_cache_key');

        return $this->json([
            'commentId' => $comment->getId(),
            'commentUserPhoto' => $comment->getUser()->getPhoto(),
            'commentUserUsername' => $comment->getUser()->getUsername(),
            'commentPostId' => $comment->getPost()->getId(),
            'comment' => $comment->getText()
        ]);
    }

    #[Route('/comments/{PostId}', name: 'comments')]
    public function getComments(int $PostId,ManagerRegistry $doctrine,ImageService $imageCache){
        $respository = $doctrine->getRepository(Comment::class);
        $postRepository = $doctrine->getRepository(Post::class);
        $post = $postRepository->find($PostId);
        $comments = $respository->findBy(['post' => $post]);
        $image = $imageCache->getPostImage($post->getPhoto());
        $profilePostUser = $imageCache->getUserProfileImage($post->getUser()->getPhoto());

        $profileCommentUsers = [];
        foreach ($comments as $comment){
            $profileCommentUsers[$comment->getId()] = $imageCache->getUserProfileImage($comment->getUser()->getPhoto());
        }
        return $this->render('partials/_comment-modal.html.twig', ['comments' => $comments, 'post' => $post, 'image' => $image,'userImage' => $profilePostUser,'commentPhoto'=>$profileCommentUsers]);
    }
}
