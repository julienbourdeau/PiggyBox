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
            ->add('price_kg')
            ->add('active')
            ->add('promo_active')
            ->add('promo_price')
            ->add('promo_expire_date')
            ->add('promo_percentage')
			->add('file')
			->add('prices', 'collection', array(
       		 	'type' => new PriceType(),
        		'allow_add' => true,
		        'by_reference' => false,
    		));
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
