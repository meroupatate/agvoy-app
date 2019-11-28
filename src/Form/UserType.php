<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Owner;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('password')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'ROLE_USER' => 'ROLE_USER',
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                    'ROLE_OWNER' => 'ROLE_OWNER',
                    'ROLE_CLIENT' => 'ROLE_CLIENT',
                ],
                'required' => true,
                'multiple' => true,
                'attr' => [
                    'class' => 'selectpicker',
                    'data-live-search' => "true"
                ]
            ])
            ->add('owner', EntityType::class, [
                'class' => Owner::class,
                'required' => false,
                'attr' => [
                    'class' => 'selectpicker',
                    'data-live-search' => "true"
                ]
            ])
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'required' => false,
                'attr' => [
                    'class' => 'selectpicker',
                    'data-live-search' => "true"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
