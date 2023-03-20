<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\Collection;
use App\Controller\GetConversationsController;
use App\Controller\CreateConversationController;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            controller: GetConversationsController::class,
            description: 'Get all conversations of the current user',
            name: 'get_conversations',
            normalizationContext: ['groups' => ['conversation:teaser']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['conversation:create']],
            security: "is_granted('ROLE_USER')",
        ),
        new Delete(security: "is_granted('ROLE_ADMIN') or object.getOwner() == user"),
    ]
)]

class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['conversation:teaser'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ownedConversations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['conversation:teaser'])]
    private ?User $owner = null;

    
    #[ORM\ManyToOne(inversedBy: 'tenantConversations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['conversation:create', 'conversation:teaser'])]
    private ?User $tenant = null;

    #[ORM\OneToMany(mappedBy: 'conversation', targetEntity: PrivateMessage::class, orphanRemoval: true)]
    #[Groups(['conversation:read'])]
    private Collection $privateMessages;

    #[ORM\Column]
    #[Groups(['conversation:teaser'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->privateMessages = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getTenant(): ?User
    {
        return $this->tenant;
    }

    public function setTenant(?User $tenant): self
    {
        $this->tenant = $tenant;

        return $this;
    }

    /**
     * @return Collection<int, PrivateMessage>
     */
    public function getPrivateMessages(): Collection
    {
        return $this->privateMessages;
    }

    public function addPrivateMessage(PrivateMessage $privateMessage): self
    {
        if (!$this->privateMessages->contains($privateMessage)) {
            $this->privateMessages->add($privateMessage);
            $privateMessage->setConversation($this);
        }

        return $this;
    }

    public function removePrivateMessage(PrivateMessage $privateMessage): self
    {
        if ($this->privateMessages->removeElement($privateMessage)) {
            // set the owning side to null (unless already changed)
            if ($privateMessage->getConversation() === $this) {
                $privateMessage->setConversation(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
