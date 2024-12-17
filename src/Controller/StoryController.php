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
    public function storyUser( ManagerRegistry $doctrine, int $userId): Response
    {
        $repository = $doctrine->getRepository(UserPostgres::class);
        $user = $repository->find($userId);

        if (!$user) {
            return $this->redirectToRoute('login');
        }

        $historiaRepository = $doctrine->getRepository(Story::class);
        $historias = $historiaRepository->findActiveStoriesByUser($user);
        $allStories = $historiaRepository->findAll();

        $groupedStories = [];
        foreach ($allStories as $story) {
            $useridNew = $story->getUserStory()->getId();
            if (!isset($groupedStories[$useridNew])) {
                $groupedStories[$useridNew] = [];
            }
            $groupedStories[$useridNew][] = $story;
        }

        $orderedStories = [];
        $seenUserIds = [];
        foreach ($allStories as $story) {
            $useridNew = $story->getUserStory()->getId();
            if (!in_array($useridNew, $seenUserIds)) {
                $seenUserIds[] = $useridNew;
                $orderedStories = array_merge($orderedStories, $groupedStories[$useridNew]);
            }
        }

        $storyUserIndex = -1;
        $index = 0;
        $previousStory = null;
        $nextStory = null;
        $previousUser = null;
        foreach ($orderedStories as $story) {
            if ($historias[0]->getImageStory() === $story->getImageStory()) {
                $storyUserIndex = $index;
            }

            if ($storyUserIndex === -1) {
                $previousUser = $story->getUserStory();
            }

            if ($storyUserIndex !== -1 && $index > $storyUserIndex && $story->getUserStory()->getId() !== $userId && !$nextStory) {
                $nextStory = $story;
            }

            $index++;
        }

        if($previousUser) {
            $previousStory = $historiaRepository->findActiveStoriesByUser($previousUser)[0];
        }

        return $this->render('page/story.html.twig', [
            'stories' => $historias,
            'allStories' => $orderedStories,
            'storyUserIndex' => $storyUserIndex,
            'user' => $user,
            'previousStory' => $previousStory,
            'nextStory' => $nextStory,
        ]);
    }


    #[Route('/stories', name: 'storiesUser')]
    public function getStories(ManagerRegistry $doctrine, Request $request){
        /*
        $data = json_decode($request->getContent(), true);

        if (!isset($data['userId'])) {
            return $this->json(['error' => 'Datos incompletos'], 400);
        }
        $repository = $doctrine->getRepository(UserPostgres::class);
        $user = $repository->find($data['userId']);
        */

        $storyRepository = $doctrine->getRepository(Story::class);
        $stories = $storyRepository->findAll();

        $groupedStories = [];
        foreach ($stories as $story) {
            $userId = $story->getUserStory()->getId();
            if (!isset($groupedStories[$userId])) {
                $groupedStories[$userId] = [];
            }
            $groupedStories[$userId][] = $story;
        }

        $orderedStories = [];
        $seenUserIds = [];
        foreach ($stories as $story) {
            $userId = $story->getUserStory()->getId();
            if (!in_array($userId, $seenUserIds)) {
                $seenUserIds[] = $userId;
                $orderedStories = array_merge($orderedStories, $groupedStories[$userId]);
            }
        }

        $storiesArray = array_map(function($story) {
            return [
                'id' => $story->getId(),
                'image' => $story->getImageStory(),
                'userId' => $story->getUserStory()->getId(),
                'userPhoto' => $story->getUserStory()->getPhoto(),
                'userUsername' => $story->getUserStory()->getUsername(),
            ];
        }, $orderedStories);

        if($orderedStories) {
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
