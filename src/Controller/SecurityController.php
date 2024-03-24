<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**login
     * Ce controller nous permet de se connecter
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route('/connexion', name: 'security.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('pages/security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**logout
     * Ce Controller nous permet de se déconnecter
     *
     * @return void
     */
    #[Route('/deconnexion', 'security.logout')]
    public function logout()
    {
        // nothing to do here...
    }

    /**Registration
     * Ce Controller nous permet de s'inscrire
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/inscription', 'security.registration', methods: ['GET', 'POST'])]
    public function registration(Request $request, EntityManagerInterface $manager): Response
    {
        // Création d'une nouvelle instance de l'entité User.
        $user = new User();

        // Attribution du rôle 'ROLE_USER' à l'utilisateur.
        $user->setRoles(['ROLE_USER']);

        // Création du formulaire en utilisant la classe RegistrationType et en y associant
        // l'instance de User créée précédemment.
        $form = $this->createForm(RegistrationType::class, $user);

        // Traitement des données de la requête HTTP et association de ces données
        // avec le formulaire.
        $form->handleRequest($request);

        // Vérification si le formulaire a été soumis et si les données sont valides.
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire (instance de User avec les données remplies).
            $user = $form->getData();

            // Ajout d'un message flash pour notifier l'utilisateur de la création réussie du compte.
            $this->addFlash(
                'success',
                'Votre compte a bien été créé'
            );

            // Persistance de l'objet User dans la base de données.
            $manager->persist($user);
            // Enregistrement effectif des données dans la base de données.
            $manager->flush();

            // Redirection de l'utilisateur vers la route 'security.login'.
            return $this->redirectToRoute('security.login');
        }

        // Si le formulaire n'a pas été soumis ou n'est pas valide, ou si la requête est une
        // méthode GET, affichage du formulaire dans la vue 'registration.html.twig'.
        return $this->render('pages/security/registration.html.twig', [
            'form' => $form->createView() // Création de la représentation du formulaire pour la vue.
        ]);
    }
}
