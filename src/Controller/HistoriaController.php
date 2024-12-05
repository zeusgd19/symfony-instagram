<?php

namespace App\Controller;

use App\Entity\Story;
use App\Form\StoryFormType;
use App\Service\FirebaseImageCache;
use App\Service\FirebaseService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class HistoriaController extends AbstractController
{
    private FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService=$firebaseService;
    }

    #[Route('/story/new', name: 'nueva_historia')]
    public function index(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger,FirebaseImageCache $firebaseImageCache, CacheInterface $cache): Response
    {

        $story = new Story();
        $formulario = $this->createForm(StoryFormType::class,$story);

        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()){
            $file = $formulario->get('photo')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                $firebasePath = 'stories/' . $newFilename;
                // Move the file to the directory where images are stored
                try {
                    $firebaseUrl = $this->firebaseService->uploadFile($file->getPathname(),$firebasePath);

                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'file$filename' property to store the PDF file name
                // instead of its contents
                $story->setImageStory($firebaseUrl);
            }
            $story = $formulario->getData();
            $story->setUserStory($this->getUser());
            $entityManager = $doctrine->getManager();
            $entityManager->persist($story);
            $entityManager->flush();

            return $this->json([
                'status' => 'success',
                'message' => 'Post creado con éxito'
            ]);
        }

        return $this->render('partials/_storyForm.html.twig', [
            'form' => $formulario->createView()
        ]);
    }
}
