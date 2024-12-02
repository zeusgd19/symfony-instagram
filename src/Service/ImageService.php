<?php
namespace App\Service;

use App\Entity\UserPostgres;
use App\Entity\Post;
use Psr\Log\LoggerInterface;

class ImageService{
    private FirebaseImageCache $imageCache;
    private $logger;

    public function __construct(FirebaseImageCache $imageCache,LoggerInterface $logger)
    {
        $this->imageCache = $imageCache;
        $this->logger = $logger;
    }

    public function getBase64Image(string $path): ?string
    {
        $cacheKey = md5($path);

        // Verificamos si la imagen en caché es válida
        if ($this->imageCache->isCachedImageValid($cacheKey)) {
            return $this->imageCache->getImage($path); // Quitamos el prefijo "valid:"
        }
        // Si no es válida, intentamos actualizar la caché descargando de nuevo
        return $this->imageCache->getImage($path);
    }


    public function getUserProfileImage(string $user): ?string
    {
        return $this->getBase64Image($user);
    }

    public function getPostImage(string $post): ?string
    {
        return $this->getBase64Image($post);
    }

    public function updateUserProfileImage(string $newImageUrl): void
    {
        $cacheKey = md5($newImageUrl);
        $this->imageCache->deleteImageFromCache($cacheKey);

    }
}
