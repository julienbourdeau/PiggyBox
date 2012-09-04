<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PiggyBox\ShopBundle\Entity\Category
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PiggyBox\ShopBundle\Entity\CategoryRepository")
 */
class Category
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
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var integer $level_depth
     *
     * @ORM\Column(name="level_depth", type="integer")
     */
    private $level_depth;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var \DateTime $createdat
     *
     * @ORM\Column(name="createdat", type="datetime")
     */
    private $createdat;

    /**
     * @var \DateTime $updatedat
     *
     * @ORM\Column(name="updatedat", type="datetime")
     */
    private $updatedat;


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
     * @return Category
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
     * @return Category
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
     * Set level_depth
     *
     * @param integer $levelDepth
     * @return Category
     */
    public function setLevelDepth($levelDepth)
    {
        $this->level_depth = $levelDepth;
    
        return $this;
    }

    /**
     * Get level_depth
     *
     * @return integer 
     */
    public function getLevelDepth()
    {
        return $this->level_depth;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Category
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
     * Set createdat
     *
     * @param \DateTime $createdat
     * @return Category
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
     * @return Category
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
}