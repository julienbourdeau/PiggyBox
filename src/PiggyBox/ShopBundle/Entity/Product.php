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
    const UNIT_PRICE = 'unit_price';
    const WEIGHT_PRICE = 'weight_price';
    const SLICE_PRICE = 'slice_price';

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
     * @var decimal $price
     *
     * @ORM\Column(name="price", type="decimal", precision=2)
     */
    private $price;

    /**
     * @var decimal $weightPrice
     *
     * @ORM\Column(name="weightPrice", type="decimal", precision=2)
     */
    private $weightPrice;
	
    /**
     * @var string $priceType
     *
     * @ORM\Column(name="priceType", type="string", length=100)
     */
    private $priceType;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean",nullable=true)
     */
    private $active;

    /**
     * @var integer $minWeight
     *
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
}
