<?php

namespace PiggyBox\OrderBundle\Twig;

use Twig_Extension;
use PiggyBox\OrderBundle\Provider\CartProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CartExtension extends \Twig_Extension
{
    /**
     * Cart provider.
     *
     * @var CartProvider
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
            'piggybox_cart_get' => new \Twig_Function_Method($this,'getCurrentCart'),
            );
    }

    /**
     * Returns a list of tests to add to the existing list.
     *
     * @return cart
     */
    public function getCurrentCart()
    {
       $em = $this->container->get('doctrine.orm.default_entity_manager');
       return  $this->container->get('piggy_box_cart.provider')->getCart();
    }

    public function getName()
    {
        return 'cart_extension';
    }
}


