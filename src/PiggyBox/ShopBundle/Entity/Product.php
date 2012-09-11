<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PiggyBox\ShopBundle\Entity\Product
 *
 * @ORM\Table(name="piggybox_product")
 * @ORM\HasLifecycleCallbacks 
 * @ORM\Entity(repositoryClass="PiggyBox\ShopBundle\Entity\ProductRepository")
 */
class Product
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
     * @var string $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean",nullable=true)
     */
    private $active;

    /**
     * @var string $image_path
     *
     * @ORM\Column(name="image_path", type="string", length=255, nullable=true)
     */
    private $image_path;

    /**
     * @var string $promo_active
     *
     * @ORM\Column(name="promo_active", type="string", length=255, nullable=true)
     */
    private $promo_active;

    /**
     * @var string $promo_price
     *
     * @ORM\Column(name="promo_price", type="string", length=255, nullable=true)
     */
    private $promo_price;

    /**
     * @var \DateTime $promo_expire_date
     *
     * @ORM\Column(name="promo_expire_date", type="datetime", nullable=true)
     */
    private $promo_expire_date;

    /**
     * @var float $promo_percentage
     *
     * @ORM\Column(name="promo_percentage", type="float", nullable=true)
     */
    private $promo_percentage;

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

    /**
     * @ORM\ManyToOne(targetEntity="Shop", inversedBy="products")
     * @ORM\JoinColumn(name="shop_id", referencedColumnName="id")
     **/
    private $shop;

    /**
     * @ORM\ManyToMany(targetEntity="PiggyBox\ShopBundle\Entity\Price", cascade={"persist"})
     * @ORM\JoinTable(name="product_prices",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="price_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    private $prices;
	
	/**
     * @ORM\ManyToMany(targetEntity="PiggyBox\ShopBundle\Entity\SlicePrice", cascade={"persist"})
     * @ORM\JoinTable(name="product_sliceprices",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="sliceprice_id", referencedColumnName="id", unique=true)}
     *      )
     **/
	private $sliceprices;
	
	/**
     * @ORM\ManyToMany(targetEntity="PiggyBox\ShopBundle\Entity\UnitPrice", cascade={"persist"})
     * @ORM\JoinTable(name="product_unitprices",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="unitprice_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    private $unitprices;	

	/**
     * @ORM\ManyToMany(targetEntity="PiggyBox\ShopBundle\Entity\WeightPrice", cascade={"persist"})
     * @ORM\JoinTable(name="product_weightprices",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="weightprices_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    private $weightprices;	

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;
	
	/**
     * @ORM\OneToOne(targetEntity="PiggyBox\ShopBundle\Entity\Sales", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="sales_id", referencedColumnName="id")
     **/
    private $sales;

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return 'uploads/products';
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            // do whatever you want to generate a unique name
            $this->path = uniqid().'.'.$this->file->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->file->move($this->getUploadRootDir(), $this->path);

        unset($this->file);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }
 
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->prices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sliceprices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->unitprices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->weightprices = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Product
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
     * Set description
     *
     * @param string $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Product
     */
    public function setActive($active)
    {
        $this->active = $active;
    
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set image_path
     *
     * @param string $imagePath
     * @return Product
     */
    public function setImagePath($imagePath)
    {
        $this->image_path = $imagePath;
    
        return $this;
    }

    /**
     * Get image_path
     *
     * @return string 
     */
    public function getImagePath()
    {
        return $this->image_path;
    }

    /**
     * Set promo_active
     *
     * @param string $promoActive
     * @return Product
     */
    public function setPromoActive($promoActive)
    {
        $this->promo_active = $promoActive;
    
        return $this;
    }

    /**
     * Get promo_active
     *
     * @return string 
     */
    public function getPromoActive()
    {
        return $this->promo_active;
    }

    /**
     * Set promo_price
     *
     * @param string $promoPrice
     * @return Product
     */
    public function setPromoPrice($promoPrice)
    {
        $this->promo_price = $promoPrice;
    
        return $this;
    }

    /**
     * Get promo_price
     *
     * @return string 
     */
    public function getPromoPrice()
    {
        return $this->promo_price;
    }

    /**
     * Set promo_expire_date
     *
     * @param \DateTime $promoExpireDate
     * @return Product
     */
    public function setPromoExpireDate($promoExpireDate)
    {
        $this->promo_expire_date = $promoExpireDate;
    
        return $this;
    }

    /**
     * Get promo_expire_date
     *
     * @return \DateTime 
     */
    public function getPromoExpireDate()
    {
        return $this->promo_expire_date;
    }

    /**
     * Set promo_percentage
     *
     * @param float $promoPercentage
     * @return Product
     */
    public function setPromoPercentage($promoPercentage)
    {
        $this->promo_percentage = $promoPercentage;
    
        return $this;
    }

    /**
     * Get promo_percentage
     *
     * @return float 
     */
    public function getPromoPercentage()
    {
        return $this->promo_percentage;
    }

    /**
     * Set createdat
     *
     * @param \DateTime $createdat
     * @return Product
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
     * @return Product
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
     * Set path
     *
     * @param string $path
     * @return Product
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set shop
     *
     * @param PiggyBox\ShopBundle\Entity\Shop $shop
     * @return Product
     */
    public function setShop(\PiggyBox\ShopBundle\Entity\Shop $shop = null)
    {
        $this->shop = $shop;
    
        return $this;
    }

    /**
     * Get shop
     *
     * @return PiggyBox\ShopBundle\Entity\Shop 
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * Add prices
     *
     * @param PiggyBox\ShopBundle\Entity\Price $prices
     * @return Product
     */
    public function addPrice(\PiggyBox\ShopBundle\Entity\Price $prices)
    {
        $this->prices[] = $prices;
    
        return $this;
    }

    /**
     * Remove prices
     *
     * @param PiggyBox\ShopBundle\Entity\Price $prices
     */
    public function removePrice(\PiggyBox\ShopBundle\Entity\Price $prices)
    {
        $this->prices->removeElement($prices);
    }

    /**
     * Get prices
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * Add sliceprices
     *
     * @param PiggyBox\ShopBundle\Entity\SlicePrice $sliceprices
     * @return Product
     */
    public function addSliceprix(\PiggyBox\ShopBundle\Entity\SlicePrice $sliceprices)
    {
        $this->sliceprices[] = $sliceprices;
    
        return $this;
    }

    /**
     * Remove sliceprices
     *
     * @param PiggyBox\ShopBundle\Entity\SlicePrice $sliceprices
     */
    public function removeSliceprix(\PiggyBox\ShopBundle\Entity\SlicePrice $sliceprices)
    {
        $this->sliceprices->removeElement($sliceprices);
    }

    /**
     * Get sliceprices
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSliceprices()
    {
        return $this->sliceprices;
    }

    /**
     * Add unitprices
     *
     * @param PiggyBox\ShopBundle\Entity\UnitPrice $unitprices
     * @return Product
     */
    public function addUnitprix(\PiggyBox\ShopBundle\Entity\UnitPrice $unitprices)
    {
        $this->unitprices[] = $unitprices;
    
        return $this;
    }

    /**
     * Remove unitprices
     *
     * @param PiggyBox\ShopBundle\Entity\UnitPrice $unitprices
     */
    public function removeUnitprix(\PiggyBox\ShopBundle\Entity\UnitPrice $unitprices)
    {
        $this->unitprices->removeElement($unitprices);
    }

    /**
     * Get unitprices
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUnitprices()
    {
        return $this->unitprices;
    }

    /**
     * Add weightprices
     *
     * @param PiggyBox\ShopBundle\Entity\WeightPrice $weightprices
     * @return Product
     */
    public function addWeightprix(\PiggyBox\ShopBundle\Entity\WeightPrice $weightprices)
    {
        $this->weightprices[] = $weightprices;
    
        return $this;
    }

    /**
     * Remove weightprices
     *
     * @param PiggyBox\ShopBundle\Entity\WeightPrice $weightprices
     */
    public function removeWeightprix(\PiggyBox\ShopBundle\Entity\WeightPrice $weightprices)
    {
        $this->weightprices->removeElement($weightprices);
    }

    /**
     * Get weightprices
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getWeightprices()
    {
        return $this->weightprices;
    }

    /**
     * Set sales
     *
     * @param PiggyBox\ShopBundle\Entity\Sales $sales
     * @return Product
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
}
