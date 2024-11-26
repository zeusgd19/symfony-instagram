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
            $this->logger->info("La imagen está en caché y es válida. Clave: $cacheKey");
            return substr($this->imageCache->getImage($path), 6); // Quitamos el prefijo "valid:"
        }
        $this->logger->info("La imagen no es válida o no está en caché. Clave: $cacheKey. Intentando descargar de nuevo.");
        // Si no es válida, intentamos actualizar la caché descargando de nuevo
        return $this->imageCache->getImage($path);
    }


    public function getUserProfileImage(UserPostgres $user): ?string
    {
        return $this->getBase64Image($user->getPhoto());
    }

    public function getPostImage(Post $post): ?string
    {
        return $this->getBase64Image($post->getPhoto());
    }

    public function updateUserProfileImage(string $newImageUrl): void
    {
        $cacheKey = md5($newImageUrl);
        $this->imageCache->deleteImageFromCache($cacheKey);

    }
}
