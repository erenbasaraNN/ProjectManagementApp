<?php

namespace App\Controller;

use App\Entity\Issue;
use App\Entity\Project;
use App\Entity\Tag;
use App\Form\ProjectType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/project')]
final class ProjectController extends AbstractController
{
    #[Route(name: 'project_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $projects = $entityManager
            ->getRepository(Project::class)
            ->findAll();

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/new', name: 'project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/project/{id}', name: 'project_show')]
    public function show(Project $project, EntityManagerInterface $entityManager): Response
    {
        // Get all tags associated with the project
        $tags = $entityManager->getRepository(Tag::class)->findBy(['project' => $project]);

        // Initialize an array to hold the issues grouped by tags
        $tagIssues = [];

        foreach ($tags as $tag) {
            if ($tag !== null) {
                // Find all issues related to this tag
                $issues = $entityManager->getRepository(Issue::class)
                    ->createQueryBuilder('i')
                    ->join('i.tags', 't')
                    ->where('t = :tag')
                    ->setParameter('tag', $tag)
                    ->getQuery()
                    ->getResult();

                // Add the tag and its issues to the array
                $tagIssues[] = [
                    'name' => $tag->getName(),
                    'color' => $tag->getColor(),
                    'issues' => $issues,
                ];
            }
        }

        // Pass the project and the issues grouped by tags to the template
        return $this->render('project/show.html.twig', [
            'project' => $project,
            'tags' => $tagIssues,
        ]);
    }

    #[Route('/project/{id}/add-group', name: 'add_group', methods: ['POST'])]
    public function addGroup(Project $project, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Get the group name and color from the request
        $groupName = $request->request->get('group_name');
        $groupColor = $request->request->get('group_color');

        if (empty($groupName) || empty($groupColor)) {
            return new JsonResponse(['error' => 'Group name and color are required'], 400);
        }

        // Create a new Tag (Group)
        $tag = new Tag();
        $tag->setName($groupName);
        $tag->setColor($groupColor);
        $tag->setProject($project);

        // Save the tag to the database
        $entityManager->persist($tag);
        $entityManager->flush();

        return new JsonResponse([
            'name' => $tag->getName(),
            'color' => $tag->getColor(),
            'id' => $tag->getId(),
        ], 200);
    }

    #[Route('/{id}/edit', name: 'project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'project_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project_index', [], Response::HTTP_SEE_OTHER);
    }
}
