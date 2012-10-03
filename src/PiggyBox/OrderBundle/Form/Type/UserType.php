<?php

namespace PiggyBox\OrderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PiggyBox\OrderBundle\Form\EventListener\AddUserInfoFieldsSubscriber;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$subscriber = new AddUserInfoFieldsSubscriber($builder->getFormFactory());
		$builder->addEventSubscriber($subscriber);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PiggyBox\UserBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'piggybox_orderbundle_usertype';
    }
}
