<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\UserPostgres;
use App\Form\CommentFormType;
use App\Service\FirebaseImageCache;
use App\Service\FirebaseService;
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
    public function index(ManagerRegistry $doctrine, Request $request, FirebaseImageCache $imageCache): Response
    {
        $repository = $doctrine->getRepository(UserPostgres::class);
        $allUsers = $repository->findAll();
        $users = [];
        $profileImages = [];
        $base64Profile = [];
        $repositoryPosts = $doctrine->getRepository(Post::class);
        $posts = $repositoryPosts->findAll();
        foreach ($allUsers as $user) {
            if($user != $this->getUser()) {
                $users[] = $user;
            }
            $profileImages[$user->getId()] = $imageCache->getImage($user->getPhoto());
            $base64Profile[$user->getId()] = base64_encode($profileImages[$user->getId()]);
        }

        $commentForms = [];
        $images = [];
        $base64 = [];
        foreach ($posts as $post) {
            $images[$post->getId()] = $imageCache->getImage($post->getPhoto());
            $base64[$post->getId()] = base64_encode($images[$post->getId()]);
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
            'profileImage' => $base64Profile,
            'user' => $this->getUser(),
            'posts' => $posts,
            'images' => $base64,
            'form' =>  array_map(fn($form) => $form->createView(), $commentForms),
        ]);
    }
}
