<?php

namespace App\Controller\Admin;

use App\Demo;
use App\Entity\Category;
use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use \DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\VarDumper\Cloner\Data;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

#[Route('/admin/recettes', name: 'admin.recipe.')]
#[IsGranted('ROLE_ADMIN')]
final class RecipeController extends AbstractController
{
    public function __construct(private RecipeRepository $repository) {}

    // #[Route('/demo', name: 'recipe.demo')]
    // public function demo(Demo $demo)
    // {
    //     dd($demo);
    // }

    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository, Request $request): Response
    {

        // $this->denyAccessUnlessGranted('ROLE_USER');

        // $rice = $this->repository->findOneBy(['slug' => 'riz-cantonais']);
        // $mainDish = $categoryRepository->findOneBy(['slug' => 'plat-principal']);
        // $rice->setCategory($mainDish);
        // $em->flush();
        // dd($rice);

        // dd($this->container->get('validator'));

        // dd($repository->findTotalDuration());
        // $recipes = $em->getRepository(Recipe::class)->findAll();
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


        // $category = (new Category())->setCreatedAt(new DateTimeImmutable())
        //     ->setUpdatedAt(new DateTimeImmutable())
        //     ->setName('demo')
        //     ->setSlug('demo');



        // $recipes[0]->setCategory($category);
        // $em->flush();

        // $recipes = $this->repository->findWithDurationLowerThan(20);

        // $limit = 2;
        $page = $request->query->getInt('page', 1);
        $titleFilter = $request->query->getString('titleFilter', '');

        $recipes = $this->repository->paginateRecipes($page, $titleFilter);
        // $pages = ceil($recipes->getTotalItemCount() / $limit);

        // dd($recipes->count());

        return $this->render(
            'admin/recipe/index.html.twig',
            [
                'recipes' => $recipes,
                // 'pages' => $pages,
                // 'page' => $page,
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
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em, UploaderHelper $helper): Response
    {
        // Symfony finds the recipe with the id
        // dd($helper->asset($recipe, 'thumbnailFile'));

        $form = $this->createForm(RecipeType::class, $recipe);
        // Update the $recipe instance of Recipe Entity with form data
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $recipe->setUpdatedAt(new DateTimeImmutable());
            // /**
            //  * @var UploadedFile
            //  */
            // $file = $form->get('thumbnailFile')->getData();
            // $fileName = $recipe->getId() . '.' . $file->getClientOriginalExtension();
            // // dd($this->getParameter('kernel.project_dir'));
            // $file->move($this->getParameter('kernel.project_dir') . '/public/images/recipes', $fileName);
            // $recipe->setThumbnail($fileName);
            // // dd($file->getClientOriginalName(), $file->getClientOriginalExtension());

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
