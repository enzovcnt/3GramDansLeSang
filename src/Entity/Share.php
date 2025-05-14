<?php

namespace App\Entity;

use App\Repository\ShareRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShareRepository::class)]
class Share
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'shares')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post = null;

    #[ORM\ManyToOne(inversedBy: 'shares')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $shareSender = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): static
    {
        $this->post = $post;

        return $this;
    }

    public function getShareSender(): ?Profile
    {
        return $this->shareSender;
    }

    public function setShareSender(?Profile $shareSender): static
    {
        $this->shareSender = $shareSender;

        return $this;
    }
}
