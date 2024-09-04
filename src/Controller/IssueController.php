<?php

// src/Controller/IssueController.php

namespace App\Controller;

use App\Entity\Issue;
use App\Form\IssueType;
use App\Repository\IssueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IssueController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/issues', name: 'all_issues', methods: ['GET'])]
    public function allIssues(IssueRepository $issueRepository): Response
    {
        $issues = $issueRepository->findAll();

        return $this->render('issue/all_issues.html.twig', [
            'issues' => $issues,
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

        return $this->render('issue/show.html.twig', [
            'issue' => $issue,
        ]);
    }

    #[Route('/issues/{issueId}/edit', name: 'issue_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $issueId): Response
    {
        $issue = $this->entityManager->getRepository(Issue::class)->find($issueId);
        $form = $this->createForm(IssueType::class, $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('all_issues');
        }

        return $this->render('issue/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/issues/{issueId}/delete', name: 'issue_delete', methods: ['POST'])]
    public function delete(Request $request, int $issueId): Response
    {
        $issue = $this->entityManager->getRepository(Issue::class)->find($issueId);

        if ($this->isCsrfTokenValid('delete' . $issueId, $request->request->get('_token'))) {
            $this->entityManager->remove($issue);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('all_issues');
    }
}
