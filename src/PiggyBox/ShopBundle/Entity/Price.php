<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PiggyBox\ShopBundle\Entity\Price
 *
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"unitprice" = "UnitPrice", "sliceprice" = "SlicePrice"})*
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
     * @var float $price
     *
     * @ORM\Column(name="price_kg", type="float")
     */
    private $price_kg;

    /**
     * @var \DateTime $createdat

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

}
