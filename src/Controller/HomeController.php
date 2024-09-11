<?php

// src/Controller/HomeController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends BaseController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->renderWithProjects('home/index.html.twig');
    }
}

