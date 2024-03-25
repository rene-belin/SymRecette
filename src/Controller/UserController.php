<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    // Route pour l'édition du profil utilisateur
    #[Route('/utilisateur/edition/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
    public function edit(
        User $choosenUser,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $hasher
    ): Response {

        // Création du formulaire pour éditer l'utilisateur
        $form = $this->createForm(UserType::class, $choosenUser);
        // Traitement de la requête HTTP et soumission du formulaire
        $form->handleRequest($request);

        // Vérification de la soumission et de la validité du formulaire
        if ($form->isSubmitted()) {
            // Récupération du mot de passe en clair depuis le formulaire
            $plainPassword = $form->get('plainPassword')->getData();

            // Vérification de la validité du mot de passe
            if ($hasher->isPasswordValid($choosenUser, $plainPassword)) {
                // Vérification de la validité des autres champs du formulaire
                if ($form->isValid()) {
                    // Enregistrement des modifications de l'utilisateur dans la base de données
                    $manager->persist($choosenUser);
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

    /**
     * Ce controller permet de modifier le mot de passe
     *
     * @param User $user
     * @param Request $request
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    #[Route('/utilisateur/edition-mot-de-passe/{id}', name: 'user.edit.password', methods: ['GET', 'POST'])]
    public function editPassword(
        User $choosenUser,
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $manager
    ): Response {

        // Création du formulaire pour modifier le mot de passe de l'utilisateur
        $form = $this->createForm(UserPasswordType::class);

        // Traitement de la requête HTTP et soumission du formulaire
        $form->handleRequest($request);

        // Vérification de la soumission et de la validité du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification de la validité du mot de passe
            if ($hasher->isPasswordValid($choosenUser, $form->getData()['plainPassword'])) {
                // Hashage et enregistrement du nouveau mot de passe dans la base de données
                $choosenUser->setPassword($hasher->hashPassword($choosenUser, $form->getData()['newPassword']));
                $manager->persist($choosenUser);
                $manager->flush();

                // Message flash de succès
                $this->addFlash(
                    'success',
                    'Le mot de passe a été modifié avec succès.'
                );
                // Redirection vers l'index des recettes
                return $this->redirectToRoute('recipe.index');
            } else {
                // Message flash d'avertissement si le mot de passe est incorrect
                $this->addFlash(
                    'warning',
                    'Le mot de passe saisi est incorrect. Veuillez réessayer.'
                );
            }
        }

        // Affichage du formulaire dans la vue
        return $this->render('pages/user/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
