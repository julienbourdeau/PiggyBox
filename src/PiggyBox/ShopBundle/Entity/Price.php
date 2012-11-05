<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PiggyBox\ShopBundle\Entity\Price
 *
 * @ORM\Table(name="piggybox_price")
 * @ORM\Entity(repositoryClass="PiggyBox\ShopBundle\Entity\PriceRepository")
 */
class Price
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
     * @var float $price
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var integer $total_weight
     *
     * @ORM\Column(name="total_weight", type="string", length=100, nullable=true)
     */
    private $total_weight;

    /**
     * @var integer $slice_nbr
     *
     * @ORM\Column(name="slice_nbr", type="string", length=100, nullable=true)
     */
    private $slice_nbr;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set price
     *
     * @param  float $price
     * @return Price
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set createdat
     *
     * @param  \DateTime $createdat
     * @return Price
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
     * @param  \DateTime $updatedat
     * @return Price
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
     * Set total_weight
     *
     * @param  integer $totalWeight
     * @return Price
     */
    public function setTotalWeight($totalWeight)
    {
        $this->total_weight = $totalWeight;

        return $this;
    }

    /**
     * Get total_weight
     *
     * @return integer
     */
    public function getTotalWeight()
    {
        return $this->total_weight;
    }

    /**
     * Set slice_nbr
     *
     * @param  integer $sliceNbr
     * @return Price
     */
    public function setSliceNbr($sliceNbr)
    {
        $this->slice_nbr = $sliceNbr;

        return $this;
    }

    /**
     * Get slice_nbr
     *
     * @return integer
     */
    public function getSliceNbr()
    {
        return $this->slice_nbr;
    }
}
