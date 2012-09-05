<?php

namespace PiggyBox\ManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PiggyBox\ManagerBundle\Entity\Manager
 *
 * @ORM\Table(name="piggybox_manager")
 * @ORM\Entity(repositoryClass="PiggyBox\ManagerBundle\Entity\ManagerRepository")
 */
class Manager
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
