<?php

namespace App\Controller\API;

use App\DTO\PaginationDTO;
use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use \DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Dom\DtdNamedNodeMap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class RecipesControler extends AbstractController
{
    #[Route('/api/recipes', methods: ['GET'])]
    public function index(
        // Request $request,
        // SerializerInterface $serializer
        RecipeRepository $recipeRepository,
        #[MapQueryString()]
        ?PaginationDTO $paginationDTO = new PaginationDTO(),
    ): JsonResponse {
        // $recipes =  $recipeRepository->findAll();
        // $recipes =  $recipeRepository->paginateRecipes($request->query->getInt('page', 1));
        $recipes =  $recipeRepository->paginateRecipes($paginationDTO->page);
        // dd($serializer->serialize($recipes, 'xml', [
        //     'groups' => ['recipes.index']
        // ]));

        return $this->json($recipes, 200, [], [
            'groups' => ['recipes.index']
        ]);
    }
    #[Route('/api/recipes/{id}', requirements: ['id' => Requirement::DIGITS])]
    public function show(Recipe $recipe): JsonResponse
    {

        return $this->json($recipe, 200, [], [
            'groups' => ['recipes.index', 'recipes.show']
        ]);
    }
    #[Route('/api/recipes', methods: ['POST'])]
    public function create(
        // Request $request,
        // SerializerInterface $serializer
        // TODO: Use a CreatRecipeDTO to avoid direct contact with the entity
        #[MapRequestPayload(serializationContext: ['groups' => ['recipes.create']])]
        Recipe $recipe,
        EntityManagerInterface $em
    ): JsonResponse {
        // dd($request->getContent());       
        // $recipe = new Recipe();

        $recipe
            ->setCreatedAt(new DateTimeImmutable())
            ->setUpdatedAt(new DateTimeImmutable());

        // $serializer->deserialize($request->getContent(), Recipe::class, 'json', [
        //     AbstractNormalizer::OBJECT_TO_POPULATE => $recipe,
        //     'groups' => ['recipes.create']
        // ]);
        // dd($recipe);

        $em->persist($recipe);
        $em->flush();

        return $this->json($recipe, 200, [], [
            'groups' => ['recipes.index', 'recipes.show']
        ]);
    }
}
