<?php

namespace App\Security;

use App\Entity\Task;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    const VIEW = 'view_task';
    const EDIT = 'edit_task';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Task $task */
        $task = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($task, $user);
            case self::EDIT:
                return $this->canEdit($task, $user);
        }

        return false;
    }

    private function canView(Task $task, UserInterface $user): bool
    {
        if ($this->security->isGranted('ROLE_PROJECT_MANAGER')) {
            return true;
        }


        foreach ($task->getIssues() as $issue) {
            if ($issue->getAssignedUsers()->contains($user)) {
                return true;
            }
        }

        return false;
    }

    private function canEdit(Task $task, UserInterface $user): bool
    {
        // Project Managers can edit any task
        if ($this->security->isGranted('ROLE_PROJECT_MANAGER')) {
            return true;
        }

        // Users can edit the task if they are assigned to it
        if ($task->getAssignedUsers()->contains($user)) {
            return true;
        }

        // Check if the user is assigned to any issues under the task
        foreach ($task->getIssues() as $issue) {
            if ($issue->getAssignedUsers()->contains($user)) {
                return true;
            }
        }

        return false;
    }
}
