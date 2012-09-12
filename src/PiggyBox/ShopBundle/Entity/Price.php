<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PiggyBox\ShopBundle\Entity\Price
 *
 * @ORM\Table(name="piggybox_price")
 * @ORM\Entity(repositoryClass="PiggyBox\ShopBundle\Entity\PriceRepository")
 */
abstract class Price
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
     * @var float $price
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

	/**
     * @var float $price
     *
     * @ORM\Column(name="price_kg", type="float")
     */
    private $price_kg;

	/**
     * @var integer $weight
     *
     * @ORM\Column(name="weight", type="integer", nullable=true)
     */
    private $weight;

	/**
     * @var integer $total_weight
     *
     * @ORM\Column(name="total_weight", type="integer", nullable=true)
     */
    private $total_weight;

	/**
     * @var integer $slice_nbr
     *
     * @ORM\Column(name="slice_nbr", type="integer", nullable=true)
     */
    private $slice_nbr;
	
    /**
     * @var \DateTime $createdat

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set price_kg
     *
     * @param float $priceKg
     * @return Price
     */
    public function setPriceKg($priceKg)
    {
        $this->price_kg = $priceKg;
    
        return $this;
    }

    /**
     * Get price_kg
     *
     * @return float 
     */
    public function getPriceKg()
    {
        return $this->price_kg;
    }

    /**
     * Set createdat
     *
     * @param \DateTime $createdat
     * @return Price
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
     * @return Price
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

	public function __toString()
	{
  		return strval($this->id);
	}

    /**
     * Set weight
     *
     * @param integer $weight
     * @return Price
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    
        return $this;
    }

    /**
     * Get weight
     *
     * @return integer 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set total_weight
     *
     * @param integer $totalWeight
     * @return Price
     */
    public function setTotalWeight($totalWeight)
    {
        $this->total_weight = $totalWeight;
    
        return $this;
    }

    /**
     * Get total_weight
     *
     * @return integer 
     */
    public function getTotalWeight()
    {
        return $this->total_weight;
    }

    /**
     * Set slice_nbr
     *
     * @param integer $sliceNbr
     * @return Price
     */
    public function setSliceNbr($sliceNbr)
    {
        $this->slice_nbr = $sliceNbr;
    
        return $this;
    }

    /**
     * Get slice_nbr
     *
     * @return integer 
     */
    public function getSliceNbr()
    {
        return $this->slice_nbr;
    }
}