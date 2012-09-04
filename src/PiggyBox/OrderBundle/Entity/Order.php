<?php

namespace PiggyBox\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PiggyBox\OrderBundle\Entity\Orders
 *
 * @ORM\Table(name="piggybox_order")
 * @ORM\Entity(repositoryClass="PiggyBox\OrderBundle\Entity\OrderRepository")
 */
class Order
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $note
     *
     * @ORM\Column(name="note", type="text")
     */
    private $note;

    /**
     * @var float $total_price
     *
     * @ORM\Column(name="total_price", type="float")
     */
    private $total_price;

    /**
     * @var \DateTime $pickupat
     *
     * @ORM\Column(name="pickupat", type="datetime")
     */
    private $pickupat;

    /**
     * @var integer $total_products
     *
     * @ORM\Column(name="total_products", type="integer")
     */
    private $total_products;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return Orders
     */
    public function setNote($note)
    {
        $this->note = $note;
    
        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set total_price
     *
     * @param float $totalPrice
     * @return Orders
     */
    public function setTotalPrice($totalPrice)
    {
        $this->total_price = $totalPrice;
    
        return $this;
    }

    /**
     * Get total_price
     *
     * @return float 
     */
    public function getTotalPrice()
    {
        return $this->total_price;
    }

    /**
     * Set pickupat
     *
     * @param \DateTime $pickupat
     * @return Orders
     */
    public function setPickupat($pickupat)
    {
        $this->pickupat = $pickupat;
    
        return $this;
    }

    /**
     * Get pickupat
     *
     * @return \DateTime 
     */
    public function getPickupat()
    {
        return $this->pickupat;
    }

    /**
     * Set total_products
     *
     * @param integer $totalProducts
     * @return Orders
     */
    public function setTotalProducts($totalProducts)
    {
        $this->total_products = $totalProducts;
    
        return $this;
    }

    /**
     * Get total_products
     *
     * @return integer 
     */
    public function getTotalProducts()
    {
        return $this->total_products;
    }
}
