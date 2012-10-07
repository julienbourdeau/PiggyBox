<?php

namespace PiggyBox\OrderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PiggyBox\OrderBundle\Form\EventListener\AddOpeningHoursFieldsSubscriber;
use PiggyBox\OrderBundle\Form\Type\UserType;
use PiggyBox\OrderBundle\Form\Type\DateUniqueSelectorType;
use Doctrine\ORM\EntityRepository; 
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use PiggyBox\OrderBundle\Form\DataTransformer\DayToStringTransformer;
use Doctrine\ORM\EntityManager;

class OrderType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {		

		$builder->add('createdat',new DateUniqueSelectorType());
		
				// $builder->addEventListener(
				// 					FormEvents::PRE_SET_DATA,
				// 					function (FormEvent $event) use ($formFactory) {
				// 
				// 						if($event->getData()){
				// 							if($event->getData()->getId() !== null){
				// 								$shop_id = $event->getData()->getShop()->getId();	
				// 							}
				// 							else{
				// 								$shop_id = null;
				// 							}
				// 
				// 							$event->getForm()->add(
				// 								$formFactory->createNamed('day', 'entity', null, 
				// 									array (
				// 										'label' => 'NomDuLabel',
				// 										'class' => 'PiggyBox\\ShopBundle\\Entity\\Day',
				// 										'property' => 'day_of_the_week',
				// 										'query_builder' => function(EntityRepository $er) use ($shop_id){
				// 											return $er->createQueryBuilder('d')												
				// 													->where('d.shop = ?1')
				// 													->orderBy('d.id', 'ASC')
				// 													->setParameter(1, $shop_id);;
				// 										},)
				// 									))->addModelTransformer($transformer);							
				// 						}
				// 					}
				// 				);	
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