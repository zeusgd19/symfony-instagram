<?php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use GuzzleHttp\Client;

class FirebaseImageCache {
    private CacheInterface $cache;
    private Client $httpClient;
    private $logger;

    public function __construct(CacheInterface $cache,LoggerInterface $logger) {
        $this->cache = $cache;
        $this->httpClient = new Client();
        $this->logger = $logger;
    }

    public function getImage(string $imageUrl): string {
        $cacheKey = md5($imageUrl);
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($cacheKey, $imageUrl) {
        $item->expiresAfter(3600); // La imagen se cacheará por una hora

        $this->logger->info("Descargando imagen: $imageUrl y guardando en caché con la clave $cacheKey");
        // Descarga la imagen desde Firebase
        $response = $this->httpClient->get($imageUrl);
        return $response->getBody()->getContents();
        });
    }
}
