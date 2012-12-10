<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PiggyBox\ShopBundle\Entity\Day
 *
 * @ORM\Table(name="piggybox_day")
 * @ORM\Entity(repositoryClass="PiggyBox\ShopBundle\Entity\DayRepository")
 */
class Day
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
     * @var boolean $open
     *
     * @ORM\Column(name="open", type="boolean", nullable=true)
     */
    private $open;

    /**
     * @var \DateTime $from_time_morning
     *
     * @ORM\Column(name="from_time_morning", type="time", nullable=true)
     */
    private $from_time_morning;

    /**
     * @var \DateTime $to_time_morning
     *
     * @ORM\Column(name="to_time_morning", type="time", nullable=true)
     */
    private $to_time_morning;

    /**
     * @var \DateTime $from_time_afternoon
     *
     * @ORM\Column(name="from_time_afternoon", type="time", nullable=true)
     */
    private $from_time_afternoon;

    /**
     * @var \DateTime $to_time_afternoon
     *
     * @ORM\Column(name="to_time_afternoon", type="time", nullable=true)
     */
    private $to_time_afternoon;

    /**
     * @var integer $day_of_the_week
     *
     * @ORM\Column(name="day_of_the_week", type="integer")
     */
    private $day_of_the_week;

    /**
     * @ORM\ManyToOne(targetEntity="Shop", inversedBy="opening_days")
     * @ORM\JoinColumn(name="shop_id", referencedColumnName="id")
     **/
     private $shop;

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
     * Set open
     *
     * @param  boolean $open
     * @return Day
     */
    public function setOpen($open)
    {
        $this->open = $open;

        return $this;
    }

    /**
     * Get open
     *
     * @return boolean
     */
    public function getOpen()
    {
        return $this->open;
    }

    /**
     * Set from_time_morning
     *
     * @param  \DateTime $fromTimeMorning
     * @return Day
     */
    public function setFromTimeMorning($fromTimeMorning)
    {
        $this->from_time_morning = $fromTimeMorning;

        return $this;
    }

    /**
     * Get from_time_morning
     *
     * @return \DateTime
     */
    public function getFromTimeMorning()
    {
        return $this->from_time_morning;
    }

    /**
     * Set to_time_morning
     *
     * @param  \DateTime $toTimeMorning
     * @return Day
     */
    public function setToTimeMorning($toTimeMorning)
    {
        $this->to_time_morning = $toTimeMorning;

        return $this;
    }

    /**
     * Get to_time_morning
     *
     * @return \DateTime
     */
    public function getToTimeMorning()
    {
        return $this->to_time_morning;
    }

    /**
     * Set from_time_afternoon
     *
     * @param  \DateTime $fromTimeAfternoon
     * @return Day
     */
    public function setFromTimeAfternoon($fromTimeAfternoon)
    {
        $this->from_time_afternoon = $fromTimeAfternoon;

        return $this;
    }

    /**
     * Get from_time_afternoon
     *
     * @return \DateTime
     */
    public function getFromTimeAfternoon()
    {
        return $this->from_time_afternoon;
    }

    /**
     * Set to_time_afternoon
     *
     * @param  \DateTime $toTimeAfternoon
     * @return Day
     */
    public function setToTimeAfternoon($toTimeAfternoon)
    {
        $this->to_time_afternoon = $toTimeAfternoon;

        return $this;
    }

    /**
     * Get to_time_afternoon
     *
     * @return \DateTime
     */
    public function getToTimeAfternoon()
    {
        return $this->to_time_afternoon;
    }

    /**
     * Set day_of_the_week
     *
     * @param  integer $dayOfTheWeek
     * @return Day
     */
    public function setDayOfTheWeek($dayOfTheWeek)
    {
        $this->day_of_the_week = $dayOfTheWeek;

        return $this;
    }

    /**
     * Get day_of_the_week
     *
     * @return integer
     */
    public function getDayOfTheWeek()
    {
        return $this->day_of_the_week;
    }

    /**
     * Set shop
     *
     * @param  PiggyBox\ShopBundle\Entity\Shop $shop
     * @return Day
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
}
