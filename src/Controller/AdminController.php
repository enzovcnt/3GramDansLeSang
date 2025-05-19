<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route('/users', name: 'app_admin')]
    public function index(UserRepository $repository, PostRepository $postRepository): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('admin/index.html.twig', [
            'users' => $repository->findAll(),
            'posts' => $postRepository->findAll(),
        ]);
    }


    #[Route('/promote/{id}', name: 'app_admin_promote')]
    public function promote(EntityManagerInterface $manager, User $user): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        if(!in_array('ROLE_ADMIN',$user->getRoles()))
        {
            $user->setRoles(['ROLE_ADMIN']);
            $manager->persist($user);
            $manager->flush();
        }
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/demote/{id}', name: 'app_admin_demote')]
    public function demote(EntityManagerInterface $manager, User $user): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        if(in_array('ROLE_ADMIN',$user->getRoles()))
        {
            $user->setRoles([]);
            $manager->persist($user);
            $manager->flush();
        }
        return $this->redirectToRoute('app_admin');
    }
}
