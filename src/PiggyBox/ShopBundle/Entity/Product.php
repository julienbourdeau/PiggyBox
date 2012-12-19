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
     * @Assert\NotBlank(message = "Le nom du produit est obligatoire.")
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
     * @var string $origin
     *
     * @ORM\Column(name="origin", type="string", length=255, nullable=true)
     */
    private $origin;

    /**
     * @var string $preservation
     *
     * @ORM\Column(name="preservation", type="string", length=255, nullable=true)
     */
    private $preservation;

    /**
     * @var float $price
     *
     * @Assert\Min(limit = "0", message = "Le prix du produit doit être supérieur à 0,00 €.", invalidMessage = "Un nombre doit être indiqué.")
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;

    /**
     * @var float $weightPrice
     *
     * @Assert\Min(limit = "0", message = "Le prix au poids du produit doit être supérieur à 0,00 €.", invalidMessage = "Un nombre doit être indiqué.")
     * @ORM\Column(name="weightPrice", type="float", nullable=true)
     */
    private $weightPrice;

    /**
     * @var float $productWeightPerSlice
     *
     * @Assert\Min(limit = "0", message = "Le poids du produit doit être supérieur à 0Kg.", invalidMessage = "Un nombre doit être indiqué.")
     * @ORM\Column(name="productWeightPerSlice", type="float", nullable=true)
     */
    private $productWeightPerSlice;

    /**
     * @var float $priceType
     *
     * @ORM\Column(name="priceType", type="string", length=100)
     */
    private $priceType;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean",nullable=true)
     */
    private $active = true;

    /**
     * @var integer $minWeight
     *
     * @Assert\Min(limit = "0", message = "Un poids supérieur à 0 gramme doit être indiqué.", invalidMessage = "Un nombre doit être indiqué.")
     * @ORM\Column(name="minWeight", type="integer", nullable=true)
     */
    private $minWeight;

    /**
     * @var integer $maxWeight
     *
     * @ORM\Column(name="maxWeight", type="integer", nullable=true)
     */
    private $maxWeight;

    /**
     * @var integer $minPerson
     *
     * @Assert\Min(limit = "1", message = "Au moins une personne doit pouvoir commander votre produit.", invalidMessage = "Un nombre doit être indiqué.")
     * @ORM\Column(name="minPerson", type="integer", nullable=true)
     */
    private $minPerson;

    /**
     * @var integer $maxPerson
     *
     * @ORM\Column(name="maxPerson", type="integer", nullable=true)
     */
    private $maxPerson;

    /**
     * @var string $image_path
     *
     * @ORM\Column(name="image_path", type="string", length=255, nullable=true)
     */
    private $image_path;

    /**
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=255, unique=false)
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
     * @ORM\ManyToOne(targetEntity="Shop", inversedBy="products")
     * @ORM\JoinColumn(name="shop_id", referencedColumnName="id")
     **/
    private $shop;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @Assert\Image(maxSize="6000000")
     */
    public $file;

    /**
     * @ORM\OneToOne(targetEntity="PiggyBox\ShopBundle\Entity\Sales", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="sales_id", referencedColumnName="id")
     **/
    private $sales;

    /**
     * @ORM\ManyToOne(targetEntity="PiggyBox\ShopBundle\Entity\Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     **/
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity="PiggyBox\ShopBundle\Entity\MenuItem", mappedBy="products")
     **/
    private $menuItems;

    /**
     * @var boolean $promotion
     *
     * @ORM\Column(name="promotion", type="boolean",nullable=true)
     */
    private $promotion = false;

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
            $this->path = $this->name.'-'.uniqid().'.'.$this->file->guessExtension();
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
     * @param  string  $name
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
     * @param  string  $description
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
     * Set origin
     *
     * @param  string  $origin
     * @return Product
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Get origin
     *
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Set preservation
     *
     * @param  string  $preservation
     * @return Product
     */
    public function setPreservation($preservation)
    {
        $this->preservation = $preservation;

        return $this;
    }

    /**
     * Get preservation
     *
     * @return string
     */
    public function getPreservation()
    {
        return $this->preservation;
    }

    /**
     * Set price
     *
     * @param  float   $price
     * @return Product
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
     * Set weightPrice
     *
     * @param  float   $weightPrice
     * @return Product
     */
    public function setWeightPrice($weightPrice)
    {
        $this->weightPrice = $weightPrice;

        return $this;
    }

    /**
     * Get weightPrice
     *
     * @return float
     */
    public function getWeightPrice()
    {
        return $this->weightPrice;
    }

    /**
     * Set priceType
     *
     * @param  string  $priceType
     * @return Product
     */
    public function setPriceType($priceType)
    {
        $this->priceType = $priceType;

        return $this;
    }

    /**
     * Get priceType
     *
     * @return string
     */
    public function getPriceType()
    {
        return $this->priceType;
    }

    /**
     * Set active
     *
     * @param  boolean $active
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
     * Set minWeight
     *
     * @param  integer $minWeight
     * @return Product
     */
    public function setMinWeight($minWeight)
    {
        $this->minWeight = $minWeight;

        return $this;
    }

    /**
     * Get minWeight
     *
     * @return integer
     */
    public function getMinWeight()
    {
        return $this->minWeight;
    }

    /**
     * Set maxWeight
     *
     * @param  integer $maxWeight
     * @return Product
     */
    public function setMaxWeight($maxWeight)
    {
        $this->maxWeight = $maxWeight;

        return $this;
    }

    /**
     * Get maxWeight
     *
     * @return integer
     */
    public function getMaxWeight()
    {
        return $this->maxWeight;
    }

    /**
     * Set minPerson
     *
     * @param  integer $minPerson
     * @return Product
     */
    public function setMinPerson($minPerson)
    {
        $this->minPerson = $minPerson;

        return $this;
    }

    /**
     * Get minPerson
     *
     * @return integer
     */
    public function getMinPerson()
    {
        return $this->minPerson;
    }

    /**
     * Set maxPerson
     *
     * @param  integer $maxPerson
     * @return Product
     */
    public function setMaxPerson($maxPerson)
    {
        $this->maxPerson = $maxPerson;

        return $this;
    }

    /**
     * Get maxPerson
     *
     * @return integer
     */
    public function getMaxPerson()
    {
        return $this->maxPerson;
    }

    /**
     * Set image_path
     *
     * @param  string  $imagePath
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
     * Set createdat
     *
     * @param  \DateTime $createdat
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
     * @param  \DateTime $updatedat
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
     * @param  string  $path
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
     * @param  \PiggyBox\ShopBundle\Entity\Shop $shop
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
     * @return \PiggyBox\ShopBundle\Entity\Shop
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * Set sales
     *
     * @param  \PiggyBox\ShopBundle\Entity\Sales $sales
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
     * @return \PiggyBox\ShopBundle\Entity\Sales
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * Set category
     *
     * @param  \PiggyBox\ShopBundle\Entity\Category $category
     * @return Product
     */
    public function setCategory(\PiggyBox\ShopBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \PiggyBox\ShopBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set productWeightPerSlice
     *
     * @param  float   $productWeightPerSlice
     * @return Product
     */
    public function setProductWeightPerSlice($productWeightPerSlice)
    {
        $this->productWeightPerSlice = $productWeightPerSlice;

        return $this;
    }

    /**
     * Get productWeightPerSlice
     *
     * @return float
     */
    public function getProductWeightPerSlice()
    {
        return $this->productWeightPerSlice;
    }

    /**
     * Set slug
     *
     * @param  string  $slug
     * @return Product
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

    public function hasImage()
    {
        return file_exists($this->getWebPath());
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setPriceForChunkPrice()
    {
        if ($this->priceType == 'chunk_price') {
            $this->price = round($this->minWeight * $this->weightPrice/1000, 2, PHP_ROUND_HALF_UP);
        }
        if ($this->priceType == 'unit_variable_price') {
            $this->price = round($this->productWeightPerSlice * $this->weightPrice/1000, 2, PHP_ROUND_HALF_UP);
        }
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
     * @return Product
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
     * Set promotion
     *
     * @param  boolean $promotion
     * @return Product
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;

        return $this;
    }

    /**
     * Get promotion
     *
     * @return boolean
     */
    public function getPromotion()
    {
        return $this->promotion;
    }
}
