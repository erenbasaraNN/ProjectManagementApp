<?php

// src/Entity/Issue.php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Issue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private $assignees;

    #[ORM\Column(type: 'datetime')]
    private $startDate;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $endDate;

    #[ORM\Column(type: 'string', length: 50)]
    private $status;

    #[ORM\Column(type: 'string', length: 50)]
    private $priority;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'issues')]
    private $project;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'issues')]
    #[ORM\JoinTable(name: 'issue_tag')]
    private $tags;


    public function getStatusColor(): string
    {
        return match ($this->status) {
            'Completed' => '#46c965',
            'In Progress' => '#e3d324',
            'Blocked' => '#f12436',
            'Not Started' => '#29a5d1',
        };
    }

    public function __construct()
    {
        // Initialize the assignees property with an ArrayCollection
        $this->assignees = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
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

    public function getAssignees()
    {
        return $this->assignees;
    }

    public function addAssignee(User $assignee): self
    {
        if (!$this->assignees->contains($assignee)) {
            $this->assignees[] = $assignee;
        }
        return $this;
    }

    public function removeAssignee(User $assignee): self
    {
        $this->assignees->removeElement($assignee);
        return $this;
    }

    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;
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

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;
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

}
