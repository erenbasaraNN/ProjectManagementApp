<?php

// src/Controller/ProjectController.php

namespace App\Controller;

use App\Entity\Brand;
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

    #[Route('/brand/{brandId}/projects', name: 'project_index', methods: ['GET'])]
    public function index(int $brandId): Response
    {
        $brand = $this->entityManager->getRepository(Brand::class)->find($brandId);
        $projects = $brand->getProjects();

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
            'brand' => $brand,
        ]);
    }

    #[Route('/brand/{brandId}/projects/new', name: 'project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, int $brandId): Response
    {
        $brand = $this->entityManager->getRepository(Brand::class)->find($brandId);
        $project = new Project();
        $project->setBrand($brand);

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($project);
            $this->entityManager->flush();

            return $this->redirectToRoute('project_index', ['brandId' => $brandId]);
        }

        return $this->render('project/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/brand/{brandId}/projects/{id}', name: 'project_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
            'tasks' => $project->getTasks(),
        ]);
    }

    #[Route('/brand/{brandId}/projects/{id}/edit', name: 'project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, int $brandId): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('project_index', ['brandId' => $brandId]);
        }

        return $this->render('project/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
