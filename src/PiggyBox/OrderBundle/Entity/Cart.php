<?php

namespace PiggyBox\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
     * Get createdat
     *
     * @return \DateTime 
     */
    public function getCreatedat()
    {
        return $this->createdat;
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
