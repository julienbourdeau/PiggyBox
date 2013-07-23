<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * LatLng
 *
 * @ORM\Table(name="piggybox_latlng")
 * @ORM\Entity
 */
class LatLng
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
     * Latitude ranges between -90 and 90 degrees, inclusive.
     *
     * @var float
     * @Assert\Range(
     *      min = -90,
     *      max = 90,
     *      minMessage = "You must at least have a -90 latitude position",
     *      maxMessage = "You cannot have a higher value than 90"
     * )
     *
     * @ORM\Column(name="latitude", type="float")
     */
    private $latitude;

    /**
     * Longitude ranges between -180 and 180 degrees, inclusive.
     *
     * @var float
     * @Assert\Range(
     *      min = -180,
     *      max = 180,
     *      minMessage = "You must at least have a -180 longitude position",
     *      maxMessage = "You cannot have a higher value than 180"
     * )
     *
     * @ORM\Column(name="longitude", type="float")
     */
    private $longitude;


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
     * Set latitude
     *
     * @param float $latitude
     * @return LatLng
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    
        return $this;
    }

    /**
     * Get latitude
     *
     * @return float 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return LatLng
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    
        return $this;
    }

    /**
     * Get longitude
     *
     * @return float 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
}
