<?php

namespace PiggyBox\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('price')
            ->add('stepsNumber', 'integer', array(
                'required' => true,
                'data' => 3,
                ))
            ->add('menuItems', 'collection', array(
                    'type' => new MenuItemType(),
                    'allow_delete' => true,
                    'by_reference' => false,
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PiggyBox\ShopBundle\Entity\Menu'
        ));
    }

    public function getName()
    {
        return 'piggybox_shopbundle_menutype';
    }
}
