<?php

namespace App\Controller;

use App\Entity\Issue;
use App\Entity\Tag;
use App\Form\IssueType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/issue')]
final class IssueController extends AbstractController
{
    #[Route(name: 'issue_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $issues = $entityManager
            ->getRepository(Issue::class)
            ->findAll();

        return $this->render('issue/index.html.twig', [
            'issues' => $issues,
        ]);
    }

    #[Route('/issue/new', name: 'issue_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $issue = new Issue();
        $form = $this->createForm(IssueType::class, $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Tagify'dan gelen etiketler
            $submittedTags = $request->request->get('tags'); // Tagify'dan gelen veriyi alın
            $tagNames = json_decode($submittedTags, true);   // JSON'u bir diziye çevirin

            if (is_null($tagNames)) {
                throw new \Exception('Tagify JSON verisi çözülemedi.');
            }

            foreach ($tagNames as $tagData) {
                $tagName = $tagData['value'];
                $tag = $entityManager->getRepository(Tag::class)->findOneBy(['name' => $tagName]);

                if (!$tag) {
                    $tag = new Tag();
                    $tag->setName($tagName);
                    $tag->setColor('#' . dechex(rand(0x000000, 0xFFFFFF)));  // Yeni etiket için rastgele bir renk belirle
                    $entityManager->persist($tag);
                }

                $issue->addTag($tag); // Issue'ya etiketi ekle
            }


            $entityManager->persist($issue);
            $entityManager->flush();

            return $this->redirectToRoute('issue_index');
        }

        return $this->render('issue/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_issue_show', methods: ['GET'])]
    public function show(Issue $issue): Response
    {
        return $this->render('issue/show.html.twig', [
            'issue' => $issue,
        ]);
    }

    #[Route('/{id}/edit', name: 'issue_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Issue $issue, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(IssueType::class, $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('issue_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('issue/edit.html.twig', [
            'issue' => $issue,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'issue_delete', methods: ['POST'])]
    public function delete(Request $request, Issue $issue, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$issue->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($issue);
            $entityManager->flush();
        }

        return $this->redirectToRoute('issue_index', [], Response::HTTP_SEE_OTHER);
    }
}
