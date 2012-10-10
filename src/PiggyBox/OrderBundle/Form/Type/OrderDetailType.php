<?php

namespace PiggyBox\OrderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PiggyBox\OrderBundle\Entity\OrderDetail;
use PiggyBox\ShopBundle\Entity\Product;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class OrderDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product');


		$formFactory = $builder->getFormFactory();
		
		$builder->addEventListener(
			FormEvents::PRE_SET_DATA,
			function (FormEvent $event) use ($formFactory) {
				if (null === $event->getData()) {
         	    	return;
        		}

				if($event->getData()){
					$choice_list = array();

					if ($event->getData()->getProduct() !== null) {
						$product = $event->getData()->getProduct();
						$price_kg = $product->getPriceKg();

						if($product->getPriceType() == Product::UNIT_PRICE){
							$choice_list = array(
								'1' => '1',
								'2' => '2',
								'3' => '3',
								'4' => '4',
								'5' => '5',
								'6' => '6',
								'7' => '7',
								'8' => '8',
								'9' => '9',
								'10' => '10',
							);		
						}

						if($product->getPriceType() == Product::WEIGHT_PRICE){
							$choice_list = array(
								'0.05' => '  &nbsp;50g &nbsp;&nbsp; - &nbsp;  €',
								'0.1' => ' 100g &nbsp;&nbsp; - &nbsp;  €',
								'0.2' => ' 200g &nbsp;&nbsp; - &nbsp;  €',
								'0.3' => ' 300g &nbsp;&nbsp; - &nbsp;  €',
								'0.4' => ' 400g &nbsp;&nbsp; - &nbsp;  €',
								'0.5' => ' 500g &nbsp;&nbsp; - &nbsp;  €',
								'0.6' => ' 600g &nbsp;&nbsp; - &nbsp;  €',
								'0.7' => ' 700g &nbsp;&nbsp; - &nbsp;  €',
								'0.8' => ' 800g &nbsp;&nbsp; - &nbsp;  €',
								'0.9' => ' 900g &nbsp;&nbsp; - &nbsp;  €',
								'1' => '1000g &nbsp;&nbsp; - &nbsp;    €',
							);		
						}

						if($product->getPriceType() == Product::SLICE_PRICE){
							$choice_list = array('1' => '1');		
						}
					}					

					$event->getForm()->add(
						$formFactory->createNamed('quantity','choice', array(
								'choices' => $choice_list,
							)
					)
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
