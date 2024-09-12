<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/users', name: 'get_users', methods: ['GET'])]
    public function getUsers(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $users = $entityManager->getRepository(User::class)->findAll();
            $userData = array_map(function($user) {
                return ['value' => $user->getId(), 'name' => $user->getName(), 'color' => $user->getColor()];
            }, $users);

            return new JsonResponse($userData);
        } catch (\Exception $e) {
            // Log the error
            $this->get('logger')->error('Error fetching users: ' . $e->getMessage());

            // Return an error response
            return new JsonResponse(['error' => 'An error occurred while fetching users'], 500);
        }
    }
}
