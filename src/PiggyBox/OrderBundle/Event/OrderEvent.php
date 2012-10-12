<?php

namespace PiggyBox\OrderBundle\Event;

use PiggyBox\OrderBundle\Entity\Order;
use Symfony\Component\EventDispatcher\Event;

class OrderEvent extends Event
{
    
    const OPERATION_ORDER = 'piggy_box_cart.operation_order';

    /**
     * @var PiggyBox\OrderBundle\Entity\Order
     */
    private $order;

    /**
     * @param PiggyBox\OrderBundle\Entity\Order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return PiggyBox\OrderBundle\Entity\Order
     */
    public function getOrder()
    {
        return $this->order;
    }
}
