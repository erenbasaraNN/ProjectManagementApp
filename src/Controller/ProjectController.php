<?php

// src/Controller/ProjectController.php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/projects', name: 'all_projects', methods: ['GET'])]
    public function allProjects(): Response
    {
        $projects = $this->entityManager->getRepository(Project::class)->findAll();

        return $this->render('project/all_projects.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/projects/new', name: 'project_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($project);
            $this->entityManager->flush();

            return $this->redirectToRoute('project_show', ['projectId' => $project->getId()]);
        }

        return $this->render('project/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/projects/{projectId}', name: 'project_show', methods: ['GET'])]
    public function show(int $projectId): Response
    {
        $project = $this->entityManager->getRepository(Project::class)->find($projectId);

        if (!$project) {
            throw $this->createNotFoundException('The project does not exist');
        }

        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }




    #[Route('/projects/{projectId}/edit', name: 'project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $projectId): Response
    {
        $project = $this->entityManager->getRepository(Project::class)->find($projectId);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('project_show', ['projectId' => $projectId]);
        }

        return $this->render('project/edit.html.twig', [
            'form' => $form->createView(),
            'project' => $project,  // Pass project to template
        ]);
    }

}
