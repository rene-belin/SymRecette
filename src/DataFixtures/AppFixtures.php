<?php

// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

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

        $manager->flush();
    }
}