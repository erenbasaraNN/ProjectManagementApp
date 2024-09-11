<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TagController extends AbstractController
{
    #[Route('/tags', name: 'tags_list')]
    public function list(EntityManagerInterface $entityManager): JsonResponse
    {
        // Mevcut etiketleri çek
        $tags = $entityManager->getRepository(Tag::class)->findAll();

        // Tagify için JSON formatına uygun hale getir
        $tagNames = [];
        foreach ($tags as $tag) {
            $tagNames[] = ['value' => $tag->getName()];
        }

        return new JsonResponse($tagNames);
    }
}
