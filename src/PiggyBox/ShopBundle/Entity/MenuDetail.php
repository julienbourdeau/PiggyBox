<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * MenuDetail
 *
 * @ORM\Table(name="piggybox_menudetail")
 * @ORM\Entity(repositoryClass="PiggyBox\ShopBundle\Entity\MenuDetailRepository")
 */
class MenuDetail
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \stdClass
     *
     * @ORM\Column(name="menu", type="object")
     */
    private $menu;

    /**
     * @ORM\ManyToMany(targetEntity="PiggyBox\ShopBundle\Entity\Product")
     * @ORM\JoinTable(name="menudetails_products",
     *      joinColumns={@ORM\JoinColumn(name="menudetail_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    private $products;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity="PiggyBox\OrderBundle\Entity\OrderDetail", mappedBy="menuDetail")
     **/
    private $orderDetail;

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
     * Set menu
     *
     * @param  \stdClass  $menu
     * @return MenuDetail
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Get menu
     *
     * @return \stdClass
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Set createdAt
     *
     * @param  \DateTime  $createdAt
     * @return MenuDetail
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param  \DateTime  $updatedAt
     * @return MenuDetail
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set orderDetail
     *
     * @param  \PiggyBox\OrderBundle\Entity\OrderDetail $orderDetail
     * @return MenuDetail
     */
    public function setOrderDetail(\PiggyBox\OrderBundle\Entity\OrderDetail $orderDetail = null)
    {
        $this->orderDetail = $orderDetail;

        return $this;
    }

    /**
     * Get orderDetail
     *
     * @return \PiggyBox\OrderBundle\Entity\OrderDetail
     */
    public function getOrderDetail()
    {
        return $this->orderDetail;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add products
     *
     * @param  \PiggyBox\ShopBundle\Entity\Product $products
     * @return MenuDetail
     */
    public function addProduct(\PiggyBox\ShopBundle\Entity\Product $products)
    {
        $this->products[] = $products;

        return $this;
    }

    /**
     * Remove products
     *
     * @param \PiggyBox\ShopBundle\Entity\Product $products
     */
    public function removeProduct(\PiggyBox\ShopBundle\Entity\Product $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }
}
