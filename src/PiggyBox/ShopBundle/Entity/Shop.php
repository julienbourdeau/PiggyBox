<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PiggyBox\ShopBundle\Entity\Shop
 *
 * @ORM\Table(name="piggybox_shop")
 * @ORM\Entity(repositoryClass="PiggyBox\ShopBundle\Entity\ShopRepository")
 */
class Shop
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="string", length=100)
     */
    private $type;
 
    /**
     * @var text $category_html
     *
     * @ORM\Column(name="category_html", type="text", nullable=true)
     */
    private $category_html;

    /**
     * @var string $slug
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;


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
     * @ORM\OneToMany(targetEntity="PiggyBox\ShopBundle\Entity\Product", mappedBy="shop")
     **/
    private $products;

    /**
     * @ORM\OneToOne(targetEntity="PiggyBox\ShopBundle\Entity\Sales")
     * @ORM\JoinColumn(name="sales_id", referencedColumnName="id")
     **/
    private $sales;
    
    /**
     * @ORM\ManyToMany(targetEntity="PiggyBox\UserBundle\Entity\User", inversedBy="shops")
     * @ORM\JoinTable(name="shops_clients")
     **/
    private $clients;

	/**
     * @ORM\OneToMany(targetEntity="PiggyBox\OrderBundle\Entity\Order", mappedBy="shop")
     **/
    private $orders;	
	
	/**
     * @ORM\OneToMany(targetEntity="Day", mappedBy="shop")
     **/
    private $opening_days;
	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
        $this->clients = new \Doctrine\Common\Collections\ArrayCollection();
        $this->orders = new \Doctrine\Common\Collections\ArrayCollection();
		$this->opening_days = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
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
     * Set name
     *
     * @param string $name
     * @return Shop
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Shop
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set category_html
     *
     * @param string $categoryHtml
     * @return Shop
     */
    public function setCategoryHtml($categoryHtml)
    {
        $this->category_html = $categoryHtml;
    
        return $this;
    }

    /**
     * Get category_html
     *
     * @return string 
     */
    public function getCategoryHtml()
    {
        return $this->category_html;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Shop
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
     * Set createdat
     *
     * @param \DateTime $createdat
     * @return Shop
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
     * @return Shop
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
     * Add products
     *
     * @param PiggyBox\ShopBundle\Entity\Product $products
     * @return Shop
     */
    public function addProduct(\PiggyBox\ShopBundle\Entity\Product $products)
    {
        $this->products[] = $products;
    
        return $this;
    }

    /**
     * Remove products
     *
     * @param PiggyBox\ShopBundle\Entity\Product $products
     */
    public function removeProduct(\PiggyBox\ShopBundle\Entity\Product $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set sales
     *
     * @param PiggyBox\ShopBundle\Entity\Sales $sales
     * @return Shop
     */
    public function setSales(\PiggyBox\ShopBundle\Entity\Sales $sales = null)
    {
        $this->sales = $sales;
    
        return $this;
    }

    /**
     * Get sales
     *
     * @return PiggyBox\ShopBundle\Entity\Sales 
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * Add clients
     *
     * @param PiggyBox\UserBundle\Entity\User $clients
     * @return Shop
     */
    public function addClient(\PiggyBox\UserBundle\Entity\User $clients)
    {
        $this->clients[] = $clients;
    
        return $this;
    }

    /**
     * Remove clients
     *
     * @param PiggyBox\UserBundle\Entity\User $clients
     */
    public function removeClient(\PiggyBox\UserBundle\Entity\User $clients)
    {
        $this->clients->removeElement($clients);
    }

    /**
     * Get clients
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getClients()
    {
        return $this->clients;
    }

    /**
     * Add orders
     *
     * @param PiggyBox\OrderBundle\Entity\Order $orders
     * @return Shop
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

    /**
     * Add opening_days
     *
     * @param PiggyBox\ShopBundle\Entity\Day $openingDays
     * @return Shop
     */
    public function addOpeningDay(\PiggyBox\ShopBundle\Entity\Day $openingDays)
    {
        $this->opening_days[] = $openingDays;
    
        return $this;
    }

    /**
     * Remove opening_days
     *
     * @param PiggyBox\ShopBundle\Entity\Day $openingDays
     */
    public function removeOpeningDay(\PiggyBox\ShopBundle\Entity\Day $openingDays)
    {
        $this->opening_days->removeElement($openingDays);
    }

    /**
     * Get opening_days
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getOpeningDays()
    {
        return $this->opening_days;
    }
}