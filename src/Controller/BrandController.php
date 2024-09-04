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

#[Route('/brand')]
class BrandController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // List all brands
    #[Route('/', name: 'brand_index', methods: ['GET'])]
    public function index(): Response
    {
        $brands = $this->entityManager->getRepository(Brand::class)->findAll();

        return $this->render('brand/index.html.twig', [
            'brands' => $brands,
        ]);
    }

    // Create a new brand
    #[Route('/new', name: 'brand_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $brand = new Brand();
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($brand);
            $this->entityManager->flush();

            return $this->redirectToRoute('brand_index');
        }

        return $this->render('brand/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Show a specific brand and its projects
    #[Route('/{id}', name: 'brand_show', methods: ['GET'])]
    public function show(Brand $brand): Response
    {
        return $this->render('brand/show.html.twig', [
            'brand' => $brand,
            'projects' => $brand->getProjects(),
        ]);
    }

    // Edit a brand
    #[Route('/{id}/edit', name: 'brand_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Brand $brand): Response
    {
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('brand_index');
        }

        return $this->render('brand/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
