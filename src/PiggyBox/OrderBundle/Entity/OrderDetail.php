<?php

namespace PiggyBox\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PiggyBox\OrderBundle\Entity\OrderDetail
 *
 * @ORM\Table(name="piggybox_orderdetail")
 * @ORM\Entity(repositoryClass="PiggyBox\OrderBundle\Entity\OrderDetailRepository")
 */
class OrderDetail
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
     * @var integer $product_quantity
     *
     * @ORM\Column(name="product_quantity", type="integer")
     */
    private $product_quantity;

    /**
     * @var float $product_price
     *
     * @ORM\Column(name="product_price", type="float")
     */
    private $product_price;

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
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="order_detail")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     **/
    private $order;

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
     * Set product_quantity
     *
     * @param integer $productQuantity
     * @return OrderDetail
     */
    public function setProductQuantity($productQuantity)
    {
        $this->product_quantity = $productQuantity;
    
        return $this;
    }

    /**
     * Get product_quantity
     *
     * @return integer 
     */
    public function getProductQuantity()
    {
        return $this->product_quantity;
    }

    /**
     * Set product_price
     *
     * @param float $productPrice
     * @return OrderDetail
     */
    public function setProductPrice($productPrice)
    {
        $this->product_price = $productPrice;
    
        return $this;
    }

    /**
     * Get product_price
     *
     * @return float 
     */
    public function getProductPrice()
    {
        return $this->product_price;
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
     * Set order
     *
     * @param PiggyBox\OrderBundle\Entity\Order $order
     * @return OrderDetail
     */
    public function setOrder(\PiggyBox\OrderBundle\Entity\Order $order = null)
    {
        $this->order = $order;
    
        return $this;
    }

    /**
     * Get order
     *
     * @return PiggyBox\OrderBundle\Entity\Order 
     */
    public function getOrder()
    {
        return $this->order;
    }
}
