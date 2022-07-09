<?php

namespace App\Form;

use App\Entity\Distribute;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DistributeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('assetTag')
            ->add('slotNumber')
            ->add('createdAt')
            ->add('distributed')
            ->add('distributedAt')
            ->add('distributedBy')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Distribute::class,
        ]);
    }
}
