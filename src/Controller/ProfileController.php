<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\UserPostgres;
use App\Form\UserFormType;
use App\Service\FirebaseImageCache;
use App\Service\FirebaseService;
use App\Service\ImageService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileController extends AbstractController
{

    private FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    #[Route('/profile/{username}', name: 'profile_page')]
    public function profile(string $username, ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger,ImageService $imageCache): Response
    {
        $user = $doctrine->getRepository(UserPostgres::class)->findOneBy(['username'=>$username]);

        $allPosts = $doctrine->getRepository(Post::class)->findAll();

        $posts = [];

        foreach ($allPosts as $post){
            if(!$post){
                throw $this->createNotFoundException("No hay posts.");
            }
            if ($post->getUser() == $user){
                $posts[] = $post;
            }
        }
        $formulario = $this->createForm(UserFormType::class,$user);
        $formulario->handleRequest($request);


        if($formulario->isSubmitted() && $formulario->isValid()){
            $file = $formulario -> get ('photo') -> getData();

            if ($file){
                $originalFileName = pathinfo($file->getClientOriginalName(),PATHINFO_FILENAME);
                //  esto se hace para poder incluir de manera segura el nombre del archivo como parte de la URL
                $safeFilename = $slugger -> slug($originalFileName);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                $firebasePath = 'profile-images/' . $newFilename;
                //  mover el archivo al directorio donde se almacenan las imagenes

                try {
                    $actualPhoto = $user->getPhoto();

                    $decodedUrl = urldecode($actualPhoto);
                    $path = parse_url($decodedUrl, PHP_URL_PATH);
                    $imageName = pathinfo($path, PATHINFO_BASENAME);
                    if(strpos($actualPhoto, 'sin-imagen.png') === false){
                        $this->firebaseService->deleteFile("profile-images/" . $imageName);
                    }
                    $firebaseUrl = $this->firebaseService->uploadFile($file->getPathname(),$firebasePath);
                }catch (FileException $e){
                }

                //  actualiza la propiedad $filename de $file para que guarde
                // el nombre del PDF en vez de su contenido
                $imageCache->updateUserProfileImage($user->getPhoto());
                $user->setPhoto($firebaseUrl);
            }
            $manager = $doctrine->getManager();
            $manager->persist($user);
            $manager->flush();
        }

        $isFollowing = $this->getUser()->getFollowing()->contains($user);

        return $this->render('page/profile.html.twig',['user'=>$user,'posts'=>$posts,'isFollowing' => $isFollowing,'formulario'=>$formulario->createView()]);
    }

    #[Route('/addFollowing/{id}', name: 'add_following')]
    public function addFollower(ManagerRegistry $doctrine, int $id): Response
    {
        $repository = $doctrine->getRepository(UserPostgres::class);

        $user = $this->getUser();
        $userToFollow = $repository->find($id);

        if ($user != $userToFollow) {

            $user->addFollowing($userToFollow);

            $manager = $doctrine->getManager();
            $manager->persist($user);
            $manager->flush();
        }

        return $this->json([
            'followers' => count($userToFollow->getFollower())
        ]);
    }

    #[Route('/removeFollowing/{id}', name: 'remove_following')]
    public function removeFollowing(ManagerRegistry $doctrine, int $id): Response
    {
        $repository = $doctrine->getRepository(UserPostgres::class);

        $user = $this->getUser();
        $userToFollow = $repository->find($id);

        // Solo seguir si el usuario no se está siguiendo a sí mismo y si aún no lo sigue
        if ($user != $userToFollow) {
            $user->removeFollowing($userToFollow);

            $manager = $doctrine->getManager();
            $manager->persist($user);
            $manager->flush();
        }

        return $this->json([
            'followers' => count($userToFollow->getFollower())
        ]);
    }
    #[Route('/profile/change-username/{username}', name: 'change-name')]
    public function changeUsername(ManagerRegistry $doctrine,string $username): Response
    {
        $user = $this->getUser();
        $user->setUsername($username);
        $manager = $doctrine->getManager();
        $manager->persist($user);
        $manager->flush();
        return $this->json("{}");
    }
    #[Route('/profile/change-description/{description}', name: 'change-description')]
    public function changeDescription(ManagerRegistry $doctrine,string $description): Response
    {
        $user = $this->getUser();
        $user->setDescription($description);
        $manager = $doctrine->getManager();
        $manager->persist($user);
        $manager->flush();
        return $this->json("{}");
    }
}
