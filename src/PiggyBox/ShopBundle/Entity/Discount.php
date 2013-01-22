<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Discount
 *
 * @ORM\Table(name="piggybox_discount")
 * @ORM\Entity
 */
class Discount
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
     * @var string
     *
     * @ORM\Column(name="discountName", type="string", length=255)
     */
    private $discountName;

    /**
     * @var string
     *
     * @ORM\Column(name="discountDescription", type="string", length=255)
     */
    private $discountDescription;

    /**
     * @var float
     *
     * @ORM\Column(name="discountPrice", type="float")
     */
    private $discountPrice;

    /**
     * @var integer
     *
     * @ORM\Column(name="discountQuantity", type="integer")
     */
    private $discountQuantity;


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
     * Set discountName
     *
     * @param string $discountName
     * @return Discount
     */
    public function setDiscountName($discountName)
    {
        $this->discountName = $discountName;
    
        return $this;
    }

    /**
     * Get discountName
     *
     * @return string 
     */
    public function getDiscountName()
    {
        return $this->discountName;
    }

    /**
     * Set discountDescription
     *
     * @param string $discountDescription
     * @return Discount
     */
    public function setDiscountDescription($discountDescription)
    {
        $this->discountDescription = $discountDescription;
    
        return $this;
    }

    /**
     * Get discountDescription
     *
     * @return string 
     */
    public function getDiscountDescription()
    {
        return $this->discountDescription;
    }

    /**
     * Set discountPrice
     *
     * @param float $discountPrice
     * @return Discount
     */
    public function setDiscountPrice($discountPrice)
    {
        $this->discountPrice = $discountPrice;
    
        return $this;
    }

    /**
     * Get discountPrice
     *
     * @return float 
     */
    public function getDiscountPrice()
    {
        return $this->discountPrice;
    }

    /**
     * Set discountQuantity
     *
     * @param integer $discountQuantity
     * @return Discount
     */
    public function setDiscountQuantity($discountQuantity)
    {
        $this->discountQuantity = $discountQuantity;
    
        return $this;
    }

    /**
     * Get discountQuantity
     *
     * @return integer 
     */
    public function getDiscountQuantity()
    {
        return $this->discountQuantity;
    }
}
