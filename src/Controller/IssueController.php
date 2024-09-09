<?php

// src/Controller/IssueController.php

namespace App\Controller;

use App\Entity\Issue;
use App\Entity\PostIt;
use App\Entity\Task;
use App\Form\IssueType;
use App\Repository\IssueRepository;
use App\Service\IssueService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

class IssueController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private Security $security;
    private IssueService $issueService;

    public function __construct(EntityManagerInterface $entityManager, Security $security, IssueService $issueService)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->issueService = $issueService;

    }

    #[Route('/issues', name: 'all_issues', methods: ['GET'])]
    public function allIssues(IssueRepository $issueRepository, Request $request): Response
    {
        $user = $this->getUser();
        $status = $request->query->get('status');

        if (!$user instanceof UserInterface) {
            throw $this->createAccessDeniedException('You must be logged in to view issues.');
        }

        if ($this->security->isGranted('ROLE_PROJECT_MANAGER')) {
            $issuesQuery = $issueRepository->createQueryBuilder('i');

            if ($status) {
                $issuesQuery->where('i.status = :status')
                    ->setParameter('status', $status);
            }

            $issues = $issuesQuery->getQuery()->getResult();
            $issuesWithNeighbors = $this->issueService->getNeighborsForIssues($issues);
        } else {
            $qb = $this->entityManager->createQueryBuilder();
            $qb->select('i')
                ->from(Issue::class, 'i')
                ->leftJoin('i.task', 't')
                ->leftJoin('t.assignedUsers', 'tu')
                ->leftJoin('i.assignedUsers', 'iu')
                ->where('iu.id = :userId OR tu.id = :userId')
                ->setParameter('userId', $user->getId());

            if ($status) {
                $qb->andWhere('i.status = :status')
                    ->setParameter('status', $status);
            }

            $issues = $qb->getQuery()->getResult();
            $issuesWithNeighbors = $this->issueService->getNeighborsForIssues($issues);
        }

        return $this->render('issue/all_issues.html.twig', [
            'issuesWithNeighbors' => $issuesWithNeighbors,
            'status' => $status,
        ]);
    }






    #[Route('/issues/new', name: 'issue_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $issue = new Issue();
        $form = $this->createForm(IssueType::class, $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Assign the selected users to the issue
            $assignedUsers = $form->get('assignedUsers')->getData();
            foreach ($assignedUsers as $user) {
                $issue->addAssignedUser($user);
            }

            $entityManager->persist($issue);
            $entityManager->flush();

            return $this->redirectToRoute('all_issues');
        }

        return $this->render('issue/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/issues/{issueId}', name: 'issue_show', methods: ['GET'])]
    public function show(int $issueId): Response
    {
        $issue = $this->entityManager->getRepository(Issue::class)->find($issueId);
        $task = $issue->getTask();

        // Use the TaskVoter to check if the user has access to this task
        if (!$this->isGranted('view_task', $task)) {
            throw new AccessDeniedException('You do not have access to view issues under this task.');
        }

        return $this->render('issue/show.html.twig', [
            'issue' => $issue,
        ]);
    }

    #[Route('/issues/{issueId}/edit', name: 'issue_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $issueId): Response
    {
        $issue = $this->entityManager->getRepository(Issue::class)->find($issueId);
        $task = $issue->getTask();

        // Use the TaskVoter to check if the user has access to edit this task
        if (!$this->isGranted('edit_task', $task)) {
            throw new AccessDeniedException('You do not have access to edit issues under this task.');
        }

        $form = $this->createForm(IssueType::class, $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('all_issues');
        }

        return $this->render('issue/edit.html.twig', [
            'form' => $form->createView(),
            'issue' => $issue
        ]);
    }

    #[Route('/issues/{issueId}/complete', name: 'issue_complete', methods: ['POST'])]
    public function setCompleted(Request $request, int $issueId): Response
    {
        $issue = $this->entityManager->getRepository(Issue::class)->find($issueId);

        if (!$issue) {
            throw $this->createNotFoundException('Issue not found');
        }

        $issue->setStatus('completed');

        if ($this->isCsrfTokenValid('complete' . $issueId, $request->request->get('_token'))) {
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('all_issues');
    }

    #[Route('/issues/{issueId}/postits', name: 'issue_postit_add', methods: ['POST'])]
    public function addPostIt(int $issueId, Request $request): JsonResponse
    {
        $issue = $this->entityManager->getRepository(Issue::class)->find($issueId);
        $content = $request->request->get('content');

        $postIt = new PostIt();
        $postIt->setContent($content);
        $postIt->setCreatedBy($this->getUser());
        $postIt->setIssue($issue);

        $this->entityManager->persist($postIt);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'PostIt added']);
    }

    #[Route('/postits/{postItId}/edit', name: 'postit_edit', methods: ['POST'])]
    public function editPostIt(int $postItId, Request $request): JsonResponse
    {
        $postIt = $this->entityManager->getRepository(PostIt::class)->find($postItId);

        if ($postIt->getCreatedBy() !== $this->getUser()) {
            throw new AccessDeniedException('You do not have permission to edit this post-it');
        }

        $content = $request->request->get('content');
        $postIt->setContent($content);

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'PostIt updated']);
    }

    #[Route('/postits/{postItId}/delete', name: 'postit_delete', methods: ['DELETE'])]
    public function deletePostIt(int $postItId): JsonResponse
    {
        $postIt = $this->entityManager->getRepository(PostIt::class)->find($postItId);

        if ($postIt->getCreatedBy() !== $this->getUser()) {
            throw new AccessDeniedException('You do not have permission to delete this post-it');
        }

        $this->entityManager->remove($postIt);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'PostIt deleted']);
    }
}
