<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entry for users that become clients for an uniq shop
 *
 * @ORM\Table(name="piggybox_client")
 * @ORM\Entity
 */
class Client
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="PiggyBox\UserBundle\Entity\User", inversedBy="clients")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="PiggyBox\ShopBundle\Entity\Shop", inversedBy="clients")
     * @ORM\JoinColumn(name="shop_id", referencedColumnName="id")
     */
    private $shop;

    /**
     * @ORM\OneToMany(targetEntity="PiggyBox\OrderBundle\Entity\Order", mappedBy="client")
     * @ORM\Column(name="orders", type="object")
     */
    private $orders;

    /**
     * @var integer
     *
     * @ORM\Column(name="totalOrders", type="integer")
     */
    private $totalOrders;

    /**
     * @var float
     *
     * @ORM\Column(name="averageRevenue", type="float")
     */
    private $averageRevenue;

    /**
     * @var float
     *
     * @ORM\Column(name="minRevenue", type="float")
     */
    private $minRevenue;

    /**
     * @var float
     *
     * @ORM\Column(name="maxRevenue", type="float")
     */
    private $maxRevenue;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

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
     * Set totalOrders
     *
     * @param integer $totalOrders
     * @return Client
     */
    public function setTotalOrders($totalOrders)
    {
        $this->totalOrders = $totalOrders;
    
        return $this;
    }

    /**
     * Get totalOrders
     *
     * @return integer 
     */
    public function getTotalOrders()
    {
        return $this->totalOrders;
    }

    /**
     * Set averageRevenue
     *
     * @param float $averageRevenue
     * @return Client
     */
    public function setAverageRevenue($averageRevenue)
    {
        $this->averageRevenue = $averageRevenue;
    
        return $this;
    }

    /**
     * Get averageRevenue
     *
     * @return float 
     */
    public function getAverageRevenue()
    {
        return $this->averageRevenue;
    }

    /**
     * Set minRevenue
     *
     * @param float $minRevenue
     * @return Client
     */
    public function setMinRevenue($minRevenue)
    {
        $this->minRevenue = $minRevenue;
    
        return $this;
    }

    /**
     * Get minRevenue
     *
     * @return float 
     */
    public function getMinRevenue()
    {
        return $this->minRevenue;
    }

    /**
     * Set maxRevenue
     *
     * @param float $maxRevenue
     * @return Client
     */
    public function setMaxRevenue($maxRevenue)
    {
        $this->maxRevenue = $maxRevenue;
    
        return $this;
    }

    /**
     * Get maxRevenue
     *
     * @return float 
     */
    public function getMaxRevenue()
    {
        return $this->maxRevenue;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set user
     *
     * @param \PiggyBox\UserBundle\Entity\User $user
     * @return Client
     */
    public function setUser(\PiggyBox\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \PiggyBox\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set shop
     *
     * @param \PiggyBox\ShopBundle\Entity\Shop $shop
     * @return Client
     */
    public function setShop(\PiggyBox\ShopBundle\Entity\Shop $shop = null)
    {
        $this->shop = $shop;
    
        return $this;
    }

    /**
     * Get shop
     *
     * @return \PiggyBox\ShopBundle\Entity\Shop 
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Client
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Client
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Set orders
     *
     * @param \stdClass $orders
     * @return Client
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
    
        return $this;
    }

    /**
     * Get orders
     *
     * @return \stdClass 
     */
    public function getOrders()
    {
        return $this->orders;
    }
}