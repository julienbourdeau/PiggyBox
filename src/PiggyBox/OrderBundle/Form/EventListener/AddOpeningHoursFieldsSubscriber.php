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
					if($date->format('N') == $close_day) {
						unset($opening_days_tab[$date->format('Ymd')]);
					}
				}
				$date->modify('+1 day');
			}
			
			$opening_days_array_keys = array_keys($opening_days_tab);
			
			foreach ($opening_days_array_keys as $day) {
				
				$day_of_the_week = new \DateTime($day);
								
				foreach ($opening_days as $opening_day) {
					if($opening_day->getDayOfTheWeek() == $day_of_the_week->format('N')){
						$day = $opening_day;
					}
				}
								
				if($day->getFromTimeMorning() !== null){
					$opening_hour_tab[$day->getDayOfTheWeek().$day->getFromTimeMorning()->format('H:i')] = $day->getFromTimeMorning()->format('H:i');
					$day->getFromTimeMorning()->modify($day->getFromTimeMorning()->format('i')+30-$day->getFromTimeMorning()->format('i').' minutes'); 
					while ( $day->getFromTimeMorning()->format('Hi') <= $day->getToTimeMorning()->format('Hi')) {
						$opening_hour_tab[$day->getDayOfTheWeek().$day->getFromTimeMorning()->format('H:i')] = $day->getFromTimeMorning()->format('H:i');
						$day->getFromTimeMorning()->modify('30 minutes');
					}
				}
				
				if($day->getFromTimeAfternoon() !== null){
					$opening_hour_tab[$day->getDayOfTheWeek().$day->getFromTimeAfternoon()->format('Hi')] = $day->getFromTimeAfternoon()->format('H:i');
					$day->getFromTimeAfternoon()->modify($day->getFromTimeAfternoon()->format('i')+30-$day->getFromTimeAfternoon()->format('i').' minutes');
					while ( $day->getFromTimeAfternoon()->format('Hi') <= $day->getToTimeAfternoon()->format('Hi')) {
						$opening_hour_tab[$day->getDayOfTheWeek().$day->getFromTimeAfternoon()->format('Hi')] = $day->getFromTimeAfternoon()->format('H:i');
						$day->getFromTimeAfternoon()->modify('30 minutes');
					}						
				}
			}						
			
		    $form->add($this->factory->createNamed('pickup_date', 'choice','null', array(
				'choices' => $opening_days_tab)
			));
			$form->add($this->factory->createNamed('pickup_time', 'choice','null', array(
				'choices' => $opening_hour_tab)
			));		
				
				
		}
        
    }
}