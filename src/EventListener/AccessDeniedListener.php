<?php

// src/EventListener/AccessDeniedListener.php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AccessDeniedListener
{
    private Environment $twig;

    public function __construct(RouterInterface $router, Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Check if the exception is an AccessDeniedException
        if ($exception instanceof AccessDeniedHttpException) {
            // Render custom access denied page
            $response = new Response($this->twig->render('error/access_denied.html.twig'));
            $response->setStatusCode(Response::HTTP_FORBIDDEN);

            $event->setResponse($response);
        }
    }
}
