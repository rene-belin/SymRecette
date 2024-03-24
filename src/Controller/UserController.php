<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    // Route pour l'édition du profil utilisateur
    #[Route('/utilisateur/edition/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
    public function edit(
        User $user,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $hasher
    ): Response {
        // Vérifie si un utilisateur est connecté
        if (!$this->getUser()) {
            // Redirection vers la page de connexion si aucun utilisateur n'est connecté
            return $this->redirectToRoute('security.login');
        }

        // Vérifie si l'utilisateur connecté est différent de celui qu'on tente d'éditer
        if ($this->getUser() !== $user) {
            // Redirection vers l'index des recettes si l'utilisateur n'est pas autorisé à éditer ce profil
            return $this->redirectToRoute('recipe.index');
        }

        // Création du formulaire pour éditer l'utilisateur
        $form = $this->createForm(UserType::class, $user);
        // Traitement de la requête HTTP et soumission du formulaire
        $form->handleRequest($request);

        // Vérification de la soumission et de la validité du formulaire
        if ($form->isSubmitted()) {
            // Récupération du mot de passe en clair depuis le formulaire
            $plainPassword = $form->get('plainPassword')->getData();

            // Vérification de la validité du mot de passe
            if ($hasher->isPasswordValid($user, $plainPassword)) {
                // Vérification de la validité des autres champs du formulaire
                if ($form->isValid()) {
                    // Enregistrement des modifications de l'utilisateur dans la base de données
                    $manager->persist($user);
                    $manager->flush();

                    // Message flash de succès
                    $this->addFlash(
                        'success',
                        'Les informations de votre compte ont bien été modifiées.'
                    );

                    // Redirection vers l'index des recettes
                    return $this->redirectToRoute('recipe.index');
                }
            } else {
                // Message flash d'avertissement si le mot de passe est incorrect
                $this->addFlash(
                    'warning',
                    'Le mot de passe saisi est incorrect. Veuillez réessayer.'
                );
            }
        }

        // Affichage de la vue avec le formulaire d'édition de l'utilisateur
        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
