<?php

// src/Controller/TaskController.php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/tasks', name: 'all_tasks', methods: ['GET'])]
    public function allTasks(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();

        return $this->render('task/all_tasks.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/project/{projectId}/tasks', name: 'task_index', methods: ['GET'])]
    public function index(int $projectId, TaskRepository $taskRepository): Response
    {
        $project = $this->entityManager->getRepository(Project::class)->find($projectId);
        $tasks = $taskRepository->findBy(['project' => $project]);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'project' => $project,
        ]);
    }

    #[Route('/project/{projectId}/tasks/new', name: 'task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, int $projectId): Response
    {
        $project = $this->entityManager->getRepository(Project::class)->find($projectId);
        $task = new Task();
        $task->setProject($project);

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $this->redirectToRoute('task_index', ['projectId' => $projectId]);
        }

        return $this->render('task/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/project/{projectId}/tasks/{taskId}', name: 'task_show', methods: ['GET'])]
    public function show(int $projectId, int $taskId): Response
    {
        $task = $this->entityManager->getRepository(Task::class)->find($taskId);
        $project = $this->entityManager->getRepository(Project::class)->find($projectId);

        return $this->render('task/show.html.twig', [
            'task' => $task,
            'project' => $project,
        ]);
    }

    #[Route('/project/{projectId}/tasks/{taskId}/edit', name: 'task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $projectId, int $taskId): Response
    {
        $task = $this->entityManager->getRepository(Task::class)->find($taskId);
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('task_index', ['projectId' => $projectId]);
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
            'project' => $task->getProject(),
        ]);
    }
}
