<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Menu
 *
 * @ORM\Table(name="piggybox_menu")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ORM\Entity()
 */
class Menu
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

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
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var integer
     *
     * @ORM\Column(name="stepsNumber", type="integer")
     */
    private $stepsNumber;

    /**
     * @var string $slug
     *
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="PiggyBox\ShopBundle\Entity\Shop")
     * @ORM\JoinColumn(name="shop_id", referencedColumnName="id")
     **/
    private $shop;

    /**
     * @ORM\OneToMany(targetEntity="PiggyBox\ShopBundle\Entity\MenuItem", mappedBy="menu", cascade={"persist"})
     **/
    private $menuItems;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;

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
     * Set title
     *
     * @param  string $title
     * @return Menu
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set price
     *
     * @param  float $price
     * @return Menu
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
     * Set shop
     *
     * @param  \PiggyBox\ShopBundle\Entity\Shop $shop
     * @return Menu
     */
    public function setShop(\PiggyBox\ShopBundle\Entity\Shop $shop = null)
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * Get shop
     *
     * @return \PiggyBox\ShopBundle\Entity\Shop
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * Set createdAt
     *
     * @param  \DateTime $createdAt
     * @return Menu
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Set updatedAt
     *
     * @param  \DateTime $updatedAt
     * @return Menu
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Set stepsNumber
     *
     * @param  integer $stepsNumber
     * @return Menu
     */
    public function setStepsNumber($stepsNumber)
    {
        $this->stepsNumber = $stepsNumber;

        return $this;
    }

    /**
     * Get stepsNumber
     *
     * @return integer
     */
    public function getStepsNumber()
    {
        return $this->stepsNumber;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->menuItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add menuItems
     *
     * @param  \PiggyBox\ShopBundle\Entity\MenuItem $menuItems
     * @return Menu
     */
    public function addMenuItem(\PiggyBox\ShopBundle\Entity\MenuItem $menuItems)
    {
        $this->menuItems[] = $menuItems;

        return $this;
    }

    /**
     * Remove menuItems
     *
     * @param \PiggyBox\ShopBundle\Entity\MenuItem $menuItems
     */
    public function removeMenuItem(\PiggyBox\ShopBundle\Entity\MenuItem $menuItems)
    {
        $this->menuItems->removeElement($menuItems);
    }

    /**
     * Get menuItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMenuItems()
    {
        return $this->menuItems;
    }

    /**
     * Set slug
     *
     * @param  string $slug
     * @return Menu
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set deletedAt
     *
     * @param  \DateTime $deletedAt
     * @return Menu
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }
}
