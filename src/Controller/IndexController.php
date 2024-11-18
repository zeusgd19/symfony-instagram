<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $repository = $doctrine->getRepository(User::class);
        $allUsers = $repository->findAll();
        $users = [];

        $repositoryPosts = $doctrine->getRepository(Post::class);
        $posts = $repositoryPosts->findAll();
        foreach ($allUsers as $user) {
            if($user != $this->getUser()) {
                $users[] = $user;
            }
        }

        $commentForms = [];

        foreach ($posts as $post) {
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
            'user' => $this->getUser(),
            'posts' => $posts,
            'form' =>  array_map(fn($form) => $form->createView(), $commentForms),
        ]);
    }
}
