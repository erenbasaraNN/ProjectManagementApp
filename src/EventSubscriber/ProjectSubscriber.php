<?php
// src/EventSubscriber/ProjectSubscriber.php

namespace App\EventSubscriber;

use App\Repository\ProjectRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;

class ProjectSubscriber implements EventSubscriberInterface
{
    private $projectRepository;
    private $twig;

    public function __construct(ProjectRepository $projectRepository, Environment $twig)
    {
        $this->projectRepository = $projectRepository;
        $this->twig = $twig;
    }

    public function onKernelController(ControllerEvent $event)
    {
        // Fetch projects from the repository
        $projects = $this->projectRepository->findAll();

        // Make the projects available globally in Twig templates
        $this->twig->addGlobal('projects', $projects);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
