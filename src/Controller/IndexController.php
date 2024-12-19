<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Notification;
use App\Entity\Post;
use App\Entity\Story;
use App\Entity\UserPostgres;
use App\Form\CommentFormType;
use App\Service\FirebaseImageCache;
use App\Service\FirebaseService;
use App\Service\ImageService;
use Carbon\Carbon;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

class IndexController extends AbstractController
{
    private FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    #[Route('/', name: 'index')]
    public function index(ManagerRegistry $doctrine, ImageService $imageCache, FirebaseImageCache $firebaseImageCache, CacheInterface $cache): Response
    {
        $repository = $doctrine->getRepository(UserPostgres::class);
        $manager = $doctrine->getManager();
        $allUsers = $cache->get('users_list_cache_key', function() use ($repository) {
            return array_map(function($user) {
                return [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'photo' => $user->getPhoto(),
                    'description' => $user->getDescription()
                ];
            }, $repository->findAll());
        });

        $storyRepository = $doctrine->getRepository(Story::class);
        $stories = $storyRepository->findAll();

        foreach ($stories as $story){
            if($story->getExpireDate() < new \DateTime()){
                $decodedUrl = urldecode($story->getImageStory());
                $path = parse_url($decodedUrl, PHP_URL_PATH);
                $imageName = pathinfo($path, PATHINFO_BASENAME);
                $this->firebaseService->deleteFile("stories/" . $imageName);
                $manager->remove($story);
                $manager->flush();
            }
        }

        $newStories = $storyRepository->findAll();
        $followingIds = array_map(fn($user) => $user->getId(), $this->getUser()->getFollowing()->toArray());

        $users = [];
        $isFollowing = [];
        $profileImages = [];

        foreach ($allUsers as $user) {
            if ($user['id'] == $this->getUser()->getId()) {
                $profileImages[$user['id']] = $imageCache->getUserProfileImage($user['photo']);
                continue; // Saltar al usuario actual
            }


            $users[] = $user;

            $isFollowing[$user['id']] = in_array($user['id'], $followingIds);

            if (!$firebaseImageCache->existCachedImagen($user['photo'])) {
                $firebaseImageCache->getImage($user['photo']);
            }
            $profileImages[$user['id']] = $imageCache->getUserProfileImage($user['photo']);
        }

        $repositoryPosts = $doctrine->getRepository(Post::class);

        $allPosts = $cache->get('posts_list_cache_key', function() use ($repositoryPosts) {
            return array_map(function($post) {
                return [
                    'id' => $post->getId(),
                    'photo' => $post->getPhoto(),
                    'description' => $post->getDescription(),
                    'createdAt' => $post->getCreatedAt(),
                    'user' =>  [
                        'id' => $post->getUser()->getId(),
                        'username' => $post->getUser()->getUsername(),
                        'photo' => $post->getUser()->getPhoto(),
                    ],
                    'likedBy' => array_map(function($user) {
                        return [
                            'id' => $user->getId(),
                            'username' => $user->getUsername(),
                            'photo' => $user->getPhoto(),
                        ];
                    }, $post->getLikedBy()->toArray()),
                    'savedBy' => array_map(function($user) {
                        return [
                            'id' => $user->getId(),
                            'username' => $user->getUsername(),
                            'photo' => $user->getPhoto(),
                        ];
                    }, $post->getSavedBy()->toArray())
                ];
            }, $repositoryPosts->findAll());
        });

        $images = [];
        $posts = [];
        $isLikedByUser = [];
        $isSavedByUser = [];
        $timeElapsed = [];
        foreach ($allPosts as $post) {
            $posts[] = $post;
            $timeElapsed[$post['id']] = Carbon::parse($post['createdAt'])->diffForHumans();
            $isLikedByUser[$post['id']] = in_array($this->getUser()->getId(), array_column($post['likedBy'], 'id'));
            $isSavedByUser[$post['id']] = in_array($this->getUser()->getId(), array_column($post['savedBy'], 'id'));

            if (!$firebaseImageCache->existCachedImagen($post['photo'])) {
                $firebaseImageCache->getImage($post['photo']);
            }
            $images[$post['id']] = $imageCache->getPostImage($post['photo']);
        }

        $notificationRepository = $doctrine->getRepository(Notification::class);

        return $this->render('page/index.html.twig', [
            'users' => $users,
            'profileImage' => $profileImages,
            'user' => $this->getUser(),
            'posts' => $posts,
            'images' => $images,
            'isLikedByUser' => $isLikedByUser,
            'isSavedByUser' => $isSavedByUser,
            'isFollowing' => $isFollowing,
            'timeElapsed' => $timeElapsed,
            'stories' => $newStories,
            'notification' => $notification
        ]);
    }



}
