<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Reservation;
use App\Entity\Room;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'attr' => [
                    'class' => 'selectpicker',
                    'data-live-search' => "true"
                ]
            ])
            ->add('room', EntityType::class, [
                'class' => Room::class,
                'attr' => [
                    'class' => 'selectpicker',
                    'data-live-search' => "true"
                ]
            ])
            ->add('startDate')
            ->add('endDate');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
