<?php

namespace PiggyBox\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ShopType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('type')
			->add('opening_days', 'collection', array(
				'type' => new DayType(),
				'by_reference' => false,
				'allow_add' => true,
				'allow_delete' => true,
					));
			;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PiggyBox\ShopBundle\Entity\Shop'
        ));
    }

    public function getName()
    {
        return 'piggybox_shopbundle_shoptype';
    }
}
