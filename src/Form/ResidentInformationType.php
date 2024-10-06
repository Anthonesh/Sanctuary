<?php

namespace App\Form;

use App\Entity\ResidentInformation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResidentInformationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('food', null, [
                'label' => 'Alimentation',
            ])
            ->add('care', null, [
                'label' => 'Soins',
            ])
            ->add('health_record', null, [
                'label' => 'Dossier médical',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ResidentInformation::class,
        ]);
    }
}
