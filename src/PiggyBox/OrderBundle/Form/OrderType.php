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
            ->add('pickup_date', 'date', array(
				'input'  => 'datetime',
				'widget' => 'choice',
				'format' => 'eeee d Y',
				'with_seconds' => false,
				'data_timezone' => "Europe/Paris",
				'user_timezone' => "Europe/Paris"
				))
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
