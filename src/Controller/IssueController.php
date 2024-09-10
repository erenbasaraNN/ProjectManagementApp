<?php

namespace App\Controller;

use App\Entity\Issue;
use App\Form\IssueType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/issue')]
final class IssueController extends AbstractController
{
    #[Route(name: 'app_issue_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $issues = $entityManager
            ->getRepository(Issue::class)
            ->findAll();

        return $this->render('issue/index.html.twig', [
            'issues' => $issues,
        ]);
    }

    #[Route('/new', name: 'app_issue_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $issue = new Issue();
        $form = $this->createForm(IssueType::class, $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($issue);
            $entityManager->flush();

            return $this->redirectToRoute('app_issue_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('issue/new.html.twig', [
            'issue' => $issue,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_issue_show', methods: ['GET'])]
    public function show(Issue $issue): Response
    {
        return $this->render('issue/show.html.twig', [
            'issue' => $issue,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_issue_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Issue $issue, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(IssueType::class, $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_issue_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('issue/edit.html.twig', [
            'issue' => $issue,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_issue_delete', methods: ['POST'])]
    public function delete(Request $request, Issue $issue, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$issue->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($issue);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_issue_index', [], Response::HTTP_SEE_OTHER);
    }
}
