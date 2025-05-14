<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ShareController extends AbstractController
{
    #[Route('/share/{postId}/', name: 'app_share')]
    public function index(Post $post, Profile $profile, EntityManagerInterface $manager,
    Request $request,
    ): Response
    {
        return $this->render('share/index.html.twig', [
            'controller_name' => 'ShareController',
        ]);
    }
}
