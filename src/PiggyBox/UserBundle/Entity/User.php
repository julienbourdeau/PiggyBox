<?php

namespace PiggyBox\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;

/**
 * PiggyBox\UserBundle\Entity\User
 *
 * @ORM\Table(name="piggybox_user")
 * @ORM\Entity(repositoryClass="PiggyBox\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * @var string $lastname
     *
     * @ORM\Column(name="lastname", type="string", nullable=true)
     */
    private $name;
	
    /**
     * @var string $phone_number
     *
     * @ORM\Column(name="phone_number", type="string", nullable=true)
     */
    private $phone_number;

    /**
     * @ORM\OneToOne(targetEntity="PiggyBox\ShopBundle\Entity\Shop")
     * @ORM\JoinColumn(name="ownshop_id", referencedColumnName="id")
     **/
    private $ownshop;
    
     /**
     * @ORM\ManyToMany(targetEntity="PiggyBox\ShopBundle\Entity\Shop", mappedBy="clients")
     **/
    private $shops;
	
	/**
     * @ORM\OneToMany(targetEntity="PiggyBox\OrderBundle\Entity\Order", mappedBy="user")
     **/
    private $orders;

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
     * Set ownshop
     *
     * @param PiggyBox\ShopBundle\Entity\Shop $ownshop
     * @return User
     */
    public function setOwnshop(\PiggyBox\ShopBundle\Entity\Shop $ownshop = null)
    {
        $this->ownshop = $ownshop;
    
        return $this;
    }

    /**
     * Get ownshop
     *
     * @return PiggyBox\ShopBundle\Entity\Shop 
     */
    public function getOwnshop()
    {
        return $this->ownshop;
    }

    /**
     * Add shops
     *
     * @param PiggyBox\ShopBundle\Entity\Shop $shops
     * @return User
     */
    public function addShop(\PiggyBox\ShopBundle\Entity\Shop $shops)
    {
        $this->shops[] = $shops;
    
        return $this;
    }

    /**
     * Remove shops
     *
     * @param PiggyBox\ShopBundle\Entity\Shop $shops
     */
    public function removeShop(\PiggyBox\ShopBundle\Entity\Shop $shops)
    {
        $this->shops->removeElement($shops);
    }

    /**
     * Get shops
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getShops()
    {
        return $this->shops;
    }

    /**
     * Add orders
     *
     * @param PiggyBox\OrderBundle\Entity\Order $orders
     * @return User
     */
    public function addOrder(\PiggyBox\OrderBundle\Entity\Order $orders)
    {
        $this->orders[] = $orders;
    
        return $this;
    }

    /**
     * Remove orders
     *
     * @param PiggyBox\OrderBundle\Entity\Order $orders
     */
    public function removeOrder(\PiggyBox\OrderBundle\Entity\Order $orders)
    {
        $this->orders->removeElement($orders);
    }

    /**
     * Get orders
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getOrders()
    {
        return $this->orders;
    }


    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set phone_number
     *
     * @param string $phoneNumber
     * @return User
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phone_number = $phoneNumber;
    
        return $this;
    }

    /**
     * Get phone_number
     *
     * @return string 
     */
    public function getPhoneNumber()
    {
        return $this->phone_number;
    }
}