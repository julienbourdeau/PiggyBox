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

                if (!$data->getId()) {
					if($data->getProduct()){
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
                    $form->add(
                        $formFactory->createNamed('quantity', 'number', null, array(
                            'read_only' => true,
                        ))
                    );
                }
            }
        );
        $builder->add('quantityDetail', 'choice', array(
            'choices'   => array(
                'big'   => 'Bon mangeur',
                'normal' => 'Normal',
                'small'   => 'Petite faim',
            ),
			'data' => 'normal',
            'multiple'  => false,
            'expanded'  => true,
        ));
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
