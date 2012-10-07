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
use Doctrine\ORM\EntityManager;

class OrderType extends AbstractType
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

				if($event->getData()){
					$closeDays = array();
					if ($event->getData()->getShop() !== null) {
						$openingDays = $event->getData()->getShop()->getOpeningDays();

						foreach ($openingDays as $day) {
							if(!$day->getOpen()){
								array_push($closeDays, $day->getDayOfTheWeek());	
							}
						}
					}					

					$event->getForm()->add(
						$formFactory->createNamed('createdat',new DateUniqueSelectorType(),null ,array(
							'number_of_days' => 8,
							'closed_days' => $closeDays,
						))
					);
				}
			}
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
