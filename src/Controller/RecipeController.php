<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    /**
     * recipe.
     */
    #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    public function index(RecipeRepository $recipeRepo, PaginatorInterface $paginator, Request $request): Response
    {
        // Utilisation du Paginator pour paginer les résultats de la requête
        $recipes = $paginator->paginate(
            $recipeRepo->findAll(), // Requête pour obtenir toutes les recettes
            $request->query->getInt('page', 1), // Numéro de la page actuelle
            10 // Nombre de recettes par page
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes, // Passez la variable, pas une chaîne
        ]);
    }

    #[Route('/recette/creation', name: 'recipe.new', methods: ['GET', 'POST'])]
    public function new(): Response
    {
        return $this->render('pages/recipe/new.html.twig');
    }
}
