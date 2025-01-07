<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Notification;
use App\Form\MessageFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DirectMessagesController extends AbstractController
{
    #[Route('/directMessages', name: 'direct_messages')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $currentUser = $this->getUser();
        $notificationsRepository = $doctrine->getRepository(Notification::class);
        $messageRepository = $doctrine->getRepository(Message::class);

        $notifications = $notificationsRepository->findAll();
        // Obtener conversaciones agrupadas por usuario
        $conversations = $messageRepository->findConversationsForUser($currentUser->getId());
        $supabaseKey = $this->getParameter('SUPABASE_KEY');

        return $this->render('page/direct-messages.html.twig', [
            'conversations' => $conversations,
            'supabaseKey' => $supabaseKey,
            'notifications' => $notifications,
        ]);
    }
}
