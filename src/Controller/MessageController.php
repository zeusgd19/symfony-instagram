<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\UserPostgres;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    #[Route('/message/new', name: 'new_message', methods: ['POST'])]
    public function newMessage(Request $request, ManagerRegistry $doctrine): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['receiverId'], $data['content'])) {
            return $this->json(['error' => 'Datos incompletos'], 400);
        }

        $manager = $doctrine->getManager();
        $repositoryUser = $doctrine->getRepository(UserPostgres::class);

        // Busca al receptor
        $receiver = $repositoryUser->find($data['receiverId']);
        if (!$receiver) {
            throw $this->createNotFoundException('Usuario receptor no encontrado');
        }

        // Crea el mensaje
        $message = new Message();
        $message->setSender($this->getUser());
        $message->setReceiver($receiver);
        $message->setContent($data['content']);

        $notification = new Notification();
        $notification->setType('message');
        $notification->setNotifiedUser($receiver);

        $manager->persist($message);
        $manager->persist($notification);
        $manager->flush();

        return $this->json([
            'messageId' => $message->getId(),
            'messageSender' => $message->getSender()->getId(),
            'messageReceiver' => $message->getReceiver()->getId(),
            'message' => $message->getContent()
        ]);
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
