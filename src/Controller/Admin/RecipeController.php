<?php

namespace App\Controller\Admin;

use App\Demo;
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
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\VarDumper\Cloner\Data;

#[Route('/admin/recettes', name: 'admin.recipe.')]
final class RecipeController extends AbstractController
{
    public function __construct(private RecipeRepository $repository) {}

    // #[Route('/demo', name: 'recipe.demo')]
    // public function demo(Demo $demo)
    // {
    //     dd($demo);
    // }

    #[Route('/', name: 'index')]
    public function index(): Response
    {

        // dd($this->container->get('validator'));

        // dd($repository->findTotalDuration());
        // $recipes = $em->getRepository(Recipe::class)->findAll();
        $recipes = $this->repository->findWithDurationLowerThan(20);
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
            'admin/recipe/index.html.twig',
            [
                'recipes' => $recipes
            ]
        );
    }

    #[Route('/{slug}-{id}', name: 'show', requirements: [
        'id' => '\d+',
        'slug' => '[a-z0-9-]+'
    ])]
    public function show(string $slug, int $id): Response
    {
        $recipe = $this->repository->find($id);

        if ($recipe->getSlug() !== $slug) {
            return $this->redirectToRoute('admin.recipe.show', [
                'slug' => $recipe->getSlug(),
                'id' => $recipe->getId(),
            ]);
        }

        return $this->render('admin/recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);

        // return new JsonResponse(['slug' => $slug]);
        // return new Response('Recette : ' . $slug);
        // dd($slug, $id);
        // dd($request->attributes->get('slug'), $request->attributes->getInt('id'));
    }

    #[Route('/create', 'create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $recipe->setCreateAt(new DateTimeImmutable());
            // $recipe->setUpdatedAt(new DateTimeImmutable());
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette a bien été crée');

            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em): Response
    {
        // Symfony finds the recipe with the id

        $form = $this->createForm(RecipeType::class, $recipe);
        // Update the $recipe instance of Recipe Entity with form data
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $recipe->setUpdatedAt(new DateTimeImmutable());
            $em->flush();
            $this->addFlash('success', 'La recette a bien été modifiée');

            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }



    #[Route('/{id}', name: 'delete', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function remove(Recipe $recipe, EntityManagerInterface $em)
    {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', 'La recette a bien été supprimée');

        return  $this->redirectToRoute('admin.recipe.index');
    }
}
