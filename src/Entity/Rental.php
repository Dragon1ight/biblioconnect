<?php

namespace App\Entity;

use App\Repository\RentalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RentalRepository::class)]
class Rental
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Book::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'rentals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userId = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $rentFrom = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $rentTo = null;

    #[ORM\Column(type: 'boolean')]
    private bool $returned = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;
        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $user): self
    {
        $this->userId = $user;
        return $this;
    }

    public function getRentFrom(): ?\DateTimeInterface
    {
        return $this->rentFrom;
    }

    public function setRentFrom(\DateTimeInterface $rentFrom): self
    {
        $this->rentFrom = $rentFrom;
        return $this;
    }

    public function getRentTo(): ?\DateTimeInterface
    {
        return $this->rentTo;
    }

    public function setRentTo(\DateTimeInterface $rentTo): self
    {
        $this->rentTo = $rentTo;
        return $this;
    }

    public function isReturned(): bool
    {
        return $this->returned;
    }

    public function setReturned(bool $returned): self
    {
        $this->returned = $returned;
        return $this;
    }
} 