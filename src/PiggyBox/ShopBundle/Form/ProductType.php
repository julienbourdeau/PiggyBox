<?php

namespace PiggyBox\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('origin')
            ->add('preservation')
            ->add('price', 'money', array(
                'required' => false,
            ))
            ->add('weightPrice', 'money', array(
                'required' => false,
            ))
            ->add('priceType', 'choice', array(
                'choices'   => array(
                    'unit_fixed_price' => 'Unit Fixed Price',
                    'unit_variable_price' => 'Unit Variable Price',
                    'chunk_price' => 'Chunk Price'),
                'required'  => true,
            ))
            ->add('productWeightPerSlice')
            ->add('file')
            ->add('category')
            ->add('minWeight')
            ->add('maxWeight')
            ->add('minPerson')
            ->add('maxPerson')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PiggyBox\ShopBundle\Entity\Product'
        ));
    }

    public function getName()
    {
        return 'piggybox_shopbundle_producttype';
    }
}
