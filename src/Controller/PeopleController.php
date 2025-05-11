<?php

namespace App\Controller;

use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PeopleController extends AbstractController
{
    #[Route('/people', name: 'app_people')]
    public function index(ProfileRepository $profileRepository): Response
    {
        return $this->render('people/index.html.twig', [
            'profiles' => $profileRepository->findAll(),
        ]);
    }
}
