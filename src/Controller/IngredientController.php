<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IngredientController extends AbstractController
{
    /**
     * Displays a paginated list of Ingredient entities.
     *
     * This method uses dependency injection to access the ingredient repository
     * and the pagination service. It utilizes the Paginator to paginate the query
     * results and renders the view with the paginated list of ingredients.
     *
     * @Route('/ingredient', name: 'ingredient.index', methods: ['GET'])
     *
     * @param IngredientRepository $ingredientRepo The ingredient repository
     * @param PaginatorInterface   $paginator      The paginator service
     * @param Request              $request        The request object
     *
     * @return Response A Response instance containing the rendered view
     */
    #[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
    // Cette méthode affiche la liste des ingrédients avec pagination.
    // Injection de dépendance pour accéder au repository des ingrédients
    // et au service de pagination.
    public function index(
        IngredientRepository $ingredientRepo,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        // Utilisation du Paginator pour paginer les résultats de la requête
        $ingredients = $paginator->paginate(
            $ingredientRepo->findAll(), // Requête pour obtenir tous les ingrédients
            $request->query->getInt('page', 1), // Numéro de la page actuelle
            10 // Nombre d'ingrédients par page
        );

        // Rendu de la vue avec la liste des ingrédients paginée
        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients, // Passage des ingrédients à la vue
        ]);
    }

    /**
     * Creates a new Ingredient entity and handles the form submission.
     *
     * This method initializes a form for creating a new Ingredient. On form submission,
     * it persists the new entity to the database and redirects to the ingredient list
     * if the form is valid. It also sets a flash message indicating success.
     *
     * @Route('/ingredient/nouveau', name: 'ingredient.new', methods: ['GET', 'POST'])
     *
     * @param Request                $request The request object
     * @param EntityManagerInterface $manager The entity manager interface
     *
     * @return Response A Response instance containing the rendered view or a redirect
     */
    #[Route('/ingredient/nouveau', 'ingredient.new')]
    public function new(Request $request,
     EntityManagerInterface $manager
     ): Response  {
        // Création du formulaire
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        // Traitement du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            $manager->persist($ingredient); // Demande à Doctrine de gérer l'objet
            $manager->flush(); // Exécute les requêtes SQL et écrit dans la base de données

            // Message flash correct
            $this->addFlash(
                'success', // Utilisez 'success' comme type de message flash
                'Votre ingrédient a été créé avec succès !'
            );

            // Redirection vers la page de l'index des ingrédients après la création
            return $this->redirectToRoute('ingredient.index');
        }

        // Génère le HTML pour le formulaire d'ajout d'un nouvel ingrédient
        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**Function edit
     * Edits an existing Ingredient entity.
     *
     * This method retrieves an Ingredient entity by its ID and initializes a form
     * for editing. The form is then rendered in the view. If the form is submitted
     * and valid, the changes are persisted to the database.
     *
     * @Route('/ingredient/edition/{id}', name: 'ingredient.edit', methods: ['GET', 'POST'])
     *
     * @param IngredientRepository $ingredientRepo The repository for Ingredient entities
     * @param int $id The ID of the Ingredient to edit
     * @return Response A Response instance containing the rendered view
     */

    #[Route('/ingredient/edition/{id}', name: 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(
        Ingredient $ingredient,
         Request $request,
          EntityManagerInterface $manager
          ): Response  {
        // Création du formulaire
        $form = $this->createForm(IngredientType::class, $ingredient);
        // Traitement du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            $manager->persist($ingredient); // Demande à Doctrine de gérer l'objet
            $manager->flush(); // Exécute les requêtes SQL et écrit dans la base de données

            // Message flash correct
            $this->addFlash(
                'success', // Utilisez 'success' comme type de message flash
                'Votre ingrédient a été modifié avec succès !'
            );

            // Redirection vers la page de l'index des ingrédients après la création
            return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Ce contrôleur nous permet de supprimer une recette
     */
    #[Route('/ingredient/suppression/{id}', 'ingredient.delete', methods: ['GET'])]
    public function delete(
        EntityManagerInterface $manager,
        Ingredient $ingredient
    ): Response {
        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre ingrédient a été supprimé avec succès !'
        );

        return $this->redirectToRoute('ingredient.index');
    }
}
