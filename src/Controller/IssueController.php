<?php

namespace App\Controller;

use App\Entity\Issue;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class IssueController extends AbstractController
{
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    #[Route('/issue/{id}/edit-name', name: 'edit_issue_name', methods: ['POST'])]
    public function editIssueName(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Log the request for debugging
        error_log('Received request for issue ID: ' . $id);
        error_log('Request content: ' . $request->getContent());

        $submittedToken = $request->headers->get('X-CSRF-TOKEN');
        if (!$this->isCsrfTokenValid('authenticate', $submittedToken)) {
            return new JsonResponse(['success' => false, 'error' => 'Invalid CSRF token'], 403);
        }

        $issue = $entityManager->getRepository(Issue::class)->find($id);

        if (!$issue) {
            return new JsonResponse(['success' => false, 'error' => 'Issue not found.'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'])) {
            return new JsonResponse(['success' => false, 'error' => 'Name is required.'], 400);
        }

        $issue->setName($data['name']);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/issue/{id}/edit-status', name: 'edit_issue_status', methods: ['POST'])]
    public function editStatus(Issue $issue, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Retrieve the data from the request
        $data = json_decode($request->getContent(), true);

        // Check if 'status' is provided in the request
        if (!isset($data['status'])) {
            return new JsonResponse(['success' => false, 'error' => 'Status not provided'], 400);
        }

        // Update the status of the issue
        $newStatus = $data['status'];
        $issue->setStatus($newStatus);

        // Save the updated issue
        try {
            $entityManager->persist($issue);
            $entityManager->flush();

            return new JsonResponse(['success' => true, 'status' => $newStatus]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error' => 'Failed to update status'], 500);
        }
    }

    /**
     * @Route("/issue/{id}/edit-priority", name="issue_edit_priority", methods={"POST"})
     */
    public function editIssuePriority(Issue $issue, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $issue->setPriority($data['priority']);
        $entityManager->flush();

        return new JsonResponse(['status' => 'success']);
    }

    #[Route("/issue/{id}/edit-assignees", name: "issue_edit_assignees", methods: ["POST"])]
    public function editIssueAssignees(
        Issue $issue,
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['assignees']) || !is_array($data['assignees'])) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid assignees data'], 400);
        }

        // Clear current assignees
        foreach ($issue->getAssignees() as $assignee) {
            $issue->removeAssignee($assignee);
        }

        // Assign new users
        foreach ($data['assignees'] as $assigneeName) {
            $user = $userRepository->findOneBy(['name' => $assigneeName]);
            if ($user) {
                $issue->addAssignee($user);
            }
        }

        $entityManager->flush();

        // Return the updated list of assignees
        $updatedAssignees = array_map(function ($assignee) {
            return $assignee->getName();
        }, $issue->getAssignees()->toArray());

        return new JsonResponse([
            'status' => 'success',
            'assignees' => $updatedAssignees
        ]);
    }

    /**
     * @Route("/issue/{id}/edit-date", name="issue_edit_date", methods={"POST"})
     */
    public function editIssueDate(Issue $issue, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $issue->setEndDate(new \DateTime($data['endDate']));
        $entityManager->flush();

        return new JsonResponse(['status' => 'success']);
    }
}