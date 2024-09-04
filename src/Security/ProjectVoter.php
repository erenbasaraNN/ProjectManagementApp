<?php

// src/Security/ProjectVoter.php

namespace App\Security;

use App\Entity\Project;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectVoter extends Voter
{
    const VIEW = 'view_project';
    const EDIT = 'edit_project';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof Project;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Project $project */
        $project = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($project, $user),
            self::EDIT => $this->canEdit($project, $user),
            default => false,
        };

    }

    private function canView(Project $project, UserInterface $user): bool
    {
        // Project Managers can view any project
        if ($this->security->isGranted('ROLE_PROJECT_MANAGER')) {
            return true;
        }

        // Users can view projects if they're assigned
        return $project->getUsers()->contains($user);
    }

    private function canEdit(Project $project, UserInterface $user): bool
    {
        // Project Managers can edit any project
        if ($this->security->isGranted('ROLE_PROJECT_MANAGER')) {
            return true;
        }

        // Users cannot edit projects, only Project Managers can
        return false;
    }
}
