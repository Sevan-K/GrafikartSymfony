<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use \DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\VarDumper\Cloner\Data;

final class RecipeController extends AbstractController
{
    #[Route('/recette', name: 'recipe.index')]
    public function index(Request $request, RecipeRepository $repository): Response
    {
        // dd($repository->findTotalDuration());
        // $recipes = $em->getRepository(Recipe::class)->findAll();
        $recipes = $repository->findWithDurationLowerThan(20);
        // $recipes[0]->setTitle('Pâtes Bolognaises');

        // add recipe
        // $recipe = new Recipe();
        // $recipe
        //     ->setTitle('Barbe à papa')
        //     ->setSlug('barbe-papa')
        //     ->setContent('Ajouter le sucre dans la machine magique')
        //     ->setDuration(2)
        //     ->setCreateAt(new DateTimeImmutable())
        //     ->setUpdatedAt(new DateTimeImmutable());

        // $em->persist($recipe);

        // Remove a recipe
        // $em->remove($recipes[0]);


        // $em->flush();


        return $this->render(
            'recipe/index.html.twig',
            [
                'recipes' => $recipes
            ]
        );
    }

    #[Route('/recette/{slug}-{id}', name: 'recipe.show', requirements: [
        'id' => '\d+',
        'slug' => '[a-z0-9-]+'
    ])]
    public function show(Request $request, string $slug, int $id, RecipeRepository $repository): Response
    {
        $recipe = $repository->find($id);

        if ($recipe->getSlug() !== $slug) {
            return $this->redirectToRoute('recipe.show', [
                'slug' => $recipe->getSlug(),
                'id' => $recipe->getId(),
            ]);
        }

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);

        // return new JsonResponse(['slug' => $slug]);
        // return new Response('Recette : ' . $slug);
        // dd($slug, $id);
        // dd($request->attributes->get('slug'), $request->attributes->getInt('id'));
    }

    #[Route('/recette/{id}/edit', name: 'recipe.edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em): Response
    {
        // Symfony finds the recipe with the id

        $form = $this->createForm(RecipeType::class, $recipe);
        // Update the $recipe instance of Recipe Entity with form data
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setUpdatedAt(new DateTimeImmutable());
            $em->flush();
            $this->addFlash('success', 'La recette a bien été modifiée');

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    #[Route('/recette/create', 'recipe.create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setCreateAt(new DateTimeImmutable());
            $recipe->setUpdatedAt(new DateTimeImmutable());
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette a bien été crée');

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipe/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/recette/{id}/edit', name: 'recipe.delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function remove(Recipe $recipe, EntityManagerInterface $em)
    {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', 'La recette a bien été supprimée');

        return  $this->redirectToRoute('recipe.index');
    }
}
