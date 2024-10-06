<?php

namespace App\Form;

use App\Entity\Donations;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\IsTrue;

class DonationsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire.']),
                ],
                'attr' => ['placeholder' => 'Nom de famille']
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est obligatoire.']),
                ],
                'attr' => ['placeholder' => 'Prénom']
            ])
            ->add('streetNumber', TextType::class, [
                'label' => 'Numéro de rue',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro de rue est obligatoire.']),
                ],
                'attr' => ['placeholder' => 'Numéro de rue']
            ])
            ->add('streetName', TextType::class, [
                'label' => 'Libellé de la rue',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le libellé de la rue est obligatoire.']),
                ],
                'attr' => ['placeholder' => 'Libellé de la rue']
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code Postal',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le code postal est obligatoire.']),
                ],
                'attr' => ['placeholder' => 'Code Postal']
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'La ville est obligatoire.']),
                ],
                'attr' => ['placeholder' => 'Ville']
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le pays est obligatoire.']),
                ],
                'attr' => ['placeholder' => 'Pays']
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro de téléphone est obligatoire.']),
                ],
                'attr' => ['placeholder' => 'Numéro de téléphone']
            ])
            ->add('mail', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un email']),
                    new Email(['message' => 'L\'email {{ value }} n\'est pas valide.']),
                ],
                'attr' => ['placeholder' => 'Adresse email']
            ])
            ->add('amount', IntegerType::class, [
                'label' => 'Montant du don',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le montant du don est obligatoire.']),
                ],
                'attr' => ['placeholder' => 'Montant du don']
            ])
            ->add('currency', ChoiceType::class, [
                'label' => 'Monnaie',
                'required' => true,
                'choices' => [
                    'EUR' => 'EUR',
                    'USD' => 'USD',
                    'GBP' => 'GBP',
                ],
                'placeholder' => 'Choisissez une monnaie',
                'constraints' => [
                    new NotBlank(['message' => 'La monnaie est obligatoire.']),
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'J\'accepte les termes et conditions',
                'mapped' => false,
                'constraints' => [
                    new IsTrue(['message' => 'Vous devez accepter les conditions.']),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Donations::class,
        ]);
    }
}
