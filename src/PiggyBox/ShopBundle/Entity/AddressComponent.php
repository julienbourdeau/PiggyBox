<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AddressComponent
 *
 * @ORM\Table(name="piggybox_address_components")
 * @ORM\Entity
 */
class AddressComponent
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
     * Is an array indicating the type of the address component
     *
     * @var array
     *
     * @ORM\Column(name="types", type="array")
     */
    private $types;

    /**
     * Is the full text description or name of the address component as returned by the Geocoder
     *
     * @var string
     *
     * @ORM\Column(name="longName", type="text")
     */
    private $longName;

    /**
     * Is an abbreviated textual name for the address component, if available. For example, an address component for the state of Alaska may have a long_name of "Alaska" and a short_name of "AK" using the 2-letter postal abbreviation.
     *
     * @var string
     *
     * @ORM\Column(name="shortName", type="text")
     */
    private $shortName;


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
     * Set types
     *
     * @param array $types
     * @return AddressComponent
     */
    public function setTypes($types)
    {
        $this->types = $types;
    
        return $this;
    }

    /**
     * Get types
     *
     * @return array 
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Set longName
     *
     * @param string $longName
     * @return AddressComponent
     */
    public function setLongName($longName)
    {
        $this->longName = $longName;
    
        return $this;
    }

    /**
     * Get longName
     *
     * @return string 
     */
    public function getLongName()
    {
        return $this->longName;
    }

    /**
     * Set shortName
     *
     * @param string $shortName
     * @return AddressComponent
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;
    
        return $this;
    }

    /**
     * Get shortName
     *
     * @return string 
     */
    public function getShortName()
    {
        return $this->shortName;
    }
}
