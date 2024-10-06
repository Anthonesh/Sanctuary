<?php

namespace App\Form;

use App\Entity\Volunteer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Time;

class VolunteerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startTime', TimeType::class, [
                'label' => 'Heure de début',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer l\'heure de début.']),
                    new Time(['message' => 'Veuillez entrer une heure valide.']),
                ],
            ])
            ->add('endTime', TimeType::class, [
                'label' => 'Heure de fin',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer l\'heure de fin.']),
                    new Time(['message' => 'Veuillez entrer une heure valide.']),
                ],
            ])
            ->add('numberOfVolunteers', IntegerType::class, [
                'label' => 'Nombre de bénévoles',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer le nombre de bénévoles.']),
                    new Positive(['message' => 'Le nombre doit être positif.']),
                    new Length([
                        'max' => 3,
                        'maxMessage' => 'Le nombre de bénévoles ne peut pas dépasser {{ limit }} chiffres.',
                    ]),
                ],
            ])
            ->add('calendar', HiddenType::class, [
                'mapped' => false,
            ])
            ->add('user', HiddenType::class, [
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Volunteer::class,
        ]);
    }
}
