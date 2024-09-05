<?php
// src/Controller/BrandController.php

namespace App\Controller;

use App\Entity\Brand;
use App\Form\BrandType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BrandController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/brands', name: 'all_brands', methods: ['GET'])]
    public function allBrands(): Response
    {
        $brands = $this->entityManager->getRepository(Brand::class)->findAll();

        return $this->render('brand/all_brands.html.twig', [
            'brands' => $brands,
        ]);
    }
    #[Route('/brands/new', name: 'brand_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $brand = new Brand();
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($brand);
            $this->entityManager->flush();

            return $this->redirectToRoute('brand_show', ['brandId' => $brand->getId()]);
        }

        return $this->render('brand/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/brands/{brandId}', name: 'brand_show', methods: ['GET'])]
    public function show(int $brandId): Response
    {
        $brand = $this->entityManager->getRepository(Brand::class)->find($brandId);
        $totalTimeSpent = $brand->getTotalTimeSpent();  // Calculate total time spent for the brand

        return $this->render('brand/show.html.twig', [
            'brand' => $brand,
            'totalTimeSpent' => $totalTimeSpent,  // Pass total time to template
        ]);
    }


    #[Route('/brands/{brandId}/edit', name: 'brand_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $brandId): Response
    {
        $brand = $this->entityManager->getRepository(Brand::class)->find($brandId);
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('all_brands');
        }

        return $this->render('brand/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/brands/{brandId}/delete', name: 'brand_delete', methods: ['POST'])]
    public function delete(Request $request, int $brandId): Response
    {
        $brand = $this->entityManager->getRepository(Brand::class)->find($brandId);

        if (!$brand) {
            return $this->redirectToRoute('all_brands');
        }

        if ($this->isCsrfTokenValid('delete' . $brandId, $request->request->get('_token'))) {
            try {
                $this->entityManager->remove($brand);
                $this->entityManager->flush();
            } catch (ForeignKeyConstraintViolationException $e) {
                // Handle the foreign key violation
                $this->addFlash('error', 'Bu Brandin bir projesi var. Projesi olan brandler silinemez.');
            }
        }

        return $this->redirectToRoute('all_brands');
    }
}
