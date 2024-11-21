<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\UserPostgres;
use App\Form\CommentFormType;
use App\Form\PostFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostController extends AbstractController
{
    #[Route('/post/new', name: 'new_post', methods: 'POST')]
    public function index(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
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

                // Move the file to the directory where images are stored
                try {

                    $file->move(
                        $this->getParameter('images_directory'), $newFilename
                    );

                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'file$filename' property to store the PDF file name
                // instead of its contents
                $post->setPhoto($newFilename);
            }
            $post = $formulario->getData();
            $post->setUser($this->getUser());
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

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
    public function deletePost(int $id, ManagerRegistry $doctrine){
        $repository = $doctrine->getRepository(Post::class);
        $post = $repository->find($id);
        $manager = $doctrine->getManager();

        if ($post) {
            // Obtenemos la ruta de la imagen desde el directorio donde las guardas
            $imagePath = $this->getParameter('images_directory') . '/' . $post->getPhoto();

            // Verificamos si el archivo existe y lo eliminamos
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Eliminamos el post de la base de datos
            $manager->remove($post);
            $manager->flush();
        }

        // Renderiza solo el post eliminado o una vista vacía para que desaparezca
        return $this->json(['success' => true, 'id' => $id]);
    }
}
