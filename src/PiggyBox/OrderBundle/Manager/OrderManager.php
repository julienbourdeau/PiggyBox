<?php

namespace PiggyBox\OrderBundle\Manager;

use Doctrine\ORM\EntityManager;
use PiggyBox\ShopBundle\Entity\Shop;
use PiggyBox\ShopBundle\Entity\Product;
use PiggyBox\OrderBundle\Entity\Order;
use PiggyBox\OrderBundle\Entity\OrderDetail;

class OrderManager
{
    protected $em;
    protected $cartProvider;

    public function __construct(EntityManager $em, $cartProvider)
    {
        $this->em = $em;
        $this->cartProvider = $cartProvider;
    }

    public function addOrGetOrderFromCart(Shop $shop)
    {
        $order = $this->getOrderFromCart($shop);
        if ($order != null) {
            return $order;
        }

        $order = new Order();
        $order->setShop($shop);
        $this->cartProvider->getCart()->addOrder($order);
        $this->persistAndFlush($this->cartProvider->getCart());

        return $order;
    }

    public function changeOrderStatus(Order $order, $status)
    {
        $order->setStatus($status);
        $this->persistAndFlush($order);
    }

    public function addOrderDetailToOrder(Order $order, OrderDetail $orderDetail)
    {
        $order->addOrderDetail($orderDetail);
        $orderDetail->setOrder($order);
        $this->setTotalPrice($order);
        $this->persistAndFlush($order);
    }

    public function removeOrderDetailFromOrder(Order $order, OrderDetail $orderDetail)
    {
        $order->removeOrderDetail($orderDetail);
        $this->em->remove($orderDetail);
        $this->setTotalPrice($order);
        $this->persistAndFlush($order);
    }

    public function removeOrderFromCart(Order $order)
    {
        $cart = $this->cartProvider->getCart();
        $cart->removeOrder($order);
        $this->persistAndFlush($cart);
    }

    public function removeOrder(Order $order)
    {
        $this->em->remove($order);
        $this->em->flush();
    }

    public function getOrderFromCart(Shop $shop)
    {
        $detailCart = $this->em->getRepository('PiggyBoxOrderBundle:Cart')->getDetailCartByShopAndCart($shop->getId(), $this->cartProvider->getCart()->getId());

        if ($detailCart == null) {
            return null;
        }

        return $detailCart[0]->getOrders()->first();
    }

    public function setTotalPrice(Order $order)
    {
        $orderDetails = $order->getOrderDetail();
        $result = 0;

        foreach ($orderDetails as $orderDetail) {
            if ($orderDetail->getProduct()->getPriceType() == Product::WEIGHT_PRICE) {
                $result = $result + $orderDetail->getProduct()->getPriceKg()*$orderDetail->getQuantity()/10;
            }
            if ($orderDetail->getProduct()->getPriceType() != Product::WEIGHT_PRICE) {
                $result = $result + $orderDetail->getPrice()->getPrice()*$orderDetail->getQuantity();
            }
        }
        $order->setTotalPrice($result);
    }

    protected function persistAndFlush($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

}
