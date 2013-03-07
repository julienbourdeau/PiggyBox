<?php

namespace PiggyBox\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;
use Symfony\Component\Security\Core\Validator\Constraint\UserPassword;

class ProfileFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //parent::buildForm($builder, $options);

        $builder->add('lastName')
                ->add('firstName')
                ->add('phoneNumber')
                ->add('city')
                ->add('gender', 'choice', array(
                    'choices'   => array(
                        'M.' => 'M.',
                        'Mme' => 'Mme',
                        'Mlle' => 'Mlle'),
                    'expanded' => true,
                    'required' => true,
                ))
                ->add('birthday', 'birthday', array(
                    'empty_value' => array('year' => '[Année]', 'month' => '[Mois]', 'day' => '[Jour]'),
                ))
                ->add('email', 'email', array(
                    'label' => 'form.email', 
                    'translation_domain' => 'FOSUserBundle'
                ));
    }

    public function getName()
    {
        return 'piggybox_userbundle_profile';
    }
}
