<?php

namespace App\Controller;

use App\Entity\Message;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DirectMessagesController extends AbstractController
{
    #[Route('/directMessages', name: 'direct_messages')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $currentUser = $this->getUser();
        $messageRepository = $doctrine->getRepository(Message::class);
        // Obtener conversaciones agrupadas por usuario
        $conversations = $messageRepository->findConversationsForUser($currentUser->getId());

        return $this->render('page/direct-messages.html.twig', [
            'conversations' => $conversations,
        ]);
    }
}
