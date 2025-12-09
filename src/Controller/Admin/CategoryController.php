<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/categories', 'admin.category.')]
#[IsGranted('ROLE_ADMIN')]
final class CategoryController extends AbstractController
{
    public function __construct(private CategoryRepository $repository) {}

    #[Route('/', name: 'index')]
    public function index(CategoryRepository $repository): Response
    {
        $categories = $repository->findAll();

        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/{slug}-{id}', name: 'show', requirements: [
        'id' => Requirement::DIGITS,
        'slug' => Requirement::ASCII_SLUG,
    ])]
    public function show(string $slug, int $id): Response
    {
        /**
         * @var Category
         */
        $category = $this->repository->find($id);

        if ($category->getSlug() !== $slug) {
            return $this->redirectToRoute('admin.category.show', [
                'slug' => $category->getSlug(),
                'id' => $category->getId(),
            ]);
        }

        return $this->render('admin/category/show.html.twig', [
            'category' => $category,
        ]);

        // return new JsonResponse(['slug' => $slug]);
        // return new Response('Recette : ' . $slug);
        // dd($slug, $id);
        // dd($request->attributes->get('slug'), $request->attributes->getInt('id'));
    }

    #[Route('/create', 'create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', "Catégorie crée avec succès");

            return $this->redirectToRoute('admin.category.index');
        }

        return $this->render('admin/category/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', 'edit', requirements: ['id' => Requirement::DIGITS], methods: ['POST', 'GET'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $em)
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', "La catégorie a bien été modifiée");

            return $this->redirectToRoute('admin.category.index');
        }

        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', 'delete', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function remove(Category $category, EntityManagerInterface $em)
    {
        $em->remove($category);
        $em->flush();
        $this->addFlash('success', "La catégorie a bien été supprimée");

        return $this->redirectToRoute('admin.category.index');
    }
}
