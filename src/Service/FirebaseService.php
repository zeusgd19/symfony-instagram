<?php
namespace App\Service;

use Kreait\Firebase\Factory;

class FirebaseService {
    private $storage;

    public function __construct(string $firebaseCredentials) {
        $factory = (new Factory())->withServiceAccount($firebaseCredentials);
        $this->storage = $factory->createStorage();
    }

    public function uploadFile(string $localPath, string $firebasePath): string {
        $bucket = $this->storage->getBucket();
        $file = fopen($localPath, 'r');

        $bucket->upload($file, [
            'name' => $firebasePath, // Ruta dentro del bucket
        ]);

        // Devuelve la URL pública del archivo
        return "https://firebasestorage.googleapis.com/v0/b/" . $bucket->name() . "/o/" . urlencode($firebasePath) . "?alt=media";
    }

    public function deleteFile(string $firebasePath): bool {
        $bucket = $this->storage->getBucket();

        try {
            // Elimina el archivo en el bucket especificado
            $object = $bucket->object($firebasePath);
            if (!$object->exists()) {
                throw new \Exception("El archivo no existe en Firebase: $firebasePath");
            }
            $object->delete();

            return true; // Archivo eliminado exitosamente
        } catch (\Exception $e) {
            // Manejo de errores si no se encuentra el archivo o no se puede borrar
            return false;
        }
    }
}
