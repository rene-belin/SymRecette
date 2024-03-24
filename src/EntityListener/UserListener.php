<?php

namespace App\EntityListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * La classe UserListener est responsable de l'encodage du mot de passe d'une
 * entité User avant qu'elle ne soit enregistrée ou mise à jour dans la base de
 * données.
 */
class UserListener
{
    private UserPasswordHasherInterface $hasher;

    /**
     * Constructeur de la classe UserListener.
     *
     * @param UserPasswordHasherInterface $hasher Le service de hachage de mot de passe.
     */
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * Écouteur d'événement prePersist.
     *
     * Cette méthode est appelée avant qu'une entité User soit persistée.
     * Elle encode le mot de passe de l'utilisateur.
     *
     * @param User $user L'entité User qui va être persistée.
     */
    public function prePersist(User $user)
    {
        $this->encodePassword($user);
    }

    // /**
    //  * Écouteur d'événement preUpdate.
    //  *
    //  * Cette méthode est appelée avant qu'une entité User soit mise à jour.
    //  * Elle encode le mot de passe de l'utilisateur s'il a été modifié.
    //  *
    //  * @param User $user L'entité User qui va être mise à jour.
    //  */
    // public function preUpdate(User $user)
    // {
    //     $this->encodePassword($user);
    // }

    /**
     * Encode le mot de passe en se basant sur le mot de passe en clair.
     *
     * Si le mot de passe en clair est null, la méthode ne fait rien.
     * Sinon, elle encode le mot de passe et le définit dans l'entité User,
     * puis efface le mot de passe en clair pour des raisons de sécurité.
     *
     * @param User $user L'entité User dont le mot de passe doit être encodé.
     * @return void
     */
    public function encodePassword(User $user)
    {
        if ($user->getPlainPassword() === null) {
            return;
        }

        $user->setPassword(
            $this->hasher->hashPassword(
                $user,
                $user->getPlainPassword()
            )
        );

        // Efface le mot de passe en clair après l'encodage pour la sécurité.
        $user->setPlainPassword(null);
    }
}
