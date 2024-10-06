<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'label' => 'Email',
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'Utilisateur' => 'ROLE_USER',
                    'Bénévole en attente' => 'ROLE_VOLUNTEER_PENDING',
                    'Bénévole' => 'ROLE_VOLUNTEER',
                    'Membre' => 'ROLE_MEMBER',
                ],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('password', null, [
                'label' => 'Mot de passe',
            ])
            ->add('firstName', null, [
                'label' => 'Prénom',
            ])
            ->add('lastName', null, [
                'label' => 'Nom de famille',
            ])
            ->add('phoneNumber', null, [
                'label' => 'Numéro de téléphone',
            ])
            ->add('streetNumber', null, [
                'label' => 'Numéro de rue',
            ])
            ->add('streetName', null, [
                'label' => 'Nom de rue',
            ])
            ->add('postalCode', null, [
                'label' => 'Code postal',
            ])
            ->add('city', null, [
                'label' => 'Ville',
            ])
            ->add('country', null, [
                'label' => 'Pays',
            ])
            ->add('isVerified', null, [
                'label' => 'Vérifié',
            ])
            ->add('createdAt', null, [
                'label' => 'Date de création',
                'widget' => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
