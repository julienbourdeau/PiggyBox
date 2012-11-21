<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PiggyBox\ShopBundle\Entity\Sales
 *
 * @ORM\Table(name="piggybox_sales")
 * @ORM\Entity
 */
class Sales
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
     * @var integer $sales_nbr
     *
     * @ORM\Column(name="sales_nbr", type="integer")
     */
    private $sales_nbr = 0;

    /**
     * @var \DateTime $updatedat
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updatedat", type="datetime")
     */
    private $updatedat;

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
     * Set sales_nbr
     *
     * @param  integer $salesNbr
     * @return Sales
     */
    public function setSalesNbr($salesNbr)
    {
        $this->sales_nbr = $salesNbr;

        return $this;
    }

    /**
     * Get sales_nbr
     *
     * @return integer
     */
    public function getSalesNbr()
    {
        return $this->sales_nbr;
    }

    /**
     * Set updatedat
     *
     * @param  \DateTime $updatedat
     * @return Sales
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
}
