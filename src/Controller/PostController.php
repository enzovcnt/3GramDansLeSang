<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Post;
use App\Entity\Profile;
use App\Entity\User;
use App\Form\CommentForm;
use App\Form\ImageForm;
use App\Form\PostForm;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostController extends AbstractController
{
    #[Route('/', name: 'app_posts')]
    public function index(PostRepository $postRepository): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/post/{id}', name: 'app_post_show', priority: -1)]
    public function show(Post $post, Request $request, EntityManagerInterface $manager): Response
    {
        if(!$this->getUser() || !$post)
        {
            return $this->redirectToRoute('app_login');
        }
        $comment = new Comment();
        $form = $this->createForm(CommentForm::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setPost($post);
            $comment->setAuthor($this->getUser());
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('show_post', ['id' => $post->getId()]);
        }
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'formComment' => $form,
            'comment' => $comment,
        ]);
    }

    #[Route('/post/create', name: 'app_post_create')]
    public function create(EntityManagerInterface $manager, Request $request): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $post = new Post();
        $form = $this->createForm(PostForm::class, $post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //redondance > corrige ça
            //$post->setAuthor($this->getUser()->getProfile());
            $user = $this->getUser();
            $profile = $user->getProfile();
            $post->setAuthor($user);
            $post->setProfile($profile);

            $manager->persist($post);
            $manager->flush();
            return $this->redirectToRoute('app_post_addimage', ['id' => $post->getId()]);
        }

        return $this->render('post/create.html.twig', [
            'formNew' => $form,
        ]);
    }

    #[Route('/post/{id}/edit', name: 'app_post_edit')]
    public function edit(Post $post, EntityManagerInterface $manager, Request $request): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(PostForm::class, $post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $post->setAuthor($this->getUser());
            $manager->persist($post);
            $manager->flush();
            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }
        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'formEdit' => $form->createView(),
        ]);
    }

    #[Route('/post/{id}/delete', name: 'app_post_delete')]
    public function delete(Post $post, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        if(!$this->getUser() || !$post)
        {
            return $this->redirectToRoute('app_login');
        }
        $manager->remove($post);
        $manager->flush();
        return $this->redirectToRoute('app_posts');
    }

    #[Route('/postFriend', name: 'app_post_friend')]
    public function friendPost(PostRepository $postRepository) : Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $profile = $user->getProfile();
        $posts = $postRepository->findByFriends($profile);
        return $this->render('post/friend.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/post/addimage/{id}', name: 'app_post_addimage')]
    public function addImage(Post $post, Request $request, EntityManagerInterface $manager) : Response
    {
        if(!$this->getUser() || !$post)
        {
            return $this->redirectToRoute('app_login');
        }
        if($post->getAuthor() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }
        $image = new Image();
        $formImage = $this->createForm(ImageForm::class, $image);
        $formImage->handleRequest($request);
        if($formImage->isSubmitted() && $formImage->isValid()){
            $image->setPost($post);
            $manager->persist($image);
            $manager->flush();
            return $this->redirectToRoute('app_post_addimage', ['id' => $post->getId()]);
        }


        return $this->render('post/image.html.twig', [
            'post' => $post,
            'formImage' => $formImage->createView(),
        ]);
    }

    #[Route('/post/removeImage/{id}', name: 'app_removeImage')]
    public function removeImage(Image $image, EntityManagerInterface $manager) : Response
    {
        if(!$this->getUser() || !$image)
        {
            return $this->redirectToRoute('app_login');
        }
        if($image->getPost()->getAuthor() !== $this->getUser())
        {
            return $this->redirectToRoute('app_post_show', ['id' => $image->getId()]);
        }
        $postId = $image->getPost()->getId();
        $manager->remove($image);
        $manager->flush();


        return $this->redirectToRoute('app_post_addimage', ['id' => $postId]);
    }

}
