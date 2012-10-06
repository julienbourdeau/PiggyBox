<?php

namespace PiggyBox\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
     * @ORM\Column(name="note", type="string", nullable=true)
     */
    private $note;

    /**
     * @var string $status
     *
     * @ORM\Column(name="status", type="text")
     */
    private $status = "ordering";
	

    /**
     * @var float $total_price
     *
     * @ORM\Column(name="total_price", type="float", nullable=true)
     */
    private $total_price;

	/**
	 * @ORM\OneToOne(targetEntity="PiggyBox\ShopBundle\Entity\Day")
	 * @ORM\JoinColumn(name="day_id", referencedColumnName="id")
	 */
	 private $day;

    /**
     * @var integer $total_products
     *
     * @ORM\Column(name="total_products", type="integer", nullable=true)
     */
    private $total_products;

    /**
     * @var \DateTime $createdat
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createdat", type="datetime")
     */
    private $createdat;

    /**
     * @var \DateTime $updatedat
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updatedat", type="datetime")
     */
    private $updatedat;

    /**
     * @ORM\OneToMany(targetEntity="OrderDetail", mappedBy="order", cascade={"persist", "remove"})
     **/
    private $order_detail;

	/**
     * @ORM\ManyToOne(targetEntity="PiggyBox\ShopBundle\Entity\Shop", inversedBy="orders")
     * @ORM\JoinColumn(name="shop_id", referencedColumnName="id")
     **/
    private $shop;

	/**
     * @ORM\ManyToOne(targetEntity="PiggyBox\UserBundle\Entity\User", inversedBy="orders")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    private $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->order_detail = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
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
     * @return Order
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
     * Set status
     *
     * @param string $status
     * @return Order
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set total_price
     *
     * @param float $totalPrice
     * @return Order
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
     * Set pickup_date
     *
     * @param \DateTime $pickupDate
     * @return Order
     */
    public function setPickupDate($pickupDate)
    {
        $this->pickup_date = $pickupDate;
    
        return $this;
    }

    /**
     * Get pickup_date
     *
     * @return \DateTime 
     */
    public function getPickupDate()
    {
        return $this->pickup_date;
    }

    /**
     * Set pickup_time
     *
     * @param \DateTime $pickupTime
     * @return Order
     */
    public function setPickupTime($pickupTime)
    {
        $this->pickup_time = $pickupTime;
    
        return $this;
    }

    /**
     * Get pickup_time
     *
     * @return \DateTime 
     */
    public function getPickupTime()
    {
        return $this->pickup_time;
    }

    /**
     * Set total_products
     *
     * @param integer $totalProducts
     * @return Order
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

    /**
     * Set createdat
     *
     * @param \DateTime $createdat
     * @return Order
     */
    public function setCreatedat($createdat)
    {
        $this->createdat = $createdat;
    
        return $this;
    }

    /**
     * Get createdat
     *
     * @return \DateTime 
     */
    public function getCreatedat()
    {
        return $this->createdat;
    }

    /**
     * Set updatedat
     *
     * @param \DateTime $updatedat
     * @return Order
     */
    public function setUpdatedat($updatedat)
    {
        $this->updatedat = $updatedat;
    
        return $this;
    }

    /**
     * Get updatedat
     *
     * @return \DateTime 
     */
    public function getUpdatedat()
    {
        return $this->updatedat;
    }

    /**
     * Add order_detail
     *
     * @param PiggyBox\OrderBundle\Entity\OrderDetail $orderDetail
     * @return Order
     */
    public function addOrderDetail(\PiggyBox\OrderBundle\Entity\OrderDetail $orderDetail)
    {
        $this->order_detail[] = $orderDetail;
    
        return $this;
    }

    /**
     * Remove order_detail
     *
     * @param PiggyBox\OrderBundle\Entity\OrderDetail $orderDetail
     */
    public function removeOrderDetail(\PiggyBox\OrderBundle\Entity\OrderDetail $orderDetail)
    {
        $this->order_detail->removeElement($orderDetail);
    }

    /**
     * Get order_detail
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getOrderDetail()
    {
        return $this->order_detail;
    }

    /**
     * Set shop
     *
     * @param PiggyBox\ShopBundle\Entity\Shop $shop
     * @return Order
     */
    public function setShop(\PiggyBox\ShopBundle\Entity\Shop $shop = null)
    {
        $this->shop = $shop;
    
        return $this;
    }

    /**
     * Get shop
     *
     * @return PiggyBox\ShopBundle\Entity\Shop 
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * Set user
     *
     * @param PiggyBox\UserBundle\Entity\User $user
     * @return Order
     */
    public function setUser(\PiggyBox\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return PiggyBox\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set day
     *
     * @param PiggyBox\ShopBundle\Entity\Day $day
     * @return Order
     */
    public function setDay(\PiggyBox\ShopBundle\Entity\Day $day = null)
    {
        $this->day = $day;
    
        return $this;
    }

    /**
     * Get day
     *
     * @return PiggyBox\ShopBundle\Entity\Day 
     */
    public function getDay()
    {
        return $this->day;
    }
}