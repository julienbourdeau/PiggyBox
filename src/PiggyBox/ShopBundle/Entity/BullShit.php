<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BullShit
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class BullShit
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
     * @ORM\Column(name="addressComponents", type="string", length=255)
     */
    private $addressComponents;

    /**
     * @var string
     *
     * @ORM\Column(name="formattedAddress", type="string", length=255)
     */
    private $formattedAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="formattedPhoneNumber", type="string", length=255)
     */
    private $formattedPhoneNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="geometry", type="string", length=255)
     */
    private $geometry;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="photos", type="string", length=255)
     */
    private $photos;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255)
     */
    private $website;

    /**
     * @var string
     *
     * @ORM\Column(name="vicinity", type="string", length=512)
     */
    private $vicinity;

    /**
     * @var array
     *
     * @ORM\Column(name="types", type="array")
     */
    private $types;

    /**
     * @var string
     *
     * @ORM\Column(name="openingHours", type="string", length=255)
     */
    private $openingHours;


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
     * Set addressComponents
     *
     * @param string $addressComponents
     * @return BullShit
     */
    public function setAddressComponents($addressComponents)
    {
        $this->addressComponents = $addressComponents;
    
        return $this;
    }

    /**
     * Get addressComponents
     *
     * @return string 
     */
    public function getAddressComponents()
    {
        return $this->addressComponents;
    }

    /**
     * Set formattedAddress
     *
     * @param string $formattedAddress
     * @return BullShit
     */
    public function setFormattedAddress($formattedAddress)
    {
        $this->formattedAddress = $formattedAddress;
    
        return $this;
    }

    /**
     * Get formattedAddress
     *
     * @return string 
     */
    public function getFormattedAddress()
    {
        return $this->formattedAddress;
    }

    /**
     * Set formattedPhoneNumber
     *
     * @param string $formattedPhoneNumber
     * @return BullShit
     */
    public function setFormattedPhoneNumber($formattedPhoneNumber)
    {
        $this->formattedPhoneNumber = $formattedPhoneNumber;
    
        return $this;
    }

    /**
     * Get formattedPhoneNumber
     *
     * @return string 
     */
    public function getFormattedPhoneNumber()
    {
        return $this->formattedPhoneNumber;
    }

    /**
     * Set geometry
     *
     * @param string $geometry
     * @return BullShit
     */
    public function setGeometry($geometry)
    {
        $this->geometry = $geometry;
    
        return $this;
    }

    /**
     * Get geometry
     *
     * @return string 
     */
    public function getGeometry()
    {
        return $this->geometry;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return BullShit
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
     * Set photos
     *
     * @param string $photos
     * @return BullShit
     */
    public function setPhotos($photos)
    {
        $this->photos = $photos;
    
        return $this;
    }

    /**
     * Get photos
     *
     * @return string 
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return BullShit
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    
        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set vicinity
     *
     * @param string $vicinity
     * @return BullShit
     */
    public function setVicinity($vicinity)
    {
        $this->vicinity = $vicinity;
    
        return $this;
    }

    /**
     * Get vicinity
     *
     * @return string 
     */
    public function getVicinity()
    {
        return $this->vicinity;
    }

    /**
     * Set types
     *
     * @param array $types
     * @return BullShit
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
     * Set openingHours
     *
     * @param string $openingHours
     * @return BullShit
     */
    public function setOpeningHours($openingHours)
    {
        $this->openingHours = $openingHours;
    
        return $this;
    }

    /**
     * Get openingHours
     *
     * @return string 
     */
    public function getOpeningHours()
    {
        return $this->openingHours;
    }
}
