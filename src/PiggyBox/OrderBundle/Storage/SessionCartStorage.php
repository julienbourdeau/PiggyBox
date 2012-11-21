<?php

namespace PiggyBox\OrderBundle\Storage;

use PiggyBox\OrderBundle\Entity\Cart;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Stores current cart id inside the user session.
 *
 */
class SessionCartStorage
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

    public function getCurrentCartIdentifier()
    {
        return $this->session->get('_piggybox.cart-id');
    }

    public function setCurrentCartIdentifier(Cart $cart)
    {
        $this->session->set('_piggybox.cart-id', $cart->getId());
    }

    public function resetCurrentCartIdentifier()
    {
        $this->session->remove('_piggybox.cart-id');
    }
}
