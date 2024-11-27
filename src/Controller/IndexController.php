<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\UserPostgres;
use App\Form\CommentFormType;
use App\Service\FirebaseImageCache;
use App\Service\FirebaseService;
use App\Service\ImageService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{

    private FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    #[Route('/', name: 'index')]
    public function index(ManagerRegistry $doctrine, Request $request, ImageService $imageCache, FirebaseImageCache $firebaseImageCache): Response
    {
        $repository = $doctrine->getRepository(UserPostgres::class);
        $allUsers = $repository->findAll();
        $users = [];
        $profileImages = [];
        $repositoryPosts = $doctrine->getRepository(Post::class);
        $posts = $repositoryPosts->findAll();
        foreach ($allUsers as $user) {
            if($user != $this->getUser()) {
                $users[] = $user;
            }
            if(!$firebaseImageCache->existCachedImagen($user->getPhoto())) {
                $firebaseImageCache->getImage($user->getPhoto());
            }
            $profileImages[$user->getId()] = $imageCache->getUserProfileImage($user);
        }

        $commentForms = [];
        $images = [];
        foreach ($posts as $post) {
            if(!$firebaseImageCache->existCachedImagen($post->getPhoto())) {
                $firebaseImageCache->getImage($post->getPhoto());
            }
            $images[$post->getId()] = $imageCache->getPostImage($post);
            // Creamos una entidad de comentario nueva para cada post
            $comment = new Comment();

            // Añadimos una opción personalizada con el ID del post
            $form = $this->createForm(CommentFormType::class, $comment, [
                'action' => $this->generateUrl('index', ['post_id' => $post->getId()]),
            ]);
            $form->handleRequest($request);

            // Guardamos el formulario en un array para renderizarlo luego
            $commentForms[$post->getId()] = $form;

            // Verificamos si este formulario específico fue enviado y es válido
            if ($form->isSubmitted() && $form->isValid() && $request->query->get('post_id') == $post->getId()) {
                $comment = $form->getData();
                $comment->setPost($post);
                $comment->setUser($this->getUser());

                // Guardar el comentario en la base de datos
                $manager = $doctrine->getManager();
                $manager->persist($comment);
                $manager->flush();

                // Redirigir para evitar reenvíos
                return $this->redirectToRoute('index');
            }
        }
        return $this->render('page/index.html.twig',[
            'users'=> $users,
            'profileImage' => $profileImages,
            'user' => $this->getUser(),
            'posts' => $posts,
            'images' => $images,
            'form' =>  array_map(fn($form) => $form->createView(), $commentForms),
        ]);
    }
}
