<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlaceGeometry
 *
 * @ORM\Table(name="piggybox_placegeometry")
 * @ORM\Entity
 */
class PlaceGeometry
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
     * @ORM\ManyToOne(targetEntity="PiggyBox\ShopBundle\Entity\LatLng")
     * @ORM\JoinColumn(name="southWest_id", referencedColumnName="id")
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="PiggyBox\ShopBundle\Entity\LatLngBounds")
     * @ORM\JoinColumn(name="southWest_id", referencedColumnName="id")
     */
    private $viewport;

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
     * Set location
     *
     * @param \PiggyBox\ShopBundle\Entity\LatLng $location
     * @return PlaceGeometry
     */
    public function setLocation(\PiggyBox\ShopBundle\Entity\LatLng $location = null)
    {
        $this->location = $location;
    
        return $this;
    }

    /**
     * Get location
     *
     * @return \PiggyBox\ShopBundle\Entity\LatLng 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set viewport
     *
     * @param \PiggyBox\ShopBundle\Entity\LatLngBounds $viewport
     * @return PlaceGeometry
     */
    public function setViewport(\PiggyBox\ShopBundle\Entity\LatLngBounds $viewport = null)
    {
        $this->viewport = $viewport;
    
        return $this;
    }

    /**
     * Get viewport
     *
     * @return \PiggyBox\ShopBundle\Entity\LatLngBounds 
     */
    public function getViewport()
    {
        return $this->viewport;
    }
}
