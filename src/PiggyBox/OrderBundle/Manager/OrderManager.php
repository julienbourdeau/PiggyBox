<?php

namespace PiggyBox\OrderBundle\Manager;

use Doctrine\ORM\EntityManager;
use PiggyBox\ShopBundle\Entity\Shop;
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
        $this->setOrderDetailTotalPrice($orderDetail);
        $this->setOrderTotalPrice($order);
        $this->persistAndFlush($order);
    }

    public function removeOrderDetailFromOrder(Order $order, OrderDetail $orderDetail)
    {
        $order->removeOrderDetail($orderDetail);
        $this->em->remove($orderDetail);
        $this->setOrderTotalPrice($order);
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

    public function setOrderTotalPrice(Order $order)
    {
        $result = 0;
        $menuDetails = array();

        foreach ($order->getOrderDetail() as $orderDetail) {
            if ($orderDetail->getMenuDetail() != null) {
                if (!in_array($orderDetail->getMenuDetail(), $menuDetails)) {
                    $menuDetails[] = $orderDetail->getMenuDetail();
                    $result = $result + $orderDetail->getMenuDetail()->getMenu()->getPrice();
                }
            }
            if ($orderDetail->getMenuDetail() == null) {
                $result = $result + $orderDetail->getTotalPrice();
            }
        }

        $order->setTotalPrice(round($result, 2, PHP_ROUND_HALF_UP));
    }

    public function setOrderDetailTotalPrice(OrderDetail $orderDetail)
    {
        if ($orderDetail->getMenuDetail() != null) {
            $orderDetail->setTotalPrice(round($orderDetail->getProduct()->getPrice(), 2, PHP_ROUND_HALF_UP));
        }
        if ($orderDetail->getMenuDetail() == null) {
            if ($orderDetail->getProduct()->getPriceType() == 'chunk_price') {
                $maxPerson = $orderDetail->getProduct()->getMaxPerson();
                $minPerson = $orderDetail->getProduct()->getMinPerson();
                $maxWeight = $orderDetail->getProduct()->getMaxWeight();
                $minWeight = $orderDetail->getProduct()->getMinWeight();
                $weightPrice = $orderDetail->getProduct()->getWeightPrice();
                $step1 = $maxWeight-$minWeight;
                $step2 = $maxPerson-$minPerson;
                $step3 = $step1/$step2;
                $step4 = $orderDetail->getQuantity()-$minPerson;
                $step5 = $step3*$step4;
                $step6 = $step5*$weightPrice/1000;
                $step7 = $minWeight*$weightPrice/1000;
                $step8 = $step6+$step7;
                $orderDetail->setTotalPrice(round($step8, 2, PHP_ROUND_HALF_UP));
            }
            if ($orderDetail->getProduct()->getPriceType() != 'chunk_price') {
                $orderDetail->setTotalPrice(round($orderDetail->getProduct()->getPrice() * $orderDetail->getQuantity(), 2, PHP_ROUND_HALF_UP));
            }
            if ($orderDetail->getProduct()->getDiscount() != null && $orderDetail->getProduct()->getDiscount()->getDiscountQuantity() == $orderDetail->getQuantity()) {
                $orderDetail->setTotalPrice($orderDetail->getProduct()->getDiscount()->getDiscountPrice());
            }
        }
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
                if ($start->format('Hi') >= '1200' && $start->format('Hi') <= '1400') {
                    $start->modify('10 minutes');
                } else {
                    $start->modify('30 minutes');
                }
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
