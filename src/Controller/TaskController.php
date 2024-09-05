<?php

// src/Controller/TaskController.php

namespace App\Controller;

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
    public function allTasks(Request $request, TaskRepository $taskRepository): Response
    {
        $status = $request->query->get('status');
        $criteria = [];

        if ($status) {
            $criteria['status'] = strtolower($status);
        }

        $tasks = $taskRepository->findBy($criteria);

        return $this->render('task/all_tasks.html.twig', [
            'tasks' => $tasks,
        ]);
    }



    #[Route('/tasks/new', name: 'task_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $this->redirectToRoute('all_tasks');
        }

        return $this->render('task/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/tasks/{taskId}', name: 'task_show', methods: ['GET'])]
    public function show(int $taskId): Response
    {
        $task = $this->entityManager->getRepository(Task::class)->find($taskId);
        $totalTimeSpent = $task->getTotalTimeSpent();

        return $this->render('task/show.html.twig', [
            'task' => $task,
            'totalTimeSpent' => $totalTimeSpent,
        ]);
    }



    #[Route('/tasks/{taskId}/edit', name: 'task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $taskId): Response
    {
        $task = $this->entityManager->getRepository(Task::class)->find($taskId);
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('all_tasks');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // src/Controller/TaskController.php

    #[Route('/tasks/{taskId}/complete', name: 'task_complete', methods: ['POST'])]
    public function setCompleted(Request $request, int $taskId): Response
    {
        $task = $this->entityManager->getRepository(Task::class)->find($taskId);

        if (!$task) {
            throw $this->createNotFoundException('Task not found');
        }

        // Set the task status to 'completed'
        $task->setStatus('completed');

        if ($this->isCsrfTokenValid('complete' . $taskId, $request->request->get('_token'))) {
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('all_tasks');
    }

}
