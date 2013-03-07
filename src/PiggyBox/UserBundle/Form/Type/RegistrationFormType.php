<?php

namespace PiggyBox\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //parent::buildForm($builder, $options);

        $builder->add('lastName')
                ->add('firstName')
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
                ->add('email', 'repeated', array(
                    'type' => 'email',
                    'options' => array('translation_domain' => 'FOSUserBundle'),
                    'first_options' => array('label' => 'form.email'),
                    'second_options' => array('label' => 'form.email'),
                    'invalid_message' => 'Les emails ne sont pas égaux',
                ))
                ->add('plainPassword', 'repeated', array(
                    'type' => 'password',
                    'options' => array('translation_domain' => 'FOSUserBundle'),
                    'first_options' => array('label' => 'form.password'),
                    'second_options' => array('label' => 'form.password_confirmation'),
                    'invalid_message' => 'Mots de passe incorrects',
                ));
    }

    public function getName()
    {
        return 'piggybox_userbundle_registration';
    }
}
