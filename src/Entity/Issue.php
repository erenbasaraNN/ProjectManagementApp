<?php

namespace App\Entity;

use AllowDynamicProperties;
use App\Repository\IssueRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[AllowDynamicProperties] #[ORM\Entity(repositoryClass: IssueRepository::class)]
class Issue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;
    public const STATUS_OPTIONS = [
        'not_started' => 'Not Started',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'canceled' => 'Canceled',
    ];

    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTimeInterface $createdAt = null;
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTimeInterface $deadline = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $timeSpent = 0;

    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'issues')]
    private ?Task $task = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'issues')]
    private Collection $assignedUsers;

    #[ORM\OneToMany(mappedBy: 'issue', targetEntity: PostIt::class, cascade: ['persist', 'remove'])]
    private Collection $postIts;

    public function __construct()
    {
        $this->assignedUsers = new ArrayCollection();
        $this->postIts = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function addPostIt(PostIt $postIt): self
    {
        if (!$this->postIts->contains($postIt)) {
            $this->postIts[] = $postIt;
            $postIt->setIssue($this);
        }

        return $this;
    }

    public function removePostIt(PostIt $postIt): self
    {
        if ($this->postIts->removeElement($postIt)) {
            // set the owning side to null (unless already changed)
            if ($postIt->getIssue() === $this) {
                $postIt->setIssue(null);
            }
        }

        return $this;
    }
    public function getPostIts(): Collection
    {
        return $this->postIts;
    }

    public function getTimeSpent(): int
    {
        return $this->timeSpent;
    }

    public function setTimeSpent(int $timeSpent): self
    {
        $this->timeSpent = $timeSpent;

        return $this;
    }
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }public function getDeadline(): ?DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(?DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getAssignedUsers(): Collection
    {
        return $this->assignedUsers;
    }

    public function addAssignedUser(User $user): self
    {
        if (!$this->assignedUsers->contains($user)) {
            $this->assignedUsers[] = $user;
        }

        return $this;
    }

    public function removeAssignedUser(User $user): self
    {
        $this->assignedUsers->removeElement($user);

        return $this;
    }
}
