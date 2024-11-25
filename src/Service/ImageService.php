<?php
namespace App\Service;

use App\Entity\UserPostgres;
use App\Entity\Post;

class ImageService{
    private FirebaseImageCache $imageCache;

    public function __construct(FirebaseImageCache $imageCache)
    {
        $this->imageCache = $imageCache;
    }

    public function getBase64Image(string $path): ?string
    {
        $image = $this->imageCache->getImage($path);
        return $image ? base64_encode($image) : null;
    }

    public function getUserProfileImage(UserPostgres $user): ?string
    {
        return $this->getBase64Image($user->getPhoto());
    }

    public function getPostImage(Post $post): ?string
    {
        return $this->getBase64Image($post->getPhoto());
    }
}
