<?php
// src/Controller/BrandController.php

namespace App\Controller;

use App\Entity\Brand;
use App\Form\BrandType;
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

        return $this->render('brand/show.html.twig', [
            'brand' => $brand,
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
}
