<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MenuDetail
 *
 * @ORM\Table()
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
     * @var \stdClass
     *
     * @ORM\Column(name="products", type="object")
     */
    private $products;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;


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
     * @param \stdClass $menu
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
     * Set products
     *
     * @param \stdClass $products
     * @return MenuDetail
     */
    public function setProducts($products)
    {
        $this->products = $products;
    
        return $this;
    }

    /**
     * Get products
     *
     * @return \stdClass 
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
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
     * @param \DateTime $updatedAt
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
}
