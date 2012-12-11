<?php

namespace PiggyBox\OrderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PiggyBox\OrderBundle\Form\Type\TimeUniqueSelectorType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use PiggyBox\OrderBundle\Entity\Order;

class OrderType extends AbstractType
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

                if (!$data->getId()) {
                    if ($data->getProduct()) {
                        if ($data->getProduct()->getPriceType() == 'chunk_price') {
                            $choices = array();

                            for ($i = $data->getProduct()->getMinPerson(); $i <= $data->getProduct()->getMaxPerson(); $i++) {
                                $choices[$i] = $i.' pers.';
                            }

                            $form->add(
                                $formFactory->createNamed('quantity', 'choice', null, array(
                                    'choices'   => $choices,
                                ))
                            );
                        }
                        if ($data->getProduct()->getPriceType() != 'chunk_price') {
                            $choices = array();

                            $form->add(
                                $formFactory->createNamed('quantity', 'number', null, array(
                                    'data' => 1,
                                    'read_only' => true,
                                ))
                            );
                        }
                    }
                }

                if ($data->getId()) {
                    if ($data->getProduct()) {
                        if ($data->getProduct()->getPriceType() == 'chunk_price') {
                            $choices = array();

                            for ($i = $data->getProduct()->getMinPerson(); $i <= $data->getProduct()->getMaxPerson(); $i++) {
                                $choices[$i] = $i.' pers.';
                            }

                            $form->add(
                                $formFactory->createNamed('quantity', 'choice', null, array(
                                    'choices'   => $choices,
                                ))
                            );
                        }
                        if ($data->getProduct()->getPriceType() != 'chunk_price') {
                            $choices = array();

                            $form->add(
                                $formFactory->createNamed('quantity', 'number', null, array(
                                    'data' => 1,
                                    'read_only' => true,
                                ))
                            );
                        }
                    }
                }
            }
        );

        $builder
            ->add('pickupatTime',new TimeUniqueSelectorType())
            ->add('totalPrice', 'hidden')
            ->add('order_detail', 'collection', array(
                    'type' => new OrderDetailType(),
                    'allow_delete' => true,
                    'by_reference' => false,
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PiggyBox\OrderBundle\Entity\Order'
        ));
    }

    public function getName()
    {
        return 'piggybox_orderbundle_ordertype';
    }
}
