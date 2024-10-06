<?php

namespace App\Form;

use App\Entity\Residents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResidentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom',
            ])
            ->add('type', null, [
                'label' => 'Type',
            ])
            ->add('birthDate', null, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
            ])
            ->add('image', null, [
                'label' => 'Image',
            ])
            ->add('description', null, [
                'label' => 'Description',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Residents::class,
        ]);
    }
}
