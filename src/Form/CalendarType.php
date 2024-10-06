<?php

namespace App\Form;

use App\Entity\Calendar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startTime', DateTimeType::class, [
                'label' => 'Heure de début',
                'widget' => 'single_text',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'L\'heure de début est obligatoire.']),
                ]
            ])
            ->add('endTime', DateTimeType::class, [
                'label' => 'Heure de fin',
                'widget' => 'single_text',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'L\'heure de fin est obligatoire.']),
                ]
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le titre est obligatoire.']),
                ],
                'attr' => ['placeholder' => 'Titre de l\'événement']
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['placeholder' => 'Description de l\'événement']
            ])
            ->add('places', IntegerType::class, [
                'label' => 'Nombre de places',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le nombre de places est obligatoire.']),
                ],
                'attr' => ['placeholder' => 'Nombre de places disponibles']
            ])
            ->add('volunteerPlaces', IntegerType::class, [
                'label' => 'Places pour bénévoles',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le nombre de places pour les bénévoles est obligatoire.']),
                ],
                'attr' => ['placeholder' => 'Nombre de places pour bénévoles']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
        ]);
    }
}

