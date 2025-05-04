<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\UserRepository;


#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank]
    private ?string $username = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private ?string $prenom = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateNaissance;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $profession = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isAdmin = false;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): \DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function setProfession(?string $profession): self
    {
        $this->profession = $profession;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null; // Not needed for modern hashers
    }

    public function getRoles(): array
    {
        return ['ROLE_USER']; // Par défaut, un utilisateur aura le rôle "ROLE_USER"
    }

    public function eraseCredentials(): void{
       
    }


    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getIsAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getFriends(): array{
        // Récupérer tous les amis dans ta base de données
        $friendRequests = $this->getDoctrine()
            ->getRepository(FriendRequest::class)
            ->findBy([
                'sender' => $this,
                'status' => 'accepted',
            ]);
        
        $friends = [];
        foreach ($friendRequests as $request) {
            $friends[] = $request->getReceiver();
        }

        $friendRequests = $this->getDoctrine()
            ->getRepository(FriendRequest::class)
            ->findBy([
                'receiver' => $this,
                'status' => 'accepted',
            ]);
        
        foreach ($friendRequests as $request) {
            $friends[] = $request->getSender();
        }
        return $friends;
    }

    public function getFriendRequests(): array{
        // Récupérer toutes les demandes d'amis envoyées par l'utilisateur
        $sentRequests = $this->getDoctrine()
            ->getRepository(FriendRequest::class)
            ->findBy(['sender' => $this, 'status' => 'pending']);

        // Récupérer toutes les demandes d'amis reçues par l'utilisateur
        $receivedRequests = $this->getDoctrine()
            ->getRepository(FriendRequest::class)
            ->findBy(['receiver' => $this, 'status' => 'pending']);

        // Fusionner les deux tableaux de demandes
        return array_merge($sentRequests, $receivedRequests);
    }

}
