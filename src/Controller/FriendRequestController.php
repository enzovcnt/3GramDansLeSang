<?php

namespace App\Controller;

use App\Entity\FriendRequest;
use App\Entity\Friendship;
use App\Entity\Profile;
use App\Repository\FriendRequestRepository;
use App\Repository\FriendshipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/people/friendRequest/")]
final class FriendRequestController extends AbstractController
{
    #[Route('send/{id}', name: 'app_send_friend_request')]
    public function send(Profile $profile, EntityManagerInterface $manager, FriendRequestRepository $friendRequestRepository): Response
    {
        $sender = $this->getUser()->getProfile();
        $recipient = $profile;
        $friendRequest = new FriendRequest();
        $friendRequest->setSender($sender);
        $friendRequest->setRecipient($recipient);
        if(!$friendRequestRepository->findOneBy(['sender' => $sender, 'recipient' => $recipient])
            &&
            !$friendRequestRepository->findOneBy(['sender' => $recipient, 'recipient' => $sender])
        ){
            $manager->persist($friendRequest);
            $manager->flush();

        }
        return $this->redirectToRoute('app_people');
    }

    #[Route('accept/{id}', name: 'app_accept_friend_request')]
    public function accept(FriendRequest $friendRequest, EntityManagerInterface $manager, FriendshipRepository $friendshipRepository): Response
    {
        if(!$friendRequest){return $this->redirectToRoute('app_people');}
        $friendship = new Friendship();
        $friendship->setPersonA($friendRequest->getSender());
        $friendship->setPersonB($friendRequest->getRecipient());

        if(!$friendshipRepository->findOneBy(['personA'=> $friendship->getPersonA(), 'personB'=> $friendship->getPersonB()])
            &&
            !$friendshipRepository->findOneBy(['personB'=> $friendship->getPersonA(), 'personA'=> $friendship->getPersonB()])
        ){
            $manager->remove($friendRequest);
            $manager->persist($friendship);
            $manager->flush();
        }
        return $this->redirectToRoute('app_people');
    }

    #[Route('decline/{id}', name: 'app_decline_friend_request')]
    public function decline(FriendRequest $friendRequest, EntityManagerInterface $manager): Response
    {
        if(!$friendRequest){return $this->redirectToRoute('app_people');}

        $manager->remove($friendRequest);

        $manager->flush();

        return $this->redirectToRoute('app_people');
    }
}
