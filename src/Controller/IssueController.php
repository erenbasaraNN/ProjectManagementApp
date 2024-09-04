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

    #[Route('/issues', name: 'issue_index', methods: ['GET'])]
    public function index(IssueRepository $issueRepository): Response
    {
        $issues = $issueRepository->findAll();

        return $this->render('issue/index.html.twig', [
            'issues' => $issues,
        ]);
    }

    #[Route('/tasks/{taskId}/issues/new', name: 'issue_new', methods: ['GET', 'POST'])]
    public function new(Request $request, int $taskId): Response
    {
        $issue = new Issue();
        $form = $this->createForm(IssueType::class, $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($issue);
            $this->entityManager->flush();

            return $this->redirectToRoute('issue_index', ['taskId' => $taskId]);
        }

        return $this->render('issue/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/tasks/{taskId}/issues/{id}', name: 'issue_show', methods: ['GET'])]
    public function show(Issue $issue): Response
    {
        return $this->render('issue/show.html.twig', [
            'issue' => $issue,
        ]);
    }

    #[Route('/tasks/{taskId}/issues/{id}/edit', name: 'issue_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Issue $issue, int $taskId): Response
    {
        $form = $this->createForm(IssueType::class, $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('issue_index', ['taskId' => $taskId]);
        }

        return $this->render('issue/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/tasks/{taskId}/issues/{id}/delete', name: 'issue_delete', methods: ['POST'])]
    public function delete(Request $request, Issue $issue, int $taskId): Response
    {
        if ($this->isCsrfTokenValid('delete' . $issue->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($issue);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('issue_index', ['taskId' => $taskId]);
    }
}
