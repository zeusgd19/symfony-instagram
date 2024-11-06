<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DirectMessagesController extends AbstractController
{
    #[Route('/directMessages', name: 'direct_messages')]
    public function index(): Response
    {
        return $this->render('page/direct-messages.html.twig');
    }
}
