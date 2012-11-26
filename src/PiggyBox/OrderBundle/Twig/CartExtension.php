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
            'piggybox_product_number_in_cart' => new \Twig_Function_Method($this,'getProductsNumberInCart'),
            );
    }

    /**
     * Returns a list of tests to add to the existing list.
     *
     * @return cart
     */
    public function getProductsNumberInCart()
    {
       $orders = $this->container->get('piggy_box_cart.provider')->getCart()->getOrders();
       $result = 0;

       foreach ($orders as $order) {
           $result = $result + $order->getTotalProducts();
       }

       return $result;
    }

    public function getName()
    {
        return 'cart_extension';
    }
}
