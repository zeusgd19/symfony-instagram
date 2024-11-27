<?php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use GuzzleHttp\Client;

class FirebaseImageCache {
    private CacheInterface $cache;
    private Client $httpClient;
    private $logger;
    private string $projectDir;

    public function __construct(CacheInterface $cache,LoggerInterface $logger,string $projectDir) {
        $this->cache = $cache;
        $this->httpClient = new Client();
        $this->logger = $logger;
        $this->projectDir = $projectDir;
    }

    public function getImage(string $imageUrl): string {
        $cacheKey = md5($imageUrl);

        // Intentamos obtener el valor directamente desde la caché sin crear uno nuevo
        $cachedImage = $this->cache->getItem($cacheKey)->get();
        if ($cachedImage && !str_starts_with($cachedImage, 'valid:')) {
            // Si la imagen cacheada es un error, eliminamos la entrada de la caché
            $this->cache->delete($cacheKey);
        }

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($imageUrl, $cacheKey) {
            $item->expiresAfter(3600); // La imagen se cacheará por una hora

            try {
                // Intentamos descargar la imagen desde Firebase
                $response = $this->httpClient->get($imageUrl);
                $imageData = $response->getBody()->getContents();

                // Guardamos la imagen descargada como "válida" en caché
                $valueToCache = 'valid:' . base64_encode($imageData);
                $item->set($valueToCache);
                dump("Guardando en caché: $valueToCache"); // Debug
                return $valueToCache;

            } catch (\Exception $e) {
                // Si falla, guardamos la imagen de "no encontrada" como "error" en caché
                $noFoundImage = file_get_contents($this->projectDir . '/public/img/imagen-no-encontrada.png');
                $valueToCache = 'error:' . base64_encode($noFoundImage);
                $item->set($valueToCache);
                dump("Guardando en caché: $valueToCache"); // Debug
                return base64_encode($noFoundImage);
            }
        });
    }

    public function isCachedImageValid(string $cacheKey): bool {
        $cachedImage = $this->cache->getItem($cacheKey)->get();
        return $cachedImage && str_starts_with($cachedImage, 'valid:');
    }

    public function deleteImageFromCache(string $imageUrl): void
    {
        $this->cache->delete($imageUrl);
    }

    public function existCachedImagen(string $posibleCachedImage){
        $cacheKey = md5($posibleCachedImage);
        if ($this->cache->getItem($cacheKey)) {
            return true;
        } else {
            return false;
        }
    }
}
