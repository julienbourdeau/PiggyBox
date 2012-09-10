<?php

namespace PiggyBox\OrderBundle\Provider;

use PiggyBox\OrderBundle\Storage\SessionOrderStorage;
use Doctrine\ORM\EntityManager;
use PiggyBox\OrderBundle\Entity\Order;

/**
 * Default provider cart.
 *
 */
class OrderProvider
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
    public function __construct(EntityManager $em, SessionOrderStorage $storage)
    {
        $this->storage = $storage;
        $this->em = $em;
    }

    /**
     * Retrieve the Cart from the DB if the session exist and create a new Cart with it's session if it does not exist
     */
    public function getOrder()
    {
        //NOTE: Check if there is a session or and order associate to the session
        if (null == $this->storage->getCurrentOrderIdentifier() or null == $this->em->getRepository('PiggyBoxOrderBundle:Order')->find($this->storage->getCurrentOrderIdentifier())){

            //NOTE: Create the Cart and save it in the DB
            $order  = new Order();


        $this->em->persist($order);
        $this->em->flush();

        //NOTE: Set the session
        $this->storage->setCurrentOrderIdentifier($order);

        return $order;
        }

        return $this->em->getRepository('PiggyBoxOrderBundle:Order')->find($this->storage->getCurrentOrderIdentifier());
    }
}

