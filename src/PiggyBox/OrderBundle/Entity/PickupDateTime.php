<?php

namespace PiggyBox\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PiggyBox\OrderBundle\Entity\PickupDateTime
 *
 * @ORM\Table(name="piggybox_pickupdatetime")
 * @ORM\Entity
 */
class PickupDateTime
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
