<?php

namespace PiggyBox\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PiggyBox\UserBundle\Entity\User
 *
 * @ORM\Table(name="piggybox_user")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ORM\Entity(repositoryClass="PiggyBox\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * @var string $lastName
     *
     * @ORM\Column(name="lastName", type="string", nullable=true)
     *
     * @Assert\NotBlank(message="SVP, indiquez votre nom.", groups={"Registration", "Profile"})
     * @Assert\MinLength(limit="3", message="Le nom est trop petit.", groups={"Registration", "Profile"})
     * @Assert\MaxLength(limit="255", message="Le nom est trop long.", groups={"Registration", "Profile"})
     */
    private $lastName;

    /**
     * @var string $firstName
     *
     * @ORM\Column(name="firstName", type="string", nullable=true)
     *
     * @Assert\NotBlank(message="SVP, indiquez votre prénom.", groups={"Registration", "Profile"})
     * @Assert\MinLength(limit="3", message="Le prénom est trop petit.", groups={"Registration", "Profile"})
     * @Assert\MaxLength(limit="255", message="Le prénom est trop long.", groups={"Registration", "Profile"})
     */
    private $firstName;

    /**
     * @var string $gender
     *
     * @ORM\Column(name="gender", type="string", nullable=true)
     * @Assert\Choice(choices = {"M.", "Mme", "Mlle"}, message = "Choisissez votre civilité.")
     */
    private $gender;

    /**
     * @var string $birthday
     *
     * @ORM\Column(name="birthday", type="datetime", nullable=true)
     */
    private $birthday;

    /**
     * @var string $city
     *
     * @ORM\Column(name="city", type="string", nullable=true)
     */
    private $city;

    /**
     * @var string $phoneNumber
     *
     * @ORM\Column(name="phoneNumber", type="string", nullable=true)
     */
    private $phoneNumber;

     /**
     * @var string
     *
     * @ORM\Column(name="facebookId", type="string", length=255, nullable=true)
     */
    protected $facebookId;

    /**
     * @ORM\OneToOne(targetEntity="PiggyBox\ShopBundle\Entity\Shop")
     * @ORM\JoinColumn(name="ownshop_id", referencedColumnName="id")
     **/
    private $ownshop;

     /**
     * @ORM\ManyToMany(targetEntity="PiggyBox\ShopBundle\Entity\Shop", mappedBy="clients")
     **/
    private $shops;

    /**
     * @ORM\OneToMany(targetEntity="PiggyBox\OrderBundle\Entity\Order", mappedBy="user")
     **/
    private $orders;

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
     * Set ownshop
     *
     * @param  PiggyBox\ShopBundle\Entity\Shop $ownshop
     * @return User
     */
    public function setOwnshop(\PiggyBox\ShopBundle\Entity\Shop $ownshop = null)
    {
        $this->ownshop = $ownshop;

        return $this;
    }

    /**
     * Get ownshop
     *
     * @return PiggyBox\ShopBundle\Entity\Shop
     */
    public function getOwnshop()
    {
        return $this->ownshop;
    }

    /**
     * Add shops
     *
     * @param  PiggyBox\ShopBundle\Entity\Shop $shops
     * @return User
     */
    public function addShop(\PiggyBox\ShopBundle\Entity\Shop $shops)
    {
        $this->shops[] = $shops;

        return $this;
    }

    /**
     * Remove shops
     *
     * @param PiggyBox\ShopBundle\Entity\Shop $shops
     */
    public function removeShop(\PiggyBox\ShopBundle\Entity\Shop $shops)
    {
        $this->shops->removeElement($shops);
    }

    /**
     * Get shops
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getShops()
    {
        return $this->shops;
    }

    /**
     * Add orders
     *
     * @param  PiggyBox\OrderBundle\Entity\Order $orders
     * @return User
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
     * Set deletedAt
     *
     * @param  \DateTime $deletedAt
     * @return User
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

    /**
     * Set lastName
     *
     * @param  string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set firstName
     *
     * @param  string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set gender
     *
     * @param  string $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set birthday
     *
     * @param  \DateTime $birthday
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set phoneNumber
     *
     * @param  string $phoneNumber
     * @return User
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set city
     *
     * @param  string $city
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    //public function unserialize($data)
    //{
    //    list($this->facebookId, $parentData) = unserialize($data);
    //    parent::unserialize($parentData);
    //}

    /**
     * @param string $facebookId
     * @return void
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
        $this->setUsername($facebookId);
    }

    /**
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * @param Array
     */
    public function setFBData($fbdata)
    {
        if (isset($fbdata['id'])) {
            $this->setFacebookId($fbdata['id']);
            $this->addRole('ROLE_FACEBOOK');
            $this->addRole('ROLE_USER');
        }
        if (isset($fbdata['first_name'])) {
            $this->setFirstname($fbdata['first_name']);
        }
        if (isset($fbdata['last_name'])) {
            $this->setLastname($fbdata['last_name']);
        }
        if (isset($fbdata['email'])) {
            $this->setEmail($fbdata['email']);
        }
        if (isset($fbdata['birthday'])) {
            $this->setBirthday(new \Datetime($fbdata['birthday']));
        }
        if (isset($fbdata['location'])) {
            $this->setCity($fbdata['location']['name']);
        }
        if (isset($fbdata['gender'])) {
            if ($fbdata['gender'] == 'male') {
                $this->setGender('M.');
            }
            if ($fbdata['gender'] == 'female') {
                $this->setGender('Mme');
            }
        }
    }
}
