<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Notification;
use App\Entity\Post;
use App\Form\PostFormType;
use App\Service\FirebaseImageCache;
use App\Service\FirebaseService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class PostController extends AbstractController
{

    private FirebaseService $firebaseService;


    public function __construct(FirebaseService $firebaseService,FirebaseImageCache $firebaseImageCache)
    {
        $this->firebaseService = $firebaseService;
    }
    #[Route('/post/new', name: 'new_post', methods: 'POST')]
    public function index(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger,FirebaseImageCache $firebaseImageCache, CacheInterface $cache): Response
    {

        $post = new Post();
        $formulario = $this->createForm(PostFormType::class,$post);

        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()){
            $file = $formulario->get('photo')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                $firebasePath = 'posts/' . $newFilename;
                // Move the file to the directory where images are stored
                try {
                    $firebaseUrl = $this->firebaseService->uploadFile($file->getPathname(),$firebasePath);

                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'file$filename' property to store the PDF file name
                // instead of its contents
                $post->setPhoto($firebaseUrl);
            }
            $post = $formulario->getData();
            $post->setUser($this->getUser());
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $cache->delete('posts_list_cache_key');

            if(!$firebaseImageCache->existCachedImagen($post->getPhoto())) {
                $firebaseImageCache->getImage($post->getPhoto());
            }
            return $this->json([
                'status' => 'success',
                'message' => 'Post creado con éxito'
            ]);
        }

        return $this->render('partials/_postForm.html.twig', [
            'form' => $formulario->createView()
        ]);
    }

    #[Route('/post/delete/{id}', name: 'delete_post')]
    public function deletePost(int $id, ManagerRegistry $doctrine,CacheInterface $cache): JsonResponse
    {
        $repository = $doctrine->getRepository(Post::class);
        $post = $repository->find($id);
        $manager = $doctrine->getManager();
        $repositoryComments = $doctrine->getRepository(Comment::class);
        $comments = $repositoryComments->findAll();

        if ($post) {
            $decodedUrl = urldecode($post->getPhoto());
            $path = parse_url($decodedUrl, PHP_URL_PATH);
            $imageName = pathinfo($path, PATHINFO_BASENAME);

            $this->firebaseService->deleteFile("posts/" . $imageName);

            foreach ($comments as $comment){
                if($post == $comment->getPost()){
                    $manager->remove($comment);
                    $manager->flush();
                }
            }

            // Eliminamos el post de la base de datos
            $manager->remove($post);
            $manager->flush();

            $cache->delete('posts_list_cache_key');
        }

        return $this->json(['success' => true, 'id' => $id]);
    }

    #[Route('/addLike/{postId}', name: 'addLike_post')]
    public function addLike(int $postId,ManagerRegistry $doctrine, CacheInterface $cache){
        $repository = $doctrine->getRepository(Post::class);
        $manager = $doctrine->getManager();
        $post = $repository->find($postId);
        $user = $this->getUser();
        if($post){
            $user->addLikedPost($post);
            $manager->persist($user);

            $notification =  new Notification();
            $notification->setType('like');
            $notification->setGeneratedNotifyBy($this->getUser());
            $notification->setNotifiedUser($post->getUser());
            $manager->persist($notification);
            $manager->flush();
        }
        $totalLikes = count($post->getLikedBy());

        $cache->delete('posts_list_cache_key');

        $likedBYYou = $post->getLikedBy()->contains($user);

        return $this->json(['totalLikes' => $totalLikes,'likedBYYou' => $likedBYYou]);
    }

    #[Route('/removeLike/{postId}', name: 'removeLike_post')]
    public function removeLike(int $postId,ManagerRegistry $doctrine,CacheInterface $cache){
        $repository = $doctrine->getRepository(Post::class);
        $manager = $doctrine->getManager();
        $post = $repository->find($postId);
        $user = $this->getUser();
        if($post){
            $user->removeLikedPost($post);
            $manager->persist($user);
            $manager->flush();
        }
        $totalLikes = count($post->getLikedBy());
        $cache->delete('posts_list_cache_key');
        $likedBYYou = $post->getLikedBy()->contains($user);

        return $this->json(['totalLikes' => $totalLikes,'likedBYYou' => $likedBYYou]);
    }

    #[Route('/addSave/{postId}', name: 'addSave_post')]
    public function addSave(int $postId,ManagerRegistry $doctrine, CacheInterface $cache){
        $repository = $doctrine->getRepository(Post::class);
        $manager = $doctrine->getManager();
        $post = $repository->find($postId);
        $user = $this->getUser();
        if($post){
            $user->addSavedPost($post);
            $manager->persist($user);
            $manager->flush();
        }
        $cache->delete('posts_list_cache_key');
        $savedBYYou = $post->getSavedBy()->contains($user);

        return $this->json(['savedBYYou' => $savedBYYou]);
    }

    #[Route('/removeSave/{postId}', name: 'removeSave_post')]
    public function removeSave(int $postId,ManagerRegistry $doctrine, CacheInterface $cache){
        $repository = $doctrine->getRepository(Post::class);
        $manager = $doctrine->getManager();
        $post = $repository->find($postId);
        $user = $this->getUser();
        if($post){
            $user->removeSavedPost($post);
            $manager->persist($user);
            $manager->flush();
        }
        $cache->delete('posts_list_cache_key');
        $savedBYYou = $post->getSavedBy()->contains($user);

        return $this->json(['savedBYYou' => $savedBYYou]);
    }
}
