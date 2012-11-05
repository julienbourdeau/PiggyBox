<?php

namespace PiggyBox\OrderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formFactory = $builder->getFormFactory();

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formFactory) {
                if (null === $event->getData()) {
                     return;
                }

                if ($event->getData()) {
                    if ($event->getData()->getName()==null or $event->getData()->getPhoneNumber()==null or $event->getData()->getEmail()==null) {
                        $event->getForm()->add(
                            $formFactory->createNamed('name','text')
                        )->add(
                            $formFactory->createNamed('email','email')
                        )->add(
                            $formFactory->createNamed('phone_number','text')
                        );
                    }
                }
            }
        );

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
