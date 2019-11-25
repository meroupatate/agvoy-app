<?php

namespace App\Form;

use App\Entity\Region;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IndexRegionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', EntityType::class, [
                'class' => Region::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'label' => false,
            ])
            ->add('startDate', DateType::class, [
                'widget' => 'choice',
                'input'  => 'datetime_immutable'
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'choice',
                'input'  => 'datetime_immutable'
            ])
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Region::class,
        ]);
    }
}
