<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Entity\Issue;
use App\Entity\PostIt;
use App\Repository\IssueRepository;
use App\Repository\PostItRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[AllowDynamicProperties]
final class IssueController extends AbstractController
{
    private $csrfTokenManager;
    private $issueRepository;
    private $postItRepository;
    private $userRepository;
    public function __construct(CsrfTokenManagerInterface $csrfTokenManager,EntityManagerInterface $entityManager, IssueRepository $issueRepository, PostItRepository $postItRepository, UserRepository $userRepository)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->entityManager = $entityManager;
        $this->issueRepository = $issueRepository;
        $this->postItRepository = $postItRepository;
        $this->userRepository = $userRepository;
    }

    #[Route('/issue/{id}/edit-name', name: 'edit_issue_name', methods: ['POST'])]
    public function editIssueName(int $id, Request $request): JsonResponse
    {
        $submittedToken = $request->headers->get('X-CSRF-TOKEN');
        if (!$this->isCsrfTokenValid('authenticate', $submittedToken)) {
            return $this->jsonError('Invalid CSRF token', Response::HTTP_FORBIDDEN);
        }

        $issue = $this->issueRepository->find($id);
        if (!$issue) {
            return $this->jsonError('Issue not found.', Response::HTTP_NOT_FOUND);
        }

        $data = $this->getJsonData($request);
        if (!isset($data['name'])) {
            return $this->jsonError('Name is required.', Response::HTTP_BAD_REQUEST);
        }

        $issue->setName($data['name']);
        $this->entityManager->flush();

        return $this->jsonSuccess();
    }

    #[Route('/issue/{id}/edit-status', name: 'edit_issue_status', methods: ['POST'])]
    public function editStatus(Issue $issue, Request $request): JsonResponse
    {
        $data = $this->getJsonData($request);
        if (empty($data['status'])) {
            return $this->jsonError('Status cannot be empty', Response::HTTP_BAD_REQUEST);
        }

        $issue->setStatus($data['status']);
        $this->entityManager->flush();

        return $this->jsonSuccess(['status' => $issue->getStatus()]);
    }

    #[Route('/issue/{id}/edit-priority', name: 'edit_issue_priority', methods: ['POST'])]
    public function editPriority(Issue $issue, Request $request): JsonResponse
    {
        $data = $this->getJsonData($request);
        if (!isset($data['priority'])) {
            return $this->jsonError('Priority not provided', Response::HTTP_BAD_REQUEST);
        }

        $issue->setPriority($data['priority']);
        $this->entityManager->flush();

        return $this->jsonSuccess(['priority' => $issue->getPriority()]);
    }

    #[Route('/issue/{id}/edit-assignees', name: 'issue_edit_assignees', methods: ['POST'])]
    public function editIssueAssignees(Issue $issue, Request $request): JsonResponse
    {
        $data = $this->getJsonData($request);
        if (!isset($data['assignees']) || !is_array($data['assignees'])) {
            return $this->jsonError('Invalid assignees data', Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->entityManager->beginTransaction();

            // Remove all current assignees
            foreach ($issue->getAssignees() as $currentAssignee) {
                $issue->removeAssignee($currentAssignee);
            }

            // Add new assignees
            foreach ($data['assignees'] as $assigneeName) {
                $user = $this->userRepository->findOneBy(['name' => $assigneeName]);
                if ($user) {
                    $issue->addAssignee($user);
                }
            }

            $this->entityManager->flush();
            $this->entityManager->commit();

            $updatedAssignees = $issue->getAssignees()->map(fn($assignee) => $assignee->getName())->toArray();
            return $this->jsonSuccess(['assignees' => $updatedAssignees]);
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            return $this->jsonError('Failed to update assignees: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/issue/{id}/edit-date', name: 'edit_issue_date', methods: ['POST'])]
    public function editIssueDate(Issue $issue, Request $request): JsonResponse
    {
        $data = $this->getJsonData($request);
        if (!isset($data['endDate'])) {
            return $this->jsonError('Invalid date', Response::HTTP_BAD_REQUEST);
        }

        try {
            $issue->setEndDate(new \DateTime($data['endDate']));
            $this->entityManager->flush();
            return $this->jsonSuccess();
        } catch (\Exception $e) {
            return $this->jsonError('Invalid date format', Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/issue/{id}/description', name: 'app_issue_getdescription', methods: ['GET'])]
    public function getDescription(Issue $issue): JsonResponse
    {
        return $this->jsonSuccess(['description' => $issue->getDescription()]);
    }

    #[Route('/api/issue/{id}/description', name: 'api_issue_description_post', methods: ['POST'])]
    public function saveDescription(Issue $issue, Request $request): JsonResponse
    {
        $data = $this->getJsonData($request);
        if (!isset($data['description'])) {
            return $this->jsonError('Invalid description', Response::HTTP_BAD_REQUEST);
        }

        $issue->setDescription($data['description']);
        $this->entityManager->flush();

        return $this->jsonSuccess();
    }

    #[Route('/api/issue/{id}/data', name: 'api_issue_data', methods: ['GET'])]
    public function getIssueData(Issue $issue): JsonResponse
    {
        $postIts = $this->postItRepository->findBy(['issue' => $issue]);
        $postItData = array_map(fn($postIt) => [
            'id' => $postIt->getId(),
            'content' => $postIt->getContent(),
            'createdBy' => $postIt->getCreatedBy()->getName(),
            'createdAt' => $postIt->getCreatedAt()->format('c'),
        ], $postIts);

        return $this->jsonSuccess([
            'description' => $issue->getDescription(),
            'postIts' => $postItData,
        ]);
    }

    #[Route('/project/{projectId}/add-issue', name: 'add_issue', methods: ['POST'])]
    public function addIssue(int $projectId, Request $request): JsonResponse
    {
        $data = $this->getJsonData($request);
        $issue = new Issue();
        $issue->setName($data['name'] ?? 'New Issue');
        // Add more fields as necessary

        $this->entityManager->persist($issue);
        $this->entityManager->flush();

        return $this->jsonSuccess([
            'id' => $issue->getId(),
            'name' => $issue->getName(),
            'endDate' => $issue->getEndDate()?->format('Y-m-d')
        ]);
    }

    #[Route('/issue/{id}/archive', name: 'archive_issue', methods: ['POST'])]
    public function archiveIssue(Issue $issue, Request $request): Response
    {
        $issue->setIsArchived(true);
        $this->entityManager->flush();

        return $this->redirectToReferer($request);
    }

    #[Route('/{id}/unarchive', name: 'unarchive', methods: ['POST'])]
    public function unarchiveIssue(Issue $issue, Request $request): Response
    {
        $issue->setIsArchived(false);
        $this->entityManager->flush();

        return $this->redirectToReferer($request);
    }

    #[Route('/issues/{issueId}/postits', name: 'issue_postit_add', methods: ['POST'])]
    public function addPostIt(int $issueId, Request $request): JsonResponse
    {
        $issue = $this->issueRepository->find($issueId);
        $content = $request->request->get('content');

        $postIt = new PostIt();
        $postIt->setContent($content)
            ->setCreatedBy($this->getUser())
            ->setIssue($issue);

        $this->entityManager->persist($postIt);
        $this->entityManager->flush();

        return $this->jsonSuccess(['message' => 'PostIt added']);
    }

    #[Route('/postits/{postItId}/edit', name: 'postit_edit', methods: ['POST'])]
    public function editPostIt(int $postItId, Request $request): JsonResponse
    {
        $postIt = $this->postItRepository->find($postItId);
        $this->denyAccessUnlessGranted('edit', $postIt);

        $content = $request->request->get('content');
        $postIt->setContent($content);

        $this->entityManager->flush();

        return $this->jsonSuccess(['message' => 'PostIt updated']);
    }

    #[Route('/postits/{postItId}/delete', name: 'postit_delete', methods: ['DELETE'])]
    public function deletePostIt(int $postItId): JsonResponse
    {
        $postIt = $this->postItRepository->find($postItId);
        $this->denyAccessUnlessGranted('delete', $postIt);

        $this->entityManager->remove($postIt);
        $this->entityManager->flush();

        return $this->jsonSuccess(['message' => 'PostIt deleted']);
    }

    private function getJsonData(Request $request): array
    {
        return json_decode($request->getContent(), true) ?? [];
    }

    private function jsonError(string $message, int $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return new JsonResponse(['success' => false, 'error' => $message], $status);
    }

    private function jsonSuccess(array $data = []): JsonResponse
    {
        return new JsonResponse(array_merge(['success' => true], $data));
    }


    private function redirectToReferer(Request $request): Response
    {
        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?: $this->generateUrl('issue_list'));
    }
}