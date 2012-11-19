<?php

namespace PiggyBox\OrderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PiggyBox\OrderBundle\Entity\OrderDetail;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class OrderDetailType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formFactory = $builder->getFormFactory();

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formFactory) {
                $data = $event->getData();
                $form = $event->getForm();

                if (null === $data) {
                    return;
                }

                // check if the product object is "new"
                if (!$data->getId()) {
                    $form->add(
                        $formFactory->createNamed('quantity', 'number', null, array(
                            'data' => 1,
                            'read_only' => true,
                        ))
                    );
                }

                if ($data->getId()) {
                    $form->add(
                        $formFactory->createNamed('quantity', 'number', null, array(
                            'read_only' => true,
                        ))
                    );
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PiggyBox\OrderBundle\Entity\OrderDetail'
        ));
    }

    public function getName()
    {
        return 'piggybox_orderbundle_orderdetailtype';
    }
}
