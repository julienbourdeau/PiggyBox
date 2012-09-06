<?php

namespace PiggyBox\OrderBundle\Storage;

use PiggyBox\OrderBundle\Entity\Order;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Stores current order id inside the user session.
 *
 */
class SessionOrderStorage
{
    /**
     * Session.
     *
     * @var Session
     */
    protected $session;

    /**
     * Constructor.
     *
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function getCurrentOrderIdentifier()
    {
        return $this->session->get('_piggybox.order-id');
    }

    public function setCurrentOrderIdentifier(Order $order)
    {
        $this->session->set('_piggybox.order-id', $order->getId());
    }

    public function resetCurrentOrderIdentifier()
    {
        $this->session->remove('_piggybox.order-id');
    }
}

