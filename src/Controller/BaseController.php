<?php

// src/Controller/BaseController.php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractController
{
    private ProjectRepository $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function renderWithProjects(string $view, array $parameters = [], Response $response = null): Response
    {
        $projects = $this->projectRepository->findAll(); // Fetch all projects

        // Merge projects into the parameters and render the view
        return $this->render($view, array_merge($parameters, ['projects' => $projects]), $response);
    }
}
