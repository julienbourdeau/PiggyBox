<?php 

namespace PiggyBox\OrderBundle\EventListener\Ordering;

use PiggyBox\OrderBundle\Event\OrderEvent;
use PiggyBox\OrderBundle\Entity\Order;
use PiggyBox\OrderBundle\Entity\OrderDetail;

class OperationListener 
{

    public function onOperationProcessed(OrderEvent $event)
	{
		$result = 0;

		$order = $event->getOrder();
		$order_details = $order->getOrderDetail();

		foreach ($order_details as $order_detail) {
				if($order_detail->getPrice() !==null) {
					$result = $result + $order_detail->getPrice()->getPrice();
			} 
		}

		$order->setTotalPrice($result);
    }
}

