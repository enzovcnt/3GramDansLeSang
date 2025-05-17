<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Post;
use App\Entity\Profile;
use App\Entity\Share;
use App\Repository\PostRepository;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ShareController extends AbstractController
{

    #[Route('/share/{id}', name: 'app_share')]
    public function share(Post $post): Response
    {
        if (!$this->getUser()) {
            $this->redirectToRoute('app_login');
        }
        if (!$post){
            $this->redirectToRoute('app_login');
        }

        return $this->render('share/index.html.twig',[
            'post' => $post,
        ]);
    }


    #[Route('/share/{id}/{recipientId}', name: 'app_share_to_recipient')]
    public function shareToRecipient(Post $post, $recipientId, ProfileRepository $profileRepository, EntityManagerInterface $manager,
        UrlGeneratorInterface $urlGenerator
    ): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        $recipient = $profileRepository->find($recipientId);
        if (!$recipient)
        {
            return $this->redirectToRoute('app_people');
        }

        $share = New Share();
        $share->setPost($post);
        $share->setShareSender($this->getUser()->getProfile());
        $share->setShareRecipient($recipient);
        $manager->persist($share);
        $manager->flush();

        $conversation = $this->getUser()->getProfile()->isChattingWith($recipient);

        if(!$conversation){
            $conversation = new Conversation();
            $conversation->addParticipant($this->getUser()->getProfile());
            $conversation->addParticipant($recipient);
            $conversation->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($conversation);
            $manager->flush();
        }
        $postUrl = $urlGenerator->generate('app_post_show', [
            'id' => $post->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);


        $message= new Message();
        $message->setAuthor($this->getUser()->getProfile());
        $message->setConversation($conversation);
        $message->setContent($postUrl);
        $message->setType(1);
        $message->setPostType($post);
        $message->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($message);
        $manager->flush();



        return $this->redirectToRoute('app_tchat', [
            'id' => $conversation->getId(),
            'profiles' => $profileRepository->findAll(),

        ]);
    }

}
