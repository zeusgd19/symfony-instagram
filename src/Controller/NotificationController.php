<?php

namespace App\Controller;

use App\Entity\Notification;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    #[Route('/notification', name: 'app_notification')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();
        $notificationRepository = $manager->getRepository(Notification::class);

        $allNotification = $notificationRepository->findAll();
        $notifications = [];
        foreach ($allNotification as $notification) {
            if($notification->getType() !== 'message'){
                $notifications[] = $notification;
            }
        }
        return $this->render('page/notification.html.twig',[
            'notifications' => $notifications,
        ]);
    }
}
