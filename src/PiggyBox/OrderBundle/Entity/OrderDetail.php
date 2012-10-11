<?php

namespace PiggyBox\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\ManyToOne(targetEntity="PiggyBox\ShopBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     **/
    private $product;

	/**
     * @ORM\ManyToOne(targetEntity="PiggyBox\ShopBundle\Entity\Price")
     * @ORM\JoinColumn(name="price_id", referencedColumnName="id")
     **/
    private $price;

    /**
     * @var string $quantity
	 *
     * @ORM\Column(name="quantity", type="string", nullable=true)
     */
    private $quantity;

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
     * @param \DateTime $createdat
     * @return OrderDetail
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
     * @param \DateTime $updatedat
     * @return OrderDetail
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

    /**
     * Set product
     *
     * @param PiggyBox\ShopBundle\Entity\Product $product
     * @return OrderDetail
     */
    public function setProduct(\PiggyBox\ShopBundle\Entity\Product $product = null)
    {
        $this->product = $product;
    
        return $this;
    }

    /**
     * Get product
     *
     * @return PiggyBox\ShopBundle\Entity\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set price
     *
     * @param PiggyBox\ShopBundle\Entity\Price $price
     * @return OrderDetail
     */
    public function setPrice(\PiggyBox\ShopBundle\Entity\Price $price = null)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return PiggyBox\ShopBundle\Entity\Price 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return OrderDetail
     */
    public function setQuantity($quantity)
    {
        $this->quantity = (int) $quantity;
    
        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    public function __toString()
    {
        return 'order_detail';
    }
}
