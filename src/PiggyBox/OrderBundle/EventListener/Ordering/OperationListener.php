<?php 

namespace PiggyBox\OrderBundle\EventListener\Ordering;

use PiggyBox\OrderBundle\Event\OrderEvent;
use PiggyBox\OrderBundle\Entity\Order;
use PiggyBox\ShopBundle\Entity\Product;
use PiggyBox\OrderBundle\Entity\OrderDetail;

class OperationListener 
{

    public function onOperationProcessed(OrderEvent $event)
	{
		$result = 0;

		$order = $event->getOrder();
		$order_details = $order->getOrderDetail();

		foreach ($order_details as $order_detail) {
			if($order_detail->getProduct()->getPriceType() == Product::WEIGHT_PRICE){
				$result = $result + $order_detail->getProduct()->getPriceKg()*$order_detail->getQuantity();	
			}
			if($order_detail->getProduct()->getPriceType() != Product::WEIGHT_PRICE){
				$result = $result + $order_detail->getPrice()->getPrice()*$order_detail->getQuantity();
			} 
		}
		$order->setTotalPrice($result);
    }
}

