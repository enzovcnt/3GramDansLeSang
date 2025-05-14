<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Post;
use App\Entity\Profile;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ShareController extends AbstractController
{

    #[Route('/share/{postId}/to/{profileId}', name: 'share')]
    public function share(): Response
    {

        return $this->render('share/index.html.twig');
    }

}
