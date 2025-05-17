<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Post;
use App\Entity\Profile;
use App\Form\MessageForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TchatController extends AbstractController
{
    #[Route('/tchat/initiate/{id}', name: 'app_tchat_initiate')]
    public function initiate(Profile $profile, EntityManagerInterface $manager, Request $request): Response
    {

        if(!$profile)
        {
            return $this->redirectToRoute('app_people');
        }


        $conversation = $this->getUser()->getProfile()->isChattingWith($profile);

        if(!$conversation){
            $conversation = new Conversation();
            $conversation->addParticipant($this->getUser()->getProfile());
            $conversation->addParticipant($profile);
            $conversation->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($conversation);
            $manager->flush();
        }


        return $this->redirectToRoute('app_tchat', [
            'id' => $conversation->getId(),

        ]);
    }

    #[Route('/tchat/{id}', name: 'app_tchat')]
    public function chat(Conversation $tchat, Request $request, EntityManagerInterface $manager): Response
    {
        if (!$tchat) {
            return $this->redirectToRoute('app_people');
        }


        $message = new Message();

        $form = $this->createForm(MessageForm::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $message->setAuthor($this->getUser()->getProfile());
            $message->setConversation($tchat);
            $message->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($message);
            $manager->flush();
            return $this->redirectToRoute('app_tchat', ['id' => $tchat->getId()]);
        }


        return $this->render('tchat/index.html.twig', [
            'tchat' => $tchat,
            'form' => $form,
        ]);
    }
}
