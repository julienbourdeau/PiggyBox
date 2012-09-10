<?php

namespace PiggyBox\OrderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('note')
            ->add('total_price')
            ->add('pickupat')
            ->add('total_products')
            ->add('createdat')
            ->add('updatedat')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PiggyBox\OrderBundle\Entity\Order'
        ));
    }

    public function getName()
    {
        return 'piggybox_orderbundle_ordertype';
    }
}
