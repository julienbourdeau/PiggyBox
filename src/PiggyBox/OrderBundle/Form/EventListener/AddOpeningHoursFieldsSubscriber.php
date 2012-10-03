<?php

namespace PiggyBox\OrderBundle\Form\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;

class AddOpeningHoursFieldsSubscriber implements EventSubscriberInterface
{
    private $factory;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that we want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(DataEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        // During form creation setData() is called with null as an argument
        // by the FormBuilder constructor. We're only concerned with when
        // setData is called with an actual Entity object in it (whether new,
        // or fetched with Doctrine). This if statement let's us skip right
        // over the null condition.
        if (null === $data) {
            return;
        }

		if ($data->getId()) {
			$opening_days = $data->getShop()->getOpeningDays();
			
			$opening_days_tab = array();			
			$date = new \DateTime('now');					
			
			$close_day_tab = array();
				
			foreach ($opening_days as $day) {
				if ($day->getOpen() == false) {
					array_push($close_day_tab,$day->getDayOfTheWeek());
				}
				$opening_days_tab[$date->format('Ymd')] = $date->format('l jS F Y');
				$date->modify('+1 day');
			}
			
			$date = new \DateTime('now');
			foreach ($opening_days_tab as $day) {
				foreach ($close_day_tab as $close_day) {
					if($date->format('N') == $close_day){
						unset($opening_days_tab[$date->format('Ymd')]);
					}
				}
				$date->modify('+1 day');
			}
			
			$opening_hours = array();
			$date = new \DateTime('now');
			
			foreach ($opening_days_tab as $day) {
				var_dump($date->format('Ymd'));
				var_dump($date->format('Hm'));
				var_dump(array_key_exists($date->format('Ymd'),$opening_days_tab) );die();
				if (array_keys($opening_days_tab,$day) === $date->format('Ymd')) {
					var_dump("yalllaaa");die();
				}
			}
			
		    $form->add($this->factory->createNamed('pickup_date', 'choice','null', array(
				'choices' => $opening_days_tab)
			));
				
				
		}
        
    }
}