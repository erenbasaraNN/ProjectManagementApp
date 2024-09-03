<?php

// src/Security/IssueVoter.php

namespace App\Security;

use App\Entity\Issue;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;  // Updated to use the SecurityBundle class
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class IssueVoter extends Voter
{
    const VIEW = 'view_issue';
    const EDIT = 'edit_issue';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof Issue;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Issue $issue */
        $issue = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($issue, $user);
            case self::EDIT:
                return $this->canEdit($issue, $user);
        }

        return false;
    }

    private function canView(Issue $issue, User $user): bool
    {
        // Project Managers can view any issue
        if ($this->security->isGranted('ROLE_PROJECT_MANAGER')) {
            return true;
        }

        // Users can view issues they are assigned to
        return $issue->getAssignedUsers()->contains($user);
    }

    private function canEdit(Issue $issue, User $user): bool
    {
        // Project Managers can edit any issue
        if ($this->security->isGranted('ROLE_PROJECT_MANAGER')) {
            return true;
        }

        // Users can edit issues they are assigned to
        return $issue->getAssignedUsers()->contains($user);
    }
}
