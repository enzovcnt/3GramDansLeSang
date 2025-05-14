<?php

namespace App\Controller;

use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(ProfileRepository $profileRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'profiles' => $profileRepository->findAll(),
        ]);
    }
}
