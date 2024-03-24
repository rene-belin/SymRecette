<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    // Définition de la route pour l'édition d'un utilisateur avec son ID dans l'URL

    /**
     * Ce controller permet d'éditer le profil utilisateur
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/utilisateur/edition/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request, EntityManagerInterface $manager): Response
    {
        // Vérifie si un utilisateur est connecté
        if (!$this->getUser()) {
            // Si aucun utilisateur n'est connecté, redirige vers la page de connexion
            return $this->redirectToRoute('security.login');
        }

        // Vérifie si l'utilisateur connecté est différent de celui qu'on tente d'éditer
        if ($this->getUser() !== $user) {
            // Si c'est le cas, redirige vers l'index des recettes
            return $this->redirectToRoute('recipe.index');
        }

        // Crée le formulaire pour éditer l'utilisateur
        $form = $this->createForm(UserType::class, $user);

        // Gère la requête et soumet le formulaire
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les données du formulaire
            $user = $form->getData();

            // Persiste les données de l'utilisateur dans la base de données
            $manager->persist($user);
            // Applique les changements dans la base de données
            $manager->flush();

            // Ajoute un message flash de succès
            $this->addFlash(
                'success',
                'Les informations de votre compte ont bien été modifiées.'
            );

            // Redirige vers l'index des recettes
            return $this->redirectToRoute('recipe.index');
        }

        // Rend la vue avec le formulaire pour éditer l'utilisateur
        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
