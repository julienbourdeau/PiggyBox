<?php

namespace PiggyBox\OrderBundle\Provider;

use PiggyBox\OrderBundle\Storage\SessionCartStorage;
use Doctrine\ORM\EntityManager;
use PiggyBox\OrderBundle\Entity\Cart;

/**
 * Default provider cart.
 *
 */
class CartProvider
{
    /**
     * SessionCartStorage.
     *
     * @var SessionCartStorage
     */
    protected $storage;

    /**
     * Entity manager.
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * Constructor.
     *
     * @param EntityManager $em
     * @param Session       $session
     */
    public function __construct(EntityManager $em, SessionCartStorage $storage)
    {
        $this->storage = $storage;
        $this->em = $em;
    }

    /**
     * Retrieve the Cart from the DB if the session exist and create a new Cart with it's session if it does not exist
     */
    public function getCart()
    {
        //NOTE: Check if there is a session or and cart associate to the session
        if (null == $this->storage->getCurrentCartIdentifier()) {
            //NOTE: Create the Cart and save it in the DB
            $cart  = new Cart();


            $this->em->persist($cart);
            $this->em->flush();

            //NOTE: Set the session
            $this->storage->setCurrentCartIdentifier($cart);

            return $cart;
        }

        return $this->em->getRepository('PiggyBoxOrderBundle:Cart')->find($this->storage->getCurrentCartIdentifier());
    }
}
