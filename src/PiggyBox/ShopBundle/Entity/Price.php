<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PiggyBox\ShopBundle\Entity\Price
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PiggyBox\ShopBundle\Entity\PriceRepository")
 */
class Price
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
     * @var float $weight
     *
     * @ORM\Column(name="weight", type="float")
     */
    private $weight;

    /**
     * @var string $unity
     *
     * @ORM\Column(name="unity", type="string", length=10)
     */
    private $unity;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;


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
     * Set weight
     *
     * @param float $weight
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
     * @return float 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set unity
     *
     * @param string $unity
     * @return Price
     */
    public function setUnity($unity)
    {
        $this->unity = $unity;
    
        return $this;
    }

    /**
     * Get unity
     *
     * @return string 
     */
    public function getUnity()
    {
        return $this->unity;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Price
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
}
