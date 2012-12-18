<?php

namespace PiggyBox\OrderBundle;

final class OrderEvents
{
    /**
     * The store.order event is thrown each time an order is created
     * in the system.
     *
     * The event listener receives an Acme\StoreBundle\Event\FilterOrderEvent
     * instance.
     *
     * @var string
     */
    const ORDER_VALIDATED = 'order.validated';

    const ORDER_PASSED = 'order.passed';
}
