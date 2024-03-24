<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    // login
    #[Route('/connexion', name: 'security.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('pages/security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    // logout
    #[Route('/deconnexion', 'security.logout')]
    public function logout()
    {
        // nothing to do here...
    }

    // Registration
    #[Route('/inscription', 'security.registration', methods: ['GET', 'POST'])]
    public function registration(): Response
    {
        // Définir le formulaire
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        return $this->render('pages/security/registration.html.twig',[
            'form'=>$form->createView()
        ]);
    }
}
