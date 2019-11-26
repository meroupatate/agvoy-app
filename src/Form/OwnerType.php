<?php

namespace App\Form;

use App\Entity\Owner;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OwnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname')
            ->add('familyName')
            ->add('address')
            ->add('country')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'attr' => [
                    'class' => 'selectpicker',
                    'data-live-search' => "true"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Owner::class,
        ]);
    }
}
