<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Chat::class)]
    private Collection $sentChats;

    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: Chat::class)]
    private Collection $receivedChats;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->sentChats = new ArrayCollection();
        $this->receivedChats = new ArrayCollection();
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

    public function getFriends(): array
    {
        // Cette méthode ne devrait pas utiliser getDoctrine() car elle est dans une entité
        // Elle devrait plutôt recevoir l'EntityManager en paramètre
        return [];
    }

    public function getFriendRequests(): array
    {
        // Cette méthode ne devrait pas utiliser getDoctrine() car elle est dans une entité
        // Elle devrait plutôt recevoir l'EntityManager en paramètre
        return [];
    }

    /**
     * @return Collection<int, Chat>
     */
    public function getSentChats(): Collection
    {
        return $this->sentChats;
    }

    public function addSentChat(Chat $chat): self
    {
        if (!$this->sentChats->contains($chat)) {
            $this->sentChats->add($chat);
            $chat->setSender($this);
        }

        return $this;
    }

    public function removeSentChat(Chat $chat): self
    {
        if ($this->sentChats->removeElement($chat)) {
            // set the owning side to null (unless already changed)
            if ($chat->getSender() === $this) {
                $chat->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Chat>
     */
    public function getReceivedChats(): Collection
    {
        return $this->receivedChats;
    }

    public function addReceivedChat(Chat $chat): self
    {
        if (!$this->receivedChats->contains($chat)) {
            $this->receivedChats->add($chat);
            $chat->setReceiver($this);
        }

        return $this;
    }

    public function removeReceivedChat(Chat $chat): self
    {
        if ($this->receivedChats->removeElement($chat)) {
            // set the owning side to null (unless already changed)
            if ($chat->getReceiver() === $this) {
                $chat->setReceiver(null);
            }
        }

        return $this;
    }
}
