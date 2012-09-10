<?php

namespace PiggyBox\OrderBundle\Twig;

use Twig_Extension;
use PiggyBox\OrderBundle\Provider\OrderProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrderExtension extends \Twig_Extension
{
    /**
     * Order provider.
     *
     * @var OrderProvider
     */
    private $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Functions declaration
    */
    public function getFunctions()
    {
        return array(
            'piggybox_order_get' => new \Twig_Function_Method($this,'getCurrentOrder'),
            );
    }

    /**
     * Returns a list of tests to add to the existing list.
     *
     * @return cart
     */
    public function getCurrentOrder()
    {
        $em = $this->container->get('doctrine.orm.default_entity_manager');
        $order_details = $this->container->get('piggy_box_order.provider')->getOrder()->getOrderDetail();
		return $order_details->toArray();
    }

    public function getName()
    {
        return 'cart_extension';
    }
}

