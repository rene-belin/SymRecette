<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Form\IngredientType;
use Doctrine\ORM\EntityManager;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    /**
     * le Controller affiche toutes les recettes
     * @param RecipeRepository $recipeRepo
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return request
     */
    #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    public function index(
        RecipeRepository $recipeRepo,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
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
    /**
     * Ce contrôleur nous permet de créer une nouvelle recette
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recette/creation', name: 'recipe.new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        // Création d'une nouvelle instance de l'entité Recipe
        $recipe = new Recipe();

        // Création du formulaire lié à l'entité Recipe
        $form = $this->createForm(RecipeType::class, $recipe);

        // Gestion de la requête et hydratation de l'entité avec les données du formulaire
        $form->handleRequest($request);

        // Vérification si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $recipe = $form->getData();

            // Persistance de l'entité Recipe
            $manager->persist($recipe);

            // Enregistrement des données dans la base de données
            $manager->flush();

            // Ajout d'un message flash pour informer de la création réussie
            $this->addFlash(
                'success',
                'Votre recette a été créé avec succès !'
            );

            // Redirection vers la route qui affiche la liste des recettes
            return $this->redirectToRoute('recipe.index');
        }

        // Si le formulaire n'est pas soumis ou n'est pas valide, on affiche le formulaire
        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * Ce contrôleur nous permet d'éditer une recette
     * @param Recipe $recipe
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return response
     */
    #[Route('/recette/edition/{id}', name: 'recipe.edit', methods: ['GET', 'POST'])]
    public function edit(
        recipe $recipe,
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        // Création du formulaire
        $form = $this->createForm(RecipeType::class, $recipe);

        // Traitement du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // récupère les données qui ont été soumises et mappées aux champs du formulaire
            $recipe = $form->getData();

            $manager->persist($recipe); // Demande à Doctrine de gérer l'objet
            $manager->flush(); // Exécute les requêtes SQL et écrit dans la base de données

            // Message flash correct
            $this->addFlash(
                'success', // Utilisez 'success' comme type de message flash
                'Votre recette a été modifiée avec succès !'
            );

            // Redirection vers la page de l'index des ingrédients après la création
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * Ce contrôleur nous permet de supprimer une recette
     * 
     * @param EntityManagerInterface $manager
     * @param Recipe $recipe
     * @return Response
     */
    #[Route('/recette/suppression/{id}', 'recipe.delete', methods: ['GET'])]
    public function delete(
        EntityManagerInterface $manager,
        Recipe $recipe
    ): Response {
        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre recette a été supprimé avec succès !'
        );

        return $this->redirectToRoute('recipe.index');
    }
}
