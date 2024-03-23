<?php

// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Ingredients
        $ingredients = [];
        for ($i = 0; $i < 50; ++$i) {
            $ingredient = new Ingredient();
            $ingredient->setName('Ingredient ' . ($i + 1))
                ->setPrice(mt_rand(0, 100));
            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }

        // Recipes
        for ($i = 0; $i < 25; $i++) {
            $recipe = new Recipe();
            $recipe->setName('Recipe ' . ($i + 1))
                ->setTime(rand(10, 120))
                ->setNbPeople(rand(1, 6))
                ->setDifficulty(rand(1, 3))
                ->setDescription('This is a sample description for recipe ' . ($i + 1))
                ->setPrice(rand(500, 2000) / 100)
                ->setIsFavorite(rand(0, 1) == 1)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable());

            $manager->persist($recipe);
        }

        // Admin User
        $admin = new User();
        $admin->setFullName('Administrateur de SymRecipe')
            ->setPseudo(null)
            ->setEmail('admin@symrecipe.fr')
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
            ->setPlainPassword('password'); // Définissez simplement le mot de passe en clair

        $manager->persist($admin);

        // Regular Users
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setFullName('User' . ($i + 1))
                ->setPseudo(mt_rand(0, 1) === 1 ? 'Pseudo' . ($i + 1) : null)
                ->setEmail('user' . ($i + 1) . '@example.com')
                ->setRoles(['ROLE_USER'])
                // Définissez également le mot de passe en clair pour les utilisateurs réguliers
                ->setPlainPassword('password');

            $manager->persist($user);
        }
        $manager->flush();
    }
}
