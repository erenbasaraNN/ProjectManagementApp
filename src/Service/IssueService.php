<?php

namespace App\Service;

use App\Entity\Issue;
use Doctrine\ORM\EntityManagerInterface;

class IssueService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getNeighborsForIssues(array $issues): array
    {
        $result = [];
        $issueRepository = $this->entityManager->getRepository(Issue::class);

        foreach ($issues as $issue) {
            $task = $issue->getTask();

            if (!$task) {
                continue;
            }

            // Get all issues for this task
            $neighborIssues = $issueRepository->findBy(['task' => $task]);

            $result[] = [
                'task' => $task,
                'mainIssue' => $issue,
                'neighborIssues' => $neighborIssues
            ];
        }

        return $result;
    }
}