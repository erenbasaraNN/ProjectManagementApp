<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
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

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[ORM\OneToMany(mappedBy: 'task', targetEntity: Issue::class, cascade: ['persist', 'remove'])]
    private Collection $issues;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $assignedUsers;

    #[ORM\Column(type: 'date', nullable: true)]

    private $createdAt;

    #[ORM\Column(type: 'date', nullable: true)]

    private $deadline;

    public function __construct()
    {
        $this->issues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getTotalTimeSpent(): int
    {
        $totalTime = 0;

        foreach ($this->issues as $issue) {
            $totalTime += $issue->getTimeSpent();  // Assuming getTimeSpent() is defined in Issue
        }

        return $totalTime;
    }
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getDeadline(): ?DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(?DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;
        return $this;
    }

    public function getAssignedUsers(): Collection
    {
        return $this->assignedUsers;
    }


    public function addAssignedUser(User $user): self
    {
        if (!$this->assignedUsers->contains($user)) {
            $this->assignedUsers->add($user);
        }

        return $this;
    }

    public function removeAssignedUser(User $user): self
    {
        $this->assignedUsers->removeElement($user);

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

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;
        return $this;
    }

    public function getIssues(): Collection
    {
        return $this->issues;
    }

    public function addIssue(Issue $issue): self
    {
        if (!$this->issues->contains($issue)) {
            $this->issues[] = $issue;
            $issue->setTask($this);
        }
        return $this;
    }

    public function removeIssue(Issue $issue): self
    {
        if ($this->issues->removeElement($issue)) {
            // set the owning side to null (unless already changed)
            if ($issue->getTask() === $this) {
                $issue->setTask(null);
            }
        }
        return $this;
    }
}
