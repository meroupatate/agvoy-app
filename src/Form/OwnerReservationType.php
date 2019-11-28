<?php

namespace App\Form;

use App\Entity\Room;
use App\Entity\Unavailability;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class OwnerReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate')
            ->add('endDate')
            ->add('room', EntityType::class, [
                'class' => Room::class,
                'attr' => [
                    'class' => 'selectpicker',
                    'data-live-search' => "true"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
