<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\UserPostgres;
use Doctrine\Persistence\ManagerRegistry;
use http\Client\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    #[Route('/message/new/{receiver}/{content}', name: 'new_message')]
    public function newMessage(int $receiver,string $content,ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();
        $repositoryUser = $doctrine->getRepository(UserPostgres::class);
        $receiver = $repositoryUser->find($receiver);
        $message = new Message();
        $message->setSender($this->getUser());
        $message->setReceiver($receiver);
        $message->setContent($content);

        $manager->persist($message);
        $manager->flush();
        return $this->json(['messageId'=>$message->getId(),'messageSender'=>$message->getSender()->getId(),'messageReceiver'=>$message->getReceiver()->getId(),'message'=>$message->getContent()]);
    }

    #[Route('/messages/{userId}', name: 'messages')]
    public function getMessages(int $userId, ManagerRegistry $doctrine): JsonResponse {
        $currentUser = $this->getUser();
        $otherUserId = $userId;
        $messageRepository = $doctrine->getRepository(Message::class);

        // Validar que el usuario tiene acceso
        $messages = $messageRepository->findMessagesBetweenUsers(
            $currentUser->getId(),
            $otherUserId
        );

        return new JsonResponse([
            'messages' => $this->renderView('partials/_messages.html.twig', [
                'messages' => $messages,
                'currentUser' => $currentUser,
            ]),
        ]);
    }
}
