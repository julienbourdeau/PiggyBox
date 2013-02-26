<?php

namespace PiggyBox\UserBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('lastName')
                ->add('firstName')
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
                ));
    }

    public function getName()
    {
        return 'piggybox_userbundle_registration';
    }
}
