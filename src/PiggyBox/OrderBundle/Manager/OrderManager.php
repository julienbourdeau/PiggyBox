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

    private function getTimeInterval($start, $end, $openingHours)
    {
        if ($start !== null) {
            if ($start->format('i')%30 != 0) {
                if ($start->format('i')>30) {
                    $start->modify(abs(60-$start->format('i')).' minutes');
                } elseif ($start->format('i')<30) {
                    $start->modify(abs(30-$start->format('i')).' minutes');
                }
                $openingHours[$start->format('H:i')] = $start->format('H:i');
            }
            while ( $start->format('Hi') < $end->format('Hi')) {
                $openingHours[$start->format('H:i')] = $start->format('H:i');
                $start->modify('30 minutes');
            }
        }

        return $openingHours;
    }

    public function getOpeningHoursFromShopForDay(Shop $shop, $date)
    {
        $day = $this->em->getRepository('PiggyBoxShopBundle:Day')->getDayDetailsFromShop($shop->getId(), $date->format('N'));
        $openingHours = array();
        $today = new \DateTime('now');

        if ($today->format('YYYY/mm/dd') == $date->format('YYYY/mm/dd')) {
            if ($today->format('H:i') > $day->getFromTimeMorning()->format('H:i') && $today->format('H:i') < $day->getToTimeMorning()->format('H:i')) {
                $openingHours = $this->getTimeInterval($today, $day->getToTimeMorning(), $openingHours);
                $openingHours = $this->getTimeInterval($day->getFromTimeAfternoon(), $day->getToTimeAfternoon(), $openingHours);

                return $openingHours;
            }

            if ($today->format('H:i') > $day->getToTimeMorning()->format('H:i') && $today->format('H:i') < $day->getToTimeAfternoon()->format('H:i')) {
                $openingHours = $this->getTimeInterval($today, $day->getToTimeAfternoon(), $openingHours);

                return $openingHours;
            }

            return null;
        }
        $openingHours = $this->getTimeInterval($day->getFromTimeMorning(), $day->getToTimeMorning(), $openingHours);
        $openingHours = $this->getTimeInterval($day->getFromTimeAfternoon(), $day->getToTimeAfternoon(), $openingHours);

        return $openingHours;
    }

    protected function persistAndFlush($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

}
