<?php

namespace App\Form;

use App\Entity\ReservationForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ReservationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre prénom.']),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom de famille',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre nom de famille.']),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Le nom de famille ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre adresse e-mail.']),
                    new Email(['message' => 'Veuillez entrer une adresse e-mail valide.']),
                ],
            ])
            ->add('phoneNumber', TelType::class, [
                'label' => 'Numéro de téléphone',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre numéro de téléphone.']),
                    new Length([
                        'min' => 10,
                        'max' => 15,
                        'minMessage' => 'Le numéro de téléphone doit comporter au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le numéro de téléphone ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('streetNumber', TextType::class, [
                'label' => 'Numéro de rue',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre numéro de rue.']),
                ],
            ])
            ->add('streetName', TextType::class, [
                'label' => 'Nom de la rue',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer le nom de votre rue.']),
                ],
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre code postal.']),
                    new Length([
                        'max' => 10,
                        'maxMessage' => 'Le code postal ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre ville.']),
                ],
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre pays.']),
                ],
            ])
            ->add('reservation', TextType::class, [
                'label' => 'Nombre de participants',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer le nombre de participants.']),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Accepter les termes et conditions',
                'mapped' => false,
                'constraints' => [
                    new IsTrue(['message' => 'Vous devez accepter les termes et conditions.']),
                ],
            ])
            ->add('calendar', HiddenType::class, [
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReservationForm::class,
        ]);
    }
}
