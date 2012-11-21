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
     * @ORM\ManyToMany(targetEntity="PiggyBox\OrderBundle\Entity\Order", cascade={"persist"})
     * @ORM\JoinTable(name="cart_orders",
     *      joinColumns={@ORM\JoinColumn(name="cart_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="orders_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    private $orders;

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
     * @param  \DateTime $createdat
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
     * @param  \DateTime $updatedat
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
     * Constructor
     */
    public function __construct()
    {
        $this->orders = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add orders
     *
     * @param  PiggyBox\OrderBundle\Entity\Order $orders
     * @return Cart
     */
    public function addOrder(\PiggyBox\OrderBundle\Entity\Order $orders)
    {
        $this->orders[] = $orders;

        return $this;
    }

    /**
     * Remove orders
     *
     * @param PiggyBox\OrderBundle\Entity\Order $orders
     */
    public function removeOrder(\PiggyBox\OrderBundle\Entity\Order $orders)
    {
        $this->orders->removeElement($orders);
    }

    /**
     * Get orders
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }
}
