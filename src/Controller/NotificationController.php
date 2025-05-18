<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NotificationController extends AbstractController
{
    #[Route('/notification', name: 'app_notification')]
    public function index(NotificationRepository $notificationRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $profile = $this->getUser()->getProfile();


        $notifications = $notificationRepository->findBy(
            ['profile' => $profile],
            ['date' => 'DESC']
        );

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/notification/delete/{id}', name: 'app_notification_delete')]
    public function delete(Notification $notification, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        $manager->remove($notification);
        $manager->flush();
        return $this->redirectToRoute('app_notification');
    }
}
