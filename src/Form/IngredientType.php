<?php

namespace App\Form;

use App\Entity\Ingredient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;

class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Ajout du champ 'name' de type texte
            ->add('name', TextType::class, [
                // Attributs HTML pour le champ 'name'
                'attr' => [
                    'class' => 'form-control', // Classe CSS pour le style
                    'minlength' => '2',        // Longueur minimale du texte
                    'maxlength' => '50'        // Longueur maximale du texte
                ],
                'label' => 'Nom', // Label du champ
                // Attributs HTML pour le label du champ 'name'
                'label_attr' => [
                    'class' => 'form-label mt-4' // Classe CSS pour le style du label
                ],
                // Contraintes de validation pour le champ 'name'
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 50]), // Longueur du texte
                    new Assert\NotBlank() // Le champ ne doit pas être vide
                ]
            ])
            // Ajout du champ 'price' de type monétaire
            ->add('price', MoneyType::class, [
                // Attributs HTML pour le champ 'price'
                'attr' => [
                    'class' => 'form-control', // Classe CSS pour le style
                ],
                'label' => 'Prix ', // Label du champ
                // Attributs HTML pour le label du champ 'price'
                'label_attr' => [
                    'class' => 'form-label mt-4' // Classe CSS pour le style du label
                ],
                // Contraintes de validation pour le champ 'price'
                'constraints' => [
                    new Assert\Positive(), // Le prix doit être positif
                    new Assert\LessThan(200) // Le prix doit être inférieur à 200
                ]
            ])
            // Ajout d'un bouton de soumission
            ->add('submit', SubmitType::class, [
                // Attributs HTML pour le bouton de soumission
                'attr' => [
                    'class' => 'btn btn-primary mt-4' // Classe CSS pour le style
                ],
                'label' => 'Créer mon ingrédient' // Texte du bouton
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
        ]);
    }
}
