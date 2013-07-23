<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LatLngBounds
 *
 * @ORM\Table(name="piggybox_latlngbounds")
 * @ORM\Entity
 */
class LatLngBounds
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
     *
     * @ORM\ManyToOne(targetEntity="PiggyBox\ShopBundle\Entity\LatLng")
     * @ORM\JoinColumn(name="southWest_id", referencedColumnName="id")
     */
    private $southWest;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="PiggyBox\ShopBundle\Entity\LatLng")
     * @ORM\JoinColumn(name="northEast_id", referencedColumnName="id")
     */
    private $northEast;


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
     * Set southWest
     *
     * @param \PiggyBox\ShopBundle\Entity\LatLng $southWest
     * @return LatLngBounds
     */
    public function setSouthWest(\PiggyBox\ShopBundle\Entity\LatLng $southWest = null)
    {
        $this->southWest = $southWest;
    
        return $this;
    }

    /**
     * Get southWest
     *
     * @return \PiggyBox\ShopBundle\Entity\LatLng 
     */
    public function getSouthWest()
    {
        return $this->southWest;
    }

    /**
     * Set northEast
     *
     * @param \PiggyBox\ShopBundle\Entity\LatLng $northEast
     * @return LatLngBounds
     */
    public function setNorthEast(\PiggyBox\ShopBundle\Entity\LatLng $northEast = null)
    {
        $this->northEast = $northEast;
    
        return $this;
    }

    /**
     * Get northEast
     *
     * @return \PiggyBox\ShopBundle\Entity\LatLng 
     */
    public function getNorthEast()
    {
        return $this->northEast;
    }
}
