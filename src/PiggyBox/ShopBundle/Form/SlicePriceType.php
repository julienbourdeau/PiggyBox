<?php

namespace PiggyBox\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SlicePriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price')
            ->add('price_kg')
            ->add('createdat')
            ->add('updatedat')
            ->add('slices_nbr')
            ->add('total_weight')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PiggyBox\ShopBundle\Entity\SlicePrice'
        ));
    }

    public function getName()
    {
        return 'piggybox_shopbundle_slicepricetype';
    }
}
