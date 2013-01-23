<?php

namespace PiggyBox\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DiscountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('discountName')
            ->add('discountDescription')
            ->add('discountPrice', 'money')
            ->add('discountQuantity')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PiggyBox\ShopBundle\Entity\Discount'
        ));
    }

    public function getName()
    {
        return 'piggybox_shopbundle_discounttype';
    }
}
