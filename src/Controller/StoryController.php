<?php

namespace App\Controller;

use App\Entity\Story;
use App\Entity\UserPostgres;
use App\Form\StoryFormType;

use App\Repository\StoryRepository;
use App\Service\FirebaseImageCache;
use App\Service\FirebaseService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class StoryController extends AbstractController
{
    private FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService=$firebaseService;
    }

    #[Route('/story/new', name: 'nueva_historia')]
    public function newStory(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger,FirebaseImageCache $firebaseImageCache, CacheInterface $cache): Response
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
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
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
    #[Route('/story', name: 'story')]
    public function index(StoryRepository $historiaRepository): Response
    {
        $user = $this->getUser();

        $historias = $historiaRepository->findActiveStoriesByUser($user);

        if (!$user) {
            return $this->redirectToRoute('login');
        }

        $currentIndex = 0; // Simulamos que estamos viendo la segunda historia (índice 1)
        $previousStory = $historias[$currentIndex - 1] ?? null;
        $currentStory = $historias[$currentIndex]->getImageStory() ?? null;
        $nextStory = $historias[$currentIndex + 1] ?? null;

        return $this->render('page/story.html.twig', [
            'user' => $user,
            'previousStory' => $previousStory,
            'currentStory' => $currentStory,
            'nextStory' => $nextStory,
        ]);
    }
    #[Route('/story/{userId}', name: 'storyUser')]
    public function storyUser(StoryRepository $historiaRepository, ManagerRegistry $doctrine, int $userId): Response
    {
        $repository = $doctrine->getRepository(UserPostgres::class);
        $user = $repository->find($userId);

        $historias = $historiaRepository->findActiveStoriesByUser($user);

        if (!$user) {
            return $this->redirectToRoute('login');
        }

        $currentIndex = 0; // Simulamos que estamos viendo la segunda historia (índice 1)
        $previousStory = $historias[$currentIndex - 1] ?? null;
        $currentStory = $historias[$currentIndex]->getImageStory() ?? null;
        $nextStory = $historias[$currentIndex + 1]->getImageStory() ?? null;

        return $this->render('page/story.html.twig', [
            'stories' => $historias,
            'user' => $user,
            'previousStory' => $previousStory,
            'currentStory' => $currentStory,
            'nextStory' => $nextStory,
        ]);
    }

    #[Route('/stories', name: 'storiesUser')]
    public function getStories(ManagerRegistry $doctrine, Request $request){
        $data = json_decode($request->getContent(), true);

        if (!isset($data['userId'])) {
            return $this->json(['error' => 'Datos incompletos'], 400);
        }
        $repository = $doctrine->getRepository(UserPostgres::class);
        $user = $repository->find($data['userId']);

        $storyRepository = $doctrine->getRepository(Story::class);
        $stories = $storyRepository->findBy(['userStory' => $user]);

        $storiesArray = array_map(function($story) {
            return [
                'id' => $story->getId(),
                'image' => $story->getImageStory(),
            ];
        }, $stories);

        if($stories) {
            return $this->json([
                'status' => 200,
                'textStatus' => 'success',
                'stories' => $storiesArray
            ]);
        } else {
            return $this->json([
                'status' => 404,
                'textStatus' => 'No stories found'
            ]);
        }
    }


}
