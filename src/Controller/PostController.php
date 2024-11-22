<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\PostFormType;
use App\Service\FirebaseService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostController extends AbstractController
{

    private FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    #[Route('/post/new', name: 'new_post', methods: 'POST')]
    public function index(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger, FirebaseService $firebaseService): Response
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
    public function deletePost(int $id, ManagerRegistry $doctrine): JsonResponse
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
        }

        return $this->json(['success' => true, 'id' => $id]);
    }
}
