<?php

namespace PiggyBox\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PiggyBox\UserBundle\Entity\User
 *
 * @ORM\Table(name="piggybox_user")
 * @ORM\Entity(repositoryClass="PiggyBox\UserBundle\Entity\UserRepository")
 */
class User
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
