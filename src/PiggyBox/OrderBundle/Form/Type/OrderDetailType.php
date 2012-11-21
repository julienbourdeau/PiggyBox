<?php

namespace PiggyBox\OrderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PiggyBox\OrderBundle\Entity\OrderDetail;

class OrderDetailType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('quantity', 'number', array(
            'data' => '1',
            'read_only' => true,
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PiggyBox\OrderBundle\Entity\OrderDetail'
        ));
    }

    public function getName()
    {
        return 'piggybox_orderbundle_orderdetailtype';
    }
}
