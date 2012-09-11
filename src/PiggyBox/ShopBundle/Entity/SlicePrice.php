<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PiggyBox\ShopBundle\Entity\SlicePrice
 *
 * @ORM\Entity
 */
class SlicePrice extends Price
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
     * @var integer $slices_nbr
     *
     * @ORM\Column(name="slices_nbr", type="integer")
     */
    private $slices_nbr;

	/**
     * @var integer $total_weight
     *
     * @ORM\Column(name="total_weight", type="integer")
     */
    private $total_weight;

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
