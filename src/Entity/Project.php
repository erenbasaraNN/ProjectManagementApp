<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
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

    #[ORM\ManyToOne(inversedBy: 'projects')]
    private ?Brand $brand = null;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Task::class)]
    private Collection $tasks;

    #[ORM\Column(type: 'date', nullable: true)]

    private ?DateTimeInterface $createdAt;

    #[ORM\Column(type: 'date', nullable: true)]

    private ?DateTimeInterface $deadline;
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?: 'İsim Bulunamadı';
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getTotalTimeSpent(): int
    {
        $totalTime = 0;

        foreach ($this->tasks as $task) {
            $totalTime += $task->getTotalTimeSpent();  // Using the method we defined in Task
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

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;
        return $this;
    }

    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setProject($this);
        }
        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getProject() === $this) {
                $task->setProject(null);
            }
        }
        return $this;
    }
}
