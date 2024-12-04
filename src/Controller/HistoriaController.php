<?php

namespace App\Controller;

use App\Service\FirebaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HistoriaController extends AbstractController
{
    private FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService=$firebaseService;
    }

    #[Route('/historia/nueva', name: 'nueva_historia')]
    public function index(Request $request): Response
    {
        $usuario = $this->getUser();
        $archivo = $request->files->get('contenido');


    }
}
