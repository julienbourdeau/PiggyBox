<?php

namespace PiggyBox\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PiggyBox\OrderBundle\Entity\Cart
 *
 * @ORM\Table(name="piggybox_cart")
 * @ORM\Entity(repositoryClass="PiggyBox\OrderBundle\Entity\CartRepository")
 */
class Cart
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
     * @var \DateTime $createdat
     *
     * @ORM\Column(name="createdat", type="datetime")
     */
    private $createdat;

    /**
     * @var \DateTime $updatedat
     *
     * @ORM\Column(name="updatedat", type="datetime")
     */
    private $updatedat;

    /**
     * @var \DateTime $expiredat
     *
     * @ORM\Column(name="expiredat", type="datetime")
     */
    private $expiredat;


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
     * Set createdat
     *
     * @param \DateTime $createdat
     * @return Cart
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
     * @return Cart
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

    /**
     * Set expiredat
     *
     * @param \DateTime $expiredat
     * @return Cart
     */
    public function setExpiredat($expiredat)
    {
        $this->expiredat = $expiredat;
    
        return $this;
    }

    /**
     * Get expiredat
     *
     * @return \DateTime 
     */
    public function getExpiredat()
    {
        return $this->expiredat;
    }
}
